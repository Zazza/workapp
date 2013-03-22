<div style="text-align: left; padding: 4px 10px">
{% set flag = 1 %}
<p style="margin-bottom: 5px; font-size: 14px; font-weight: bold">Responsible:</p>
{% for part in ruser %}

    {% if part != '' %}
    {% set flag = 0 %}
    {% endif %}
    
    <p>{{ part }}</p>
    
{% endfor %}

{% if flag == 1 %}
<p style="color: red">not assigned</p>
{% endif %}

{% if data.0.close == 1 %}
{% if cuser.name %}
<p style="margin: 10px 0 5px 0"><b>Closed task:</b></p>
<p style="color: red; padding: 2px 0">{{ cuser.name }} {{ cuser.soname }}</p>
{% endif %}
{% endif %}
</div>