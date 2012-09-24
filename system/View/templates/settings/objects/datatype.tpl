<div style="overflow: hidden; margin: 10px 0">
<div style="float: right;">
	<div class="input-append">
		<input id="datatypename" class="span7" style="margin: 0" type="text" size="16">
		<button class="btn" type="button" onclick="addDataType()"><i class="icon-plus"></i> Создать</button>
	</div>
</div>
</div>

{% for part in datatypes %}
<div class="well">
	<div style="margin-bottom: 5px; overflow: hidden">
		<h3 style="float: left">{{ part.name }}</h3>

		<a style="float: right" onclick="delDataTypeConfirm('{{ part.id }}')" class="btn">
			<i class="icon-remove-circle"></i>
			удалить
		</a>
		
		<a style="float: right; margin-right: 5px" href="{{ registry.uri }}settings/datatypes/?action=del&id={{ part.id }}" class="btn">
			<i class="icon-pencil"></i>
			правка
		</a>
	</div>
	
	
	<select>
	{% for val in part.vals %}
		<option>{{ val.val }}</option>
	{% endfor %}
	</select>
</div>
{% endfor %}

<script type="text/javascript">
function addDataType() {
	var data = "action=addDataType&name=" + $("#datatypename").val();
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	})
}

function delDataTypeConfirm(id) {
	$('<div title="Удаление">Действительно удалить?<div>').dialog({
		modal: true,
	    buttons: {
            "Да": function() {
            	delDataType(id);
                $(this).dialog("close");
            },
			"Нет": function() {
                 $(this).dialog("close");
            }
		},
		width: 200,
        height: 140
	});
}

function delDataType(id) {
	var data = "action=delDataType&id=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	})
}
</script>