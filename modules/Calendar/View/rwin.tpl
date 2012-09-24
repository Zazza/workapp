<div id="rwin">

		<div>
		<b>Объект:</b>
		<a style="cursor: pointer;" onclick="getInfo('{{ data.0.oid }}')">
		{% for val in data.obj %}
		{{ val.val }}&nbsp;
		{% endfor %}
		</a>
		</div>
		
		<div><b>Кем занят:</b>
			<a style="cursor: pointer;" onclick="getUserInfo('{{ data.user.id }}')">{{ data.user.name }} {{ data.user.soname }}</a>
		</div>
		
		<div><b>Сроки:</b>
			{{ data.start }} - {{ data.end }}
		</div>
		
		<input type="hidden" id="res_hid" />
		
		</div>

	<div><a onclick="$('#{{ wid }}').popover('hide')" class="btn btn-small">Закрыть</a></div>
	
</div>