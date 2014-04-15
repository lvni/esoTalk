<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays the settings form for the Proto skin.
 *
 * @package esoTalk
 */

$form = $data["statisticsSettingsForm"];
?>
<?php echo $form->open(); ?>

<div class='section'>

<ul class='form'>

<li>
<label><?php echo T("Code"); ?></label>
<?php echo $form->input("code", "textarea",array("style" => "height:200px; width:350px")); ?>
</li>
</ul>

</div>

<div class='buttons'>
<?php echo $form->saveButton("statisticsSave"); ?>
</div>

<?php echo $form->close(); ?>
