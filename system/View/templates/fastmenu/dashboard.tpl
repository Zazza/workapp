<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="text-shadow: none;">
		<i class="icon-calendar"></i>
		<span id="notifspan" class="label"></span>
		<b class="caret" style="border-bottom-color: #0088CC; border-top-color: #0088CC"></b>
	</a>
	
	<ul class="dropdown-menu unclicked" id="dashajax">
		<li>
			<span class="btn btn-mini">
				<a href="{{ registry.uri }}dashboard/settings/" style="color: black">
				<i class="icon-cog"></i>
				Настройки уведомлений
				</a>
			</span>
	
			<span class="btn btn-mini">
				<a onclick="clearEvents()" style="cursor: pointer; color: black">
				<i class="icon-trash"></i>
				Очистить
				</a>
			</span>
		</li>
		
		<li style="overflow: hidden" id="dashbut"></li>
		
		<li><div id="dashajaxlogs"></div></li>
	</ul>
</li>

<input type="hidden" id="settitle" />

<script type="text/javascript">
$("#notifspan").html("&nbsp;");

var height = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
$("#dashajaxlogs").css("max-height", (height - 180));

$(document).ready(function(){
	$("#settitle").val($("title").text());
			
	var service = 0;
	var notify = 0;
	var numChats = 0;
	var rooms = null;
	var numNotify = 0;
	var events = "";
	
	$.each({{ dash }}, function(key, val) {
		switch(key) {
			case "service":
				service = 1
				numNotify = val
				break
			case "notify":
				if (val > 0) {
					notify = 1
					numNotify = val
				}
				break
			case "numChats":
				numChats = val
				break
			case "rooms":
				rendRooms(val);
				break
			case "events":
				events = "<div>" + val + "</div>"
				break
		}
	});

	if (service) {
		$("#notifspan").addClass("label-important");
	} else if (notify) {
		$("#notifspan").addClass("label-success");
	};
	
	$("#dashajaxlogs").html(events);
	if (numChats > 0) {
		$("#numChats").addClass("label-success");
	} else {
		$("#numChats").removeClass("label-success");
	}
	if (numChats == 0) {
		$("#chat_rooms").html("<li style='text-align: center; padding: 3px 15px;'>чатов нет</li>");
	}
	$("#numChats").text(numChats);
	$("#notifspan").text(numNotify);
	
	if (notify) {
		$("title").text(numNotify + " новых уведомлений!");
	};

	$("#dashajax").everyTime(20000, function() {
		var data = "action=newevents";
		$.ajax({
			type: "POST",
			url: "{{ registry.uri }}ajax/dashboard/",
			data: data,
			dataType: 'json',
			success: function(res) {
				//$("#centerContent").prepend(res + "<br />");	
				var service = 0;
				var notify = 0;
				var numChats = 0;
				var rooms = null;
				var events = null;
				var prev = parseInt($("#notifspan").html());
				var numNotify = prev;

				$.each(res, function(key, val) {
					switch(key) {
						case "service":
							service = 1
							numNotify = prev + parseInt(val);
							break
						case "notify":
							if (val > 0) {
								notify = 1
								numNotify = prev + parseInt(val);
								$("title").text(numNotify + " новых уведомлений!");
							}
							break
						case "numChats":
							numChats = val
							break
						case "rooms":
							$("#chat_rooms").html("<li style='text-align: center; padding: 3px 15px;'>чатов нет</li>");
							
							rendRooms(val)
							break
						case "events":
							if (val) {
								$("#dashajaxlogs #emptyEvents").hide();
								$("#dashajaxlogs").prepend(val);
							};
							break
					}
				});

				if (service) {
					$("#notifspan").removeClass("label-success");
					$("#notifspan").addClass("label-important");
				} else if (notify) {
					if (prev == 0) {
						$("#notifspan").addClass("label-success");
					}
				};

				if (numChats > 0) {
					$("#numChats").addClass("label-success");
				} else {
					$("#numChats").removeClass("label-success");
				}
				$("#numChats").text(numChats);
				$("#notifspan").text(numNotify);
			}
		});
	});
});

function rendRooms(val) {
	$.each(val, function(id, room) {
		$("#chat_rooms").append(room);
	});
}

function clearEvents() {
	setbStatus('Очистка лога событий...');
	$("#ajaxLoader").modal('show');
	
	var data = "action=clearEvents";
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/dashboard/",
		data: data,
		success: function(res) {
			$("#dashajaxlogs").html('<p id="emptyEvents">Новых событий нет</p>');
			$("#notifspan").text('0');
			$("#notifspan").removeClass("label-success");
			$("#notifspan").removeClass("label-important");
			$("title").text('{{ registry.title }}');

			$('#bStatus').html('');
			$("#ajaxLoader").modal('hide');
		}
	});
}
</script>
