<div class="well" style="overflow: hidden;">

<div style='padding-left: 20px; float: left' class='title'>
{{ date }}
</div>

<div class="blockbd" style="padding-left: 50px; font-size: 11px; float: right">
<p><label class="radio"><input type="radio" name="caltask" value="0" class="caltask" {% if caltype == 0 %}checked="checked"{% endif %} /> задачи, где я <b>ответственный</b></label></p>
<p><label class="radio"><input type="radio" name="caltask" value="1" class="caltask" {% if caltype == 1 %}checked="checked"{% endif %} /> задачи, где я <b>автор</b></label></p>
</div>

</div>

<script type="text/javascript">
$(".caltask").click(function() {
	var data = "action=setCalTask&caltask=" + $(this).val();
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/calendar/",
		data: data,
		success: function(res) {
			document.location.href = document.location.href;
		}
	});
});
</script>