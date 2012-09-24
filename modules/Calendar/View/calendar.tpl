<div style="overflow: hidden">

<!-- Блок выбор месяца-года -->
<div class="blockbd" style="float: right">
<p>
<span>
<select id="month" name="month">
    <option value="01"{% if month == 1 %}selected="selected"{% endif %}>январь</option>
    <option value="02"{% if month == 2 %}selected="selected"{% endif %}>февраль</option>
    <option value="03"{% if month == 3 %}selected="selected"{% endif %}>март</option>
    <option value="04"{% if month == 4 %}selected="selected"{% endif %}>апрель</option>
    <option value="05"{% if month == 5 %}selected="selected"{% endif %}>май</option>
    <option value="06"{% if month == 6 %}selected="selected"{% endif %}>июнь</option>
    <option value="07"{% if month == 7 %}selected="selected"{% endif %}>июль</option>
    <option value="08"{% if month == 8 %}selected="selected"{% endif %}>август</option>
    <option value="09"{% if month == 9 %}selected="selected"{% endif %}>сентябрь</option>
    <option value="10"{% if month == 10 %}selected="selected"{% endif %}>октябрь</option>
    <option value="11"{% if month == 11 %}selected="selected"{% endif %}>ноябрь</option>
    <option value="12"{% if month == 12 %}selected="selected"{% endif %}>декабрь</option>
</select>
</span>
<span>
<select name="year" id="year">
    {% for part in calYear %}
    <option value="{{ part }}"{% if year == part %}selected="selected"{% endif %}>{{ part }}</option>
    {% endfor %}
</select>
</span>
<input type="button" id="seldate" name="seldate" value="Сменить" class="btn" />
</p>
</div>
<!-- Блок выбор месяца-года -->

<!-- Блок выбор типа задач для вывода -->
<div class="blockbd" style="float: left; font-size: 11px">
<p><label class="radio"><input type="radio" name="caltask" value="0" class="caltask" {% if caltype == 0 %}checked="checked"{% endif %} /> задачи, где я <b>ответственный</b></label></p>
<p><label class="radio"><input type="radio" name="caltask" value="1" class="caltask" {% if caltype == 1 %}checked="checked"{% endif %} /> задачи, где я <b>автор</b></label></p>
</div>
<!-- END Блок выбор типа задач для вывода -->

</div>

<!-- Блок количеcтво задач по типам -->
<div class="well" style="padding: 2px 5px">

<span style="position: static">
<a style="text-decoration: none" title="периодические" href="{{ registry.uri }}tt/task/iter/"><img src="{{ registry.uri }}img/calendar-blue.png" alt="" border="0" /> периодические</a> <b>[{{ itertask }}]</b>
</span>

<span style="position: static; margin-left: 10px">
<a style="text-decoration: none" title="ограниченные по времени" href="{{ registry.uri }}tt/task/time/"><img src="{{ registry.uri }}img/alarm-clock.png" alt="" border="0" /> ограниченные по времени</a> <b>[{{ timetask }}]</b>
</span>

{% set noiter = allmytask - itertask - timetask %}
<span style="position: static; margin-left: 10px">
<a style="text-decoration: none" title="неограниченные по времени" href="{{ registry.uri }}tt/task/noiter/"><img src="{{ registry.uri }}img/clock.png" alt="" border="0" /> неограниченные по времени</a> <b>[{{ noiter }}]</b>
</span>

<span style="position: static; margin-left: 10px">
<img src="{{ registry.uri }}img/flag.png" alt="" /> закрытые
</span>

<span style="position: static; margin-left: 10px">
<img src="{{ registry.uri }}img/lock.png" alt="" /> забронированные ресурсы
</span>

</div>
<!-- END Блок количество задач по типам -->

<table id="cal" cellpadding="3" cellspacing="2" width="100%" style="padding: 10px 50px 0 0;">

<tr>
<td width="14%" class="weekday workday">Понедельник</td>
<td width="14%" class="weekday workday">Вторник</td>
<td width="14%" class="weekday workday">Среда</td>
<td width="14%" class="weekday workday">Четверг</td>
<td width="14%" class="weekday workday">Пятница</td>
<td width="14%" class="weekday holiday">Суббота</td>
<td width="14%" class="weekday holiday">Воскресенье</td>
</tr>

<tr style="height: 20px">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<script type="text/javascript">
	var content = "";
</script>

