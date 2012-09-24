<p><a href="{{ registry.uri }}route/add/" class="btn">Новый бизнес-процесс</a></p>

{% for part in list %}
<p id="r_{{ part.id }}"><a class="btn btn-mini" onclick="delRouteConfirm({{ part.id }})"><i class="icon-remove-sign"></i> удалить</a> <a href="{{ registry.uri }}route/draft/edit/?id={{ part.id }}">{{ part.name }}</a></p>
{% endfor %}

<div id="delRoute" title="Удаление черновика" style="display: none">Вы действительно хотите удалить черновик бизнес-маршрута?</div>

<script type="text/javascript">
function delRouteConfirm(id) {
	$("#delRoute").dialog({
	    buttons: {
	    	"Да": function() {
				$.ajax({
					type: "POST",
					url: '{{ registry.uri }}ajax/route/',
					data: "action=delRoute&rid=" + id,
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
}
</script>