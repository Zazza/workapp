<div id="fm"></div>

<div id="p_uploader" style="display: none" title="Upload files">
<div id="content">
	<div class="fieldset flash" id="fsUploadProgress">
		<span class="legend">Upload Queue</span>
	</div>
	<div id="divStatus">0 Files Uploaded</div>
	<div>
		<span id="spanButtonPlaceHolder"></span>
		<input id="btnCancel" type="button" value="Cancel All Uploads" onclick="swfu.cancelQueue();" disabled="disabled" style="margin-left: 2px; font-size: 8pt; height: 29px;" />
	</div>
</div>
</div>

<div id="photo_filesystem">
	<div style="text-align: center; margin-top: 30px"><img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></div>
</div>

<!-- FILE DIALOG  -->
<div id="fDialog" style="display: none" title="File property">
	<div id="tabs">
	
	<ul>
	<li><a href="#fdmain">Info</a></li>
	{% if registry.auth %}
	<li><a href="#fdchmod">Mode</a></li>
	{% endif %}
	
	</ul>
	
	
	<div id="fdmain" class="tabcont" style="text-align: left">
		<input type="hidden" id="fid" />
		<div id="dopenfile"></div>
		<div style="margin-top: 10px">Owner: <span id="fowner"></span></div>
		<div>Size: <span id="fsize"></span></div>
	</div>

	{% if registry.auth %}
	<div id="fdchmod" class="tabcont" style="text-align: left"></div>
	{% endif %}
	
	</div>
</div>
<!-- END FILE DIALOG  -->

<!-- DIR DIALOG  -->
<div id="dirDialog" style="display: none; text-align: left" title="Folder access mode"></div>
<!-- END DIR DIALOG  -->

<!-- FILE CONTEXT MENU -->
<div class="contextMenu" id="fileMenu" style="display: none">
	<ul class="cm">
		<li id="rm_open"><img src="{{ registry.uri }}img/context/document.png" class="cm_img" />Open</li>
		{% if registry.auth %}
		<li id="rm_rename"><img src="{{ registry.uri }}img/context/document-rename.png" class="cm_img" />Rename</li>
		{% endif %}
		<li id="rm_main"><img src="{{ registry.uri }}img/context/document-image.png" class="cm_img" />Info</li>
		{% if registry.auth %}
		<li id="rm_right"><img src="{{ registry.uri }}img/context/users.png" class="cm_img" />Mode</li>
		{% endif %}
	</ul>
</div>
<!-- DIR CONTEXT MENU -->
<div class="contextMenu" id="dirMenu" style="display: none">
	<ul class="cm">
		<li id="rd_open"><img src="{{ registry.uri }}img/context/folder-open.png" class="cm_img" />Open</li>
		{% if registry.auth %}
		<li id="rd_rename"><img src="{{ registry.uri }}img/context/document-rename.png" class="cm_img" />Rename</li>
		<li id="rd_right"><img src="{{ registry.uri }}img/context/users.png" class="cm_img" />Mode</li>
		{% endif %}
	</ul>
</div>

<div title='Notes' style='display: none;' id='pNotes'>
	{% if registry.auth %}
	<textarea id='fText' style='height: 100px; width: 340px'></textarea>
	<p><input type='button' name='addFileText' value='Add notes' onclick='p_addFileText()'></p>
	{% endif %}
	
	<div id='resNotes'></div>

</div>

<script type="text/javascript">
$(function(){
var settings = {
		flash_url: "{{ registry.uri }}{{ registry.path.modules }}FM/swf/swfupload.swf",
		upload_url: "{{ registry.uri }}photo/save/?id={{ registry.get.id }}",
		post_params: {"{{ session_name }}" : "{{ session_id }}"},
		file_size_limit: "{{ maxUploadSize }}",
		file_types: "{{ config.file_types }}",
		file_types_description: "Images",
		file_post_name: "Filedata",
		file_upload_limit: "{{ config.file_upload_limit }}",
		file_queue_limit: 0,
		custom_settings: {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,
		button_image_url: "{{ registry.uri }}{{ registry.path.modules }}Photo/img/TestImageNoText_65x29.png",
		button_width: "65",
		button_height: "29",
		button_placeholder_id: "spanButtonPlaceHolder",
		button_text: '<span class="theFont">Select</span>',
		button_text_style: ".theFont { font-size: 13px; }",
		button_text_left_padding: 15,
		button_text_top_padding: 4,
		file_queued_handler: fileQueued,
		file_queue_error_handler: fileQueueError,
		file_dialog_complete_handler: fileDialogComplete,
		upload_start_handler: uploadPhotoStart,
		upload_progress_handler: uploadProgress,
		upload_error_handler: uploadError,
		upload_success_handler: uploadSuccess,
		upload_complete_handler: uploadComplete,
		queue_complete_handler: queueComplete
	};

	swfu = new SWFUpload(settings);
});
</script>
