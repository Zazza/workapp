<div style="overflow: hidden">

<!-- Блок выбор месяца-года -->
<form class="form-inline">
<div class="blockbd" style="float: right">
<p>
<span>
<select id="month" name="month">
    <option value="01"{% if month == 1 %}selected="selected"{% endif %}>January</option>
    <option value="02"{% if month == 2 %}selected="selected"{% endif %}>February</option>
    <option value="03"{% if month == 3 %}selected="selected"{% endif %}>March</option>
    <option value="04"{% if month == 4 %}selected="selected"{% endif %}>April</option>
    <option value="05"{% if month == 5 %}selected="selected"{% endif %}>May</option>
    <option value="06"{% if month == 6 %}selected="selected"{% endif %}>June</option>
    <option value="07"{% if month == 7 %}selected="selected"{% endif %}>July</option>
    <option value="08"{% if month == 8 %}selected="selected"{% endif %}>August</option>
    <option value="09"{% if month == 9 %}selected="selected"{% endif %}>September</option>
    <option value="10"{% if month == 10 %}selected="selected"{% endif %}>October</option>
    <option value="11"{% if month == 11 %}selected="selected"{% endif %}>November</option>
    <option value="12"{% if month == 12 %}selected="selected"{% endif %}>December</option>
</select>
</span>
<span>
<select name="year" id="year">
    {% for part in calYear %}
    <option value="{{ part }}"{% if year == part %}selected="selected"{% endif %}>{{ part }}</option>
    {% endfor %}
</select>
</span>
<input type="button" id="seldate" name="seldate" value="replace" class="btn" />
</p>
</div>
</form>
<!-- Блок выбор месяца-года -->

{% if type == "all" or type == "uid" %}

<!-- Блок выбор типа задач для вывода -->
<div class="blockbd" style="float: left; font-size: 11px">
<p><label class="radio"><input type="radio" name="caltask" value="0" class="caltask" {% if caltype == 0 %}checked="checked"{% endif %} /> tasks <b>for me</b></label></p>
<p><label class="radio"><input type="radio" name="caltask" value="1" class="caltask" {% if caltype == 1 %}checked="checked"{% endif %} /> tasks <b>my</b></label></p>
</div>
<!-- END Блок выбор типа задач для вывода -->

</div>

<!-- Блок количеcтво задач по типам -->
<div class="well" style="padding: 2px 5px">

<span style="position: static">
<a style="text-decoration: none" title="periodic" href="{{ registry.uri }}task/task/iter/"><img src="{{ registry.uri }}img/calendar-blue.png" alt="" border="0" /> periodic</a> <b>[{{ itertask }}]</b>
</span>

<span style="position: static; margin-left: 10px">
<a style="text-decoration: none" title="limited" href="{{ registry.uri }}task/task/time/"><img src="{{ registry.uri }}img/alarm-clock.png" alt="" border="0" /> limited</a> <b>[{{ timetask }}]</b>
</span>

{% set noiter = allmytask - itertask - timetask %}
<span style="position: static; margin-left: 10px">
<a style="text-decoration: none" title="unlimited" href="{{ registry.uri }}task/task/noiter/"><img src="{{ registry.uri }}img/clock.png" alt="" border="0" /> unlimited</a> <b>[{{ noiter }}]</b>
</span>

<span style="position: static; margin-left: 10px">
<img src="{{ registry.uri }}img/flag.png" alt="" /> closed
</span>

<span style="position: static; margin-left: 10px">
<img src="{{ registry.uri }}img/lock.png" alt="" /> reserved resources
</span>

</div>
<!-- END Блок количество задач по типам -->

{% endif %}

<table id="ajax-load" style="width: 100%; padding: 100px 30px 0 0"><tr><td style="text-align: center"><img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></td></tr></table>

<table id="cal" cellpadding="3" cellspacing="2" width="100%" style="padding: 10px 50px 0 0; display: none">

<tr>
<td width="14%" class="weekday workday">Monday</td>
<td width="14%" class="weekday workday">Tuesday</td>
<td width="14%" class="weekday workday">Wednesday</td>
<td width="14%" class="weekday workday">Thursday</td>
<td width="14%" class="weekday workday">Friday</td>
<td width="14%" class="weekday holiday">Saturday</td>
<td width="14%" class="weekday holiday">Sunday</td>
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

	addtask += '<a style="float: left" href="' + url + 'task/add/?date=' + tdate + '" title="create task"><img src="' + url + 'img/plus-button.png" alt="" style="margin-right: 5px;" /></a>';
	addtask += '<a onclick="addReserv(\'' + fullfdate + '\')" title="add reservation" style="float: left; cursor: pointer;"><img src="' + url + 'img/lock.png" alt="" /></a>';

    result = "<p class='subtd'>" + addtask + fdate + "</p>";
    
    if (arr != 0) {
    	result += "<div class='calcont'>" + arr + "</div>";
    } else {
    	result += "<div class='calcont'>&nbsp;</div>";
    }
	
	return result;
}

function getDayReservs(date, fulldate) {
	$("#title_res").text("Date: " + fulldate);
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
	    	"Add": function() {
	    		addReserv(fulldate);
	    	},
			"Close": function() {
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
			"Cancel": function() {
				$("#newreserv").dialog("close");
			},
			"Done": function() {
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
			        		$("<div title='error'>object isn't chosen</div>").dialog({width: 180, height: 80});
			        	} else if (res == "collision") {
			        		$("<div title='error'>during the set period of time the object is already reserved</div>").dialog({width: 220, height: 120});
			        	} else if (res == "date") {
			        		$("<div title='error'>reservation start date is more than expiration date</div>").dialog({width: 220, height: 120});
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
	$("#" + id).popover({placement: 'top', title: 'Information', content: $("#resinfo_" + dataid).html()});
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
    		$('#cr_' + date).popover({placement: 'top', title: 'Reservation viewing', content: res});
    		$('#cr_' + date).popover('show');
    	}
    });
});
$(".cres").live("mouseout", function() {
	var date = $(this).attr("data-id");
	$('#cr_' + date).popover('hide');
});
</script>