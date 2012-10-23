<div style="overflow: hidden">

<label class="checkbox">

<div style="float: left; margin-right: 10px">
<input type="checkbox" name="ruser[]" value="{{ data.uid }}" class="Pg{{ data.gid }} cusers" />
</div>

<div style="float: left; text-align:center; margin-right: 10px">
	{% if data.avatar %}
	<img class="avatar" id="ava" src="{{ data.avatar }}" alt="аватар" />
	{% else %}
	<img class="avatar" id="ava" src="{{ registry.uri }}img/noavatar.gif" alt="аватар" />
	{% endif %}
	
	{% if data.status %}
	<div style="font-size: 10px; color: green">[online]</div>
	{% else %}
	<div style="font-size: 10px; color: red">[offline]</div>
	{% endif %}
</div>

<div style="float: left">

<p>
<a style="cursor: pointer; color: black; font-weight: bold" onclick="getUserInfo('{{ data.uid }}')">{{ data.soname }} {{ data.name }}</a>
<span style="color: #777; margin-left: 10px">{{ data.signature }}</span>
{% if registry.ui.admin %}
<br />
<a style="font-size: 10px; text-decoration: none" href="{{ registry.uri }}users/edituser/{{ data.uid }}/" style="margin-left: 10px">
[правка]
</a>
<a style="font-size: 10px; text-decoration: none" onclick="delUserConfirm({{ data.uid }})" style="cursor: pointer; margin-left: 10px">
[удалить]
</a>
{% endif %}
</p>

<p><a style="color: green" href="{{ registry.uri }}users/tasks/{{ data.uid }}/">задачи</a></p>

</div>

</label>
</div>