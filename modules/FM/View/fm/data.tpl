<div>

<label class="checkbox">

<div style="margin-right: 10px">
	<input type="checkbox" name="fruser[]" id="user{{ data.uid }}" value="{{ data.uid }}" class="fg{{ data.gid }} fcusers" />
</div>

<div style="overflow: hidden;">

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

<div style="float: left;">
	<a style="cursor: pointer; color: black; font-weight: bold" onclick="getUserInfo('{{ data.uid }}')">{{ data.soname }} {{ data.name }}</a>
	<span style="color: #777; margin-left: 10px">{{ data.signature }}</span>
</div>

</div>

</label>

<span class="mode umode gparent_mode_{{ data.gid }}" id="umode_{{ data.uid }}">
	<label class="radio" style="margin-right: 5px"><input type="radio" name="mode_u_{{ data.uid }}" value="1" /> Read</label>
	<label class="radio"><input type="radio" name="mode_u_{{ data.uid }}" value="2" /> Write</label>
</span>

</div>
