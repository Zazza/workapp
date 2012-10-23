<div class="sel" style="width: 150px; margin-bottom: 10px; font-weight: bold">
<img border="0" style="vertical-align: middle;" alt="plus" src="{{ registry.uri }}img/plus-button.png" />
&nbsp;
<a href="{{ registry.uri }}settings/templates/add/" style="text-decoration: none">новый шаблон</a>
</div>

<table cellpadding="3" cellspacing="3">
<tr>
<td align="center" style="font-weight: bold; font-size: 10px">удалить</td>
<td align="center" style="font-weight: bold; font-size: 10px">изменить</td>
<td align="center" style="font-weight: bold; font-size: 10px">структура</td>
<td align="center" style="font-weight: bold; font-size: 10px">название шаблона</td>
{% for part in list %}
<tr>
<td align="center" style="border: 1px solid #ccc">
    <a style="cursor: pointer" onclick="delTemplateConfirm({{ part.id }})"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}settings/templates/edit/{{ part.id }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/edititem.gif" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    <a href="{{ registry.uri }}settings/templates/list/{{ part.id }}/"><img style="vertical-align: middle" src="{{ registry.uri }}img/document-tree.png" alt="" border="0" /></a>
</td>

<td align="center" style="border: 1px solid #ccc">
    {{ part.name }}
</td>
</tr>
{% else %}
<tr><td colspan="3" align="center" style="border: 1px solid #ccc">Пусто</td></tr>
{% endfor %}
</table>