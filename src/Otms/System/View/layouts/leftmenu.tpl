<ul class="nav nav-list mainMenu">
	{% if registry.enableCheck %}
	<li><a style="cursor: pointer;" onclick="checkMail()"><img src="{{ registry.uri }}img/left/inbox-download.png" alt="" />Check mail</a></li>
	{% else %}
	<li><img src="{{ registry.uri }}img/left/inbox-download.png" alt="" />Check mail</li>
	{% endif %}
	<li><a href="{{ registry.uri }}task/add/"><img alt="" src="{{ registry.uri }}img/plus-button.png" />Task</a></li>
	<li><a href="{{ registry.uri }}mail/compose/"><img alt="" src="{{ registry.uri }}img/left/mail-plus.png" />Mail</a></li>
	
	<li class="nav-header">Calendars</li>
	{% if registry.syscontroller == "dashboard" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}dashboard/"><img alt="" src="{{ registry.uri }}img/dashboard.png" />Events</a>
	</li>
	{% if registry.module == "calendar" and registry.args.0 != "reservs" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}calendar/"><img alt="" src="{{ registry.uri }}img/calendar-blue.png" />Calendar</a>
	</li>
	{% if registry.module == "gant" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}gant/"><img alt="" src="{{ registry.uri }}img/node-select-next.png" />Gant diagram</a>
	</li>
	
	<li class="nav-header">Modules</li>
	{% if registry.module == "task" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}task/"><img alt="" src="{{ registry.uri }}img/left/task.png" />Tasks</a>
	</li>
	{% if registry.module == "mail" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}mail/"><img alt="" src="{{ registry.uri }}img/left/mail.png" />Mail</a>
	</li>
	{% if registry.module == "fm" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}filemanager/"><img alt="" src="{{ registry.uri }}img/folder.png" />Filemanager</a>
	</li>
	{% if registry.module == "photo" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}photo/"><img alt="" src="{{ registry.uri }}img/images.png" />Photo</a>
	</li>
	{% if registry.module == "calendar" and registry.args.0 == "reservs" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}calendar/reservs/"><img alt="" src="{{ registry.uri }}img/lock.png" />Reservation</a>
	</li>
	{% if registry.module == "route" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}route/"><img alt="" src="{{ registry.uri }}img/node.png" />Route</a>
	</li>
	
	<li class="nav-header">Resourses</li>
	{% if registry.module == "objects" and registry.args.0 != "kb" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}objects/lists/"><img alt="" src="{{ registry.uri }}img/g.png" />Objects</a>
	</li>
	{% if registry.module == "objects" and registry.args.0 == "kb" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}objects/kb/"><img alt="" src="{{ registry.uri }}img/information-button.png" />Knownbase</a>
	</li>

	<li class="nav-header">System</li>
	{% if registry.syscontroller == "users" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}users/"><img alt="" src="{{ registry.uri }}img/users.png" />Users</a>
	</li>
	{% if registry.syscontroller == "settings" %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}settings/"><img alt="" src="{{ registry.uri }}img/gear.png" />Settings</a>
	</li>
	
	

</ul>