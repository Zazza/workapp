<a class="btn" href="{{ registry.uri }}objects/templates/add/">
	<img border="0" alt="plus" src="{{ registry.uri }}img/plus-button.png" />
	new template
</a>

<table cellpadding="3" cellspacing="3">
<tr>
<td align="center" style="font-weight: bold; font-size: 10px">delete</td>
<td align="center" style="font-weight: bold; font-size: 10px">change</td>
<td align="center" style="font-weight: bold; font-size: 10px">template name</td>
</tr>
{% for part in list %}
<tr>
<td align="center" style="border: 1px solid #ccc">
    <a style="cursor: pointer" onclick="delTemplateConfirm({{ part.id }})"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}objects/templates/edit/{{ part.id }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/edititem.gif" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    {{ part.name }}
</td>
</tr>
{% else %}
<tr><td colspan="3" align="center" style="border: 1px solid #ccc">Empty</td></tr>
{% endfor %}
</table>