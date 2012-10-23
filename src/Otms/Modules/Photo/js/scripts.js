$(document).ready(function(){
	p_uploader();
	
	$("#photoclip").css("max-height", ($(document).height()-130));

	$("#utree > #Pstructure").treeview({
		persist: "location",
		collapsed: true
    });

	$('#Prall').click(function(){
		if ($("#Prall").attr("checked")) {
			$('#Pstructure input:checkbox:enabled').each(function(){this.disabled = !this.disabled; });
			$('#Pstructure input:checkbox').removeAttr("checked");
		} else {
			$('#Pstructure input:checkbox:disabled').each(function(){this.disabled = !this.disabled; });
			$('#Pstructure input:checkbox').removeAttr("checked");
		}
		
		$('#Prall').removeAttr("disabled");
	});

	$('.Pgruser').click(function(){
		$('input.Pg' + $(this).val() + ':checkbox').each(function(){this.disabled = !this.disabled; });
		$('#Prall').removeAttr("checked");
	});

	$('.Pcusers').click(function(){
		$('#Prall').removeAttr("checked");
	});
	
	$("#share").click(function(){
		share($("#fid").val());
	});
});

if($("ul.dropdown").length) {
	$("ul.dropdown li").dropdown();
};

$.fn.dropdown = function() {

	return this.each(function() {

		$(this).hover(function(){
			$(this).addClass("hover");
			$('> .dir',this).addClass("open");
			$('ul:first',this).css('visibility', 'visible');
		},function(){
			$(this).removeClass("hover");
			$('.open',this).removeClass("open");
			$('ul:first',this).css('visibility', 'hidden');
		});

	});

};

var url;
var did;
function p_fmInit(path, id) {
	url = path;
	if (id == '') {
		did = 0;
	} else {
		did = id;
	}
	
	p_updUQuota();
}

function p_loadChmod(res) {
	$.each(res, function(key, val) {
        if (val > 0) {
        	
        	// Users
        	if (key.substr(0, 4) == "user") {
            	$("#" + key).attr("checked", "checked");
            	$("#umode_" + key.substr(4)).show();
            	$("input[name='mode_u_" + key.substr(4) + "'][value=" + val + "]").attr('checked', 'checked');
        	}
        	
        	// Groups
        	if (key.substr(0, 2) == "fg") {
        		$("#" + key).attr("checked", "checked");
            	$('input.' + key + ':checkbox').each(function(){this.disabled = !this.disabled; });
            	
               	$("#gmode_" + key.substr(2)).show();
               	$("input[name='mode_g_" + key.substr(2) + "'][value=" + val + "]").attr('checked', 'checked');
            }

        	// All
            if (key == "frall") {
            	$('#frall').attr("checked", "checked");
            	$('#fstructure input:checkbox').each(function(){this.disabled = !this.disabled; });

               	$("#amode").show();
               	$("input[name='mode_a'][value=" + val + "]").attr('checked', 'checked');
            }
        }
    });
}

/*
*
*  Click FS Chmod
* 
*/

// Click All
$('#frall').live("click", function(){
	if ($("#frall").attr("checked")) {
		$('#fstructure input:checkbox:enabled').each(function(){this.disabled = !this.disabled; });
		$('#fstructure input:checkbox').removeAttr("checked");
		$("input[name='mode_a'][value=2]").attr('checked', 'checked');
		
		$("#amode").show();
		$(".gmode").hide();
		$(".gmode input[type='radio']").removeAttr("checked");
		$(".umode").hide();
		$(".umode input[type='radio']").removeAttr("checked");
	} else {
		$('#fstructure input:checkbox:disabled').each(function(){this.disabled = !this.disabled; });
		$('#fstructure input:checkbox').removeAttr("checked");
		
		$("#amode").hide();
		$("#amode input[type='radio']").removeAttr("checked");
	}
	
	$('#frall').removeAttr("disabled");
});

