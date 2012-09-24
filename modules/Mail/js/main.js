var loadCheck = false;
var i = 0;

function checkMail() {
    var data = "action=getMailboxes";
    $.ajax({
    	type: "POST",
    	url: url + "ajax/mail/",
    	data: data,
    	dataType: 'json',
    	success: function(res) {
    		$("#ajaxLoader").modal('show');
    		
    		$("body").everyTime(500, "timer", function() {
    			if (window.i < (res.length)) {

    				if (window.loadCheck == false) {
    					window.loadCheck = true;
    					
    					setbStatus("Проверяется: " + res[window.i]);
    					
    					checkMbox(res[window.i]);
    				}
    			} else {
    				$("body").stopTime("timer");
    				
    				$("body").oneTime(1000, function() {
    					setbStatus("Завершение...");

    					document.location.href = document.location.href;
    				});
    			}
    		});
        }
    });

};

function checkMbox(mbox) {
    var data = "action=checkMboxes&mbox=" + mbox;
    var request = $.ajax({
    	type: "POST",
    	url: url + "ajax/mail/",
    	data: data
    });
    
    request.done(function(msg) {
    	if (msg == "false") {
    		setbStatus("<span style='color: red;'>Ошибка</span>");
    		setbStatus("<span style='color: red;'>Не удаётся подключиться: " + mbox + "</span>");
    	} else if (msg == "true") {
    		setbStatus("<span style='color: green;'>OK</span>");
    	} else {
    		setbStatus("<span style='color: red;'>Ошибка</span>");
    		setbStatus("<span style='color: red;'>" + msg + "</span>");
    	}
    	
    	window.loadCheck = false;
		window.i++;
    });
    
    request.fail(function(jqXHR, textStatus) {
    	setbStatus("<span style='color: red;'>Ошибка</span>");
    	setbStatus("<span style='color: red;'>Не удаётся подключиться: " + mbox + "</span>");

    	window.loadCheck = false;
		window.i++;
	});
};

function delSortConfirm(sid) {
	$('<div title="Удаление правила сортировки">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delSort(sid); $(this).dialog("close"); }
		},
		width: 280
	});
}

function delSort(sid) {
    var data = "action=delSort&sid=" + sid;
    $.ajax({
            type: "POST",
            url: url + "ajax/mail/",
            data: data,
            success: function(res) {
    			document.location.href = document.location.href;
            }
    });
}

function delMailDirConfirm(fid) {
	$('<div title="Удаление папки">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delMailDir(fid); $(this).dialog("close"); }
		},
		width: 280
	});
}

function delMailDir(fid) {
    var data = "action=delMailDir&fid=" + fid;
    $.ajax({
            type: "POST",
            url: url + "ajax/mail/",
            data: data,
            success: function(res) {
    			document.location.href = url + 'mail/';
            }
    });
}

function sendMailCommentConfirm(email, cid) {
	$('<div title="Отправка письма">Вы уверены, что хотите отправить письмо?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { sendMailComment(email, cid); $(this).dialog("close"); }
		},
		width: 280
	});
}

function sendMailComment(email, cid) {
    var data = "action=sendMailComment&email=" + email + "&cid=" + cid;
    $.ajax({
            type: "POST",
            url: url + "ajax/mail/",
            data: data,
            success: function(res) {
    			$("#smail" + cid).attr("src", url + "img/mail--exclamation.png");
    			$("#shref" + cid).attr("onclick", "");
    			$("#shref" + cid).css("cursor", "default");
    			$("#d" + cid).css("background-color", "#DFD");
            }
    });
}

function flushAttaches() {
	$("#attach_files").html('');
}

function sign(val) {
	var text = null;
	var sign = null;

	var data = "action=getSign&bid=" + val;
    $.ajax({
    	type: "POST",
    	url: url + "ajax/mail/",
    	data: data,
    	success: function(res) {
    		text = $("#wysihtml5").val();
    	//	var pos = text.indexOf('<div id="mailsign">');
    	//	text = text.substr(0, pos + 6);
    		text = text + '<br /><div id="mailsign">' + res + '</div>';

    	    $("#wysihtml5").val(text);
    	    htmlarea();
        }
    });
};