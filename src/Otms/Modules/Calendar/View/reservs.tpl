<div style="margin-bottom: 10px;"><input type="button" class="btn" onclick="delSelectedConfirm()" value="Удалить выделенные" /></div>

<table class="table table-striped table-bordered table-condensed">
	<th style="width: 40px; text-align: center;"><input type="checkbox" id="selall" style="margin-left: 2px" /></th>
	<th style="text-align: center; width: 150px;">Дата брони</th>
	<th style="text-align: center;">Объект</th>

	{% for part in list %}
	<tr class="reservclass" id="r_{{ part.id }}">
		<td style="text-align: center;">
			<input type="checkbox" id="rc_{{ part.id }}" class="selown" />
			<hr />
			<div class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="text-shadow: none;">
					<i class="icon-wrench"></i>
					<b class="caret"></b>
				</a>
			
				<ul class="dropdown-menu">
					<li><a data-id="{{ part.id }}" class="rc_edit"><img src="{{ registry.uri }}img/edititem.gif" />Правка</a></li>
					<li><a data-id="{{ part.id }}" class="rc_del"><img src="{{ registry.uri }}img/delete.png" />Удалить</a></li>
				</ul>
			</div>
		</td>
		
		<td style="text-align: center;">
			{{ part.fstart }}<br />-<br /> {{ part.fend }}
		</td>
		
		<td>
			<a style="cursor: pointer" onclick="getInfo({{ part.oid }})">
			{% for val in part.object %}
			{% if val.main %}
			<span>{{ val.val }} </span>
			{% endif %}
			{% endfor %}
			</a>
		</td>
	</tr>
	{% endfor %}
</table>

{% include 'newreserv.tpl' with {'edit': 'true'} %}

<script type="text/javascript">
$("#selall").change( function() {
	if ($('#selall').attr('checked')){
    	$('.selown').attr('checked', true);
	} else {
    	$('.selown').attr('checked', false);
	}
});
 
function delSelectedConfirm() {
	$('<div title="Предупреждение">Действительно удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delSelected(); $(this).dialog("close"); }
		},
		width: 540
	});
}

function delSelected() {
	var objs = $(".selown");

    var formData = new Array(); var i = 0;
    $(".selown:checked").each(function(n){
		id = this.id;
		
		formData[i] = ['"' + id + '"', "1"].join(":");
		
		i++;
    });

    var json = "{" + formData.join(",") + "}";

    $.ajax({
        type: "POST",
        async: false,
        url: '{{ registry.uri }}ajax/calendar/',
        data: "action=delSelected&json=" + json,
        success: function(res) {
			document.location.href = document.location.href;
		}
    });
}

function delReservConfirm(id) {
	$('<div id="rdelConfirm" title="Предупреждение">Действительно удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() {
				$.ajax({
					type: "POST",
					async: false,
					url: '{{ registry.uri }}ajax/calendar/',
					data: "action=delReserv&id=" + id,
					success: function(res) {
						$("#rdelConfirm").dialog("close");
						window.location.href = window.location.href;
					}
				});
			}
		},
		width: 540
	});
	
};

$(".rc_edit").click(function() {
	var id = $(this).attr("data-id");

	$.ajax({
		type: "POST",
		async: false,
		url: '{{ registry.uri }}ajax/calendar/',
		data: "action=getReserv&id=" + id,
		dataType: 'json',
		success: function(res) {
			$.each(res, function(key, val) {
                if (key == "oid") { $("#selObjHid").val(val); };
                if (key == "enddate") { 
                	if (val == 1) {
						$("#enddate").attr('checked', "checked");
						$(".endform").removeAttr('disabled');
						$("#repeat").removeAttr('disabled');
					};
				};
                if (key == "object") { $("#rObject").html(val); };
                if (key == "reservstart") { $("#reservstart").val(val); };
                if (key == "reservend") { $("#reservend").val(val); };
                if (key == "reservstarttime") { $("#reservstarttime").val(val); };
                if (key == "reservendtime") { $("#reservendtime").val(val); };
			});

			editReserv(id);
		}
	});
});

$(".rc_del").click(function() {
	var id = $(this).attr("data-id");
  	
  	delReservConfirm(id);
});

function editReserv(id) {
	$("#newreserv").dialog({
		buttons: {
			"Отмена": function() {
				$("#newreserv").dialog("close");
			},
			"Готово": function() {
				if ($("#enddate").attr('checked')) {
					var ch_end = 1;
				} else {
					var ch_end = 0;
				}
				if ($("#repeat").attr('checked')) {
					var ch_r = 1;
				} else {
					var ch_r = 0;
				}
				var data = "action=editReservs&oid=" + $("#selObjHid").val() + "&id=" + id + "&reservstart=" + $("#reservstart").val() + "&reservstarttime=" + $("#reservstarttime").val() + "&enddate=" + ch_end + "&reservend=" + $("#reservend").val() + "&reservendtime=" + $("#reservendtime").val() + "&repeat=" + ch_r + "&repeat_num=" + $("#repeat_num").val() + "&repeat_period=" + $("#repeat_period").val() + "&repeat_cont_num=" + $("#repeat_cont_num").val() + "&repeat_cont_period=" + $("#repeat_cont_period").val();
			    $.ajax({
			        type: "POST",
			        url: "{{ registry.uri }}ajax/calendar/",
			        data: data,
			        success: function(res) {
			        	if (res == "collision") {
			        		$("<div title='Ошибка'>В заданный период времени объект уже забронирован</div>").dialog({width: 220, height: 120});
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
</script>