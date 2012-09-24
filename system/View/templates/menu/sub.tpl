<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
	{% if key == "Модули" %}
	<i class="icon-home icon-white"></i> {{ key }}
	{% elseif key == "Объекты" %}
	<i class="icon-th-list icon-white"></i> {{ key }}
	{% elseif key == "Система" %}
	<i class="icon-cog icon-white"></i> {{ key }}
	{% endif %}
	<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
		{% for key, val in val %}
		<li><a href="{{ val }}">{{ key }}</a></li>
		{% endfor %}
	</ul>
</li>