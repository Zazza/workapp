<ul class="nav nav-list mainMenu">
	{% if registry.enableCheck %}
	<li><a style="cursor: pointer;" onclick="checkMail()"><img src="{{ registry.uri }}img/left/inbox-download.png" alt="" />Проверить почту</a></li>
	{% else %}
	<li><img src="{{ registry.uri }}img/left/inbox-download.png" alt="" />Проверить почту</li>
	{% endif %}
	<li><a href="{{ registry.uri }}tt/add/"><img alt="" src="{{ registry.uri }}img/plus-button.png" />Задача</a></li>
	<li><a href="{{ registry.uri }}mail/compose/"><img alt="" src="{{ registry.uri }}img/left/mail-plus.png" />Письмо</a></li>
	
	<li class="nav-header">Календари</li>
	{% if registry.syscontroller == "dashboard" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}dashboard/"><img alt="" src="{{ registry.uri }}img/dashboard.png" />События</a>
	</li>
	{% if registry.module == "calendar" and registry.args.0 != "reservs" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}calendar/"><img alt="" src="{{ registry.uri }}img/calendar-blue.png" />Календарь</a>
	</li>
	{% if registry.module == "gant" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}gant/"><img alt="" src="{{ registry.uri }}img/node-select-next.png" />Диаграмма Ганта</a>
	</li>
	
	<li class="nav-header">Модули</li>
	{% if registry.module == "tt" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}tt/"><img alt="" src="{{ registry.uri }}img/left/task.png" />Задачи</a>
	</li>
	{% if registry.module == "mail" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}mail/"><img alt="" src="{{ registry.uri }}img/left/mail.png" />Почта</a>
	</li>
	{% if registry.module == "fm" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}fm/"><img alt="" src="{{ registry.uri }}img/folder.png" />Файловый менеджер</a>
	</li>
	{% if registry.module == "photo" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}photo/"><img alt="" src="{{ registry.uri }}img/images.png" />Фотографии</a>
	</li>
	{% if registry.module == "calendar" and registry.args.0 == "reservs" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}calendar/reservs/"><img alt="" src="{{ registry.uri }}img/lock.png" />Бронь</a>
	</li>
	{% if registry.module == "route" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}route/"><img alt="" src="{{ registry.uri }}img/node.png" />Маршруты</a>
	</li>
	
	<li class="nav-header">Ресурсы</li>
	{% if registry.module == "objects" and registry.args.0 != "kb" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}objects/"><img alt="" src="{{ registry.uri }}img/g.png" />Объекты</a>
	</li>
	{% if registry.module == "objects" and registry.args.0 == "kb" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}objects/kb/"><img alt="" src="{{ registry.uri }}img/information-button.png" />База знаний</a>
	</li>

	<li class="nav-header">Система</li>
	{% if registry.syscontroller == "users" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}users/"><img alt="" src="{{ registry.uri }}img/users.png" />Пользователи</a>
	</li>
	{% if registry.syscontroller == "settings" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}settings/"><img alt="" src="{{ registry.uri }}img/gear.png" />Настройки</a>
	</li>
	
	

</ul>