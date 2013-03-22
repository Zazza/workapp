<div style="display: none;" id="resinfo_{{ data.0.id }}">
<div><b>Object:</b>
	<a style="cursor: pointer;" onclick="getInfo('{{ data.0.oid }}')">
	{% for val in data.obj %}
	{{ val.val }}&nbsp;
	{% endfor %}
	</a>
</div>

<div><b>Whom:</b>
	<a style="cursor: pointer;" onclick="getUserInfo('{{ data.user.id }}')">{{ data.user.name }} {{ data.user.soname }}</a>
</div>

<div><b>Periods:</b>
	{{ data.start }} - {{ data.end }}
</div>

<input type="hidden" id="res_hid" />
<div><a onclick="var id = $('#res_hid').val(); $('#' + id).popover('hide')" class="btn btn-small">Close</a></div>
</div>