<a class="btn" href="{{ registry.uri }}users/addgroup/">
	<i class="icon-th-large"></i>
	New group
</a>

<table cellpadding="3" cellspacing="3" style="margin-bottom: 20px">
<tr>
<td align="center" style="font-weight: bold; font-size: 10px">delete</td>
<td align="center" style="font-weight: bold; font-size: 10px">change</td>
<td align="center" style="font-weight: bold; font-size: 10px">structure</td>
<td align="center" style="font-weight: bold; font-size: 10px">group name</td>
{% for part in group %}
<tr>
<td align="center" style="border: 1px solid #ccc">
    <a style="cursor: pointer" onclick="delGroupConfirm({{ part.pid }})"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}users/editgroup/{{ part.pid }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/edititem.gif" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}users/structure/list/{{ part.pid }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/document-tree.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    {{ part.pname }}
</td>
</tr>
{% else %}
<tr><td colspan="3" align="center" style="border: 1px solid #ccc">Empty</td></tr>
{% endfor %}
</table>

<a class="btn" href="{{ registry.uri }}users/adduser/">
	<i class="icon-user"></i>
	New user
</a>
