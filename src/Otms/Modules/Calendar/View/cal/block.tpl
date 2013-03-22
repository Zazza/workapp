<div style="text-align: left;">
{% if close or iter or noiter or time %}

	<div style="font-size: 12px; font-weight: bold;"><a href="{{ registry.uri }}task/date/{{ date }}/">Tasks</a></div>
	<div style="padding-left: 20px;">
	{% if close %}
	{{ close }}
	{% endif %}
	
	{% if iter %}
	{{ iter }}
	{% endif %}
	
	{% if noiter %}
	{{ noiter }}
	{% endif %}
	
	{% if time %}
	{{ time }}
	{% endif %}

{% endif %}
</div>

<div style="text-align: left;">
<div style="font-size: 12px; font-weight: bold;">
{% if reserv %}
	<a id="cr_{{ date }}" data-id="{{ date }}" class="cres" style="cursor: pointer;" onclick="getDayReservs('{{ date }}', '{{ fulldate }}')">
	Reservation
	</a>
	</div>
	<div style="padding-left: 20px;">
	{{ reserv }}
{% endif %}
</div>
</div>