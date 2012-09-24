<div style="float: right;">

Сортировать задачи:
<select id="sorttt">
<option value="group" {% if sort["sort"] == "group" %}selected="selected"{% endif %}>по группе</option>
<option value="obj" {% if sort["sort"] == "obj" %}selected="selected"{% endif %}>по объектам</option>
<option value="imp" {% if sort["sort"] == "imp" %}selected="selected"{% endif %}>по приоритету</option>
<option value="type" {% if sort["sort"] == "type" %}selected="selected"{% endif %}>по типу</option>
<option value="date" {% if sort["sort"] == "date" %}selected="selected"{% endif %}>по дате</option>
</select>

<span id="sort_group" {% if sort["sort"] != "group" %}style="display: none;"{% endif %}>
<select>
	<option onclick="setSort('group', 'false')">---</option>
	<option onclick="setSort('group', 0)" {% if sort.id == '0' %}selected="selected"{% endif %}>Внутренняя</option>

	{% for key, part in sg.gname %}
	{% if part %}
	<option onclick="setSort('group', {{ key }})" {% if sort.id == key %}selected="selected"{% endif %}>{{ part }}</option>
	{% endif %}
	{% endfor %}
</select>
</span>

<span id="sort_imp" {% if sort["sort"] != "imp" %}style="display: none;"{% endif %}>
<select>
	<option onclick="setSort('imp', 'false')">---</option>
	{% for key, part in sg.imp %}
	<option onclick="setSort('imp', {{ key }})" {% if sort.id == part %}selected="selected"{% endif %}>{{ part }}</option>
	{% endfor %}
</select>
</span>

<span id="sort_type" {% if sort["sort"] != "type" %}style="display: none;"{% endif %}>
<select>
	<option onclick="setSort('type', 'false')">---</option>

	{% for part in sg.type %}
	{% if part == "0" %}
	<option onclick="setSort('type', 0)" {% if sort.id == part %}selected="selected"{% endif %}>
	Глобальные
	</option>
	{% endif %}
	{% if part == "1" %}
	<option onclick="setSort('type', 1)" {% if sort.id == part %}selected="selected"{% endif %}>
	Ограниченные по времени
	</option>
	{% endif %}
	{% if part == "2" %}
	<option onclick="setSort('type', 2)" {% if sort.id == part %}selected="selected"{% endif %}>
	Повторяющиеся
	</option>
	{% endif %}
	{% endfor %}
</select>
</span>

</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#sorttt").change(function() {
		setSort($(this).val(), false);
	});
});

function setSort(type, id) {
	var data = "action=setSortMyTt&sort=" + type + "&id=" + id;

	$.ajax({
	        type: "POST",
	        url: "{{ registry.uri }}ajax/tt/",
	        data: data,
	        success: function(res) {
				document.location.href = document.location.href;
	        }
	});
}

$("#sp_{{ sort.sort }}").show();
</script>