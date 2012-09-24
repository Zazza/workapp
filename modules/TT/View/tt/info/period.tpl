<div style="text-align: left; padding: 4px 10px">

<p style="margin-bottom: 5px; font-size: 14px; font-weight: bold">Сроки:</p>

{% if data.0.type == "0" %}
<p style="margin-bottom: 5px"><b>Начало:</b> {{ data.0.openingF }}</p>

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Закрытие:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}

{% if data.0.type == "1" %}
<p style="margin-bottom: 5px">
{% if data.0.expire %}
<span style="padding: 2px 4px; background-color: orange; font-weight: bold">просроченная задача</span>
</p>
{% endif %}

<p style="margin-bottom: 5px"><b>Начало:</b> {{ data.0.openingF }}</p>

<p style="margin-bottom: 5px">продолжительностью <b>{{ data.0.deadline }} {{ data.0.deadline_date }}</b></p>

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Закрытие:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}

{% if data.0.type == "2" %}
<p style="margin-bottom: 5px"><b>Начало:</b> {{ data.0.openingF }}</p>

<p style="margin-bottom: 5px">каждый(е) <b>{{ data.0.iteration }} {% if data.0.timetype_iteration == "day" %}дней{% elseif data.0.timetype_iteration == "month" %}месяцев{% endif %}</b></p>
{% if data.0.deadline != 0 %}
<p style="margin-bottom: 5px">продолжительностью <b>{{ data.0.deadline }} {{ data.0.deadline_date }}</b></p>
{% endif %}

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Закрытие:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}
</div>