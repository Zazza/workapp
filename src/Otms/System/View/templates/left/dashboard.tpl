<ul class="nav nav-list">

<li>
<label class="form-inline">Date: <input type="text" id="datedash" value="select date" style="cursor: pointer; width: 67px" /></label>
<input type="hidden" name="date" id="date" />
<p style="margin-top: 20px"><b>Info in calendar:</b></p>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="task" name="task" {% if notify.task %}checked="checked"{% endif %} />
	tasks
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="com" name="com" {% if notify.com %}checked="checked"{% endif %} />
	comments
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="mail" name="mail" {% if notify.mail %}checked="checked"{% endif %} />
	mail
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="obj" name="obj" {% if notify.obj %}checked="checked"{% endif %} />
	objects
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="info" name="info" {% if notify.info %}checked="checked"{% endif %} />
	information
	</label>
</li>

<li>
	<label class="checkbox inline">
	<input type="checkbox" id="service" name="service" {% if notify.service %}checked="checked"{% endif %} />
	message
	</label>
</li>

<div class="form-inline" style="margin: 10px 0;">
Filter: <input type="text" id="filtr" name="filtr" value="{{ filtr }}" class="span12" />
</div>

<div class="btn-group">
<a class="btn btn-mini" style="margin-top: 20px" onclick="setDash()">
Save
</a>

<a class="btn btn-mini" style="margin-top: 20px" onclick="resetDash()">
Remove
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
	    dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	    dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
	    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	    monthNamesShort: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
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