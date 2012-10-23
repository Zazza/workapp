<div class="dashCmd">

<span style="font-size: 12px">
	<span style="margin-right: 10px; font-weight: bold">{{ event.timestamp }}</span>
	<span style="color: green; font-weight: bold">{{ event.event }}</span>
</span>

<span style="color: #AAA">
{% for part in event.param %}
{% if part.key %} 
<p><b>{{ part.key }}:</b> {{ part.val }}</p>
{% endif %}
{% endfor %}
</span>

</div>
