var BBCode = {

bold: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[b]", "[/b]");},
italic: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[i]", "[/i]");},
strikethrough: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[s]", "[/s]");},
header: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[h]", "[/h]");},
link: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[url=http://example.com]", "[/url]", "http://example.com", "link text");},
image: function(id) {showImageForm(id);},
fixed: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[code]", "[/code]");},

};
function showImageForm(id){
	var dialogbox = document.getElementById('BBCode_image_upload_template').innerHTML;
	ETSheet.showSheet('BBCode_image_upload_box', dialogbox, function(){
	//ETConversation.wrapText($("#"+id+" textarea"), "[img]", "[/img]", "", "http://example.com/image.jpg");
		$("#BBCode_image_upload_box form").submit(function(e) {
			e.preventDefault();
			$(this).ajaxSubmit({
				url: ET.webPath+"/?p=image/upload",
				type: "post",
				data:$("#BBCode_image_upload_box form").serialize(),
				success: function(data) {
					if(!data.status){
						alert(data.msg);
						return;
					}
					image_url = data.url;
					ETConversation.wrapText($("#"+id+" textarea"), "[img]", "[/img]", "", image_url);
					ETSheet.hideSheet("BBCode_image_upload_box");
				}
			})
		});
		
		$("#BBCode_image_upload_box input[name=addRemoteBnt]").click(function(){
			image_url = $("#BBCode_image_upload_box input[name=remote_url]").val();
			reg = new RegExp(/http(s?):\/\/(.*)/);
			if(!reg.test(image_url)){
				alert("image url error");
				$("#BBCode_image_upload_box input[name=remote_url]").focus();
				return;
			}
			ETConversation.wrapText($("#"+id+" textarea"), "[img]", "[/img]", "", image_url);
			ETSheet.hideSheet("BBCode_image_upload_box");
		});
		
	});
}