<?php
if (!defined("IN_ESOTALK")) exit;

?>
<script type="text/template" id="BBCode_image_upload_template" >
<div class='sheet' id='BBCode_image_upload_box'>
<div class='sheetContent'>
	<h3>插入图片</h3>
	
	
	<div class="sheetBody">
	<div class="section">
	<ul class='list channelList changeChannelList'>
		<li>
		<form action="" method="POST" enctype="multipart/form-data">
		<input type="file" name="my_uploaded_file" style="width: 428px;"/>
		<input type="submit" value="确定"/>
		</form>
		</li>
		<li>或者:</li>
		<li>
	
		使用远程图片:<input type="text" name="remote_url" placeholder="http://www.example.com/picturename.jpg" style="width: 320px;"/>
		<input type="button" name="addRemoteBnt" value="确定"/>
		</li>
	</ul>
	</div>
	</div>
	
</div>
</div>
</script>