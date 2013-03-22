{% if err %}
<div style="margin-bottom: 20px">
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
</div>
{% endif %}

{% if mail.0.id %}
<form method="post" action="{{ registry.uri }}mail/sort/?mid={{ mail.0.id }}">
{% else %}
<form method="post" action="{{ registry.uri }}mail/sort/">
{% endif %}

<h3><b>Sort on:</b></h3>

<p>Field "from whom"</p>
<p>
	<input type="checkbox" name="checkbox_from" />
	<input type="text" name="from" value="{{ mail.0.email }}" style="width: 280px" />
</p>

<p>Field "to whom"</p>
<p>
	<input type="checkbox" name="checkbox_to" />
	<input type="text" name="to" value="{{ mail.0.to }}" style="width: 280px" />
</p>

<p>Field "Theme" (contains text)</p>
<p>
	<input type="checkbox" name="checkbox_subject" />
	<input type="text" name="subject" value="{{ mail.0.subject }}" style="width: 280px" />
</p>

<h3 style="margin-top: 30px"><b>Action:</b></h3>

<p>
{% if not folders %}
	<a class="btn btn-mini" href="{{ registry.uri }}mail/folder/">
	<i class="icon-folder-open"></i>
	Create folder
	</a>
{% else %}
	<label class="radio inline">
	<input type="radio" class="mail_action" name="mail_action" checked="checked" value="move" />
	Move in
	</label>
	<select name="folder">
		{% for part in folders %}
		<option value="{{ part.id }}">{{ part.folder }}</option>
		{% endfor %}
	</select>
{% endif %}
</p>

<p style="margin-top: 10px">
<label class="radio inline">
	<input type="radio" class="mail_action" name="mail_action" value="remove" />
	delete
</label>
</p>

<p style="margin-top: 10px">
<label class="radio inline">
	<input type="radio" class="mail_action" name="mail_action" value="task" />
	create task
</label>
</p>

<div id="addtask" style="display: none; padding-top: 20px">
{% include "mail/sorttask.tpl" %}
</div>

<p style="margin-top: 20px">
	<input type="submit" class="btn" name="submit" value="Create sorting" />
</p>

</form>

<script type="text/javascript">
$(".mail_action").change(function(){
	if ($(this).val() == "task") {
		$("#addtask").show();
	} else {
		$("#addtask").hide();
	}
});
</script>