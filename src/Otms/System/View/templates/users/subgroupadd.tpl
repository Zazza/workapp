<h3>New template</h3>

<form method="post" accept="{{ registry.uri }}settings/templates/add/">

<p><b>Template name</b></p>
<p><input type="text" name="name" value="{{ post.name }}" /></p>

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Add new field</a></p>
<p style="margin-bottom: 10px">In created field write name, example: "Project name"</p>

<div style="height: 30px">
<div style="float: left; width: 200px; text-align: center; font-size: 11px">field name</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">main</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">textarea</div>
</div>

<div id="field"></div>

<p style="margin-top: 10px"><input name="submit" type="submit" value="Create" /></p>

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