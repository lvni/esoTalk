<?php
// Copyright 2013 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;
class ImageController extends ETController {
	
	public function upload(){
	
		if (!ET::$session->user) exit("please login");

	// If the user is suspended, show an error.
		if (ET::$session->isSuspended()) {
			$this->renderMessage("Error!", T("message.suspended"));
			return;
		}
		//if (!$this->validateToken()) return;
		$imageuploaddir = PATH_UPLOADS.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR.date("Y".DIRECTORY_SEPARATOR."n".DIRECTORY_SEPARATOR."d");
		if(!is_dir($imageuploaddir)){
			if(!@mkdir($imageuploaddir,0775,true)){
				return "The $imageuploaddir directory does not exist or is not writeable.";
			}
		}
		//var_dump($_FILES['my_uploaded_file']);
		if(isset($_FILES['my_uploaded_file']) && isset($_FILES['my_uploaded_file']['tmp_name'])){
			
			$destination = $imageuploaddir.DIRECTORY_SEPARATOR.uniqid()."-".$_FILES['my_uploaded_file']['name'];
			move_uploaded_file($_FILES['my_uploaded_file']['tmp_name'],$destination);
			$ret = $this->push2Remote(realpath($destination));
			header("Content-Type: application/json;charset=utf-8");
			echo $ret ;
			//unset($destination);
		  
		}
	}
	
	
	private function push2Remote($path){
		
		$ch = curl_init();
		$data = array( 'my_uploaded_file' => "@{$path}");
		$options = array(
			CURLOPT_URL    => 'http://wxtuchuang.duapp.com/upload', //随时会更换第三方的图床
			CURLOPT_POST   => true,
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => $data
		);
		
		curl_setopt_array($ch,$options);
		$reponse = curl_exec($ch);
		return $reponse;
	}
}