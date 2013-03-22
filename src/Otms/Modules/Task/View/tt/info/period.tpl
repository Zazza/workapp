<div style="text-align: left; padding: 4px 10px">

<p style="margin-bottom: 5px; font-size: 14px; font-weight: bold">Periods:</p>

{% if data.0.type == "0" %}
<p style="margin-bottom: 5px"><b>Start:</b> {{ data.0.openingF }}</p>

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Close:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}

{% if data.0.type == "1" %}
<p style="margin-bottom: 5px">
{% if data.0.expire %}
<span style="padding: 2px 4px; background-color: orange; font-weight: bold">expired task</span>
</p>
{% endif %}

<p style="margin-bottom: 5px"><b>Start:</b> {{ data.0.openingF }}</p>

<p style="margin-bottom: 5px">Duration <b>{{ data.0.deadline }} {{ data.0.deadline_date }}</b></p>

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Close:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}

{% if data.0.type == "2" %}
<p style="margin-bottom: 5px"><b>Start:</b> {{ data.0.openingF }}</p>

<p style="margin-bottom: 5px">each <b>{{ data.0.iteration }} {% if data.0.timetype_iteration == "day" %}days{% elseif data.0.timetype_iteration == "month" %}months{% endif %}</b></p>
{% if data.0.deadline != 0 %}
<p style="margin-bottom: 5px">duration <b>{{ data.0.deadline }} {{ data.0.deadline_date }}</b></p>
{% endif %}

{% if data.0.close == 1 %}
<p style="margin-bottom: 5px"><b>Close:</b> {{ data.0.endingF }}</p>
{% endif %}

{% endif %}
</div>