<div id="gantC">
<form class="form-inline" style="overflow: hidden;">
<div style="float: left;">
	<input class="btn" style="width: 70px;" onclick="calReset()" value="сбросить" />
	
	<div style="margin-left: 10px; position: relative; bottom: 4px;" class="btn-group">
		<input type="button" class="btn btn-info" value="Задачи" onclick="document.location.href = '{{ registry.uri }}gant/'" />
		<input type="button" class="btn btn-info" value="Брони" onclick="document.location.href = '{{ registry.uri }}gant/?reservs'" />
	</div>
</div>


<div style="float: right;">
<select id="gantlimit">
	<option value="10" {% if limit == 10 %}selected="selected"{% endif %}>10 дней</option>
	<option value="30" {% if limit == 30 %}selected="selected"{% endif %}>30 дней</option>
	</select>
	<input class="btn" style="width: 70px;" onclick="calLimit()" value="применить" />
</div>
</form>

<div style="overflow: hidden;">

<div style="float: left; width: 30px; height: 20px; text-align: center; margin-right: 1px;"><a style="cursor: pointer;" onclick="calLeft()"><img src="{{ registry.uri }}img/arrows/arrow-180.png" alt="left" /></a></div>
{% if sess == 1 %}
<div style="float: left; width: 30px; height: 20px; text-align: center; margin-right: 1px;"></div>
{% else %}
<div style="float: left; width: 30px; height: 20px; text-align: center; margin-right: 1px;"><a style="cursor: pointer;" onclick="calRight()"><img src="{{ registry.uri }}img/arrows/arrow.png" alt="left" /></a></div>
{% endif %}

{% for part in cal %}
<div style="float: left; text-align: center; margin-right: 1px; width: 80px; height: 20px; border: 1px solid #555; color: white; font-weight: bold; background-color: {{ part.color }}">{{ part.fdate }}</div>
{% endfor %}

</div>

{% if type == "tasks" %}
	{% include "tasks.tpl" %}
{% elseif type == "reservs" %}
	{% include "reservs.tpl" %}
{% endif %}

</div>

<script type="text/javascript">
{% if limit == 10 %}
$("#gantC").css("width", "1000px");
$("#centerContent").css("overflow", "auto");
{% endif %}
{% if limit == 30 %}
$("#gantC").css("width", "2700px");
$("#centerContent").css("overflow", "auto");
{% endif %}

$('.tttip').tooltip();

function calLeft() {
	var data = "action=calLeft";
	$.ajax({
		type: "POST",
		url: url + "ajax/gant/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
}

function calRight() {
	var data = "action=calRight";
	$.ajax({
		type: "POST",
		url: url + "ajax/gant/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
}

function calReset() {
	var data = "action=calReset";
	$.ajax({
		type: "POST",
		url: url + "ajax/gant/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
}

function calLimit() {
	var data = "action=calLimit&limit=" + $("#gantlimit").val();
	$.ajax({
		type: "POST",
		url: url + "ajax/gant/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
}

$(".rtip").live("click", function() {
	var data_id = $(this).attr("data-id");
	var id = $(this).attr("id");
	var data = "action=showDateReservText&id=" + data_id + "&wid=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/calendar/",
    	data: data,
    	async: false,
    	success: function(res) {
    		$("#" + id).popover({placement: 'top', title: 'Просмотр броней', content: res });
    		$("#" + id).popover('show');
    	}
    });
});
</script>

