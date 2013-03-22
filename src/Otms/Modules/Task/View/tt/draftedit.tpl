<form method="post" action="{{ registry.uri }}task/draftedit/">

<input type="hidden" name="tid" value="{{ data.0.id }}" />

<div style="margin: 10px 0">
	<div id="btaskname"><a href="#" class="btn" onclick="shTaskName()"><i class="icon-star"></i>Task name</a></div>
	<div id="dtaskname" {% if data.0.name %}style="display: block"{% endif %}><input id="titletask" type="text" name="taskname" value="{{ data.0.name }}" /></div>
</div>

<!-- attach -->
<div class="well">
	<div style="overflow: hidden">
		<a class="btn" style="float: left" onclick="fromBuffer()">
		<i class="icon-trash"></i>
		From buffer
		</a>
		
		<div id="arfiles" style="padding-left: 120px">
		</div>
	</div>

	<p>
	Attach files
	<a class="btn btn-mini" onclick="flushAttaches()">
		<i class="icon-remove"></i>
		remove
	</a>
	</p>
	<div id="attach_files" style="margin-top: 10px"></div>
</div>
<!-- /attach -->

<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

<div id="text_area" style="float: left">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px">{{ data.0.text }}</textarea>
</div>

</div>
<!-- /wysihtml5 -->

<!-- tabs -->
{% include "tt/tabs.tpl" %}
<!-- /tabs -->

<p style="margin-top: 30px">
<input type="submit" class="btn" name="submit" value="Change draft" style="margin-right: 10px" />
<input type="submit" class="btn" name="createtask" value="Create task" />
</p>

</form>

<div id="usersDialog" title="Select users" style="text-align: left"></div>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

{% for part in data.0.attach %}
$("#attach_files").append("<input type='hidden' name='attaches[]' value='{{ registry.rootPublic }}{{ registry.upload }}{{ part.filename }}' /><p><img border='0' src='{{ registry.uri }}img/paper-clip-small.png' alt='attach' style='position: relative; top: 4px; left: 1px' />{{ part.filename }}</p>");
{% endfor %}

{% for part in issRusers %}
$("#addedusers").append('{{ part.desc }}');
{% endfor %}
</script>