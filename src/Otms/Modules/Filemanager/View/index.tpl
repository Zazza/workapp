<div id="fm"></div>

<div id="uploader" style="display: none" title="Upload files">
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

<div id="fm_filesystem">
	<div style="text-align: center; margin-top: 30px"><img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></div>
</div>

<!-- FILE DIALOG  -->
<div id="fDialog" style="display: none" title="File property">
	<div id="tabs">
	
	<ul>
	<li><a href="#fdmain">Info</a></li>
	<li><a href="#fdhistory">History</a></li>
	<li><a href="#fdchmod">Mode</a></li>
	
	</ul>
	
	
	<div id="fdmain" class="tabcont" style="text-align: left">
		<input type="hidden" id="fid" />
		<div id="dopenfile"></div>
		<div style="margin-top: 10px">Owner: <span id="fowner"></span></div>
		<div>Size: <span id="fsize"></span></div>
		
		<div id="wndShare" class="lfm_border">
			<div class="btn"><label class="checkbox"><input id="share" type="checkbox" name="share" />&nbsp;Share</label></div>
			<div style="display: none; padding-top: 10px" id="shareName"><b>URL: </b>{{ registry.siteName }}{{ registry.uri }}download/?filename=<span class="shfname"></span></div>
		</div>
		
		<div id="dfiletext" style="margin-top: 10px"></div>
		<div style="margin-top: 20px">
			<textarea id="fText" style="height: 100px; width: 340px"></textarea>
			<p><input type="button" name="addFileText" value="Add notes" onclick="addFileText()"></p>
		</div>
	</div>
	
	<div id="fdhistory" class="tabcont" style="text-align: left"></div>
	
	<div id="fdchmod" class="tabcont" style="text-align: left"></div>
	
	</div>
</div>
<!-- END FILE DIALOG  -->

<!-- DIR DIALOG  -->
<div id="dirDialog" style="display: none; text-align: left" title="Folder access mode"></div>
<!-- END DIR DIALOG  -->

<!-- FILE CONTEXT MENU -->
<div class="contextMenu" id="fileMenu" style="display: none">
	<ul class="cm">
		<li id="rm_open"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/document.png" class="cm_img" />Open</li>
		<li id="rm_rename"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/document-rename.png" class="cm_img" />Rename</li>
		<li id="rm_main"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/document-image.png" class="cm_img" />Info</li>
		<li id="rm_history"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/clock-history.png" class="cm_img" />History</li>
		<li id="rm_right"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/users.png" class="cm_img" />Mode</li>
	</ul>
</div>
<!-- DIR CONTEXT MENU -->
<div class="contextMenu" id="dirMenu" style="display: none">
	<ul class="cm">
		<li id="rd_open"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/folder-open.png" class="cm_img" />Open</li>
		<li id="rd_rename"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/document-rename.png" class="cm_img" />Rename</li>
		<li id="rd_right"><img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/context/users.png" class="cm_img" />Mode</li>
	</ul>
</div>

<div title="Новая папка" id="diradddialog" style="display: none;">
	Имя папки:
	<input type="text" name="dirname" id="fm_dirname" />
</div>

<script type="text/javascript">
$(function(){
var settings = {
		flash_url: "{{ registry.uri }}swf/swfupload.swf",
		upload_url: "{{ registry.uri }}filemanager/save/",
		post_params: {"{{ session_name }}" : "{{ session_id }}"},
		file_size_limit: "{{ maxUploadSize }}",
		file_types: "*.*",
		file_types_description: "All Files",
		file_post_name: "Filedata",
		file_upload_limit: 100,
		file_queue_limit: 0,
		custom_settings: {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,
		button_image_url: "{{ registry.uri }}img/filemanager/TestImageNoText_65x29.png",
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
		upload_start_handler: uploadStart,
		upload_progress_handler: uploadProgress,
		upload_error_handler: uploadError,
		upload_success_handler: uploadSuccess,
		upload_complete_handler: uploadComplete,
		queue_complete_handler: queueComplete
	};

	swfu = new SWFUpload(settings);
});
</script>
