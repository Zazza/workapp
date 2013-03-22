<p><a href="{{ registry.uri }}route/add/" class="btn">New workflow</a></p>

{% for part in list %}
<p id="r_{{ part.id }}">
	<a class="btn btn-mini" onclick="delRealRouteConfirm({{ part.id }})"><i class="icon-remove-sign"></i> delete</a>
	<a class="btn btn-mini" onclick="runProcessConfirm({{ part.id }})"><i class="icon-play"></i> run process</a>
	<a href="{{ registry.uri }}route/edit/?id={{ part.id }}">{{ part.name }}</a>
</p>
{% endfor %}

<div id="delRoute" title="Deleting" style="display: none">You really want to delete workflow?</div>
<div id="runProcess" title="Run workflow" style="display: none">Begin workflow execution?</div>
<div id="doneProcess" title="Notice" style="display: none">Done</div>

<script type="text/javascript">
function delRealRouteConfirm(id) {
	$("#delRoute").dialog({
	    buttons: {
	    	"Yes": function() {
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
			"No": function() { $(this).dialog("close"); }
		},
		width: 300,
		height: 200
	});
};

function runProcessConfirm(rid) {
	$("#runProcess").dialog({
		buttons: {
	    	"Yes": function() {
	    		$.ajax({
					type: "POST",
					url: '{{ registry.uri }}ajax/route/',
					data: "action=runProcess&rid=" + rid,
					success: function(res) {
						$("#doneProcess").dialog({
							buttons: {
						    	"Close": function() {
						    		$(this).dialog("close");
						    	}
							},
						    width: 220,
							height: 150
						});
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