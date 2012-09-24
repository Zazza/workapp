<div style="display: none; text-align: left" id="task{{ data.0.id }}" title="Данные о задаче">

<div style="margin-bottom: 10px; overflow: hidden">
<div style="float: left; margin-right: 10px">
{% if data.0.remote_id == 0 %}
	<b>Автор задачи:</b> {{ author.soname }} {{ author.name }}
{% else %}
	<b>Автор задачи:</b> {{ author.soname }} {{ author.name }} (группа {{ author.gname }})
{% endif %}
</div>
{% if data.0.remote_id == 0 %}
<div style="float: left">

    <a style="cursor: pointer" onclick="getUserInfo('{{ author.uid }}')"><img src="{{ registry.uri }}img/information-button.png" title="полные данные" alt="info" border="0" style="position: relative; top: 0" /></a>
</div>
{% endif %}
</div>

<div style="margin-bottom: 10px">
<b>Задача создана:</b> {{ data.0.startF }}
</div>

<!-- объект -->
{% if notObj %}
<div style="margin-bottom: 10px; overflow: hidden">
<div><b>Объект:</b><a style="cursor: pointer; margin-left: 10px" onclick="getInfo({{ obj.0.id }})"><img src="{{ registry.uri }}img/information-button.png" title="полные данные" alt="info" border="0" style="position: relative; top: 3px" /></a></div>
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
<p ><b>Группа:</b> <span style="color: green">{{ data.0.group }}</span></p>
</div>
<!-- END группа -->

<!-- приоритет -->
<div style="margin-bottom: 10px; overflow: hidden">
<div style="float: left; margin-right: 10px"><b>Приоритет:</b></div>
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