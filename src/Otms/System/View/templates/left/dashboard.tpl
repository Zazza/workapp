<ul class="nav nav-list">

<li>
<label class="form-inline">Дата: <input type="text" id="datedash" value="выбрать дату" style="cursor: pointer; width: 67px" /></label>
<input type="hidden" name="date" id="date" />
<p style="margin-top: 20px"><b>Инфо в календаре:</b></p>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="task" name="task" {% if notify.task %}checked="checked"{% endif %} />
	задачи
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="com" name="com" {% if notify.com %}checked="checked"{% endif %} />
	комментарии
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="mail" name="mail" {% if notify.mail %}checked="checked"{% endif %} />
	почта
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="obj" name="obj" {% if notify.obj %}checked="checked"{% endif %} />
	объекты
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="info" name="info" {% if notify.info %}checked="checked"{% endif %} />
	информация
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="service" name="service" {% if notify.service %}checked="checked"{% endif %} />
	сообщения
	</label>
</li>

<div class="form-inline" style="margin: 10px 0;">
Фильтр: <input type="text" id="filtr" name="filtr" value="{{ filtr }}" class="span12" />
</div>

<div class="btn-group">
<a class="btn btn-mini" style="margin-top: 20px" onclick="setDash()">
Сохранить
</a>

<a class="btn btn-mini" style="margin-top: 20px" onclick="resetDash()">
Сбросить
</a>
</div>

</li>
</ul>

<script type="text/javascript">
$(document).ready(function() {
	$("#centerContent").animate({scrollTop:0}, 'fast');
	
	$("#datedash").val('{{ formatDate }}');
	
	$('#datedash').datepicker({
		changeYear: true,
		changeMonth: true,
	    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
	    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
	    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
	    firstDay: 1,
	    defaultDate: "{{ date }}",
		onSelect: function(dateText, inst) {
			$("#datedash").val('');
			$("#date").val(dateText);
			setDash();
		}
	});

	$("#centerContent").scroll(function(){
		if  ($("#centerContent").scrollTop() - 81 == $("#touchID").height() - $("#centerContent").height()){
			$("#ajaxScrollEventsLoad").show();

			$("body").oneTime(1000, function(){
				var data = "action=scroll";
				$.ajax({
				        type: "POST",
				        url: "{{ registry.uri }}ajax/dashboard/",
				        data: data,
				        async: false,
				        success: function(res) {
					        if (res == 'end') {
					        	$("#ajaxScrollEventsLoad").hide();
					        	$("#ajaxScrollEventsLoad").remove();
						    } else {
						    	$("#ajaxScrollEventsLoad").hide();
								$("#events").append(res);
					        }
				        }
				});

				$("#ajaxScrollEventsLoad").hide();
			});
		}
	});	
});

function setDash() {
	if ($("input#task").attr("checked")) { var task = 1; } else { var task = 0; };
	if ($("input#com").attr("checked")) { var com = 1; } else { var com = 0; };
	if ($("input#obj").attr("checked")) { var obj = 1; } else { var obj = 0; };
	if ($("input#info").attr("checked")) { var info = 1; } else { var info = 0; };
	if ($("input#mail").attr("checked")) { var mail = 1; } else { var mail = 0; };
	if ($("input#service").attr("checked")) { var service = 1; } else { var service = 0; };

	var data = "action=setNotify&date=" + $("#date").val() + "&task=" + task + "&com=" + com + "&obj=" + obj + "&info=" + info + "&mail=" + mail + "&service=" + service + "&filtr=" + $("#filtr").val();
	$.ajax({
	        type: "POST",
	        url: "{{ registry.uri }}ajax/dashboard/",
	        data: data,
	        success: function(res) {
				document.location.href = "{{ registry.uri}}dashboard/";
	        }
	});
};

function resetDash() {
	var data = "action=reset";
	$.ajax({
	        type: "POST",
	        url: "{{ registry.uri }}ajax/dashboard/",
	        data: data,
	        async: false,
	        success: function(res) {
	        	document.location.href = "{{ registry.uri}}dashboard/";
	        }
	});
}
</script>