<div style="margin-bottom: 20px; overflow: hidden">

<div style="overflow: hidden">
    <div style="float: right" class="btn-group">
    
    	{% if ai.form %}
		<a class="btn" href="{{ registry.uri }}objects/setform/edit/?oaid={{ info.id }}">
		{% else %}
		<a class="btn" href="{{ registry.uri }}objects/info/edit/?oaid={{ info.id }}">
		{% endif %}
			<img src="{{ registry.uri }}img/edititem.gif" alt="" style="vertical-align: middle" />
			Правка
		</a>

		<a class="btn" onclick="delAdvConfirm({{ info.id }})">
			<img src="{{ registry.uri }}img/delete.png" />
			Удалить
		</a>
		
		<a class="btn" href="{{ registry.uri }}objects/kb/?history={{ info.id }}">
			<img src="{{ registry.uri }}img/clock-history.png" alt="История" style="vertical-align: middle" />
			История
		</a>

    </div>
</div>

{% if ai.form %}
<div style="overflow: hidden" id="tview">
	<ul id="firstSort" class="tviewshow oinfo">
		{% for part in ai %}
		{% if part.view.x == 1 or not part.view.x %}
		{% if part.val %}<p style="margin: 4px 0"><b>{{ part.key }}:</b>&nbsp;{{ part.val }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
	<ul id="secondSort" class="tviewshow oinfo">
		{% for part in ai %}
		{% if part.view.x == 2 %}
		{% if part.val %}<p style="margin: 4px 0"><b>{{ part.key }}:</b>&nbsp;{{ part.val }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
	<ul id="thirdSort" class="tviewshow oinfo">
		{% for part in ai %}
		{% if part.view.x == 3 %}
		{% if part.val %}<p style="margin: 4px 0"><b>{{ part.key }}:</b>&nbsp;{{ part.val }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
</div>
{% else %}
<div style="overflow: hidden" id="tview">
<h3>{{ info.title }}:</h3>
<p>{{ info.val }}</p>
</div>
{% endif %}

<div style="text-align: left; margin-top: 20px; padding: 2px 4px; background-color: #EEE">Объект добавлен: <a style="cursor: pointer" onclick="getUserInfo('{{ ai.auid }}')">{{ info.aname }} {{ info.asoname }}</a> <span style="color: #777">[{{ info.adate }}]</span></div>
{% if info.edate != '0000-00-00 00:00:00' %}
<div style="text-align: left; margin-top: 5px; padding: 2px 4px; background-color: #EEE">Последняя правка: <a style="cursor: pointer" onclick="getUserInfo('{{ ai.euid }}')">{{ info.ename }} {{ info.esoname }}</a> <span style="color: #777">[{{ info.edate }}]</span></div>
{% endif %}
</div>