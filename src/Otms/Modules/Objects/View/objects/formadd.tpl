<h3>New template</h3>

<form method="post" accept="{{ registry.uri }}objects/forms/add/">

<p><b>Name template</b></p>
<p><input type="text" name="name" value="{{ post.name }}" /></p>

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Add new field</a></p>
<p style="margin-bottom: 10px">In created field write name, for example: "Width, mm"</p>

<div style="height: 30px">
<div style="float: left; width: 280px; text-align: center; font-size: 11px">field name</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">field type</div>
</div>

<div id="field"></div>

<p style="margin-top: 10px"><input name="submit" type="submit" value="Create" /></p>

</form>

<script type="text/javascript">
var i = 0;
function addField() {
    var val = '<div style="overflow: hidden; padding-bottom: 3px">';
    val += '<div style="float: left; width: 280px"><input type="text" name="field[' + i + ']" /><input type="hidden" name="new[' + i + ']" value="1" /></div><div style="float: left; width: 80px; text-align: center"><select class="selecttype" id="type[' + i + ']" name="type[' + i + ']"><option value="0">Text input</option><option value="1">Textarea</option><option value="2">Selected</option></select><select style="display: none" name="datatype[' + i + ']" class="datatype" id="datatype[' + i + ']">{% for part in datatypes %}<option value="{{ part.id }}">{{ part.name }}</option>{% endfor %}</select></div>';
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