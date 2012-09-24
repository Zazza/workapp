{% include "tt/taskinfo.tpl" %}

<div class="obj" style="border-bottom: 1px solid #EEE; border-left: 1px solid #EEE; border-right: 1px solid #EEE; overflow: hidden;">

<div style="float: left; margin-right: 10px">

<div style="margin-bottom: 5px">
<a {% if data.0.close == 1 %}class="endtask"{% else %}class="livetask"{% endif %} href="{{ registry.uri }}tt/show/{{ data.0.id }}/">Задача {{ data.0.id }}</a>
</div>

<div style="margin-bottom: 5px">
<a class="btn btn-mini" onclick="showTaskInfo('{{ data.0.id }}')">
	<i class="icon-info-sign"></i>
	информация
</a>
</div>

<div>
{% if data.0.imp == 1 %}
<div style="height: 12px; width: 20px; background-color: #00ffcc; text-align: center"><span style="font-size: 9px; position: relative; bottom: 4px">1/5</span></div>
{% elseif data.0.imp == 2 %}
<div style="height: 12px; width: 30px; background-color: #00ffcc; text-align: center"><span style="font-size: 9px; position: relative; bottom: 4px">2/5</span></div>
{% elseif data.0.imp == 3 %}
<div style="height: 12px; width: 50px; background-color: #ffcc00; text-align: center"><span style="font-size: 9px; position: relative; bottom: 4px">3/5</span></div>
{% elseif data.0.imp == 4 %}
<div style="height: 12px; width: 70px; background-color: #ff0000; text-align: center"><span style="font-size: 9px; position: relative; bottom: 4px">4/5</span></div>
{% elseif data.0.imp == 5 %}
<div style="height: 12px; width: 90px; background-color: #ff0000; text-align: center"><span style="font-size: 9px; position: relative; bottom: 4px">5/5</span></div>
{% endif %}
</div>

</div>

{% if data.0.mail_id %}
<iframe style="border: 1px solid #EEE" src="{{ registry.siteName }}{{ registry.uri }}mail/load/?mid={{ data.0.mail_id }}&part=1" frameborder="0" width="700px" height="90%"></iframe>
{% else %}
<div style="margin-left: 140px; font-size: 12px">{{ data.0.text }}</div>
{% endif %}

</div>