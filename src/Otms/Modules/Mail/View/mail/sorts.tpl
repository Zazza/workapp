<div style="overflow: hidden; margin-bottom: 20px">
<div style="float: left">
	<a class="btn" href="{{ registry.uri }}mail/sort/?add">
	<img src="{{ registry.uri }}img/left/mail-plus.png" alt="" border="0" style="vertical-align: middle" />
	Add sorting
	</a>
</div>
</div>

{% if not list %}
<p>sortings not found</p>
{% else %}

<table width="100%">

{% for part in list %}
<tr>

	<td style="text-align: center; padding: 2px">
		<a title="edit" style="cursor: pointer" href="{{ registry.uri }}mail/sort/?id={{ part.0.sort_id }}"><img style="vertical-align: middle" src="{{ registry.uri }}img/edititem.gif" alt="edit" border="0" /></a>
	</td>
	
	<td style="text-align: center; padding: 2px">
		<a title="delete" style="cursor: pointer" onclick="delSortConfirm({{ part.0.sort_id }})"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="delete" border="0" /></a>
	</td>
	
	<td style="padding: 2px">
		{% for parted in part %}
			<p><b>{{ parted.type }}:</b> {{ parted.val }}</p>
		{% endfor %}
	</td>
	
	{% if part.0.action == "move" %}
	<td style="padding: 2px"><b>move in: </b>{{ part.0.folder }}</td>
	{% elseif part.0.action == "remove" %}
	<td style="padding: 2px"><b>delete</b></td>
	{% elseif part.0.action == "task" %}
	<td style="padding: 2px"><b>create task</b></td>
	{% endif %}
	
</tr>
{% endfor %}
</table>

{% endif %}