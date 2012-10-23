<div style="padding-top: 20px;">
{% for part in data %}
<span class="label label-info" style="margin-right: 10px;"><a style="color: white; cursor: pointer;" onclick="setTag('{{ part.tag }}')">{{ part.tag }}</a></span>
{% endfor %}
</div>