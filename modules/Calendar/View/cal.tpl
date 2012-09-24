<div style="overflow: hidden">

<!-- Блок выбор месяца-года -->
<form class="form-inline">
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
</form>
<!-- Блок выбор месяца-года -->

{% if type == "all" or type == "uid" %}

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

{% endif %}

<table id="ajax-load" style="width: 100%; padding: 100px 30px 0 0"><tr><td style="text-align: center"><img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></td></tr></table>

<table id="cal" cellpadding="3" cellspacing="2" width="100%" style="padding: 10px 50px 0 0; display: none">

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

<tr style="height: 115px">
<td id="11" class="caltd"></td>
<td id="12" class="caltd"></td>
<td id="13" class="caltd"></td>
<td id="14" class="caltd"></td>
<td id="15" class="caltd"></td>
<td id="16" class="caltd"></td>
<td id="17" class="caltd"></td>
</tr>

<tr style="height: 115px">
<td id="21" class="caltd"></td>
<td id="22" class="caltd"></td>
<td id="23" class="caltd"></td>
<td id="24" class="caltd"></td>
<td id="25" class="caltd"></td>
<td id="26" class="caltd"></td>
<td id="27" class="caltd"></td>
</tr>

<tr style="height: 115px">
<td id="31" class="caltd"></td>
<td id="32" class="caltd"></td>
<td id="33" class="caltd"></td>
<td id="34" class="caltd"></td>
<td id="35" class="caltd"></td>
<td id="36" class="caltd"></td>
<td id="37" class="caltd"></td>
</tr>

<tr style="height: 115px">
<td id="41" class="caltd"></td>
<td id="42" class="caltd"></td>
<td id="43" class="caltd"></td>
<td id="44" class="caltd"></td>
<td id="45" class="caltd"></td>
<td id="46" class="caltd"></td>
<td id="47" class="caltd"></td>
</tr>

<tr style="height: 115px">
<td id="51" class="caltd"></td>
<td id="52" class="caltd"></td>
<td id="53" class="caltd"></td>
<td id="54" class="caltd"></td>
<td id="55" class="caltd"></td>
<td id="56" class="caltd"></td>
<td id="57" class="caltd"></td>
</tr>

<tr style="height: 115px">
<td id="61" class="caltd"></td>
<td id="62" class="caltd"></td>
<td id="63" class="caltd"></td>
<td id="64" class="caltd"></td>
<td id="65" class="caltd"></td>
<td id="66" class="caltd"></td>
<td id="67" class="caltd"></td>
</tr>

</table>

{% include "resday.tpl" %}

{% include "newreserv.tpl" %}

<script type="text/javascript">
getMonth();

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

function getMonth() {
    $("#ajax-load").show();
    $("#cal").hide();
    
    var arr = new Array();
    {% if type == "all" %}
    var data = "action=getMonth&month=" + $("#month").val() + "&year=" + $("#year").val();
    {% elseif type == "uid" %}
    var data = "action=getMonth&month=" + $("#month").val() + "&year=" + $("#year").val() + "&uid={{ uid }}";
    {% else %}
    var data = "action=getMonth&month=" + $("#month").val() + "&year=" + $("#year").val() + "&oid={{ oid }}";
    {% endif %}
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/calendar/",
		data: data,
        dataType: 'json',
		success: function(res) {

            $.each(res, function(key, val) {
                if (key == "first") {
                    first = val;
                } else if (key == "num") {
                    num = val;
                } else {
                    arr[key] = val;
                }
            });
            
            day = 1;
            for (k=1; k<=6; k++) {
                if (k == 1) {
                    for (l=1; l<first; l++) {
                        var tdid = "#" + 1 + l;
                        $(tdid).css("border", "0px none");
                        $(tdid).css("background-color", "#FFF");
                        $(tdid).html("");
                    }
                        
                    for (l=first; l<=7; l++) {
                        var tdid = "#" + 1 + l;
                        
                        if (day <= num) {
                            $(tdid).css("border", "1px solid #6696af");
                            if ((l == 6) || (l == 7)) {
                                $(tdid).css("background-color", "#FDD");
                            }

                            $(tdid).html(renderCell($("#year").val(), $("#month").val(), day, arr[day]));
                            
                            if (day == {{ day }}) { $(tdid).css("border", "2px solid red"); }
                            
                            day++;
                        } else {
                            $(tdid).css("border", "0px none");
                            $(tdid).css("background-color", "#FFF");
                            $(tdid).html("");
                        }
                    }
                } else {
                    for (l=1; l<=7; l++) {
                        var tdid = "#" + k + l;
                        
                        if (day <= num) {
                            $(tdid).css("border", "1px solid #6696af");
                            if ((l == 6) || (l == 7)) {
                                $(tdid).css("background-color", "#FDD");
                            }

                            $(tdid).html(renderCell($("#year").val(), $("#month").val(), day, arr[day]));
                            
                            if (day == {{ day }}) { $(tdid).css("border", "2px solid red"); }
                            
                            day++;
                        } else {
                            $(tdid).css("border", "0px none");
                            $(tdid).css("background-color", "#FFF");
                            $(tdid).html("");
                        }
                    }
                }
            }

            $("#ajax-load").hide();
            $("#cal").show();
		}
	})
}

