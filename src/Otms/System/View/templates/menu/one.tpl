{% if key == "События" %}
<li><a href="{{ val }}"><i class="icon-info-sign icon-white"></i> {{ key }}</a></li>
{% elseif key == "Календарь" %}
<li><a href="{{ val }}"><i class="icon-calendar icon-white"></i> {{ key }}</a></li>
{% elseif key == "Гант" %}
<li><a href="{{ val }}"><i class="icon-random icon-white"></i> {{ key }}</a></li>
{% else %}
<li><a href="{{ val }}">{{ key }}</a></li>
{% endif %}