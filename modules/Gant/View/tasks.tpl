{% for gid, part in gant %}
<div style="text-align: center;"><h5>{% if gid == 0 %} Внутренние задачи {% else %} {{ group[gid] }} {% endif %}</h5></div>

	{% for arrtask, val in part %}
	<div style="overflow: hidden; padding: 1px;">
	<div style="float: left; width: 55px; text-align: center; font-size: 12px; background-color: #EEE; border: 1px solid #6696AF; height: 22px; margin-right: 5px; padding-top: 3px;"><a class="tttip" href="{{ registry.uri }}tt/show/{{ arrtask }}/" rel="tooltip" data-placement="top" data-original-title="{{ task[arrtask]|e }}">№{{ arrtask }}</a></div>

		{% for part in cal %}
		
			{% set k = false %}
		
			{% for time, x in val %}

				{% if time == part.date %}
					{% set k = true %}
					<div class="tttip" rel="tooltip" data-placement="top" data-original-title="{{ task[arrtask]|e }}" style="float: left; text-align: center; margin-right: 1px; width: 80px; height: 25px; border: 1px solid #6696AF; {% if x == "over" or x == "overend" %}background-color: #FED"{% else %}background-color: #DEF"{% endif %}>
						{% if x == "start" %}
						<img src="{{ registry.uri }}img/flags/flag-green.png" alt="start" />
						{% elseif x == "end" %}
						<img src="{{ registry.uri }}img/flags/flag-pink.png" alt="end" />
						{% elseif x == "overend" %}
						<img src="{{ registry.uri }}img/flags/flag-yellow.png" alt="overend" />
						{% else %}
						&nbsp;
						{% endif %}
					</div>
				{% endif %}
				
			{% endfor %}
			
			{% if not k %}
				<div style="float: left; margin-right: 1px; width: 80px; height: 25px; border: 1px solid #FFF;">&nbsp;</div>
			{% endif %}
			
		{% endfor %}
	</div>
	{% endfor %}
	
{% endfor %}
