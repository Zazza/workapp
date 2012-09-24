<form method="post" action="{{ registry.uri }}route/draft/edit/?id={{ rid }}">

<div style="overflow: hidden">
<h3>Условие:</h3>
<span style="margin-right: 10px">
<select id="ifdata">
{% for part in data %}
<option style="background-color: black; color: white">{{ part.task_name }}</option>
{% for val in part %}
{% if val.name %}
<option type="{{ val.type }}" datatype="{{ val.datatype }}" value="{{ val.id }}">{{ val.name }}</option>
{% endif %}
{% endfor %}
{% endfor %}
</select>
</span>

<span style="margin-right: 10px">
<select id="ifcon" style="width: 120px">
<option value=">">></option>
<option value="<"><</option>
<option value="=">=</option>
<option value="!=">!=</option>
</select>
</span>

<span id="sif"></span>
</div>

<div style="overflow: hidden; margin-bottom: 10px">
<h3>Действие:</h3>
<p>
Переход:
<select id="goto">
{% for part in steps %}
<option value="{{ part.id }}">{{ part.name }}</option>
{% endfor %}
</select>
</p>
</div>

<span class="btn" onclick="addSortField()">Добавить условие</span> 

<div id="conds" class="well" style="margin-top: 10px">
{% set i = 0 %}
{% for part in action %}
<div style="margin: 10px">
<div><b>Условие: </b>{{ part.ifdataval }} {{ part.ifcon }} {{ part.ifval }}</div>
<div><b>Действие: </b>{{ part.gotoval }}</div>

<input type="hidden" name="ifdata[{{ i }}]" value="{{ part.ifdata }}" />
<input type="hidden" name="ifcon[{{ i }}]" value="{{ part.ifcon }}" />
<input type="hidden" name="ifval[{{ i }}]" value="{{ part.ifval }}" />
<input type="hidden" name="goto[{{ i }}]" value="{{ part.goto }}" />

{% set i = i + 1 %}

<a class="delStepAction btn btn-mini">удалить</a>
</div>

<input type="hidden" name="ifid[{{ i - 1 }}]" value="{{ part.id }}" />

{% endfor %}
</div>

<input type="hidden" name="step_id" value="{{ step_id }}" />
<input type="submit" name="actionsubmit" style="margin-top: 20px" class="btn" value="Готово" />
</form>

<script type="text/javascript">
$("#ifdata").change(function(){
	var type = $("#ifdata option:selected").attr("type");
	if (type == 2) {
		$.ajax({
			type: "POST",
			url: '{{ registry.uri }}ajax/route/',
			data: "action=getDatatype&datatype=" + $("#ifdata option:selected").attr("datatype"),
			success: function(res) {
				$("#sif").html(res);
			}
		});
	} else if ( (type == 1) || (type == 0) ) {
		$("#sif").html("<input type='text' id='ifval' />");
	} else {
		$("#sif").html("");
	}
});

function addSortField() {
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=addGoto&ifdata=" + $("#ifdata").val() + "&ifdataval=" + $("#ifdata option:selected").text() + "&ifcon=" + $("#ifcon").val() + "&ifval=" + $("#ifval").val() + "&goto=" + $("#goto").val() + "&gotoval=" + $("#goto option:selected").text(),
		success: function(res) {
			$("#conds").append(res);
		}
	});
}
</script>