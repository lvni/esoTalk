<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["XiamiMusic"] = array(
	"name" => "XiamiMusic",
	"description" => "支持虾米音乐",
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
class ETPlugin_XiamiMusic extends ETPlugin {


/**
 * Add an event handler to the initialization of the conversation controller to add BBCode CSS and JavaScript
 * resources.
 *
 * @return void
 */
public function handler_conversationController_renderBefore($sender)
{
	$sender->addJSFile($this->getResource("xiamimusic.js"));
	$sender->addCSSFile($this->getResource("xiamimusic.css"));
}


/**
 * Add an event handler to the "getEditControls" method of the conversation controller to add BBCode
 * formatting buttons to the edit controls.
 *
 * @return void
 */
public function handler_conversationController_getEditControls($sender, &$controls, $id)
{
	addToArrayString($controls, "XiamiMusic", "<a href='javascript:XiamiMusic.music(\"$id\");void(0)' title='".T("XiamiMusic")."' class='XiamiMusic-icon'><span>".T("XiamiMusic")."</span></a>", 0);

}

public function handler_format_format($sender)
{
	$sender->content = preg_replace_callback("/\[xiami(=weibo)?\](.*?)\[\/xiami\]/i", array($this, "xiamiCallback"), $sender->content);
}

public function xiamiCallback($matches){
	$url = isset($matches[2]) ? $matches[2] :  "" ;
	if(isset($url)){
		//有链接，检查是否是虾米url，并获取id
		$valid_pattern = "/http:\/\/www\.xiami\.com\/song\/(\d+)/";
		preg_match($valid_pattern,$url,$match);
		if(isset($match[1])){
			if($matches[1] && $matches[1] == "=weibo" && !$this->isMobile()){
				//虾米微博播放器
				$url = '<embed id="STK_139729297652716" height="200" allowscriptaccess="never" style="visibility: visible;" pluginspage="http://get.adobe.com/cn/flashplayer/" flashvars="playMovie=true&amp;auto=1" width="440" allowfullscreen="true" quality="high" src="http://www.xiami.com/res/app/img/swf/weibo.swf?dataUrl=http://www.xiami.com/app/player/song/id/'.$match[1].'/type/7/uid/0" type="application/x-shockwave-flash" wmode="transparent">';
			}else{
				$url = "";
				if($this->isMobile()){
					$url = "<style>p{margin:1em 0;}</style>";
				}
				$url .= '<script type="text/javascript" src="http://www.xiami.com/widget/player-single?uid=966701&sid='.$match[1].'&mode=js"></script>';
			}
		}
		
	}
	return $url;
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
