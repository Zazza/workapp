$(document).ready(function(){
	$("#utree > #Pstructure").treeview({
		persist: "location",
		collapsed: true
    });

	$('#Prall').click(function(){
		if ($("#Prall").attr("checked")) {
			$('#Pstructure input:checkbox:enabled').each(function(){this.disabled = !this.disabled});
			$('#Pstructure input:checkbox').removeAttr("checked");
		} else {
			$('#Pstructure input:checkbox:disabled').each(function(){this.disabled = !this.disabled});
			$('#Pstructure input:checkbox').removeAttr("checked");
		}
		
		$('#Prall').removeAttr("disabled");
	});

	$('.Pgruser').click(function(){
		$('input.Pg' + $(this).val() + ':checkbox').each(function(){this.disabled = !this.disabled});
		$('#Prall').removeAttr("checked");
	});

	$('.Pcusers').click(function(){
		$('#Prall').removeAttr("checked");
	});
	
	$("#rallbu").click(function(){
		if ($("input[name=rall]").val() == "off") {
			$(".ul").addClass("userSel");
			$("input[name=rall]").val("on");
		} else {
			$(".ul").removeClass("userSel");
			$("input[name=rall]").val("off");
		}
	});
	
	$("#addtaskbu").click(function(){
		var uids = "";
		$(".ulsel").find("input[name='ruser[]']").each(function(){
			uids = uids + "&ruser[]=" + $(this).val();
		});
		$("input[name='gruser']").each(function(){
			if (!isNaN($(this).val())) {
				uids = uids + "&gruser[]=" + $(this).val();
			};
		});
		if ($("input[name=rall]").val() == "on") {
			uids = "&rall=1";
		};
		
		document.location.href = url + "tt/add/?addUsersTask" + uids;
	});
	
	$("#addchatbu").click(function(){
		$('<div title="Новый чат"><b>Название создаваемого чата:</b><br /><input type="text" name="chatname" style="width: 220px"></div>').dialog({
			modal: true,
		    buttons: {
				"Отмена": function() { $(this).dialog("close"); },
				"Создать": function() {	
					var uids = "";
					$(".ulsel").find("input[name='ruser[]']").each(function(){
						uids = uids + "&ruser[]=" + $(this).val();
					});
					$("input[name='gruser']").each(function(){
						if (!isNaN($(this).val())) {
							uids = uids + "&gruser[]=" + $(this).val();
						};
					});
					if ($("input[name=rall]").val() == "on") {
						uids = "&rall=1";
					};
					
					document.location.href =  url + "chat/add/?add&chatname=" + $("input[name='chatname']").val() + uids;
				}
			},
			width: 270
		});
	});
});

function usersel(uid) {
	if ($("input[name=rall]").val() == "off") {
		if ($("#userid_" + uid + "").attr('class') == "ul") {
			$("#userid_" + uid + "").addClass("userSel");
			$("#userid_" + uid + "").addClass("ulsel");
		} else {
			$("#userid_" + uid + "").css("background-color", "");
			$("#userid_" + uid + "").removeClass("userSel");
			$("#userid_" + uid + "").removeClass("ulsel");
		}
	}
}

function delUserConfirm(uid) {
	$('<div title="Удаление пользователя">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delUser(uid); $(this).dialog("close"); }
		},
		width: 240
	});
}

function delUser(uid) {
    var data = "action=delUser&uid=" + uid;
	$.ajax({
		type: "POST",
		url: url + "ajax/users/",
		data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
}

function getUserInfo(uid) {
    var data = "action=getUI&uid=" + uid;
    $.ajax({
            type: "POST",
            url: url + "ajax/users/",
            data: data,
            success: function(res) {
            	$('<div title="Информация" style="text-align: left">' + res + '</div>').dialog({ width: 500 });
            }
    });
}

function sendMsg(uid) {
	$('<div title="Сообщение"><b>Текст сообщения:</b><br /><textarea id="msg" name="msg" style="width: 300px; height: 70px" /></textarea>').dialog({
		modal: true,
	    buttons: {
			"Отправить": function() {
				var data = "action=sendMsg&msg=" + encodeURIComponent($("#msg").val()) + "&uid=" + uid;
			    $.ajax({
			            type: "POST",
			            url: url + "ajax/users/",
			            data: data
			    });
			    
			    $(this).dialog("close");
			},
			"Отмена": function() { $(this).dialog("close"); }
		},
		width: 340
	});
}
