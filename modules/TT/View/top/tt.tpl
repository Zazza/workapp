<div style="float: left;" class="btn-group">

	{% if not registry.args.0 %}
	<a href="{{ registry.uri }}tt/" class="btn btn-success">
	{% else %}
	<a href="{{ registry.uri }}tt/" class="btn">
	{% endif %}
		<i class="icon-home"></i>
		Задачи [{{ registry.getNumTasks }}]
	</a>
	
	{% if registry.args.1 == "me" %}
	<a href="{{ registry.uri }}tt/task/me/" class="btn btn-success">
	{% else %}
	<a href="{{ registry.uri }}tt/task/me/" class="btn">
	{% endif %}
		<i class="icon-user"></i>
		Я автор [{{ registry.getNumMeTasks }}]
	</a>
	
	{% if registry.args.0 == "draft" %}
	<a href="{{ registry.uri }}tt/draft/" class="btn btn-success">
	{% else %}
	<a href="{{ registry.uri }}tt/draft/" class="btn">
	{% endif %}
		<i class="icon-pencil"></i>
		Черновики [{{ registry.draftttnum }}]
	</a>
	
	{% if registry.args.0 == "groups" %}
	<a href="{{ registry.uri }}tt/groups/" class="btn btn-success">
	{% else %}
	<a href="{{ registry.uri }}tt/groups/" class="btn">
	{% endif %}
		<i class="icon-th-large"></i>
		Проекты
	</a>

</div>