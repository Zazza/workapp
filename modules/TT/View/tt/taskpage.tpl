<div id="fulltask" style="display: none; float: right;"></div>

<table id="tabletask" class="table table-bordered">
{% for part in tasks %}
{{ part }}
<hr style="margin: 0;" />
{% endfor %}
</table>

<script type="text/javascript">
$(document).ready(function(){
	$(".dtask").mouseover(function() {
		if ($("#fulltask").css("display") == "block") {
			if ($(this).attr('selected') != "selected") {
				$(this).css('opacity', "1");
				$(this).css('background-color', "#FFF");
			}
		}
	}).mouseout(function() {
		if ($("#fulltask").css("display") == "block") {
			if ($(this).attr('selected') != "selected") {
				$(this).css('opacity', "0.3");
				$(this).css('background-color', "#FFF");
			}
		}
	});
});

function getTask(id) {
	$(".dtask").removeAttr("selected");
	$(".dtask .d_arr").hide();
	$("#t_" + id).attr("selected", "selected");
	$("#t_" + id + " .d_arr").show();
	$("#fulltask").show();
	$("#fulltask").html('<p style="text-align: center"><img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></p>');
	$(".dtask").css('opacity', "0.3");
	$("#t_" + id).css('opacity', "1");
	
	var data = "action=getTask&id=" + id;
    $.ajax({
        type: "POST",
        url: url + "ajax/tt/",
        data: data,
        success: function(res) {
            $("#fulltask").html(res);
        }
    })
}
</script>