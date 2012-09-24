<h3>Новый шаблон</h3>

<form method="post">

<p><b>Имя шаблона</b></p>
<p><input type="text" name="name" value="{{ post.name }}" /></p>

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Добавить новое поле</a></p>
<p style="margin-bottom: 10px">В созданном поле напишите его название, например: "Название проекта"</p>

<div style="height: 30px">
<div style="float: left; width: 200px; text-align: center; font-size: 11px">имя поля</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">главное</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">тип поля</div>
</div>

<div id="field"></div>

<p style="margin-top: 10px"><input name="submit" type="submit" value="Создать" /></p>

</form>

<script type="text/javascript">
var i = 0;
function addField() {
    var val = '<div style="overflow: hidden; padding-bottom: 3px">';
    val += '<div style="float: left; width: 200px"><input type="text" name="field[' + i + ']" /><input type="hidden" name="new[' + i + ']" value="1" /></div><div style="float: left; width: 80px; text-align: center"><input type="checkbox" name="main[' + i + ']" /></div><div style="float: left; width: 80px; text-align: center"><select class="selecttype" id="type[' + i + ']" name="type[' + i + ']"><option value="0">Обычное</option><option value="1">Расширенное</option><option value="2">Селективное</option></select><select style="display: none" name="datatype[' + i + ']" class="datatype" id="datatype[' + i + ']">{% for part in datatypes %}<option value="{{ part.id }}">{{ part.name }}</option>{% endfor %}</select></div>';
    val += '</div>';
    $("#field").append(val);
    selReload();
    
    i++;
};

function selReload(){ 
	$(".selecttype").live("change", function(){
		var value = $('option:selected', this).val();

		if (value == 2) {
			$(this).next("select").show();
		} else {
			$(this).next("select").hide();
		}
	});
}
</script>