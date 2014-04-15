<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["Statistics"] = array(
	"name" => "Statistics",
	"description" => "page statistics code settings",
	"version" => ESOTALK_VERSION,
	"author" => "lvni",
	"authorEmail" => "lvni@trierr.com",
	"authorURL" => "http://bbs.trierr.com",
	"license" => "GPLv2"
);

class ETPlugin_Statistics extends ETPlugin {



	public function settings($sender)
	{
		// Set up the settings form.
		$form = ETFactory::make("form");
		$form->action = URL("admin/plugins");
		$form->setValue("code", C("plugin.Statistics.code"));

		// If the form was submitted...
		if ($form->validPostBack("statisticsSave")) {

			// Construct an array of config options to write.
			$config = array();
			$config["plugin.Statistics.code"] = $form->getValue("code");

			if (!$form->errorCount()) {

				// Write the config file.
				ET::writeConfig($config);

				$sender->message(T("message.changesSaved"), "success");
				$sender->redirect(URL("admin/plugins"));

			}
		}

		$sender->data("statisticsSettingsForm", $form);
		return $this->getView("settings");
	}
    
    /**
     * On all controller initializations, add the Statistics code to the page.
     *
     * @return void
     */
    public function handler_init($sender)
    {
        $code = C("plugin.Statistics.code");
        if($code)
            $sender->addToMenu("meta", "Statistics", $code);
    }
    

}
