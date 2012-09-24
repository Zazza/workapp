$(document).ready(function(){

	$(document).keyup(function(e) {
		switch(e.keyCode) {
			case 192: shConsole(); break;
		};
	});
	
	
	$("#shCmd").click(function() {
		shConsole();
	});
	
	$("#mcmd").keyup(function(e) {
		switch(e.keyCode) {
			case 13: addCmd(); break;
			case 38: $("#mcmd").val($("#prevcmd").val()); break;
			case 40: $("#mcmd").val(""); break;
		};
	});
});

function shConsole() {
	if ($(".semiopacity").css("display") == "none") {
		$(".semiopacity").slideToggle("fast");
		$("#mcmd").focus();
		
		var data = "action=getHistory";
		$.ajax({
			type: "POST",
			url: url + "ajax/cmd/",
			data: data,
			async: false,
			success: function(res) {
				$("#cmdText").html(res);
			}
		});

		$("#cmdText").scrollTop($("#cmdText").prop('scrollHeight'));
	} else {
		var str = $("#mcmd").val();
		$("#mcmd").val(str.substr(0, str.length-1));
		$(".semiopacity").slideToggle("fast");
	}
};

function addCmd() {
	var cmd = $("#mcmd").val();
	$("#prevcmd").val(cmd);
	
	if (cmd == "exit") {
		$("#mcmd").val("");
		$(".semiopacity").slideToggle("fast");
	} else {
		var data = "action=addCmd&&message=" + cmd;
		$.ajax({
			type: "POST",
			url: url + "ajax/cmd/",
			data: data,
			success: function(res) {
				if (cmd == "clear") {
					$("#cmdText").html("");
					$("#mcmd").val("");
				} else {
					setCmd(res);
					$("#mcmd").val("");
				}
			}
		});
	};
};

function setCmd(res) {
	$("#cmdText").append("<p class='resCmd'>" + res + "</p>");
	
	$("#cmdText").scrollTop($("#cmdText").prop('scrollHeight'));
};
