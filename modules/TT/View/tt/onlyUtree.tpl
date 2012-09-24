<p><b>Выберите пользователей</b></p>

<div id="taskOnlyUsers">

{{ list }}

<div id="setRight" style="color: green; margin-top: 10px"></div>

</div>

<script type="text/javascript">
function setTaskUsers() {
	$("#taskOnlyUsers input:radio").each(function(n){
		if(this.checked) {
			$("#delegateuser").html("");
			
			var str = this.id;

	        if (str.indexOf("user") == 0) {
	        	var id = str.substr(4);
	           	$("#delegateuser").append('<p><span id="udesc[' + id + ']" style="font-size: 11px; margin-right: 10px"> '+  $("#hu" + id).val() + '</span>');
	            $("#delegateuser").append('<input id="uhid[' + id + ']" type="hidden" name="delegate" value="' + id + '" /></p>');
	        }
		}
	});

	$('#usersDelegateDialog').dialog("close");
}
</script>