// Click Group
$('.fgruser').live("click", function(){
	$('input.fg' + $(this).val() + ':checkbox').each(function(){this.disabled = !this.disabled; });
	$('input.fg' + $(this).val() + ':checkbox').removeAttr("checked");
	
	if ($(this).attr("checked")) {
		$("input[name='mode_g_" + $(this).val() + "'][value=2]").attr('checked', 'checked');
		
		$("#gmode_" + $(this).val()).show();
		$("#umode_" + $(this).val()).hide();
		
		$(".gparent_mode_" + $(this).val()).hide();
		$(".gparent_mode_" + $(this).val() + " input[type='radio']").removeAttr("checked");
	} else {
		$("#gmode_" + $(this).val()).hide();
		$("input[name='mode_g_" + $(this).val() + "']").removeAttr("checked");
	}
});

// Click User
$('.fcusers').live("click", function(){
	$('#frall').removeAttr("checked");
	if ($(this).attr("checked")) {
		$("input[name='mode_u_" + $(this).val() + "'][value=2]").attr('checked', 'checked');
		
		$("#umode_" + $(this).val()).show();
	} else {
		$("#umode_" + $(this).val()).hide();
		$("input[name='mode_u_" + $(this).val() + "']").removeAttr("checked");
	}
});

function p_getJson() {
	var formData = new Array(); var i = 0;
	$("#fchmod input:checkbox").each(function(n){
		id = this.id;
		attr = false;
		
		// All
		if ($(this).attr("id") == "frall") {
			attr = $("input[name='mode_a']:checked").val();
		
		// Groups
		} else if ($(this).attr("class") == "fgruser") {
			$("input[name='mode_g_" + id.substr(2) + "']:checked").each(function(n){
				data = $(this).val();
				if ( (data == 1) || (data == 2) ) {
					attr = data;
				}
			});
			
		// Users
		} else {
			$("input[name='mode_u_" + id.substr(4) + "']:checked").each(function(n){
				data = $(this).val();
				if ( (data == 1) || (data == 2) ) {
					attr = data;
				}
			});
		}

		if ( (attr == 1) || (attr == 2) ) {
			formData[i] = ['"' + id + '"', '"' + attr + '"'].join(":");
			i++;
		}
	});

	var json = "{" + formData.join(",") + "}";
	
	return json;
}


function p_shUploader() {
	$('#p_uploader').dialog({
	    buttons: {
			"Close": function() { p_uploader(); $(this).dialog("close"); }
		},
		width: 450,
		height: 470
	});
}

function p_uploader() {
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: "did=" + did + "&action=files",
		success: function(res) {
			$("#photo_filesystem").html(res);
		}
	});
}

function p_update() {
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: "did=" + did + "&action=getTotalSize",
		success: function(res) {
			p_updUQuota();
			$("#photo_total").html(res);
		}
	});
};

function p_addElement(id, fileName) {
	var shortname = "";
    var ext = fileName.substr(fileName.lastIndexOf(".")+1, fileName.length-fileName.lastIndexOf(".")-1);
    ext = ext.toLowerCase();
    
    if (fileName.length > 20) {
		shortname = fileName.substr(0, 16) + ".." + fileName.substr(fileName.lastIndexOf(".")-1, fileName.length-fileName.lastIndexOf(".")+1);
	} else {
		shortname = fileName;
	}

    var rowInsert = '<div id="photo_file' + id + '" class="photo_file" style="text-align: center; cursor: pointer"><div class="photo_unsellabel"><a class="photo_pre" name="' + fileName + '" id="photo_filename' + id + '"><img src="' + url + 'img/ftypes/unknown.png" style="width: 170px; opacity: 1.0;" alt="[FILE]" /></a><div class="fname">' + shortname + '</div><div style="color: #777; cursor: pointer" onclick="p_uploader()">refresh</div></div></div>';
    
    $("#photo_uploadDir").prepend(rowInsert);
};

function p_del(fname, id) {
    var data ="did=" + did + "&action=delfile&fname=" + encodeURIComponent(fname);
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: data,
		success: function(res) {
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("photo_sellabel");
            
            p_update();
		}
	});
};

function p_delReal(fname, id) {
    var data ="did=" + did + "&action=delfilereal&fname=" + encodeURIComponent(fname);
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: data,
		success: function(res) {
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("photo_sellabel");
            
            p_update();
		}
	});
};

function p_deldir(id, name) {
    var data ="did=" + did + "&action=deldir&did=" + id;
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: data,
		success: function(res) {
			p_renderTree();
			
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("photo_sellabel");
            
            p_update();
		}
	});

};

