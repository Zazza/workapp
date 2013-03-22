{% if registry.ui.admin %}

<div id="groupAdmin" class="obj">

<a class="btn" href="{{ registry.uri }}settings/ttgroups/add/">
	<img border="0" alt="plus" src="{{ registry.uri }}img/plus-button.png" />
	New project
</a>

<table cellpadding="3" cellspacing="3">
<tr>
<td align="center" style="font-weight: bold; font-size: 10px">delete</td>
<td align="center" style="font-weight: bold; font-size: 10px">change</td>
<td align="center" style="font-weight: bold; font-size: 10px">project</td>
{% for part in group %}
<tr>

<td align="center" style="border: 1px solid #ccc">
    <a style="cursor: pointer" onclick="delGroupTtConfirm({{ part.id }})"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}settings/ttgroups/edit/{{ part.id }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/edititem.gif" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    {{ part.name }}
</td>
</tr>
{% else %}
<tr><td colspan="3" align="center" style="border: 1px solid #ccc">Empty</td></tr>
{% endfor %}
</table>

</div>

{% endif %}