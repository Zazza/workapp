<p><a href="{{ registry.uri }}route/add/" class="btn">New workflow</a></p>

{% for part in list %}
<p id="r_{{ part.id }}"><a class="btn btn-mini" onclick="delRouteConfirm({{ part.id }})"><i class="icon-remove-sign"></i> delete</a> <a href="{{ registry.uri }}route/draft/edit/?id={{ part.id }}">{{ part.name }}</a></p>
{% endfor %}

<div id="delRoute" title="Delete draft copy" style="display: none">You really want to delete workflow draft copy?</div>

<script type="text/javascript">
function delRouteConfirm(id) {
	$("#delRoute").dialog({
	    buttons: {
	    	"Yes": function() {
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
			"No": function() { $(this).dialog("close"); }
		},
		width: 300,
		height: 200
	});
}
</script>