<a class="btn" href="{{ registry.uri }}objects/forms/add/">
<img src="{{ registry.uri }}img/plus-button.png" border="0" />
Добавить
</a>

<table cellpadding="3" cellspacing="3" style="margin-top: 10px">

{% for form in forms %}
<tr>
<td align="center" style="border: 1px solid #ccc">
	<a style="cursor: pointer" onclick="delFormConfirm({{ form.id }})">
	<img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="" border="0" />
	</a>
</td>
<td align="center" style="border: 1px solid #ccc">
	<a href="{{ registry.uri }}objects/forms/edit/?id={{ form.id }}">{{ form.name }}</a>
</td>
</tr>
{% endfor %}

</table>