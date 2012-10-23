<h3>Новый комментарий:</h3>

<!-- attach -->
<div style="padding: 4px 10px;">
	<div style="overflow: hidden">
		<a class="btn" style="float: left" onclick="fromBuffer()">
		<i class="icon-trash"></i>
		Из буфера
		</a>
		
		<div id="arfiles" style="padding-left: 120px">
		</div>
	</div>

	<p>
	Прикреплённые файлы
	<a class="btn btn-mini" onclick="flushAttaches()">
		<i class="icon-remove"></i>
		очистить
	</a>
	</p>
	<div id="attach_files" style="margin-top: 10px"></div>
</div>
<!-- /attach -->

<div style="overflow: hidden">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px"></textarea>
</div>

<p>
<b>Статус:</b>
<select id="status" name="status" style="margin-left: 10px">
<option value="0">Нет</option>
{% for part in status %}
<option value="{{ part.id }}">{{ part.status }}</option>
{% endfor %}
</select>
</p>

<div style="clear: both; margin: 10px 0"><input type="button" class="btn" onclick="addComment()" value="Написать" /></div>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

function addComment() {
	var formData = new Array(); var i = 0;
	$("input[name='attaches[]']").each(function(n){
		name = i;
		val = this.value;

		formData[i] = ['"' + name + '"', '"' + val + '"'].join(":");

		i++;
	});

	var json = "{" + formData.join(",") + "}";
	
	var data = "action=addComment&tid={{ tid }}&text=" + encodeURIComponent($("#wysihtml5").val()) + "&status=" + $("#status").val() + "&json=" + json;
	$.ajax({
		type: "POST",
    	url: "{{ registry.uri }}ajax/task/",
    	data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
};
</script>