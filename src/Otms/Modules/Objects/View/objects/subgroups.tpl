<div class="btn-group" style="padding: 10px 0 30px 0">

<a href="{{ registry.uri }}objects/add/?p={{ gid }}" class="btn">
	<img alt="add object" border="0" src="{{ registry.uri }}img/plus-button.png" />
	Добавить объект
</a>

{% if sort_flag %}
<a onclick='sortObjs()' class="btn btn-success">
	<i class="icon-search"></i>
	сортировка
</a>
{% else %}
<a onclick='sortObjs()' class="btn">
	<i class="icon-search"></i>
	сортировка
</a>
{% endif %}


{% if mail %}
<a onclick='writeMail()' class="btn">
	<img src="{{ registry.uri }}img/left/mail-plus.png" alt="mail" border="0" />
	написать письмо
</a>
{% endif %}

{% if registry.ui.admin %}
<a onclick='removeObjsConfirm()' class="btn btn-danger">
	<i class="icon-remove icon-white"></i>
	удалить
</a>

<a href="{{ registry.uri }}objects/removed/{{ gid }}/" class="btn">
	<i class="icon-trash"></i>
	Корзина
</a>
{% endif %}

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
<tr class="objclass" id="o_{{ obj.0.id }}" f="{{ obj.0.fdirid }}">
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
					<hr style="padding: 3px 0; margin: 0;" />
					<li><a style="cursor: pointer;" onclick="oc_addtask({{ obj.0.id }})"><img src="{{ registry.uri }}img/context/task--plus.png" class="cm_img" />Задача</a></li>
					<li><a style="cursor: pointer;" onclick="oc_addinfo({{ obj.0.id }})"><img src="{{ registry.uri }}img/context/database--plus.png" class="cm_img" />Инфо</a></li>
					<li><a style="cursor: pointer;" onclick="oc_addform({{ obj.0.id }})"><img src="{{ registry.uri }}img/context/address-book--plus.png" class="cm_img" />Форма</a></li>
					<li><a style="cursor: pointer;" onclick="oc_reserv({{ obj.0.id }})"><img src="{{ registry.uri }}img/context/status-busy.png" class="cm_img" />Бронь</a></li>
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
			<p style="margin: 0; padding: 0;"><b>email:</b> <a href="mailto: {{ obj.0.email }}">{{ obj.0.email }}</a></p>
			{% endif %}
		</div>
	</td>
	<td style="margin: 0">
		{% for part in obj.ai %}
		{% if part.title %}
			<p style="margin: 0; padding: 0;"><i class="icon-eye-open"></i> <a onclick="showInfo({{ part.oaid }})" style="cursor: pointer">{{ part.title }}</a></p>
		{% endif %}
		{% endfor %}
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
<div style="float: right" class="btn">
<a href="{{ registry.uri }}objects/sub/{{ gid }}/?clear">сбросить</a>
</div>
</div>

<div id="sortFields" style="text-align: left"></div>
</form>
</div>

<!-- FORMS -->
<div title="Формы" style="display: none" id="forms">
	{% if not forms %}
	<p style="text-align: center">форм нет</p>
	{% endif %}
	<select id="form_current">
	{% for form in forms %}
	<option value="{{ form.id }}">{{ form.name }}</option>
	{% endfor %}
	</select>
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

function oc_addtask(id) {
	window.location.href = "{{ registry.uri }}task/add/?oid=" + id;
}

function oc_addinfo(id) {
	window.location.href = "{{ registry.uri }}objects/info/add/?oid=" + id;
}

function oc_addform(id) {
	$("#forms").dialog({
	    buttons: {
			"Продолжить": function() {
				window.location.href = "{{ registry.uri }}objects/setform/?oid=" + id + "&fid=" + $("#form_current").val();
				$(this).dialog("close");
			},
			"Закрыть": function() {
				$(this).dialog("close");
			}
		},
		width: 350,
		height: 160
	});
}

function oc_reserv(id) {
	window.location.href = "{{ registry.uri }}calendar/?oid=" + id;
}

function addSortField() {
	$.ajax({
		type: "POST",
		async: false,
		url: '{{ registry.uri }}ajax/objects/',
		data: "action=addSortField&fid=" + $("#sortFid").val(),
		success: function(res) {
			$("#sortFields").append(res);
		}
	});
}
	
function writeMail() {
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
		url: '{{ registry.uri }}ajax/mail/',
		data: "action=writeMail&json=" + json,
		success: function(res) {
			document.location.href = url + 'mail/compose/?obj';
		}
	});
}

function removeObjsConfirm() {
	$('<div title="Предупреждение">Действительно удалить выбранные объекты?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { removeObjs(); $(this).dialog("close"); }
		},
		width: 540
	});
}

function removeObjs() {
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
        data: "action=removeObjs&json=" + json,
        success: function(res) {
			document.location.href = document.location.href;
		}
    });
}

function sortObjs() {
	$('#sortObjs').dialog({
		modal: true,
	    buttons: {
			"Закрыть": function() { $(this).dialog("close"); },
			"Применить": function() { document.forms["setSort"].submit(); $(this).dialog("close"); }
		},
		width: 540
	});
}
</script>
