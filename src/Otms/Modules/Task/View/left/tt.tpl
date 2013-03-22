<ul class="nav nav-list">
	<li class="nav-header">Actual tasks:</li>
	
	{% if not registry.args.0 %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}task/">
		<i class="icon-home"></i>
		Tasks [{{ registry.getNumTasks }}]
		</a>
	</li>
	
	{% if registry.args.1 == "me" %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}task/task/me/">
		<i class="icon-user"></i>
		I'm author [{{ registry.getNumMeTasks }}]
		</a>
	</li>
	
	{% if registry.args.0 == "draft" %}
	<li class="active">
	{% else %}
	<li>
	{% endif %}
		<a href="{{ registry.uri }}task/draft/">
		<i class="icon-pencil"></i>
		Draft [{{ registry.draftttnum }}]
		</a>
	</li>
	
</ul>