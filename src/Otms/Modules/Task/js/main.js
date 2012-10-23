$(document).ready(function(){
    $("#dFind").keypress(function(e) {
    	firstFind();
    });
});

var string = "";

function firstFind() {
        string = $("#dFind").val();

        $("#resFind").html('<img src="' + url + 'img/ajax-loader.gif" alt="ajax-loader.gif" border="0" />');

        if(find.timeout) {
            clearTimeout(find.timeout);
        }

        find.timeout = setTimeout(find, 1000);
}

function find() {
    var data = "action=findObj&find=" + $("#dFind").val() + "&page=" + window.location.hash;
    $.ajax({
        type: "POST",
        url: url + "ajax/task/",
        data: data,
        success: function(res) {
            $("#resFind").html(res);
        }
    })
}

function delGroupConfirm(gid) {
	$('<div title="Удаление группы">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delGroup(gid); $(this).dialog("close"); }
		},
		width: 240
	});
}

function delGroup(gid) {
    var data = "action=delGroup&gid=" + gid;
	$.ajax({
		type: "POST",
		url: url + "ajax/users/",
		data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
}

function delGroupTtConfirm(gid) {
	$('<div title="Удаление группы">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delGroupTt(gid); $(this).dialog("close"); }
		},
		width: 240
	});
}

function delGroupTt(gid) {
    var data = "action=delGroup&gid=" + gid;
	$.ajax({
		type: "POST",
		url: url + "ajax/task/",
		data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
}

function showTaskWindow() {
    fminit(url + 'fm/');

    $('#advinfo #tag').hide();

    $('#advinfo').dialog({
		modal: true,
	    buttons: {
			"Добавить": function() {
			     $("#tasktext").show();
                 $("#tasktext").html($("#wysihtml5").val());
			     $("#task").text($("#wysihtml5").val());
                 $(this).dialog("close");
            }
		},
		width: 800,
        height: 450
	});
    
    $("#wysihtml5").val($("#tasktext").html());
    //htmlarea();
    //$('#wysihtml5').wysihtml5();
}

function selObject() {
	$('#findObject').dialog({ width: 400, height: 500 });
}

function closeTask(tid) {
    $('<div id="ttgroup" style="display: none; text-align: center" title="Закрыть задачу"><p>Уверены, что хотите закрыть задачу?</p></div>').dialog({
		modal: true,
	    buttons: {
			"Да": function() {
			     moveTask(tid);
                 $(this).dialog("close");
            },
            "Нет": function() { $(this).dialog("close"); }
		},
		width: 220,
        height: 170
	});
}

function moveTask(tid) {
    var data = "action=closeTask&tid=" + tid;
	$.ajax({
		type: "POST",
		url: url + "ajax/task/",
		data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
}

function spam(tid) {
    var data = "action=spam&tid=" + tid;
	$.ajax({
		type: "POST",
		url: url + "ajax/users/",
		data: data,
		success: function(res) {
            document.location.href = url + "task/show/" + tid + "/";
		}
	});
}

function delDraftConfirm(did) {
    $('<div title="Удаление черновика">Вы действительно хотите удалить черновик?</div>').dialog({
                modal: true,
            buttons: {
                        "Да": function() {
                             delDraft(did);
                 $(this).dialog("close");
            },
            "Нет": function() { $(this).dialog("close"); }
                },
                width: 250,
        height: 180
        });
}

function delDraft(did) {
    var data = "action=delDraft&did=" + did;
    $.ajax({
            type: "POST",
            url: url + "ajax/task/",
            data: data,
            success: function(res) {
                        document.location.href = document.location.href;
            }
    });
}

function addruser() {
	var text = null;
	var data = "action=getListUsers";	
	$.ajax({
		type: "POST",
		url: url + "ajax/task/",
    	data: data,
    	async: false,
    	success: function(res) {
    		text = res;
    	}
    });

	$('#usersDialog').html(text);
	
	$('#usersDialog').dialog({
	    buttons: {
			"Закрыть": function() { setTaskUsers(); $("#taskUsers").remove(); }
		},
		width: 450,
		height: 470
	});
}

function delegate() {
	var text = null;
	var data = "action=getDelegateUsers";
	$.ajax({
		type: "POST",
		url: url + "ajax/task/",
    	data: data,
    	async: false,
    	success: function(res) {
    		text = res;
    	}
    });

	$('#usersDelegateDialog').html(text);
	
	$('#usersDelegateDialog').dialog({
	    buttons: {
			"Закрыть": function() { setTaskUsers(); $("#taskOnlyUsers").remove(); }
		},
		width: 450,
		height: 470
	});
}

function showTaskInfo(tid) {
	$('#task' + tid).dialog({ width: 500 });
}

function refreshurl(refreshurl) {
	document.location.href = refreshurl;
}