{% for part in reservs %}
<div style="text-align: center;"><h5>Reserved resources</h5></div>

	{% for arrtask, val in part %}
	<div style="overflow: hidden; padding: 1px;">
	<div style="float: left; cursor: pointer; width: 55px; text-align: center; font-size: 12px; background-color: #EEE; border: 1px solid #6696AF; height: 22px; margin-right: 5px; padding-top: 3px;"><a class="rtip" id="{{ arrtask }}_num" rel="tooltip" data-id="{{ arrtask }}">№{{ arrtask }}</a></div>

		{% for part in cal %}
		
			{% set k = false %}
		
			{% for time, x in val %}

				{% if time == part.date %}
					{% set k = true %}
					<div class="rtip" id="{{ arrtask }}_{{ part.date }}" rel="tooltip" data-id="{{ arrtask }}" style="float: left; text-align: center; margin-right: 1px; width: 80px; height: 25px; border: 1px solid #6696AF; background-color: #DEF;">
						&nbsp;
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
