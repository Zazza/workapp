<div class="btn-group" style="padding: 10px 0 30px 0">

<a onclick='repairObjsConfirm()' class="btn btn-danger">
	<i class="icon-remove icon-white"></i>
	восстановить
</a>

<a href="{{ registry.uri }}objects/sub/{{ gid }}/" class="btn">
	<i class="icon-trash"></i>
	перейти к группе объектов
</a>

</div>

<div id="objStructure">

<table class="table table-striped table-bordered table-condensed">
<thead>
<tr>
	<th style="width: 30px"></th>
	<th style="text-align: center; width: 30px">ID</th>
	<th>Объект</th>
	<th>Данные</th>
</tr>
</thead>
{% for obj in objs %}
<tbody>
<tr class="objclass" id="o_{{ obj.0.id }}">
	<td cellspacing="1" style="text-align: center"><input type="checkbox" class="objs tgroup{{ obj.0.type_id }}" id="obj[{{ obj.0.id }}]" name="obj[{{ obj.0.id }}]" /></td>
	<td cellspacing="1" style="text-align: center">{{ obj.0.id }}</td>
	<td cellspacing="1">
		<div style="float: left; margin-right: 10px;">
				<div class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="text-shadow: none;">
						<i class="icon-wrench"></i>
						<b class="caret"></b>
					</a>
				
					<ul class="dropdown-menu">
						<li><a style="cursor: pointer;" onclick="oc_info({{ obj.0.id }})"><img src="{{ registry.uri }}img/information-button.png" class="cm_img" />Инфо</a></li>
						<li><a style="cursor: pointer;" onclick="oc_edit({{ obj.0.id }})"><img src="{{ registry.uri }}img/edititem.gif" class="cm_img" />Правка</a></li>
						<li><a style="cursor: pointer;" onclick="oc_folder({{ obj.0.fdirid }})"><img src="{{ registry.uri }}img/folder.png" class="cm_img" />Файлы</a></li>
					</ul>
				</div>
			</div>
			
			<div style="float: left;">
				<p style="margin: 0">
					<a style="cursor: pointer" onclick="refreshurl('{{ siteName }}{{ registry.uri }}objects/show/{{ obj.0.id }}/')">
					{% for part in obj %}
					{% if part.main %}
					<span>{{ part.val }} </span>
					{% endif %}
					{% endfor %}
					</a>
				</p>
				
				{% if obj.0.email %}
				<p><b>email:</b> <a href="mailto: {{ obj.0.email }}">{{ obj.0.email }}</a></p>
				{% endif %}
			</div>
	</td>
	<td style="text-align: center; padding: 0">
		<table class="table table-striped" style="margin-bottom: 0">
		{% for part in obj.ai %}
		{% if part.title %}
		<tr><td>
			<span style="margin: 0 20px 5px 0"><i class="icon-eye-open"></i> <a onclick="showInfo({{ part.oaid }})" style="cursor: pointer">{{ part.title }}</a></span>
		</td></tr>
		{% endif %}
		{% endfor %}
		</table>
	</td>
</tr>
</tbody>
{% endfor %}
</table>

</div>

<div id="sortObjs" title="Сортировка" style="display: none">
<form name="setSort" action="{{ registry.uri }}objects/sub/{{ gid }}/" method="post">
<div style="overflow: hidden; margin-bottom: 20px">
<div style="float: left">
<select id="sortFid">
{% for part in fields %}
<option value="{{ part.fid }}">{{ part.field }}</option>
{% endfor %}
</select>
</div>
<div style="float: left">
<span class="btn" onclick="addSortField()">Добавить</span> 
</div>
</div>

<div id="sortFields" style="text-align: left"></div>
</form>
</div>

<script type="text/javascript">
function oc_info(id) {
	getInfo(id);
}

function oc_edit(id) {
	window.location.href = "{{ registry.uri }}objects/edit/" + id + "/";
}

function oc_folder(id) {
	window.location.href = "{{ registry.uri }}filemanager/?id=" + id;
}

function repairObjsConfirm() {
	$('<div title="Предупреждение">Восстановить выбранные объекты?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { repairObjs(); $(this).dialog("close"); }
		},
		width: 540
	});
}

function repairObjs() {
	var objs = $("#objStructure #obj:checked");

    var formData = new Array(); var i = 0;
    $("#objStructure .objs:checkbox:checked").each(function(n){
		id = this.id;
		
		formData[i] = ['"' + id + '"', "1"].join(":");
		
		i++;
    });

    var json = "{" + formData.join(",") + "}";

    $.ajax({
        type: "POST",
        async: false,
        url: '{{ registry.uri }}ajax/objects/',
        data: "action=repairObjs&json=" + json,
        success: function(res) {
			document.location.href = document.location.href;
		}
    });
}
</script>
