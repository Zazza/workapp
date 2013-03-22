{% if key == "Events" %}
<li><a href="{{ val }}"><i class="icon-info-sign icon-white"></i> {{ key }}</a></li>
{% elseif key == "Calendar" %}
<li><a href="{{ val }}"><i class="icon-calendar icon-white"></i> {{ key }}</a></li>
{% elseif key == "Gant" %}
<li><a href="{{ val }}"><i class="icon-random icon-white"></i> {{ key }}</a></li>
{% else %}
<li><a href="{{ val }}">{{ key }}</a></li>
{% endif %}