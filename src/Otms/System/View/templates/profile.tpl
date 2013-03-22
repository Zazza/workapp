<div id="selavatar" style="display: none" title="Select file for loading">
<form action="{{ registry.uri }}profile/" method="post" enctype="multipart/form-data">
<input type="file" name="filename" />
<input type="submit" name="upload_avatar" value="Upload" />
</form>
</div>

<form method="post" action="{{ registry.uri }}profile/">

{% if err %}
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
{% endif %}

<div style="overflow: hidden">
	<div style="float: left; width: 40%; padding: 10px; margin-right: 20px">
		<p><b>Login</b></p><p><input name='login' type='text' size='21' value="{{ post.login }}" /></p>
		<p><b>Password</b></p><p><input name='pass' size='21' type='password' value="{{ post.pass }}" /></p>
		
		<p style="margin-top: 20px"><b>Name</b></p><p><input name='name' type='text' size='21' value="{{ post.name }}" /></p>
		<p><b>Surname</b></p><p><input name='soname' type='text' size='21' value="{{ post.soname }}" /></p>
		<p><b>Signature</b></p><p><input name='signature' type='text' size='21' value="{{ post.signature }}" /></p>
		
		<p style="margin-top: 20px"><b>Email</b></p><p><input name='email' type='text' size='21' value="{{ post.email }}" /></p>
	</div>
	
	<div style="float: left; width: 40%; padding: 10px; margin-right: 20px">
		<div style="height: 100px; overflow: hidden">
			<div style="float: left; width: 80px">
				<p><b>Avatar:</b></p>
				<p>
					<a style="cursor: pointer" onclick="loadava()">
				    <img class="avatar" id="ava" src="{{ registry.ui.avatar }}" alt="avatar" />
				    </a>
				</p>
			</div>
			<div style="float: left; padding-top: 36px" class="btn-group">
				<a class="btn btn-small" onclick="loadava()">
					replace
				</a>
				<a class="btn btn-small" onclick="delava()">
					delete
				</a>
			</div>
		</div>
	
		<p><b>ICQ</b></p><p><input name='icq' type='text' size='21' value="{{ post.icq }}" /></p>
		<p><b>Skype</b></p><p><input name='skype' type='text' size='21' value="{{ post.skype }}" /></p>
		<p><b>Address</b></p><p><input name='adres' type='text' size='21' value="{{ post.adres }}" /></p>
		<p><b>Phone</b></p><p><input name='phone' type='text' size='21' value="{{ post.phone }}" /></p>
	</div>
</div>

<div class="well">
	<p style="margin: 7px 0"><b>Mail notifications</b>
	<p><label class="checkbox inline"><input name="notify" type="checkbox" {% if post.notify %} checked {% endif %} />&nbsp;switched on</label></p>
	</p>
	<p style="margin: 7px 0"><b>Notification message time about tasks</b>
	<p><input type="text" name="time_notify" value="{{ post.time_notify }}" size="15px" style="text-align: center" /></p>
	</p>
	<p style="margin: 7px 0"><b>Dublicate tasks in other copy TTW:</b>
	<p><label class="checkbox inline"><input name="email_for_task" type="checkbox" {% if post.email_for_task %} checked {% endif %} />&nbsp;switched on</label></p>
	</p>
</div>


<p style="clear: both; padding-top: 20px"><input type="submit" class="btn" name='editprofile' value='Done' /></p>

</form>