function p_deldirReal(id) {
    var data ="did=" + did + "&action=deldirreal&did=" + id;
	$.ajax({
		type: "POST",
		url: url + 'ajax/photo/',
		data: data,
		success: function(res) {
			p_renderTree();
			
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("photo_sellabel");
            
            p_update();
		}
	});
};


function p_chdir(dir) {
	window.location.href = url + "photo/?id=" + dir;
};

function p_shDirRight(did) {
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=shDirRight&did=" + did,
    	success: function(res) {
    		$('#dirDialog').html(res);
    		
    		$('#dirDialog').dialog({
    		    buttons: {
    				"Close": function() {
    					$("#fchmod").remove();
    					$(this).dialog("close");
    				}
    			},
    			width: 450,
    			height: 470
    		});
    	}
    });
}

function p_addFileText() {
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=addFileText&text=" + encodeURIComponent($("#fText").val()) + "&md5=" + $("#md5").val(),
    	success: function(res) {
    		$("#resNotes").prepend(res);
    		num = parseInt($("#numNotes").text()) + parseInt(1);
    		$("#numNotes").text(num);
    	}
    });
}

function p_getCurDirName() {
	var text = null;
	
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=getCurDirName",
    	success: function(res) {
    		text = res;
    	}
    });

    return text;
}

function p_getShare(md5) {
	var data = null;
	
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=getShare&md5=" + md5,
    	success: function(res) {
    		data = res;
    	}
    });

    return data;
}

function p_getfInfo(fname, md5, i) {
	var fowner = "";
	var fsize = "";
	var fhistory = "";
	var fchmod = "";
	
	$.ajax({
    	type: "POST",
    	async: false,
    	dataType: 'json',
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=getfinfo&md5=" + md5,
    	success: function(res) {
    		$.each(res, function(key, val) {
    	        if (key == "owner") {
    	        	fowner = val;
    	        } else if (key == "size") {
    	        	fsize = val;
    	        } else if (key == "chmod") {
    	        	fchmod = val;
    	        }
    	    });
    	}
    });

	$("#dopenfile").html("<b>Download: </b><a style='color: blue' href='" + url + "attach/?did=" + did + "&filename=" + encodeURIComponent(fname) + "' id='fdname'>" + fname + "</a>");

	$("#fid").val(md5);
	$("#fowner").html('' + fowner + '');
	$("#fsize").html('' + fsize + '');
	$("#fdhistory").html('' + fhistory + '');
	$("#fdchmod").html('' + fchmod + '');

	$("#tabs").tabs({ selected: i });

	$('#fDialog').dialog({
		buttons: {
			"Close": function() {
				$("#fchmod").remove();
				$(this).dialog("close");
			}
		},
		width: 450,
		height: 470
	});
}

function p_createDirDialog() {
	$('<div title="New folder" id="diradddialog">Folder name:&nbsp;<input type="text" name="dirname" id="photo_dirname" /></div>').dialog({
		modal: true,
	    buttons: {
	    	"Create": function() {
	    		p_createDir();
				$("#diradddialog").remove();
				$(this).dialog("close");
			},
            "Close": function() {
            	$("#diradddialog").remove();
            	$(this).dialog("close");
            }
		}
	});
}

function p_createDir() {
    var _dirName = encodeURIComponent($("#photo_dirname").val());
    if (_dirName.length == 0) {
    	$('<div title="Notify">Folder name is empty!</div>').dialog({
    		modal: true,
    	    buttons: {
                "Close": function() { $(this).dialog("close"); }
    		}
    	});
    } else {
        $.ajax({
        	type: "POST",
        	url: url + 'ajax/photo/',
        	data: "did=" + did + "&action=createDir&dirName=" + _dirName,
        	success: function(res) {
        		if (res == "error") {
        			$('<div title="Error">The folder with such name exists!</div>').dialog({
        	    		modal: true,
        	    	    buttons: {
        	                "Close": function() { $(this).dialog("close"); }
        	    		}
        	    	});
        		} else {
        			p_renderTree();
        			$("#photo_filesystem").html(res);
        		}
        	}
        });
    }
};

