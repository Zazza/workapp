{% if edit %}
<div id="newreserv" title='Редактирование' style="display: none">
{% else %}
<div id="newreserv" title='Забронировать ресурс' style="display: none">
{% endif %}
<table class="table">
<tr>
	<td><h5>Объект</h5></td>
	{% if edit %}
	<td colspan="4">
		<h5 id="rObject"></h5>
		<input type="hidden" id="selObjHid" value="" />
	</td>
	{% elseif oid %}
	<td colspan="4">
		<h5>{% for part in object %} {{ part.val }} {% endfor %}</h5>
		<input type="hidden" id="selObjHid" value="{{ oid }}" />
	</td>
	{% else %}
	<td colspan="4">
		<a class="btn" onclick="selObject()"><i class="icon-th-list"></i> Выбрать объект</a>
		<div id="newObj" style="margin-bottom: 20px; display: none">
		<div id="selObj" style="margin-top: 10px"></div>
		<input type="hidden" id="selObjHid" value="{{ oid }}" />
		</div>
	</td>
	{% endif %}
</tr>
<tr>
	<td><h5>Начало</h5></td>
	<td></td>
	<td colspan="2"><input type="text" id="reservstart" class="reservdate" style="cursor: pointer; width: 80px;" /></td>
	<td>
		<select id="reservstarttime" style="width: 80px;">
			<option>00:00</option>
			<option>01:00</option>
			<option>02:00</option>
			<option>03:00</option>
			<option>04:00</option>
			<option>05:00</option>
			<option>06:00</option>
			<option>07:00</option>
			<option>08:00</option>
			<option>09:00</option>
			<option>10:00</option>
			<option>11:00</option>
			<option>12:00</option>
			<option>13:00</option>
			<option>14:00</option>
			<option>15:00</option>
			<option>16:00</option>
			<option>17:00</option>
			<option>18:00</option>
			<option>19:00</option>
			<option>20:00</option>
			<option>21:00</option>
			<option>22:00</option>
			<option>23:00</option>
		</select>
	</td>
</tr>
<tr>
	<td><h5>Конец</h5></td>
	<td></td>
	<td colspan="2"><input type="text" id="reservend" class="reservdate endform" style="cursor: pointer; width: 80px;" /></td>
	<td>
		<select id="reservendtime" style="width: 80px;" class="endform">
			<option>01:00</option>
			<option>02:00</option>
			<option>03:00</option>
			<option>04:00</option>
			<option>05:00</option>
			<option>06:00</option>
			<option>07:00</option>
			<option>08:00</option>
			<option>09:00</option>
			<option>10:00</option>
			<option>11:00</option>
			<option>12:00</option>
			<option>13:00</option>
			<option>14:00</option>
			<option>15:00</option>
			<option>16:00</option>
			<option>17:00</option>
			<option>18:00</option>
			<option>19:00</option>
			<option>20:00</option>
			<option>21:00</option>
			<option>22:00</option>
			<option>23:00</option>
			<option value="23:59">24:00</option>
		</select>
	</td>
</tr>
<tr>
	<td rowspan="2"><h5>Повтор</h5></td>
	<td rowspan="2"><input type="checkbox" id="repeat" /></td>
	<td><b>каждые:</b></td>
	<td>
		<select id="repeat_num" style="width: 50px;" disabled="disabled" class="repeatform">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
			<option>6</option>
			<option>7</option>
			<option>8</option>
			<option>9</option>
			<option>10</option>
			<option>11</option>
			<option>12</option>
			<option>13</option>
			<option>14</option>
			<option>15</option>
			<option>16</option>
			<option>17</option>
			<option>18</option>
			<option>19</option>
			<option>20</option>
			<option>21</option>
			<option>22</option>
			<option>23</option>
			<option>24</option>
			<option>25</option>
			<option>26</option>
			<option>27</option>
			<option>28</option>
			<option>29</option>
			<option>30</option>
			<option>31</option>
		</select>
	</td>
	<td>
		<select id="repeat_period" style="width: 80px;" disabled="disabled" class="repeatform">
			<option value="d">день</option>
			<option value="m">месяц</option>
			<option value="y">год</option>
		</select>
	</td>
</tr>
<tr>
	<td><b>в течение:</b></td>
	<td>
		<select id="repeat_cont_num" style="width: 50px;" disabled="disabled" class="repeatform">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
			<option>6</option>
			<option>7</option>
			<option>8</option>
			<option>9</option>
			<option>10</option>
			<option>11</option>
			<option>12</option>
			<option>13</option>
			<option>14</option>
			<option>15</option>
			<option>16</option>
			<option>17</option>
			<option>18</option>
			<option>19</option>
			<option>20</option>
			<option>21</option>
			<option>22</option>
			<option>23</option>
			<option>24</option>
			<option>25</option>
			<option>26</option>
			<option>27</option>
			<option>28</option>
			<option>29</option>
			<option>30</option>
			<option>31</option>
		</select>
	</td>
	<td>
		<select id="repeat_cont_period" style="width: 80px;" disabled="disabled" class="repeatform">
			<option value="d">день</option>
			<option value="m">месяц</option>
			<option value="y">год</option>
		</select>
	</td>
</tr>
</table>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('.reservdate').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'dd.mm.yy',
		minDate: 0,
	    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
	    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    firstDay: 1,
	    defaultDate: "{{ date }}"
	});

	$('.reservdate').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'dd.mm.yy',
		minDate: 0,
	    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
	    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    firstDay: 1,
	    defaultDate: "{{ date }}"
	});

	$("#repeat").change(function() {
		if ($(this).attr("checked")) {
			$(".repeatform").removeAttr('disabled');
		} else {
			$(".repeatform").attr('disabled', 'disabled');
		}
	});
});
</script>