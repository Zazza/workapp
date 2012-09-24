<form method="post" action="{{ registry.uri }}tt/edit/">

<input type="hidden" name="tid" value="{{ data.0.id }}" />

<div class="input-prepend">
	<span class="add-on"><b>Название задачи:</b></span>
	<input type="text" name="taskname" id="titletask" style="margin-bottom: 0" class="span6" value="{{ data.0.name }}" />
</div>

{% if not data.0.mail_id %}
<!-- attach -->
<div style="overflow: hidden; margin-top: 10px">
<div class="alert alert-success" style="float: left; width: 500px">
	<p style="font-weight: bold">Прикреплённые файлы:</p>
	<a class="btn btn-mini btn-info" onclick="flushAttaches()">
		<i class="icon-remove icon-white"></i>
		очистить
	</a>
	<div id="attach_files" style="margin-top: 10px"></div>
</div>
</div>
<!-- /attach -->
{% endif %}

<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

{% if not data.0.mail_id %}
<div id="text_area" style="float: left">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px">{{ data.0.text }}</textarea>
</div>
{% else %}
<iframe style='height: 300px; border: 1px solid #B6B6B6' class="mailtext" src="{{ registry.siteName }}{{ registry.uri }}mail/load/?mid={{ data.0.mail_id }}&part=1" frameborder="0" width="100%" height="90%"></iframe>
<input type="hidden" name="textfield" value="1" />
{% endif %}

</div>
<!-- /wysihtml5 -->

<!-- tabs -->
{% include "tt/tabs.tpl" %}
<!-- /tabs -->

<p style="margin-top: 30px"><input type="submit" name="submit" value="Изменить" /></p>

</form>

<div id="usersDialog" title="Выбор пользователей" style="text-align: left"></div>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

{% for part in data.0.attach %}
$("#attach_files").append("<div style='margin: 4px; float: left'><input type='hidden' name='attaches[]' value='/{{ part.pdirid }}/{{ part.filename }}' /><code><img border='0' src='{{ registry.uri }}img/paper-clip-small.png' alt='attach' style='position: relative; top: 1px; left: 1px' />{{ part.filename }}</code></div>");
{% endfor %}

{% for part in issRusers %}
$("#addedusers").append('{{ part.desc }}');
{% endfor %}

function flushAttaches() {
	$("#attach_files").html('');
};
</script>
