<div style="display: none; text-align: left" id="task{{ data.0.id }}" title="Task data">

<div style="margin-bottom: 10px; overflow: hidden">
<div style="float: left; margin-right: 10px">
{% if data.0.remote_id == 0 %}
	<b>Author:</b> {% if data.0.remote_id == 0 %}<a style="cursor: pointer;" onclick="getUserInfo('{{ author.uid }}')">{% endif %}{{ author.soname }} {{ author.name }}{% if data.0.remote_id == 0 %}</a>{% endif %}
{% else %}
	<b>Author:</b> {% if data.0.remote_id == 0 %}<a style="cursor: pointer;" onclick="getUserInfo('{{ author.uid }}')">{% endif %}{{ author.soname }} {{ author.name }} (группа {{ author.gname }}){% if data.0.remote_id == 0 %}</a>{% endif %}
{% endif %}
</div>
</div>

<div style="margin-bottom: 10px">
<b>Task create:</b> {{ data.0.startF }}
</div>

<!-- объект -->
{% if notObj %}
<div style="margin-bottom: 10px; overflow: hidden">
<div><b>Object:</b><a style="cursor: pointer; margin-left: 10px" onclick="getInfo({{ obj.0.id }})"><img src="{{ registry.uri }}img/information-button.png" title="full data" alt="info" border="0" style="position: relative; top: 3px" /></a></div>
<div style="float: left" class="info">
{% for part in obj %}
<p>{{ part.field }}: {{ part.val }}</p>
{% endfor %}
</div>
</div>
{% endif %}
<!-- END объект -->

<!-- группа -->
<div style="margin-bottom: 10px">
<p ><b>Group:</b> <span style="color: green">{{ data.0.group }}</span></p>
</div>
<!-- END группа -->

<!-- приоритет -->
<div style="margin-bottom: 10px; overflow: hidden">
<div style="float: left; margin-right: 10px"><b>Priority:</b></div>
{% if data.0.imp == 1 %}
<div style="float: left; height: 15px; width: 30px; background-color: #00ffcc; text-align: center">1/5</div>
{% elseif data.0.imp == 2 %}
<div style="float: left; height: 15px; width: 50px; background-color: #00ffcc; text-align: center">2/5</div>
{% elseif data.0.imp == 3 %}
<div style="float: left; height: 15px; width: 70px; background-color: #ffcc00; text-align: center">3/5</div>
{% elseif data.0.imp == 4 %}
<div style="float: left; height: 15px; width: 90px; background-color: #ff0000; text-align: center">4/5</div>
{% elseif data.0.imp == 5 %}
<div style="float: left; height: 15px; width: 110px; background-color: #ff0000; text-align: center">5/5</div>
{% endif %}
</div>
<!-- END приоритет -->

<!-- ответственные и закрывший задачу -->
{% include "tt/info/responsible.tpl" %}
<!-- END ответственные и закрывший задачу -->

<!-- сроки -->
{% include "tt/info/period.tpl" %}
<!-- END сроки -->

</div>