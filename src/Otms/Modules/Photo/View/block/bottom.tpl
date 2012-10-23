<div style="float: left">

	<span class="btn btn-primary" onclick="p_shUploader()" style="float: left; margin-right: 3px;">
		<img src="{{ registry.uri }}img/upload2.png" alt="" /> Upload files
	</span>
	
	<span onclick="p_copyFiles()" class="btn" id="btnCopy" style="float: left;; margin-right: 3px;">
		<img src="{{ registry.uri }}img/copy.png" alt="" /> Copy
	</span>
	
	<span onclick="p_pastFiles()"  class="btn" id="btnPast" style="float: left;; margin-right: 3px;">
		<img src="{{ registry.uri }}img/paste.png" alt="" /> Past
	</span>
	
	<span onclick="p_createDirDialog()" class="btn" style="float: left;; margin-right: 3px;">
		<img src="{{ registry.uri }}img/add.png" alt="" /> Create
	</span>
	
	<span onclick="p_delmany()" class="btn" style="float: left; margin-right: 5px;">
		<img src="{{ registry.uri }}img/remove.png" alt="" /> Remove
	</span>
	
	<ul class="nav" style="float: left; position: relative; top: 5px;; margin-right: 3px;">
				
		<li class="dropdown dropup">
			<a class="dropdown-toggle topmenubutton" data-toggle="dropdown" href="#">
				<img src="{{ registry.uri }}img/clipboard.png" alt="" />
				Буфер
				<b class="caret"></b>
			</a>
			
			<ul class="dropdown-menu" style="text-align: left;">
				<div id="photoclip"></div>
			</ul>
		</li>

	</ul>
	
	{% if registry.ui.admin %}
	<span onclick="p_admin()" id="admbtn" class="btn btn-danger" style="margin-left: 15px" style="float: left;; margin-right: 3px;">"Admin" Mode</span>
	{% endif %}

</div>

{% if registry.ui.admin %}
<div id="adminFunc" style="display: none; float: left; padding-left: 15px">

		<span onclick="p_delmanyrealConfirm()" class="btn btn-danger" style="float: left;; margin-right: 3px;">
			<i class="icon-remove icon-white"></i>
			Full Remove
		</span>

		<span onclick="p_restore()" class="btn btn-success" style="float: left;">
			<i class="icon-repeat icon-white"></i>
			Repair
		</span>
</div>
{% endif %}
