<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Video"] = array(
	"name" => "Video",
	"description" => "支持插入优酷、土豆和youtube视频",
	"version" => ESOTALK_VERSION,
	"author" => "esoTalk Team",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2"
);


/**
 * XiamiMusic Formatter Plugin
 *
 * Interprets XiamiMusic in posts and converts it to HTML formatting when rendered. Also adds BBCode formatting
 * buttons to the post editing/reply area.
 */
class ETPlugin_Video extends ETPlugin {

	
/**
 * Add an event handler to the initialization of the conversation controller to add BBCode CSS and JavaScript
 * resources.
 *
 * @return void
 */
public function handler_conversationController_renderBefore($sender)
{
	$sender->addJSFile($this->getResource("video.js"));
	$sender->addCSSFile($this->getResource("video.css"));
}


/**
 * Add an event handler to the "getEditControls" method of the conversation controller to add BBCode
 * formatting buttons to the edit controls.
 *
 * @return void
 */
public function handler_conversationController_getEditControls($sender, &$controls, $id)
{
	addToArrayString($controls, "Video", "<a href='javascript:BBVideo.parse(\"$id\");void(0)' title='".T("Video")."' class='Video-icon'><span>".T("Video")."</span></a>", 0);

}

public function handler_format_format($sender)
{
	$sender->content = preg_replace_callback("/\[video\](.*?)\[\/video\]/i", array($this, "videoCallback"), $sender->content);
}

public function videoCallback($matches){
	
	$video_url = isset($matches[1]) ? $matches[1] : "";
	$video_width  = $this->isMobile() ? 320 : 825 ;
	$video_height = $this->isMobile() ? 200 : 460;
	$youku_pattern = "/v\.youku\.com\/v_show\/id_(\w+)\.html/";
	$tudou_pattern = "/www\.tudou.com\/(\w+)\/([^\s\\\\.]+)\/([^\s\\\\.]+)(\.html)?/";
	$youtube_pattern = "/(www\.youtube\.com\/watch\?v=([^\s\\\\.]+))|(youtu\.be\/([^\s\\\\.]+))/";
	if($video_url){
		//youku 
		$ret = preg_match($youku_pattern,$video_url,$match);
		if($ret && isset($match[1])){
			// match youku
			$video_url = '<iframe height='.$video_height.' width='.$video_width.' src="http://player.youku.com/embed/'.$match[1].'" frameborder=0 allowfullscreen></iframe>';
			return $video_url;
		}
		// tudou
		$ret = preg_match($tudou_pattern,$video_url,$match);
		//var_dump($match);
		$tudou_embed_types = array('programs'=>0,'listplay'=>1,'albumplay'=>2); 
		if($ret &&  isset($match[1]) && isset($match[2]) && isset($match[3]) && array_key_exists($match[1],$tudou_embed_types)){
			$type  = $tudou_embed_types[$match[1]];
			$code  = $match[3];
			$lcode = $match[2] == 'programs' ? "" : $match[2];
			$video_url = '<iframe src="http://www.tudou.com/programs/view/html5embed.action?type='.$type.'&code='.$code.'&lcode='.$lcode.'&resourceId=0_06_05_99" allowtransparency="true" scrolling="no" border="0" frameborder="0" style="width:'.$video_width.'px;height:'.$video_height.'px;"></iframe>';
			return $video_url;
		}
		// youtube 
		$ret = preg_match($youtube_pattern,$video_url,$match);
		if($ret && ( $match[4] || $match[2]) ){
			$youtube_url = $match[4] ? $match[4] : $match[2];
			$video_url = '<iframe width="'.$video_width.'" height="'.$video_height.'" src="//www.youtube.com/embed/'.$youtube_url.'" frameborder="0" allowfullscreen></iframe>';
			return $video_url;
		}
		// other but not support now
		$video_url = '<a href="'.$video_url.'" target="_blank" class="link-external">'.$video_url.' <i class="icon-external-link"></i></a>';
		return $video_url;
	}else{
		return $video_url;
	}
}

public function isMobile()
{
	static $result = array();
	if(isset($result['isMobile'])){
		return $result['isMobile'];
	}
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
		$result['isMobile'] = true;
        return $result['isMobile'];
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        $result['isMobile'] =  stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		return $result['isMobile'];
	}
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
            );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            $result['isMobile'] = true;
			return $result['isMobile'];
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            $result['isMobile'] = true;
			return $result['isMobile'];
        }
    }
	$result['isMobile'] = false;
    return $result['isMobile'];
}

}
