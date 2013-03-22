<div style="overflow: hidden; margin-bottom: 20px">

<span class="btn" style="float: left; font-weight: bold">
	<img src="{{ registry.uri }}img/edititem.gif" alt="" style="vertical-align: middle" />
	<a style="outline: none; cursor: pointer; text-decoration: none" href="{{ registry.uri }}objects/edit/{{ data.0.id }}/">Edit</a>
</span>

{% if mail %}
<div class="btn" style="margin-left: 10px; float: left">
	<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-receive.png" alt="mail" border="0" />
	<a href="{{ registry.uri }}mail/?oid={{ data.0.id }}">incoming mail</a>
</div>

<div class="btn" style="margin-left: 10px; float: left">
	<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-send.png" alt="mail" border="0" />
	<a href="{{ registry.uri }}mail/send/?oid={{ data.0.id }}">outgoing mail</a>
</div>
{% endif %}

<span class="btn" style="margin-left: 10px; float: left; font-weight: bold">
	<a style="text-decoration: none" href="{{ registry.uri }}objects/history/{{ data.0.id }}/" title="History">
	<img src="{{ registry.uri }}img/clock-history.png" alt="History" style="vertical-align: middle" border="0" />
	</a>
</span>

<span class="btn" style="margin-left: 10px; float: left; font-weight: bold">
	<a style="cursor: pointer" onclick="refreshurl('{{ siteName }}{{ registry.uri }}objects/show/{{ data.0.id }}/')">
	<img src="{{ registry.uri }}img/enter.png" title="go to object" alt="object" border="0" style="vertical-align: middle" />
	</a>
</span>

</div>

<div style="text-align: left">

{% if data.0.email %}
<p><b>email:</b> <a href="mailto: {{ data.0.email }}">{{ data.0.email }}</a></p>
{% endif %}

<div style="overflow: hidden" id="tview">
	<ul id="firstSort" class="tviewshow oinfo">
		{% for part in data %}
		{% if part.view.x == 1 or not part.view.x %}
		{% if part.field %}<p style="margin: 4px 0"><b>{{ part.field }}:</b>&nbsp;{{ part.formatval }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
	<ul id="secondSort" class="tviewshow oinfo">
		{% for part in data %}
		{% if part.view.x == 2 %}
		{% if part.field %}<p style="margin: 4px 0"><b>{{ part.field }}:</b>&nbsp;{{ part.formatval }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
	<ul id="thirdSort" class="tviewshow oinfo">
		{% for part in data %}
		{% if part.view.x == 3 %}
		{% if part.field %}<p style="margin: 4px 0"><b>{{ part.field }}:</b>&nbsp;{{ part.formatval }}</p>{% endif %}
		{% endif %}
		{% endfor %}
	</ul>
</div>

</div>

<div style="text-align: left; margin-top: 20px; padding: 2px 4px; background-color: #EEE">Object add: <a style="cursor: pointer" onclick="getUserInfo('{{ data.0.auid }}')">{{ data.0.aname }} {{ data.0.asoname }}</a> <span style="color: #777">[{{ data.0.adate }}]</span></div>
{% if data.0.edate and  data.0.edate != data.0.adate %}
<div style="text-align: left; margin-top: 5px; padding: 2px 4px; background-color: #EEE">Last edit: <a style="cursor: pointer" onclick="getUserInfo('{{ data.0.euid }}')">{{ data.0.ename }} {{ data.0.esoname }}</a> <span style="color: #777">[{{ data.0.edate }}]</span></div>
{% endif %}