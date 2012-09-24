<p style="color: #777">{{ obj.timestamp }}</p>
<div style="margin-bottom: 20px">
{% for part in obj.param %}
<p>
	<span style="font-weight: bold; margin-right: 5px">{{ part.key }}:</span>
	<span>{{ part.val }}</span>
</p>
{% endfor %}
</div>