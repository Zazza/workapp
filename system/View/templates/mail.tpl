<table style="text-align: left; font-size: 12px" width="100%">

<tr>
    <td style="font-family: Arial; font-size: 18px; padding-bottom: 10px">
        <b>Задача №{{ post.0.id }}</b>
    </td>
</tr>

<tr>
    <td>
        <b>Автор:</b> {{ post.0.who }}
    </td>
</tr>

<tr>
    <td>
        <b>Дата создания:</b> {{ post.0.start }}
    </td>
</tr>

{% if post.0.gid != 0 %}
<tr>
    <td>
        <b>Дата закрытия:</b> {{ post.0.ending }}
    </td>
</tr>

<tr>
    <td>
        <b>Группа:</b> {{ post.0.group }}
    </td>
</tr>
{% endif %}

<tr>
    {% if post.0.imp > 3 %}
    <td style="color: red">
    {% else %}
    <td style="color: blue">
    {% endif %}
        <span style="color: black"><b>Важность:</b></span>&nbsp;{{ post.0.imp }}
    </td>
</tr>

<tr>
    <td>
        <b>Ссылка:</b>&nbsp;<a href="{{ registry.siteName }}{{ registry.uri }}tt/{{ post.0.id }}/">{{ registry.siteName }}{{ registry.uri }}tt/{{ post.0.id }}/</a>
    </td>
</tr>

<tr>
    <td style="padding-top: 10px">
        <b>Текст:</b>
    </td>
</tr>

<tr>
    <td style="padding-bottom: 8px">
        {{ post.0.text }}
    </td>
</tr>

{% for part in comments %}
<tr>
    <td style="padding-bottom: 4px">
        <span style="font-weight: bold">{{ part.name }}&nbsp;{{ part.soname }}&nbsp;[{{ part.timestamp }}]:</span>
        &nbsp;
        {% if part.status_id != 0 %} <p style="color: green"><b>Статус:</b> {{ part.status }}</p> {% endif %}
        {% if part.mail_id != 0 %}
        {{ part.text.0.text }}
        {% else %}
        {{ part.text }}
        {% endif %}
    </td>
</tr>
{% endfor %}

</table>