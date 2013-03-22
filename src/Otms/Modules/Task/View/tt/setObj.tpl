<div style="margin-bottom: 10px">
<div onclick="getInfo({{ id }})" style="cursor: pointer; text-decoration: underline; padding-bottom: 5px">
{% for key, val in data %}
{{ val }}
{% endfor %}
</div>
<div class="btn btn-mini" style="cursor: pointer" onclick="selObj{{ id }}()"><img src="{{ registry.uri }}img/enter.png" alt="" style="vertical-align: middle; margin-right: 5px">select</div>
</div>
<script type="text/javascript">
function selObj{{ id }}() {
	$("#selObj").html("");
	var text = "";
	{% for key, val in data %}
		text = text + "{{ val }} ";
	{% endfor %}

	$("#findObject").dialog("close");
	$("#newObj").show();
	$("#selObjHid").val({{ id }});
	
	$("#selObj").append("<a style='cursor: pointer; text-decoration: underline' onclick='getInfo({{ id }})'>" + text + "</a> ");
}
</script>