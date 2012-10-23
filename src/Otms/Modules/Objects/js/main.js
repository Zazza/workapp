$(document).ready(function(){
    $("#kbl #structure").treeview({
		persist: "location",
		collapsed: true
    });

    $("#kbl #ajax-load").hide();
    $("#kbl #structure").show();
});

function showInfo(id) {
    var data = "action=getAIInfo&id=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
			$('<div title="Информация" style="text-align: left">' + res + '</div>').dialog({ width: 700 });
		}
	});
}

function delAdvConfirm(oaid) {
    $('<div title="Удаление записи">Удалить?</div>').dialog({
            modal: true,
        buttons: {
                    "Нет": function() { $(this).dialog("close"); },
                    "Да": function() { delAdv(oaid); $(this).dialog("close"); }
            },
            width: 240
    });
}

function delAdv(oaid) {
    var data = "action=delAdv&id=" + oaid;
        $.ajax({
                type: "POST",
                url: url + "ajax/task/",
                data: data,
                success: function(res) {
            document.location.href = document.location.href;
                }
        });
}

function delTemplateConfirm(id) {
	$('<div title="Удаление шаблона">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delTemplate(id); $(this).dialog("close"); }
		},
		width: 240
	});
}

function delTemplate(id) {
    var data = "action=delTemplate&id=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
            document.location.href = document.location.href;
		}
	});
}

function getInfo(id) {
	$("#tview").remove();
    var data = "action=getInfo&id=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
       	   $('<div title="Информация">' + res + '</div>').dialog({ width: 600, height: 600 });
		}
	});
}


function delFormConfirm(id) {
	$('<div title="Удаление формы">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { delForm(id); $(this).dialog("close"); }
		},
		width: 280
	});
}

function delForm(id) {
    var data = "action=delForm&id=" + id;
    $.ajax({
            type: "POST",
            url: url + "ajax/objects/",
            data: data,
            success: function(res) {
    			document.location.href = url + 'objects/forms/';
            }
    });
}
