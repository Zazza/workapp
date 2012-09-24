{% include "tt/taskinfo.tpl" %}

<div class="obj dtask" style="background-color: #FFF;">

<div style="margin-bottom: 10px; padding: 0 10px">

<div style="overflow: hidden; text-align: right">
{% if data.0.close == 1 %}<div class="taskstatus">[завершено]</div>{% endif %}
{% if data.0.secure == 1 %}<div class="taskstatus">[приватная]</div>{% endif %}
{% if data.0.type == "1" and data.0.expire %}<div class="taskstatus">[просроченная]</div>{% endif %}
</div>


<div style="height: 85px; font-size: 12px">

{% if data.0.mail_id == 0 %}
<span style="overflow: hidden">
<div style="float: left; text-align: center; margin-right: 10px">
	<img class="avatar" id="ava" src="{{ author.avatar }}" alt="аватар" />
</div>
</span>
{% else %}
<span style="overflow: hidden">
<div style="float: left; margin-right: 10px">
	<img class="avatar" id="ava" src="{{ registry.uri }}img/noavatar.gif" alt="аватар" />
</div>
</span>
{% endif %}

<div style="float: left;">
	<div style="clear: both; font-size: 12px">
		<span class="grSub">Автор: </span>
		{% if data.0.remote_id != 0 %}
		{{ author.soname }} {{ author.name }} (группа {{ author.gname }})
		{% else %}
		<a style="cursor: pointer" onclick="getUserInfo('{{ author.id }}')">{{ author.soname }} {{ author.name }}</a>
		{% endif %}
	</div>
	
	<div style="clear: both; font-size: 12px">
		<span class="grSub">Дата: </span>{{ data.0.start }}
	</div>
	
	<div style="font-size: 12px; padding-bottom: 3px;">
		<span class="grSub">Группа: </span>{{ data.0.group }}
	</div>
	
		<ul class="nav nav-pills" style="margin-bottom: 0">
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				Ответственные
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>{% include "tt/info/responsible.tpl" %}</li>
			</ul>
		</li>
		
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				Сроки
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>{% include "tt/info/period.tpl" %}</li>
			</ul>
		</li>
		
		{% if data.0.chid %}
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				Вложенные задачи
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				{% for part in data %}
				<li style="text-align: left; padding: 4px 10px">
				<p class="chtask">
					<a style="color: #048; text-decoration: underline" href="{{ registry.uri }}tt/show/{{ part.chid }}/">№{{ part.chid }}</a>
					{{ part.chname }}
				</p>
				</li>
				{% endfor %}
			</ul>
		</li>
		{% endif %}
		
		{% if obj %}
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				Объект
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li style="text-align: left; padding: 4px 10px">
				{% for part in obj %}
				<span style="cursor: pointer; margin: 2px;" onclick="getInfo({{ part.id }})">{{ part.val }}</span>
				{% endfor %}
				</li>
			</ul>
		</li>
		{% endif %}
	</ul>
	
</div>

<div style="float: right; text-align: right">

	{% if data.0.cid %}
	<span class="title">(подзадача для </span><a class="title" style="color: #048" href="{{ registry.uri }}tt/show/{{ data.0.cid }}/">№{{ data.0.cid }}</a><span class="title">) </span>
	{% endif %}

	{% if type != "draft" %}
	<a class="title" style="color: #048" href="{{ registry.uri }}tt/show/{{ data.0.id }}/">№{{ data.0.id }}</a>
	{% else %}
	<a class="title" style="color: #048" href="{{ registry.uri }}tt/draft/{{ data.0.id }}/">№{{ data.0.id }}</a>
	{% endif %}

</div>

</div>

</div>

<div style="padding: 4px">

<div style="float: right">

<!-- приоритет -->
<div style="float: right; font-size: 12px; font-weight: normal">
{% if data.0.imp == 1 %}
<div style="height: 14px; width: 20px; border: 2px solid #00ffcc; text-align: center"><span style="position:relative; bottom: 2px">1/5</span></div>
{% elseif data.0.imp == 2 %}
<div style="height: 14px; width: 40px; border: 2px solid #00ffcc; text-align: center"><span style="position:relative; bottom: 2px">2/5</span></div>
{% elseif data.0.imp == 3 %}
<div style="height: 14px; width: 60px; border: 2px solid #ffcc00; text-align: center"><span style="position:relative; bottom: 2px">3/5</span></div>
{% elseif data.0.imp == 4 %}
<div style="height: 14px; width: 80px; border: 2px solid #ff0000; text-align: center"><span style="position:relative; bottom: 2px">4/5</span></div>
{% elseif data.0.imp == 5 %}
<div style="height: 14px; width: 100px; border: 2px solid #ff0000; text-align: center"><span style="position:relative; bottom: 2px">5/5</span></div>
{% endif %}
</div>
<!-- END приоритет -->

</div>



