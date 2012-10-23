<form method="post" action="{{ registry.uri }}task/add/?oid={{ oid }}&date={{ now_date }}">

{% if registry.get.sub %}
<input type="hidden" name="sub" value="{{ registry.get.sub }}" />
{% endif %}

<div class="input-prepend">
	<span class="add-on"><b>Название задачи:</b></span>
	<input type="text" name="taskname" id="titletask" style="margin-bottom: 0" class="span6" />
</div>

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


<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

<div id="text_area" style="float: left">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px"></textarea>
</div>

</div>
<!-- /wysihtml5 -->

<!-- tabs -->
{% include "tt/tabs.tpl" %}
<!-- /tabs -->

<p style="margin-top: 30px">
<input type="submit" class="btn" name="draft" value="В черновик" style="margin-right: 10px" />
<input type="submit" class="btn" name="submit" value="Создать" />
</p>

</form>

<div id="usersDialog" title="Выбор пользователей" style="text-align: left"></div>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

{% if issRusers %}
{% for part in issRusers %}
$("#addedusers").append('{{ part.desc }}');
{% endfor %}
{% endif %}

function flushAttaches() {
	$("#attach_files").html('');
};
</script>
