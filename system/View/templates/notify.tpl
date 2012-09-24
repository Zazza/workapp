{% for part in post %}

<table style="text-align: left; font-size: 12px; margin-bottom: 10px" width="100%">

<tr>
    <td>
        <a href="{{ registry.sitename }}{{ registry.uri }}tt/{{ part.id }}/">Задача №{{ part.id }}</a>
    </td>
</tr>

<tr>
    <td style="padding-bottom: 8px">
        <p>{{ part.obj }}</p>
        <p>{{ part.text }}</p>
    </td>
</tr>

</table>

{% endfor %}