{% if data.0.attach %}
<div style="float: left; overflow: hidden; margin-left: 70px">
{% for part in data.0.attach %}
{% if type != "draft" %}
{% if data.0.remote_id != 0 %}
<div style="margin: 4px; float: left"><code><a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}tt/attach/?remote=1&tid={{ data.0.id }}&filename={{ part.filename }}">{{ part.filename }}</a></code></div>
{% else %}
<div style="margin: 4px; float: left"><code><a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}tt/attach/?tid={{ data.0.id }}&filename={{ part.filename }}">{{ part.filename }}</a></code></div>
{% endif %}
{% else %}
<div style="margin: 4px; float: left"><code><a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}tt/attach/?did={{ data.0.id }}&filename={{ part.filename }}">{{ part.filename }}</a></code></div>
{% endif %}
{% endfor %}
</div>
{% endif %}




<!-- Заголовок таска -->
<div style="margin-left: 65px">
{% if data.0.mail_id == 0 %}
{% if data.0.name %}
<div class="title"><a class="title" style="color: #048" href="{{ registry.uri }}tt/show/{{ data.0.id }}/">{{ data.0.name }}</a></div>
{% endif %}
{% else %}
<div style="margin-bottom: 10px">
{% if data.0.text.0.personal %}({{ data.0.text.0.personal }})&nbsp;{% endif %}
<a href="mailto: {{ data.0.text.0.email }}" style="margin-right: 10px">{{ data.0.text.0.email }}</a>
</div>
{% endif %}
</div>
<!-- /Заголовок таска -->






{% if data.0.mail_id != 0 %}
	<div class="sel" style="clear: both; font-size: 12px; margin-left: 70px">
	{% include "tt/mail.tpl" with {'mail': data.0.text, 'task': 1} %}
	</div>
{% else %}
	{{ data.0.text }}
{% endif %}

<div style="overflow: hidden; clear: both">
	<!-- кнопки-внизу -->
	{% if type != "draft" %}
	<div style="float: right; font-size: 12px; margin-top: 10px;">
	<a href="{{ registry.uri }}tt/show/{{ data.0.id }}/">
	<img src="{{ registry.uri }}img/user-medium.png" />
	{{ numComments }} {% if newComments > 0 %}<span style="color: green; font-weight: bold">+{{ newComments }}</span>{% endif %}
	</a>
	</div>
	
	<div class="btn-group" style="margin-top: 10px; float: left">
	
	<a class="btn" style="text-decoration: none" href="{{ registry.uri }}tt/history/{{ data.0.id }}/" title="История">
	<img src="{{ registry.uri }}img/clock-history.png" alt="История" border="0" />
	</a>
	
	{% if data.0.close == 0%}
	{% if data.0.spam != 0 %}
	<a class="btn" style="cursor: pointer; text-decoration: none" onclick="spam({{ data.0.id }})" title="отписаться от рассылки по задаче">
	<img src="{{ registry.uri }}img/mail--minus.png" />
	</span>
	{% else %}
	<a class="btn" style="cursor: pointer; text-decoration: none" onclick="spam({{ data.0.id }})" title="подписаться на рассылку по задаче">
	<img src="{{ registry.uri }}img/mail--plus.png" alt="" />
	</a>
	{% endif %}
	{% endif %}
	
	{% if data.0.close == 0 %}
	
	{% if registry.ui.id == data.0.who or registry.ui.admin %}
	<a class="btn" href="{{ registry.uri }}tt/edit/{{ data.0.id }}/" title="Правка">
	<img src="{{ registry.uri }}img/edititem.gif" />
	</a>
	{% endif %}
	
	{% if registry.ui.id == data.0.who or registry.ui.admin %}
	<a class="btn" href="{{ registry.uri }}tt/add/?sub={{ data.0.id }}" title="Создать подзадачу">
	<img src="{{ registry.uri }}img/plus-button.png" />
	</a>
	{% endif %}
	
	{% if not data.0.route %}
		{% if registry.ui.id == data.0.who or registry.ui.admin %}
		<a class="btn" onclick="closeTask({{ data.0.id }})" title="Завершить">
		<img src="{{ registry.uri }}img/inbox-download.png" />
		</a>
		{% endif %}
	{% else %}
		<a class="btn btn-success" onclick="setResult({{ data.0.id }})" title="Продолжить">
		<img src="{{ registry.uri }}img/node-select-next.png" />
		</a>
	{% endif %}
		
	{% endif %}
	
	</div>
	{% else %}
	<!-- Если черновик -->
	<div class="btn-group" style="margin-top: 20px">
	
	<a class="btn" style="cursor: pointer; text-decoration: none" href="{{ registry.uri }}tt/draftedit/{{ data.0.id }}/">
	<img src="{{ registry.uri }}img/edititem.gif" />
	Правка
	</a>
	
	<a class="btn" style="cursor: pointer; text-decoration: none" onclick="delDraftConfirm({{ data.0.id }})">
	<img src="{{ registry.uri }}img/inbox-download.png" />
	Удалить
	</a>
	
	</div>
	{% endif %}
	<!-- END кнопки-внизу -->
</div>

</div>

</div>