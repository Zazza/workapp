<h3>Новый шаблон</h3>

<form method="post" accept="{{ registry.uri }}settings/templates/add/">

<p><b>Имя шаблона</b></p>
<p><input type="text" name="name" value="{{ post.name }}" /></p>

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Добавить новое поле</a></p>
<p style="margin-bottom: 10px">В созданном поле напишите его название, например: "Название проекта"</p>

<div style="height: 30px">
<div style="float: left; width: 200px; text-align: center; font-size: 11px">имя поля</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">главное</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">расширенное</div>
</div>

<div id="field"></div>

<p style="margin-top: 10px"><input name="submit" type="submit" value="Создать" /></p>

</form>

<script type="text/javascript">
var i = 0;
function addField() {
    var val = '<div style="height: 30px">';
    val += '<div style="float: left; width: 200px"><input type="text" name="field[' + i + ']" /><input type="hidden" name="new[' + i + ']" value="1" /></div><div style="float: left; width: 80px; text-align: center"><input type="checkbox" name="main[' + i + ']" /></div><div style="float: left; width: 80px; text-align: center"><input type="checkbox" name="expand[' + i + ']" /></div>';
    val += '</div>';
    $("#field").append(val);
    
    i++;
}
</script>