<table cellpadding="3" cellspacing="3" style="margin-bottom: 50px">
{% for part in data %}

    <tr>
    <td colspan="3" align="left">

        <img border="0" alt="" src="{{ registry.uri }}img/g.png" style="vertical-align: middle; margin-right: 10px" /><a class="none" href="{{ registry.uri }}tt/groups/{{ part.id }}/">{{ part.name }} {{ part.open }}({{ part.close }})</a>

        <a title="добавить задачу" href="{{ registry.uri }}tt/add/?group={{ part.id }}" class="hover"><img border="0" src="{{ registry.uri }}img/plus-small.png" alt="plus" style="position: relative; top: 3px; margin-left: 4px"></a>

    </td>
    </tr>
    
{% endfor %}
</table>