function renderCell(year, month, day, arr) {
	var result = ""; var tdate = ""; var fdate = ""; var fullfdate = ""; var addtask = "";

	if (day < 10) {
		tdate = year + month + "0" + day;
		fdate = "0" + day + "." + month;
		fullfdate = "0" + day + "." + month + "." + year;
	} else {
		tdate = year + month + day;
		fdate = day + "." + month;
		fullfdate = day + "." + month + "." + year;
	}

	addtask += '<a style="float: left" href="' + url + 'tt/add/?date=' + tdate + '" title="создать задачу"><img src="' + url + 'img/plus-button.png" alt="" style="margin-right: 5px;" /></a>';
	addtask += '<a onclick="addReserv(\'' + fullfdate + '\')" title="добавить бронь" style="float: left; cursor: pointer;"><img src="' + url + 'img/lock.png" alt="" /></a>';

    result = "<p class='subtd'>" + addtask + fdate + "</p>";
    
    if (arr != 0) {
    	result += "<div class='calcont'>" + arr + "</div>";
    } else {
    	result += "<div class='calcont'>&nbsp;</div>";
    }
	
	return result;
}

function getDayReservs(date, fulldate) {
	$("#title_res").text("Дата: " + fulldate);
	$("#table_dreservs").show();
	$("#ttdres").html('');

	{% if type == "uid" %}
	var data = "action=getDateReservs&date=" + date + "&uid={{ uid }}";
	{% else %}
	var data = "action=getDateReservs&date=" + date;
	{% endif %}
    $.ajax({
        type: "POST",
        url: url + "ajax/calendar/",
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
			        url: url + "ajax/calendar/",
			        data: data,
			        success: function(res) {
			        	if (res == "object") {
			        		$("<div title='Ошибка'>Объект не выбран</div>").dialog({width: 180, height: 80});
			        	} else if (res == "collision") {
			        		$("<div title='Ошибка'>В заданный период времени объект уже забронирован</div>").dialog({width: 220, height: 120});
			        	} else if (res == "date") {
			        		$("<div title='Ошибка'>Дата начала брони больше даты окончания</div>").dialog({width: 220, height: 120});
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

$(".cres").live("mouseover", function() {
	var date = $(this).attr("data-id");
	{% if type == "uid" %}
	var data = "action=getReservsShow&date=" + date + "&uid={{ uid }}";
	{% else %}
	var data = "action=getReservsShow&date=" + date;
	{% endif %}
	$.ajax({
		type: "POST",
		url: url + "ajax/calendar/",
    	data: data,
    	async: false,
    	success: function(res) {
    		$('#cr_' + date).popover({placement: 'top', title: 'Просмотр броней', content: res});
    		$('#cr_' + date).popover('show');
    	}
    });
});
$(".cres").live("mouseout", function() {
	var date = $(this).attr("data-id");
	$('#cr_' + date).popover('hide');
});
</script>