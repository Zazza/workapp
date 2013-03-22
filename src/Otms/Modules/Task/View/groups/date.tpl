{% if registry.args.1 %}
<form method="post" action="{{ registry.uri }}task/groups/{{ registry.args.1 }}/">
{% else %}
<form method="post" action="{{ registry.uri }}task/groups/">
{% endif %}

<p style="margin-top: 10px"><b>Specify range of viewed dates <a class="btn btn-mini" href="{{ registry.uri }}task/groups/?clear">clear</a></b></p>

<div style="overflow: hidden; padding-bottom: 3px">
<div style="float: left; width: 40px">С:</div>
<div style="float: left"><input name="sday" id="sday" type="text" class="selected" value="{{ date.sday|e }}" /></div>
<div style="float: left">
<select id="smonth" name="smonth">
<option value="1"{% if date.smonth == 1 %}selected="selected"{% endif %}>January</option>
<option value="2"{% if date.smonth == 2 %}selected="selected"{% endif %}>February</option>
<option value="3"{% if date.smonth == 3 %}selected="selected"{% endif %}>March</option>
<option value="4"{% if date.smonth == 4 %}selected="selected"{% endif %}>April</option>
<option value="5"{% if date.smonth == 5 %}selected="selected"{% endif %}>May</option>
<option value="6"{% if date.smonth == 6 %}selected="selected"{% endif %}>June</option>
<option value="7"{% if date.smonth == 7 %}selected="selected"{% endif %}>July</option>
<option value="8"{% if date.smonth == 8 %}selected="selected"{% endif %}>August</option>
<option value="9"{% if date.smonth == 9 %}selected="selected"{% endif %}>September</option>
<option value="10"{% if date.smonth == 10 %}selected="selected"{% endif %}>October</option>
<option value="11"{% if date.smonth == 11 %}selected="selected"{% endif %}>November</option>
<option value="12"{% if date.smonth == 12 %}selected="selected"{% endif %}>December</option>
</select>
</div>
<div style="float: left"><input name="syear" id="syear" type="text" class="selected" value="{{ date.syear|e }}" /></div>
<div style="float: left"><input type="text" id="sbut" value="select date" style="cursor: pointer" /></div>
</div>

<div style="overflow: hidden; padding-bottom: 3px">
<div style="float: left; width: 40px">Until:</div>
<div style="float: left"><input name="fday" id="fday" type="text" class="selected" value="{{ date.fday|e }}" /></div>
<div style="float: left">
<select id="fmonth" name="fmonth">
<option value="1"{% if date.smonth == 1 %}selected="selected"{% endif %}>January</option>
<option value="2"{% if date.smonth == 2 %}selected="selected"{% endif %}>February</option>
<option value="3"{% if date.smonth == 3 %}selected="selected"{% endif %}>March</option>
<option value="4"{% if date.smonth == 4 %}selected="selected"{% endif %}>April</option>
<option value="5"{% if date.smonth == 5 %}selected="selected"{% endif %}>May</option>
<option value="6"{% if date.smonth == 6 %}selected="selected"{% endif %}>June</option>
<option value="7"{% if date.smonth == 7 %}selected="selected"{% endif %}>July</option>
<option value="8"{% if date.smonth == 8 %}selected="selected"{% endif %}>August</option>
<option value="9"{% if date.smonth == 9 %}selected="selected"{% endif %}>September</option>
<option value="10"{% if date.smonth == 10 %}selected="selected"{% endif %}>October</option>
<option value="11"{% if date.smonth == 11 %}selected="selected"{% endif %}>November</option>
<option value="12"{% if date.smonth == 12 %}selected="selected"{% endif %}>December</option>
</select>
</div>
<div style="float: left"><input name="fyear" id="fyear" type="text" class="selected" value="{{ date.fyear|e }}" /></div>
<div style="float: left"><input type="text" id="fbut" value="select date" style="cursor: pointer" /></div>
</div>


<p><input class="btn btn-info" type="submit" name="submit" value="Select" /></p>

</form>

<hr style="border: 0px; background-color: #EEE; margin: 20px 0; height: 1px" />

<script type="text/javascript">
$('#sbut').datepicker({
    dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    monthNamesShort: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    firstDay: 1,
    defaultDate: $("#smonth").val() + "/" + $("#sday").val() + "/" + $("#syear").val(),
	onSelect: function(dateText, inst) {
		$("#sbut").val("выбрать дату");
		$("#sday").val(inst.selectedDay);
		$("#syear").val(inst.selectedYear);
		var month = inst.selectedMonth + 1;
		$("#smonth [value='" + month + "']").attr("selected", "selected");
	}
});

$("#fbut").datepicker({
    dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    monthNamesShort: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    firstDay: 1,
    defaultDate: $("#fmonth").val() + "/" + $("#fday").val() + "/" + $("#fyear").val(),
	onSelect: function(dateText, inst) {
		$("#fbut").val("выбрать дату");
		$("#fday").val(inst.selectedDay);
		$("#fyear").val(inst.selectedYear);
		var month = inst.selectedMonth + 1;
		$("#fmonth [value='" + month + "']").attr("selected", "selected");
	}
});
</script>