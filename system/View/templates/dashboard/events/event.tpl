{% if event.type == "service" %}
<div class="alert alert-error">
{% elseif event.type == "mail" %}
<div class="alert alert-success">
{% elseif event.type == "task" or event.type == "com" %}
<div class="alert alert-info">
{% else %}
<div class="alert">
{% endif %}

<span style="font-size: 12px">
	<span style="margin-right: 10px; font-weight: bold">{{ event.timestamp }}</span>
	<span style="font-weight: bold">{{ event.event }}</span>
</span>

<span>
{% for part in event.param %}
{% if part.key %} 
<p style="margin: 0"><b>{{ part.key }}:</b> {{ part.val }}</p>
{% endif %}
{% endfor %}
</span>

</div>