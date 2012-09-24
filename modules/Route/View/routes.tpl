<p><a href="{{ registry.uri }}route/add/" class="btn">Новый бизнес-процесс</a></p>

{% for part in list %}
<p id="r_{{ part.id }}">
	<a class="btn btn-mini" onclick="delRealRouteConfirm({{ part.id }})"><i class="icon-remove-sign"></i> удалить</a>
	<a class="btn btn-mini" onclick="runProcessConfirm({{ part.id }})"><i class="icon-play"></i> запустить</a>
	<a href="{{ registry.uri }}route/edit/?id={{ part.id }}">{{ part.name }}</a>
</p>
{% endfor %}

<div id="delRoute" title="Удаление" style="display: none">Вы действительно хотите удалить бизнес-маршрут?</div>
<div id="runProcess" title="Запуск бизнес-процесса" style="display: none">Начать выполнение бизнес-процесса?</div>
<div id="doneProcess" title="Уведомление" style="display: none">Готово</div>

<script type="text/javascript">
function delRealRouteConfirm(id) {
	$("#delRoute").dialog({
	    buttons: {
	    	"Да": function() {
				$.ajax({
					type: "POST",
					url: '{{ registry.uri }}ajax/route/',
					data: "action=delRealRoute&rid=" + id,
					success: function(res) {
						$("#r_" + id).hide();
						$("#r_" + id).remove();
					}
				});
				
				$(this).dialog("close");
			},
			"Нет": function() { $(this).dialog("close"); }
		},
		width: 300,
		height: 200
	});
};

function runProcessConfirm(rid) {
	$("#runProcess").dialog({
		buttons: {
	    	"Да": function() {
	    		$.ajax({
					type: "POST",
					url: '{{ registry.uri }}ajax/route/',
					data: "action=runProcess&rid=" + rid,
					success: function(res) {
						$("#doneProcess").dialog({
							buttons: {
						    	"Закрыть": function() {
						    		$(this).dialog("close");
						    	}
							},
						    width: 220,
							height: 130
						});
					}
				});
	    		
				$(this).dialog("close");
			},
			"Нет": function() { $(this).dialog("close"); }
		},
		width: 300,
		height: 200
	});
}
</script>