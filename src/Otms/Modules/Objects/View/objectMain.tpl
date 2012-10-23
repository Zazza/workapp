
<div class="obj">

<div style="overflow: hidden">
	<a href="{{ registry.uri }}objects/show/{{ obj.0.id }}/" class="title" style="color: #048">Объект {{ obj.0.id }}</a>
</div>

<div class="tabs" style="overflow: visible">

<ul>
	<li><a href="#tabs-1">Главные</a></li>
	<li><a href="#tabs-2">Задачи</a></li>
	<li><a href="#tabs-3">Информация</a></li>
</ul>

<div class="tab_container" style="width: 700px">

<div id="tabs-1" class="tabcont">
	<div style="overflow: hidden" class="btn-group">
	
	<div class="btn">
		<img src="{{ registry.uri }}img/information-button.png" style="vertical-align: middle" alt="info" border="0" />
		<a style="cursor: pointer" onclick="getInfo({{ obj.0.id }})">полная информация</a>
	</div>
	
	{% if mail %}
	<div class="btn">
		<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-receive.png" alt="mail" border="0" />
		<a href="{{ registry.uri }}mail/?oid={{ obj.0.id }}">входящая почта</a>
	</div>
	
	<div class="btn">
		<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-send.png" alt="mail" border="0" />
		<a href="{{ registry.uri }}mail/send/?oid={{ obj.0.id }}">исходящая почта</a>
	</div>
	{% endif %}
	
	<div class="btn">
		<i class="icon-folder-open"></i>
		<a href="{{ registry.uri }}fm/?id={{ obj.0.fdirid }}">файлы</a>
	</div>
	
	<div class="btn">
		<img src="{{ registry.uri }}img/context/status-busy.png" class="cm_img" style="margin-right: 0" />
		<a href="{{ registry.uri }}calendar/?oid={{ obj.0.id }}">бронь</a>
	</div>
	
	
	</div>
	
	{% for part in obj %}
	<p><b>{{ part.field }}:</b>&nbsp;{{ part.val }}</p>
	{% endfor %}
	</div>

<div id="tabs-2" class="tabcont">
	<div style="overflow: hidden">
	<div class="btn" style="float: left">
		<img src="{{ registry.uri }}img/applications-stack.png" alt="all tasks" style="vertical-align: middle" />
		<a href="{{ registry.uri }}task/oid/{{ obj.0.id }}/">Все задачи</a>
	</div>
	</div>
	
	<div style="margin: 10px 0 20px 5px">
	<span title="глобальные"><img border="0" style="vertical-align: middle" alt="" src="{{ registry.uri }}img/clock.png" /><b>&nbsp;{{ numTroubles.global }}</b></span>
	<span title="ограниченнные по времени" style="margin-left: 5px"><img border="0" style="vertical-align: middle" alt="" src="{{ registry.uri }}img/alarm-clock.png" /><b>&nbsp;{{ numTroubles.time }}</b></span>
	<span title="повторяющиеся" style="margin-left: 5px"><img border="0" style="vertical-align: middle" alt="" src="{{ registry.uri }}img/calendar-blue.png" /><b>&nbsp;{{ numTroubles.iter }}</b></span>
	<span title="закрытые" style="margin-left: 5px"><img border="0" style="vertical-align: middle" alt="" src="{{ registry.uri }}img/flag.png" /><b>&nbsp;{{ numTroubles.close }}</b></span>
	</div>
</div>

<div id="tabs-3" class="tabcont">
	<div style="overflow: hidden">
	<div class="btn" style="float: left">
		<img src="{{ registry.uri }}img/plus-button.png" alt="plus" border="0" style="vertical-align: middle" />
		<a style="font-size: 11px" href="{{ registry.uri }}objects/info/add/?oid={{ obj.0.id }}">добавить</a>
	</div>
	</div>

	<ul class="nav nav-pills">
		<li class="dropdown">
			<a class="dropdown-toggle" href="#" data-toggle="dropdown">
				<i class="icon-th-list"></i>
				Формы
				<b class="caret" ></b>
			</a>
			<ul class="dropdown-menu">
				{% if not forms %}
				<p style="text-align: center">форм нет</p>
				{% endif %}
				{% for form in forms %}
				<li><a href="{{ registry.uri }}objects/setform/?oid={{ obj.0.id }}&fid={{ form.id }}">{{ form.name }}</a></li>
				{% endfor %}
			</ul>
		</li>
	</ul>
	
	{% for part in advInfo %}
	{% if part.title %}
	<div>
	<a href="{{ registry.uri }}objects/info/edit/?oaid={{ part.oaid }}" title="правка"><img src="{{ registry.uri }}img/edititem.gif" alt="правка" border="0" style="vertical-align: middle" /></a>
	&nbsp;
	<a style="cursor: pointer" onclick="delAdvConfirm({{ part.oaid }})" title="удалить"><img src="{{ registry.uri }}img/delete.png" alt="удалить" border="0" style="vertical-align: middle" /></a>
	&nbsp;
	<span style="margin: 0 20px 5px 0"><a onclick="showInfo({{ part.oaid }})" style="cursor: pointer">{{ part.title }}</a></span>
	</div>
	{% endif %}
	{% endfor %}
</div>

</div>

</div>

</div>

<script type="text/javascript">
$(".tabs").tabs();
</script>