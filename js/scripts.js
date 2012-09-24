var url;
var did;
function otmsInit(path, id) {
	url = path;
	if (id == '') {
		did = 0;
	} else {
		did = id;
	}
	
	updUQuota();
}

$(document).ready(function(){
	$("#touchID").focus();
});

function refreshurl(refreshurl) {
	document.location.href = refreshurl;
}

function delava() {
    var data = "action=delAva";
    $.ajax({
            type: "POST",
            url: url + "ajax/profile/",
            data: data,
            success: function(res) {
            	$(".avatar").attr("src", url + "img/noavatar.gif");
            }
    });
}

function loadava() {
    $("#selavatar").dialog({
            modal: true,
            width: 300,
            height: 120
    });
}

function setbStatus(val) {
	$('#bStatus').html(val);
	
	var data = "action=setHistory&string=" + val;
    $.ajax({
    	type: "POST",
    	url: url + "ajax/cmd/",
    	data: data,
    	success: function(res) {
    		setCmd(res);
    	}
    });
}