{% if event.type == "service" %}
<div class="dashEvent alert alert-error" id="ev{{ event.id }}">
{% elseif event.type == "mail" %}
<div class="dashEvent alert alert-success" id="ev{{ event.id }}">
{% elseif event.type == "task" or event.type == "com" %}
<div class="dashEvent alert alert-info" id="ev{{ event.id }}">
{% else %}
<div class="dashEvent alert" id="ev{{ event.id }}">
{% endif %}

<div style="overflow: hidden; text-align: left">
	<a onclick="closeDashEvent({{ event.id }})" class="close" data-dismiss="alert" style="margin-right: 10px">×</a>
	
	<div class="dashEventSub" style="text-align: left">
		{{ event.timestamp }}
	</div>
	
	<div style="font-weight: bold" class="evtext">{{ event.event }}</div>
	
	<span class="evadv"><a style="cursor: pointer" onclick="$('#einfo_{{ event.id }}').dialog({ width: 500, height: 300 })">подробнее</a></span>
</div>

<div style="display: none; text-align: left" id="einfo_{{ event.id }}" title="Подробный вывод">
{% for part in event.param %}
{% if part.key %} 
<p style="margin: 0"><b>{{ part.key }}:</b> {{ part.val }}</p>
{% endif %}
{% endfor %}
</div>


</div>

<script type="text/javascript">
function closeDashEvent(id) {
	var data = "action=closeEvent&eid=" + id;
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/dashboard/",
		data: data,
		success: function(res) {
			$("#ev" + id).fadeOut("fast");
			
			var num = $("#notifspan").html() - 1;
			
			if (num == 0) {
				$("#notifspan").removeClass("label-important");
				$("#notifspan").removeClass("label-success");
				
				$("#dashajaxlogs").html("<p id='emptyEvents'>Новых событий нет</p>");
			} else if (!res) {
				$("#notifspan").removeClass("label-important");
				$("#notifspan").addClass("label-success");
			}
			
			$("#notifspan").html(num);
			$("title").text($("#settitle").val());
		}
	});	
}
</script>