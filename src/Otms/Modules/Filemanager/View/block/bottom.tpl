<div style="float: left">

	<span margin-right: 10px" class="btn btn-primary" onclick="shUploader()">
		<img src="{{ registry.uri }}img/upload2.png" alt="" /> Upload files
	</span>
	
	<span onclick="copyFiles()" class="btn" id="btnCopy">
		<img src="{{ registry.uri }}img/copy.png" alt="" /> Copy
	</span>
	
	<span onclick="pastFiles()"  class="btn" id="btnPast">
		<img src="{{ registry.uri }}img/paste.png" alt="" /> Past
	</span>
	
	<span onclick="createDirDialog()" class="btn">
		<img src="{{ registry.uri }}img/add.png" alt="" /> Create
	</span>
	
	<span onclick="delmany()" class="btn">
		<img src="{{ registry.uri }}img/remove.png" alt="" /> Remove
	</span>
	
	{% if registry.ui.admin %}
	<span onclick="admin()" id="admbtn" class="btn btn-danger" style="margin-left: 15px">"Admin" Mode</span>
	{% endif %}
	
</div>

{% if registry.ui.admin %}
<div id="adminFunc" style="display: none; float: left; padding-left: 15px">

		<span onclick="delmanyrealConfirm()" class="btn btn-danger"> <i
			class="icon-remove icon-white"></i>
			Full Remove
		</span>

		<span onclick="restore()" class="btn btn-success"> <i
			class="icon-repeat icon-white"></i>
			Repair
		</span>
</div>
{% endif %}
