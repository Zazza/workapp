<ul class="nav nav-list">
	<li class="nav-header">Актуальные задачи:</li>
	
	{% if not registry.args.0 %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}tt/">
		<i class="icon-home"></i>
		Задачи [{{ registry.getNumTasks }}]
		</a>
	</li>
	
	{% if registry.args.1 == "me" %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}tt/task/me/">
		<i class="icon-user"></i>
		Я автор [{{ registry.getNumMeTasks }}]
		</a>
	</li>
	
	{% if registry.args.0 == "draft" %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}tt/draft/">
		<i class="icon-pencil"></i>
		Черновики [{{ registry.draftttnum }}]
		</a>
	</li>
	
</ul>