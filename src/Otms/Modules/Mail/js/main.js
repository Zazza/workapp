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
    					
    					setbStatus("Checking: " + res[window.i]);
    					
    					checkMbox(res[window.i]);
    				}
    			} else {
    				$("body").stopTime("timer");
    				
    				$("body").oneTime(1000, function() {
    					setbStatus("End...");

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
    		setbStatus("<span style='color: red;'>Error</span>");
    		setbStatus("<span style='color: red;'>Not possible to be connected: " + mbox + "</span>");
    	} else if (msg == "true") {
    		setbStatus("<span style='color: green;'>OK</span>");
    	} else {
    		setbStatus("<span style='color: red;'>Error</span>");
    		setbStatus("<span style='color: red;'>" + msg + "</span>");
    	}
    	
    	window.loadCheck = false;
		window.i++;
    });
    
    request.fail(function(jqXHR, textStatus) {
    	setbStatus("<span style='color: red;'>Error</span>");
    	setbStatus("<span style='color: red;'>Not possible to be connected: " + mbox + "</span>");

    	window.loadCheck = false;
		window.i++;
	});
};

function delSortConfirm(sid) {
	$('<div title="Deleting rule of sorting">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { delSort(sid); $(this).dialog("close"); }
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
	$('<div title="Deleting folder">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { delMailDir(fid); $(this).dialog("close"); }
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
	$('<div title="Sending email">You are sure that you want to send email?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { sendMailComment(email, cid); $(this).dialog("close"); }
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