function p_pastFiles() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=moveFiles",
    	success: function(res) {
    		p_renderTree();
            $("#photo_filesystem").html(res);
            $("#photoclip").html("<li style='text-align: center'>empty</li>");
    	}
    });
}

function p_admin() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=admin",
    	success: function(res) {
    		p_renderTree();
            $("#photo_filesystem").html(res);
    	}
    });
}

function p_renderTree() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "did=" + did + "&action=getTree",
    	success: function(res) {
    		$("#treestructure").html(res);
    	}
    });
}

function p_fileRename(md5, name) {
	$("<div title='File Rename'>File name:&nbsp;<input type='text' name='dirname' id='photo_dirname' value='" + name + "' /></div>").dialog({
		modal: true,
	    buttons: {
	    	"OK": function() { 
	    		$.ajax({
	    	    	type: "POST",
	    	    	async: false,
	    	    	url: url + 'ajax/photo/',
	    	    	data: "did=" + did + "&action=fileRename&md5=" + md5 + "&newname=" + encodeURIComponent($("#photo_dirname").val()),
	    	    	success: function(res) {
	    	    		$(".photo_file[md5='" + md5 + "'] .fname").text($("#photo_dirname").val());
	    	    	}
	    	    });
	    		$(this).dialog("close");
	    	},
	    	"Close": function() { $(this).dialog("close"); }
	    }
	});
}

function p_dirRename(did) {
	var name = null;
	
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	async: false,
    	data: "action=getDirName&did=" + did,
    	success: function(res) {
    		name = res;
    	}
    });
	$("<div title='Folder Rename'>Folder name:&nbsp;<input type='text' name='dirname' id='photo_dirname' value='" + name + "' /></div>").dialog({
		modal: true,
	    buttons: {
	    	"OK": function() {
	    		$.ajax({
	    	    	type: "POST",
	    	    	async: false,
	    	    	url: url + 'ajax/photo/',
	    	    	data: "action=dirRename&did=" + did + "&name=" + encodeURIComponent($("#photo_dirname").val()),
	    	    	success: function(res) {
	    	    		p_renderTree();
	    	    		$("#" + did + " .dname").text($("#photo_dirname").val());
	    	    	}
	    	    });
	    		$(this).dialog("close");
	    	},
	    	"Close": function() { $(this).dialog("close"); }
	    }
	});
}

function p_updUQuota() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/users/',
    	data: "action=getUserQuota",
    	success: function(res) {
    		$("#user_quota").html(res);
    	}
    });
}

function delSortTag(word) {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "action=delSortTag&word=" + word,
    	success: function(res) {
    		window.location.href = window.location.href;
    	}
    });
}

function delSortSel(word) {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "action=delSortSel&word=" + word,
    	success: function(res) {
    		window.location.href = window.location.href;
    	}
    });
}

function getFavorite() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "action=getFavorite",
    	success: function(res) {
    		window.location.href = window.location.href;
    	}
    });
}

function delFavorite() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: "action=delFavorite",
    	success: function(res) {
    		window.location.href = window.location.href;
    	}
    });
}

function setDescConfirm(word) {
	$("<div title='Confirm'>To pass to results of sorting?</div>").dialog({
		modal: true,
	    buttons: {
            "OK": function() { setSel(word); $(this).dialog("close"); },
            "Close": function() { $(this).dialog("close"); }
		},
		zIndex: 10000,
		width: 230,
        height: 170
	});
}

function setTagConfirm(word) {
	$("<div title='Confirm'>To pass to results of sorting?</div>").dialog({
		modal: true,
	    buttons: {
            "OK": function() { p_setTag(word); $(this).dialog("close"); },
            "Close": function() { $(this).dialog("close"); }
		},
		zIndex: 10000,
		width: 230,
        height: 170
	});
}

function setTag(word) {
	var data = "action=setTag&word=" + word;

	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: data,
    	async: false,
    	success: function(res) {
    		window.location.href = url + "photo/";
    	}
    });
}

function setSel(word) {
	var data = "action=setSel&word=" + word;

	$.ajax({
    	type: "POST",
    	url: url + 'ajax/photo/',
    	data: data,
    	async: false,
    	success: function(res) {
    		window.location.href = url + "photo/";
    	}
    });
}