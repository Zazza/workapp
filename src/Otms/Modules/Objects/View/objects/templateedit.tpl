<h3>Edit template</h3>

<ul class="nav nav-tabs">
	<li class="active"><a href="{{ registry.uri }}objects/templates/edit/{{ post.0.id }}/">Template</a></li>
	<li><a href="{{ registry.uri }}objects/templates/editview/{{ post.0.id }}/">Type</a></li>
</ul>

<form method="post">

<p><b>Template name</b></p>
<p><input type="text" name="name" value="{{ post.0.name }}" /></p>

<p><b>Field:</b></p>

<div style="height: 30px">
<div style="float: left; width: 200px; text-align: center; font-size: 11px">field name</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">main</div>
<div style="float: left; width: 80px; text-align: center; font-size: 11px">Field type</div>
</div>

{% for part in post %}
{% if part.field %}

<div style="overflow: hidden; padding-bottom: 3px">

<div style="float: left; width: 200px">
<input type="text" name="field[{{ part.fid }}]" value="{{ part.field }}" />
<input type="hidden" name="new[{{ part.fid }}]" value="0" />
</div>

<div style="float: left; width: 80px; text-align: center">
<input type="checkbox" name="main[{{ part.fid }}]" {% if part.main %}checked="checked"{% endif %} />
</div>

<div style="float: left; width: 80px; text-align: center">
	<select class="selecttype" id="type[{{ part.fid }}]" name="type[{{ part.fid }}]">
		<option value="0" {% if part.type == 0 %}selected="selected"{% endif %}>Text input</option>
		<option value="1" {% if part.type == 1 %}selected="selected"{% endif %}>Textarea</option>
		<option value="2" {% if part.type == 2 %}selected="selected"{% endif %}>Selected</option>
	</select>
	
	<select {% if part.type != 2 %}style="display: none"{% endif %} name="datatype[{{ part.fid }}]" class="datatype" id="datatype[{{ part.fid }}]">
		{% for parttype in datatypes %}
		<option value="{{ parttype.id }}" {% if part.datatype == parttype.id %}selected="selected"{% endif %}>
		{{ parttype.name }}
		</option>
		{% endfor %}
	</select>
</div>

</div>

{% endif %}
{% endfor %}

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Add new field</a></p>
<p style="margin-bottom: 10px">In created field write name, for example: "Project name"</p>

<div id="field"></div>

<p style="margin-top: 10px"><input name="submit" type="submit" value="Change" /></p>

</form>

<script type="text/javascript">
$(document).ready(function(){
	selReload();
});

var i = 0;
function addField() {
    var val = '<div style="overflow: hidden; padding-bottom: 3px">';
    val += '<div style="float: left; width: 200px"><input type="text" name="field[' + i + ']" /><input type="hidden" name="new[' + i + ']" value="1" /></div><div style="float: left; width: 80px; text-align: center"><input type="checkbox" name="main[' + i + ']" /></div><div style="float: left; width: 80px; text-align: center"><select class="selecttype" id="type[' + i + ']" name="type[' + i + ']"><option value="0">Text input</option><option value="1">Textarea</option><option value="2">Selected</option></select><select style="display: none" name="datatype[' + i + ']" class="datatype" id="datatype[' + i + ']">{% for part in datatypes %}<option value="{{ part.id }}">{{ part.name }}</option>{% endfor %}</select></div>';
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