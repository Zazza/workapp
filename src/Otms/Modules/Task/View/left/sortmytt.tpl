<ul class="nav nav-list">

<hr />

<li class="nav-header">Sort tasks:</li>

{% if sort["sort"] == "group" %}
<li class="active">
{% else %}
<li>
{% endif %}
	<a style="cursor: pointer" onclick="setSort('group', false)">
	<i class="icon-arrow-down"></i>
	By group
	</a>
</li>

<li style="display: none" id="sp_group" class="sortGroups">
<ul class="nav nav-list">
	{% if sort["sort"] == "group" and sort.id == "0" %}
	<li style="margin-left: 20px" class="active">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	<a style="cursor: pointer" onclick="setSort('group', '0')" class="{% if sort.id == "0" %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	No group</a>
	</li>
	{% for key, part in sg.gname %}
	{% if part %}
	{% if sort["sort"] == "group" and sort.id == key %}
	<li style="margin-left: 20px" class="active">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	<a style="cursor: pointer" onclick="setSort('group', {{ key }})" class="{% if sort.id == key %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	{{ part }}
	</a>
	</li>
	{% endif %}
	{% endfor %}
</ul>
</li>

{% if sort["sort"] == "obj" %}
<li class="active">
{% else %}
<li>
{% endif %}
	<a style="cursor: pointer" onclick="setSort('obj', false)">
	<i class="icon-arrow-down"></i>
	By object
	</a>
</li>

{% if sort["sort"] == "imp" %}
<li class="active">
{% else %}
<li>
{% endif %}
	<a style="cursor: pointer" onclick="setSort('imp', false)">
	<i class="icon-arrow-down"></i>
	By priority
	</a>
</li>

<li style="display: none" id="sp_imp" class="sortGroups">
<ul class="nav nav-list">
	{% for part in sg.imp %}
	{% if sort["sort"] == "imp" and sort.id == part  %}
	<li class="active" style="margin-left: 20px">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	<a style="cursor: pointer" onclick="setSort('imp', {{ part }})" class="{% if sort.id == part %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	Priority: {{ part }}
	</a>
	</li>
	{% endfor %}
</ul>
</li>

{% if sort["sort"] == "type" %}
<li class="active">
{% else %}
<li>
{% endif %}
	<a style="cursor: pointer" onclick="setSort('type', false)">
	<i class="icon-arrow-down"></i>
	By type
	</a>
</li>

<li style="display: none" id="sp_type" class="sortGroups">
<ul class="nav nav-list">
	{% for part in sg.type %}
	{% if part == "0" %}
	
	{% if sort["sort"] == "type" and sort.id == "0" %}
	<li style="margin-left: 20px" class="active">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	
	<a style="cursor: pointer" onclick="setSort('type', 0)" class="{% if sort.id == part %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	Global
	</a>
	</li>{% endif %}
	{% if part == "1" %}
	
	{% if sort["sort"] == "type" and sort.id == 1 %}
	<li style="margin-left: 20px" class="active">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	
	<a style="cursor: pointer" onclick="setSort('type', 1)" class="{% if sort.id == part %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	Limited
	</a>
	</li>{% endif %}
	{% if part == "2" %}

	{% if sort["sort"] == "type" and sort.id == 2 %}
	<li style="margin-left: 20px" class="active">
	{% else %}
	<li style="margin-left: 20px">
	{% endif %}
	
	<a style="cursor: pointer" onclick="setSort('type', 2)" class="{% if sort.id == part %}selsubmenu{% endif %}">
	<i class="icon-minus"></i>
	Repeating
	</a>
	</li>{% endif %}
	{% endfor %}
</ul>
</li>

{% if sort["sort"] == "date" %}
<li class="active">
{% else %}
<li>
{% endif %}
	<a style="cursor: pointer" onclick="setSort('date', false)">
	<i class="icon-chevron-right"></i>
	By date
	</a>
</li>

</ul>


<script type="text/javascript">
function setSort(type, id) {
	var data = "action=setSortMyTt&sort=" + type + "&id=" + id;

	$.ajax({
	        type: "POST",
	        url: "{{ registry.uri }}ajax/task/",
	        data: data,
	        success: function(res) {
				document.location.href = document.location.href;
	        }
	});
}

$("#sp_{{ sort.sort }}").show();
</script>