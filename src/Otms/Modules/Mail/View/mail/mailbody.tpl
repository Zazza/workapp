<div style="width: 150px; margin-bottom: 10px">
	{% if out %}
	<a style="font-size: 11px; color: blue" target="_blank" href="{{ registry.siteName }}{{ registry.uri }}mail/load/?out=1&mid={{ mid }}&part={{ part }}">Open in new window</a>
	{% else %}
	<a style="font-size: 11px; color: blue" target="_blank" href="{{ registry.siteName }}{{ registry.uri }}mail/load/?mid={{ mid }}&part={{ part }}">Open in new window</a>
	{% endif %}
</div>

{{ content }}