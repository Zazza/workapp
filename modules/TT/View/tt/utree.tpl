<p><b>Выберите пользователей</b></p>

<div id="taskUsers">

{{ list }}

<div id="setRight" style="color: green; margin-top: 10px"></div>

</div>

<script type="text/javascript">
function setTaskUsers() {
	$("#taskUsers input:checkbox").each(function(n){
		if(this.checked) {
			var str = this.id;

	        if (str.indexOf("user") == 0) {
	        	var id = str.substr(4);
	           	$("#addedusers").append('<p><span id="udesc[' + id + ']" style="font-size: 11px; margin-right: 10px"> '+  $("#hu" + id).val() + '</span>');
	            $("#addedusers").append('<input id="uhid[' + id + ']" type="hidden" name="ruser[]" value="' + id + '" /></p>');
	        } else if (str.indexOf("hg") == 0) {
	        	var id = str.substr(2);
	            $("#addedusers").append('<p style="font-size: 11px; margin-right: 10px">' + $("#hg" + id).attr("title") + '<input type="hidden" name="gruser[]" value="' + id + '" /></p>');
	        } else if (str.indexOf("rall") == 0) {
	        	$("#addedusers").append('<p style="font-size: 11px; margin-right: 10px">Все<input type="hidden" name="rall" value="1" /></p>');
	        }
		}
	});

	$('#usersDialog').dialog("close");
}
</script>