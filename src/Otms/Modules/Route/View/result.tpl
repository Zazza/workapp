{% for key, part in result %}
{% if part.name %}

<div style="overflow: hidden; padding-bottom: 3px">

<div style="float: left; width: 250px">
<input type="text" name="field[{{ key }}]" value="{{ part.name }}" />
<input type="hidden" name="new[{{ key }}]" value="0" />
</div>

<div style="float: left; width: 80px; text-align: center">
	<select class="selecttype" id="type[{{ key }}]" name="res_type[{{ key }}]">
		<option value="0" {% if part.type == 0 %}selected="selected"{% endif %}>Text input</option>
		<option value="1" {% if part.type == 1 %}selected="selected"{% endif %}>Textarea</option>
		<option value="2" {% if part.type == 2 %}selected="selected"{% endif %}>Selected</option>
	</select>
	
	<select {% if part.type != 2 %}style="display: none"{% endif %} name="datatype[{{ key }}]" class="datatype" id="datatype[{{ key }}]">
		{% for parttype in datatypes %}
		<option value="{{ parttype.id }}" {% if part.datatype == parttype.id %}selected="selected"{% endif %}>
		{{ parttype.name }}
		</option>
		{% endfor %}
	</select>
</div>

</div>

<input type="hidden" class="autoinc" value="{{ key }}" />
<input type="hidden" name="res_id[{{ key }}]" value="{{ part.id }}" />

{% endif %}
{% endfor %}

<p style="margin-top: 10px"><img border="0" style="vertical-align: middle" alt="plus" src="{{ registry.uri }}img/plus-button.png" />&nbsp;<a style="cursor: pointer" onclick="addField()">Add new field</a></p>

<div id="field"></div>

<script type="text/javascript">
$(document).ready(function(){
	selReload();
});

if ($(".autoinc").last().width() > 0) {
	var i = parseInt($(".autoinc").last().val()) + 1;
} else {
	var i = 0;
}
function addField() {
    var val = '<div style="overflow: hidden; padding-bottom: 3px">';
    val += '<div style="float: left; width: 250px"><input type="text" name="field[' + i + ']" /><input type="hidden" name="new[' + i + ']" value="1" /></div><div style="float: left; width: 80px; text-align: center"><select class="selecttype" id="type[' + i + ']" name="res_type[' + i + ']"><option value="0">Text input</option><option value="1">Textarea</option><option value="2">Selected</option></select><select style="display: none" name="datatype[' + i + ']" class="datatype" id="datatype[' + i + ']">{% for part in datatypes %}<option value="{{ part.id }}">{{ part.name }}</option>{% endfor %}</select></div>';
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