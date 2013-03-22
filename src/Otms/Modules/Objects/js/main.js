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
			$('<div title="Information" style="text-align: left">' + res + '</div>').dialog({ width: 700 });
		}
	});
}

function delAdvConfirm(oaid) {
    $('<div title="Deleting record">Delete?</div>').dialog({
            modal: true,
        buttons: {
                    "No": function() { $(this).dialog("close"); },
                    "Yes": function() { delAdv(oaid); $(this).dialog("close"); }
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
	$('<div title="Deleting templates">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { delTemplate(id); $(this).dialog("close"); }
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
       	   $('<div title="Information">' + res + '</div>').dialog({ width: 600, height: 600 });
		}
	});
}


function delFormConfirm(id) {
	$('<div title="Deleting form">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { delForm(id); $(this).dialog("close"); }
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
