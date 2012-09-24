<div id="chat" style="overflow: hidden; border: 3px solid #EEE">

<div id="parts" style="float: right; height: 500px; width: 200px; padding: 10px; border-left: 1px solid #AAA"></div>
	
<div id="chatbody" style="overflow-y: scroll; height: 500px; padding: 10px"></div>

</div>

<input type="hidden" id="prevcmd" />

<div class="input-append" style="padding: 10px 0">
	<input type="text" id="message" class="span6" />
	<button type="button" id="addMessage" class="btn btn-primary" style="margin-bottom: 9px" />Написать</button>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#body").html('<img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" />');
	$("#parts").html('<img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" />');
	
	$("#message").focus();
	
	var data = "action=getFirst&cid=" + {{ cid }};
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/chat/",
		data: data,
		dataType: 'json',
		success: function(res) {
			//$("#chatbody").append(res);
			$.each(res, function(key, val) {
				if (key == "body") {
					$("#chatbody").append(val);
					
					var objDiv = document.getElementById("chatbody");
					objDiv.scrollTop = objDiv.scrollHeight;
				};
				if (key == "parts") {
					$("#parts").html(val);
				};
			});
		}
	});
	
	$("#chat").everyTime(3000, function() {
		var data = "action=getInstance&cid=" + {{ cid }};
		$.ajax({
			type: "POST",
			url: "{{ registry.uri }}ajax/chat/",
			data: data,
			dataType: 'json',
			success: function(res) {
				//$("#chatbody").append(res);
				$.each(res, function(key, val) {
					if (key == "body") {
						$("#chatbody").append(val);
						
						var objDiv = document.getElementById("chatbody");
						objDiv.scrollTop = objDiv.scrollHeight;
					};
					if (key == "addparts") { $("#parts").append(val); };
					if (key == "delparts") { $("#parts > #chatuser" + val).hide(); };
				});
			}
		});
	});
	
	$("#addMessage").click(function(){
		addMessage();
	});
	
	$(document).keyup(function(e) {
		switch(e.keyCode) {
			case 13: addMessage(); break;
			case 38: $("#message").val($("#prevcmd").val()); break;
			case 40: $("#message").val(""); break;
		};
	});
});

function addMessage() {
	var cmd = $("#message").val();
	$("#prevcmd").val(cmd);
	
	var data = "action=addMessage&cid=" + {{ cid }} + "&message=" + cmd;
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/chat/",
		data: data,
		success: function(res) {
			$("#chatbody").append(res);
			$("#message").val("");
			
			var objDiv = document.getElementById("chatbody");
			objDiv.scrollTop = objDiv.scrollHeight;
		}
	});
}
</script>