{% set i = 1 %}
{% for k in range(1, 6) %}
<tr>
	{% if k == 1 %}
		{% for l in range(1, first) %}
            <td id="{{ l + 1 }}" class="caltd" style="border: 0px none; background-color: #FFF;"></td>
		{% endfor %}
			
		{% for l in range(first, 7) %}
			{% if i <= num %}
				{% if l == 6 or l == 7 %}
					{% set style1 = " background-color: #FFF; " %}
				{% else %}
					{% set style1 = "" %}
				{% endif %}
				{% if i == day %}
					{% set style2 = " border: 2px solid red; " %}
				{% else %}
					{% set style2 = "" %}
				{% endif %}
				<td id="{{ l + 1 }}" class="caltd" style="{{ style1 }}{{ style2 }}">
					<script type="text/javascript">
						content = renderCell('{{ year }}', '{{ month }}', '{{ i }}');
						content += "<div class='calcont'>{{ data[i] }}</div>";
						document.write(content);
					</script>
				</td>
				{% set i = i + 1 %}
			{% else %}
				<td id="{{ l + 1 }}" class="caltd" style="border: 0px none; background-color: #FFF;"></td>
			{% endif %}
		{% endfor %}
	{% else %}
		{% for l in range(1, 7) %}
			{% if i <= num %}
				{% if l == 6 or l == 7 %}
					{% set style1 = " background-color: #FFF; " %}
				{% else %}
					{% set style1 = "" %}
				{% endif %}
				{% if i == day %}
					{% set style2 = " border: 2px solid red; " %}
				{% else %}
					{% set style2 = "" %}
				{% endif %}
				<td id="{{ k }}{{ l }}" class="caltd" style="{{ style1 }}{{ style2 }}">
					<script type="text/javascript">
						content = renderCell('{{ year }}', '{{ month }}', '{{ i }}');
						content += "<div class='calcont'>{{ data[i] }}</div>";
						document.write(content);
					</script>
				</td>
				{% set i = i + 1 %}
			{% else %}
				<td id="{{ k }}{{ l }}" class="caltd" style="border: 0px none; background-color: #FFF;"></td>
			{% endif %}
		{% endfor %}
	{% endif %}
</tr>
{% endfor %}

</table>

{% include "resday.tpl" %}

{% include "newreserv.tpl" %}

<script type="text/javascript">
$(".caltask").click(function() {
	var data = "action=setCalTask&caltask=" + $(this).val();
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/calendar/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
});

$("#seldate").click(function() {
	var data = "action=setCalDate&month=" + $("#month").val() + "&year=" + $("#year").val();
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/calendar/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
});

function getDayReservs(date, fulldate) {
	$("#title_res").text("Дата: " + fulldate);
	$("#table_dreservs").show();
	$("#ttdres").html('');

	var data = "action=getDateReservs&date=" + date;
    $.ajax({
        type: "POST",
        url: url + "ajax/reserv/",
        data: data,
        success: function(res) {
        	$("#ttdres").append(res);
        }
    });
	
	$("#resday").dialog({
	    buttons: {
	    	"Добавить": function() {
	    		addReserv(fulldate);
	    	},
			"Закрыть": function() {
				$("#title_res").text("title", '');
				$("#ttdres").html('');
				$("#resday").dialog("close");
			}
		},
		width: 1230,
		height: 460
	});
};

function addReserv(fdate) {
	$(".reservdate").val(fdate);
	
	$("#newreserv").dialog({
		buttons: {
			"Отмена": function() {
				$("#newreserv").dialog("close");
			},
			"Готово": function() {
				if ($("#repeat").attr('checked')) {
					var ch_r = 1;
				} else {
					var ch_r = 0;
				}
				var data = "action=addReservs&oid=" + $("#selObjHid").val() + "&reservstart=" + $("#reservstart").val() + "&reservstarttime=" + $("#reservstarttime").val() + "&reservend=" + $("#reservend").val() + "&reservendtime=" + $("#reservendtime").val() + "&repeat=" + ch_r + "&repeat_num=" + $("#repeat_num").val() + "&repeat_period=" + $("#repeat_period").val() + "&repeat_cont_num=" + $("#repeat_cont_num").val() + "&repeat_cont_period=" + $("#repeat_cont_period").val();
			    $.ajax({
			        type: "POST",
			        url: url + "ajax/reserv/",
			        data: data,
			        success: function(res) {
			        	if (res == "object") {
			        		$("<div title='Ошибка'>Объект не выбран</div>").dialog({width: 180, height: 80});
			        	} else if (res == "collision") {
			        		$("<div title='Ошибка'>В заданный период времени объект уже забронирован</div>").dialog({width: 180, height: 120});
			        	} else if (res == "date") {
			        		$("<div title='Ошибка'>Дата начала брони больше даты окончания</div>").dialog({width: 180, height: 120});
			        	} else {
			        		$("#newreserv").dialog("close");
			        		$("#resday").dialog("close");
			        		window.location.href = window.location.href;
			        	}
			        }
			    });
			}
		},
		width: 650,
		height: 440
	});
};

$(".restd").live("click", function() {
	var id = $(this).attr("id");
	var dataid = $(this).attr("data-id");
	$("#" + id).popover({placement: 'top', title: 'Информация', content: $("#resinfo_" + dataid).html()});
	$("#" + id).popover('show');
	$("#res_hid").val(id);
});
</script>