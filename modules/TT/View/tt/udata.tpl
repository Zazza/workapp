<div style="overflow: hidden">
<label>

<div style="float: left; margin-right: 10px">
<input type="checkbox" name="ruser[]" id="user{{ data.uid }}"  value="{{ data.uid }}" class="g{{ data.gid }} cusers" />
</div>

<div style="float: left; margin-right: 10px">
	{% if data.avatar %}
	<img class="avatar" id="ava" src="{{ data.avatar }}" alt="аватар" />
	{% else %}
	<img class="avatar" id="ava" src="{{ registry.uri }}img/noavatar.gif" alt="аватар" />
	{% endif %}
</div>

<div style="float: left">
<input type="hidden" id="hu{{ data.uid }}" value="{{ data.soname }} {{ data.name }}" />
<p>
<a style="cursor: pointer; color: black; font-weight: bold" onclick="getUserInfo('{{ data.uid }}')">{{ data.soname }} {{ data.name }}</a>
</p>

</div>

</label>
</div>