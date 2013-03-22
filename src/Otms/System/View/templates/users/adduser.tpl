<form method="post" action="{{ registry.uri }}users/adduser/">

{% if err %}
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
{% endif %}

<div style="margin-bottom: 50px">
<h3>Registration new user</h3>

<p><b>Login</b></p>
<p><input name='login' type='text' size='60' value="{{ post.login }}" /></p>
<p><b>Name</b></p><p><input name='name' type='text' size='60' value="{{ post.name }}" /></p>
<p><b>Surname</b></p><p><input name='soname' type='text' size='60' value="{{ post.soname }}" /></p>
<p><b>Signature</b></p><p><input name='signature' type='text' size='60' value="{{ post.signature }}" /></p>
<p><b>Email</b></p><p><input name='email' type='text' size='60' value="{{ post.email }}" /></p>

<div class="well">
<p style="margin: 7px 0">
    <label class="radio inline"><input name='priv' value="admin" type='radio' {% if post.priv == "admin" %} checked {% endif %} />&nbsp;<b>Administrator</b></label>
    <label class="radio inline"><input name='priv' value="null" type='radio' {% if post.priv == FALSE  %} checked {% endif %} />&nbsp;<b>User</b></label>
</p>

<p style="margin: 7px 0"><b>Group</b>&nbsp;
<select name="gid">
{% for part in group %}
<option value="{{ part.sid }}" {% if post.gid == part.sid %} selected="selected" {% endif %}>{{ part.sname }}</option>
{% endfor %}
</select>
</p>

<div class="form-inline">
<b>Quotas</b>&nbsp;
<input class="span1" type="text" name="quota_val" value="100">
<select class="span1" name="quota_unit">
<option value="mb">Mb</option>
<option value="gb">Gb</option>
</select>
</div>

</div>

<div class="well">
<p style="margin: 7px 0"><b>Mail notifications</b>
	<p><label class="checkbox inline"><input name="notify" type="checkbox" {% if post.notify %} checked {% endif %} />&nbsp;switched on</label></p>
</p>
<p style="margin: 7px 0"><b>Notification message time about tasks</b>
	<p><input type="text" name="time_notify" value="{{ post.time_notify }}" style="width: 50px; text-align: center" /></p>
</p>

<p style="margin: 7px 0"><b>Duplicate tasks in other copy TTW:</b>
	<p><label class="checkbox inline"><input name="email_for_task" type="checkbox" {% if post.email_for_task %} checked {% endif %} />&nbsp;switched on</label></p>
</p>
</div>

<p><b>Password</b></p><p><input name='pass' type='password' /></p>
<p style="margin-top: 20px"><input type="submit" name='adduser' value='Done' /></p>
</div>

</form>