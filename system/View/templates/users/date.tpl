<form method="post" action="{{ registry.uri }}users/tasks/{{ registry.args.1 }}/">

<p style="margin-top: 10px"><b>Укажите диапазон просматриваемых дат</b>
<a class="btn btn-mini" href="{{ registry.uri }}users/tasks/{{ registry.args.1 }}/?clear">сбросить</a>
</p>

<div style="overflow: hidden;">
<div style="float: left; width: 40px">С:</div>
<div style="float: left"><input name="sday" id="sday" type="text" class="selected" value="{{ date.sday|e }}" /></div>
<div style="float: left">
<select id="smonth" name="smonth">
<option value="1"{% if date.smonth == 1 %}selected="selected"{% endif %}>января</option>
<option value="2"{% if date.smonth == 2 %}selected="selected"{% endif %}>февраля</option>
<option value="3"{% if date.smonth == 3 %}selected="selected"{% endif %}>марта</option>
<option value="4"{% if date.smonth == 4 %}selected="selected"{% endif %}>апреля</option>
<option value="5"{% if date.smonth == 5 %}selected="selected"{% endif %}>мая</option>
<option value="6"{% if date.smonth == 6 %}selected="selected"{% endif %}>июня</option>
<option value="7"{% if date.smonth == 7 %}selected="selected"{% endif %}>июля</option>
<option value="8"{% if date.smonth == 8 %}selected="selected"{% endif %}>августа</option>
<option value="9"{% if date.smonth == 9 %}selected="selected"{% endif %}>сентября</option>
<option value="10"{% if date.smonth == 10 %}selected="selected"{% endif %}>октября</option>
<option value="11"{% if date.smonth == 11 %}selected="selected"{% endif %}>ноября</option>
<option value="12"{% if date.smonth == 12 %}selected="selected"{% endif %}>декабря</option>
</select>
</div>
<div style="float: left"><input name="syear" id="syear" type="text" class="selected" value="{{ date.syear|e }}" /></div>
<div style="float: left"><input type="text" id="sbut" value="выбрать дату" style="cursor: pointer" /></div>
</div>

<div style="overflow: hidden;">
<div style="float: left; width: 40px">По:</div>
<div style="float: left"><input name="fday" id="fday" type="text" class="selected" value="{{ date.fday|e }}" /></div>
<div style="float: left">
<select id="fmonth" name="fmonth">
<option value="1"{% if date.fmonth == 1 %}selected="selected"{% endif %}>января</option>
<option value="2"{% if date.fmonth == 2 %}selected="selected"{% endif %}>февраля</option>
<option value="3"{% if date.fmonth == 3 %}selected="selected"{% endif %}>марта</option>
<option value="4"{% if date.fmonth == 4 %}selected="selected"{% endif %}>апреля</option>
<option value="5"{% if date.fmonth == 5 %}selected="selected"{% endif %}>мая</option>
<option value="6"{% if date.fmonth == 6 %}selected="selected"{% endif %}>июня</option>
<option value="7"{% if date.fmonth == 7 %}selected="selected"{% endif %}>июля</option>
<option value="8"{% if date.fmonth == 8 %}selected="selected"{% endif %}>августа</option>
<option value="9"{% if date.fmonth == 9 %}selected="selected"{% endif %}>сентября</option>
<option value="10"{% if date.fmonth == 10 %}selected="selected"{% endif %}>октября</option>
<option value="11"{% if date.fmonth == 11 %}selected="selected"{% endif %}>ноября</option>
<option value="12"{% if date.fmonth == 12 %}selected="selected"{% endif %}>декабря</option>
</select>
</div>
<div style="float: left"><input name="fyear" id="fyear" type="text" class="selected" value="{{ date.fyear|e }}" /></div>
<div style="float: left"><input type="text" id="fbut" value="выбрать дату" style="cursor: pointer" /></div>
</div>


<p><input class="btn btn-info" type="submit" name="submit" value="Выбрать" /></p>

</form>

<hr style="border: 0px; background-color: #EEE; margin: 20px 0; height: 1px" />

<script type="text/javascript">
$('#sbut').datepicker({
    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
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
    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
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