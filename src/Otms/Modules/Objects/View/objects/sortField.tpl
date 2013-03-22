{% if field %}
<div style="overflow: hidden; margin-bottom: 10px">
<span style="margin-right: 10px"><b>{{ field.field }}</b></span>
<span style="margin-right: 10px">
<select name="sort[{{field.fid}}]" style="width: 120px">
<option value=">">></option>
<option value="<"><</option>
<option value="=">=</option>
<option value="!=">!=</option>
<option value="%">contain</option>
</select>
</span>
{% if field.type == 2 %}
<span>
	<select name="name[{{ field.fid }}]">
	{% for part in field.sel %}
	<option>{{ part.val }}</option>
	{% endfor %}
	</select>
</span>
{% elseif field.type == 1 %}
<span><textarea name="name[{{ field.fid }}]"></textarea></span>
{% elseif field.type == 0 %}
<span><input type="text" name="name[{{ field.fid }}]" /></span>
{% endif %}
</div>
{% endif %}
