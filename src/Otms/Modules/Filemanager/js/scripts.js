$(document).ready(function(){
	uploader();
	
	$("#clip").css("max-height", ($(document).height()-130));

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
	
	$("#share").click(function(){
		share($("#fid").val());
	});
});

function loadChmod(res) {
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
            	$('input.' + key + ':checkbox').each(function(){this.disabled = !this.disabled});
            	
               	$("#gmode_" + key.substr(2)).show();
               	$("input[name='mode_g_" + key.substr(2) + "'][value=" + val + "]").attr('checked', 'checked');
            }

        	// All
            if (key == "frall") {
            	$('#frall').attr("checked", "checked");
            	$('#fstructure input:checkbox').each(function(){this.disabled = !this.disabled});

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
		$('#fstructure input:checkbox:enabled').each(function(){this.disabled = !this.disabled});
		$('#fstructure input:checkbox').removeAttr("checked");
		$("input[name='mode_a'][value=2]").attr('checked', 'checked');
		
		$("#amode").show();
		$(".gmode").hide();
		$(".gmode input[type='radio']").removeAttr("checked");
		$(".umode").hide();
		$(".umode input[type='radio']").removeAttr("checked");
	} else {
		$('#fstructure input:checkbox:disabled').each(function(){this.disabled = !this.disabled});
		$('#fstructure input:checkbox').removeAttr("checked");
		
		$("#amode").hide();
		$("#amode input[type='radio']").removeAttr("checked");
	}
	
	$('#frall').removeAttr("disabled");
});

// Click Group
$('.fgruser').live("click", function(){
	$('input.fg' + $(this).val() + ':checkbox').each(function(){this.disabled = !this.disabled});
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

function getJson() {
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


function shUploader() {
	$('#uploader').dialog({
	    buttons: {
			"Close": function() { uploader(); $(this).dialog("close"); }
		},
		width: 450,
		height: 470
	});
}

function uploader() {
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: "did=" + did + "&action=files",
		success: function(res) {
			$("#fm_filesystem").html(res);
		}
	});
}

function update() {
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: "did=" + did + "&action=getTotalSize",
		success: function(res) {
			updUQuota();
			$("#fm_total").html(res);
		}
	});
};

function addElement(id, fileName) {
    var ext = fileName.substr(fileName.lastIndexOf(".")+1, fileName.length-fileName.lastIndexOf(".")-1);
    ext = ext.toLowerCase();
    
    if (fileName.length > 20) {
		var shortname = fileName.substr(0, 16) + ".." + fileName.substr(fileName.lastIndexOf(".")-1, fileName.length-fileName.lastIndexOf(".")+1);
	} else {
		var shortname = fileName;
	}

    var rowInsert = '<div id="fm_file' + id + '" class="fm_file" style="text-align: center; cursor: pointer"><div class="fm_unsellabel"><a class="fm_pre" name="' + fileName + '" id="fm_filename' + id + '"><img src="' + url + 'img/filemanager/ftypes/unknown.png" style="width: 50px; opacity: 1.0;" alt="[FILE]" /></a><div class="fname">' + shortname + '</div><div style="color: #777; cursor: pointer" onclick="uploader()">refresh</div></div></div>';
    
    $("#fm_uploadDir").prepend(rowInsert);
};

function getBuffer() {
	$("#clip").html('<p style="text-align: center"><img src="' + url + 'img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></p>');
	$.ajax({
		type: "POST",
		dataType: 'json',
		url: url + 'ajax/filemanager/',
		data: "did=" + did + "&action=getClip",
		success: function(res) {
			$.each(res, function(key, val) {
				if (key == "buffer") {
					if (val == 0) {
						$("#clip").html("<li style='text-align: center'>пусто</li>");
					} else {
						$("#clip").html(val);
					}
				} else {
					if (val == 0) {
						$("#countBuffer").removeClass("label-success");
					} else {
						$("#countBuffer").addClass("label-success");
					}
					
					$("#countBuffer").text(val);
				}
			});
		}
	});
}

function del(fname, id) {
    var data ="did=" + did + "&action=delfile&fname=" + encodeURIComponent(fname);
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: data,
		success: function(res) {
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("fm_sellabel");
            
            update();
		}
	});
};

function delReal(fname, id) {
    var data = "did=" + did + "&action=delfilereal&fname=" + encodeURIComponent(fname);
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: data,
		success: function(res) {
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("fm_sellabel");
            
            update();
		}
	});
};

function deldir(id, name) {
    var data ="did=" + did + "&action=deldir&did=" + id;
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: data,
		success: function(res) {
			renderTree();
			
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("fm_sellabel");
            
            update();
		}
	});

};

function deldirReal(id) {
    var data ="did=" + did + "&action=deldirreal&did=" + id;
	$.ajax({
		type: "POST",
		url: url + 'ajax/filemanager/',
		data: data,
		success: function(res) {
			renderTree();
			
			$("#" + id + "").fadeOut("fast");
			$("#" + id + "").removeClass("fm_sellabel");
            
            update();
		}
	});
};


function chdir(dir) {
	window.location.href = url + "filemanager/?id=" + dir;
};

function shDirRight(did) {
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/filemanager/',
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

function addFileText() {
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=addFileText&text=" + encodeURIComponent($("#fText").val()) + "&md5=" + $("#fid").val(),
    	success: function(res) {
    		$("#dfiletext").prepend(res);
    	}
    });
}

function getCurDirName() {
	var text = null;
	
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=getCurDirName",
    	success: function(res) {
    		text = res;
    	}
    });

    return text;
}

function getShare(md5) {
	var data = null;
	
	$.ajax({
    	type: "POST",
    	async: false,
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=getShare&md5=" + md5,
    	success: function(res) {
    		data = res;
    	}
    });

    return data;
}

function getfInfo(fname, i) {
	var md5 = "";
	var fowner = "";
	var fsize = "";
	var fshare = "";
	var ftext = "";
	var fhistory = "";
	var fchmod = "";
	
	$.ajax({
    	type: "POST",
    	async: false,
    	dataType: 'json',
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=getfinfo&fname=" + fname,
    	success: function(res) {
    		$.each(res, function(key, val) {
    	        if (key == "md5") {
    	        	md5 = val;
    	        } else if (key == "owner") {
    	        	fowner = val;
    	        } else if (key == "size") {
    	        	fsize = val;
    	        } else if (key == "share") {
    	        	fshare = val;
    	        } else if (key == "text") {
    	        	ftext = val;
    	        } else if (key == "history") {
    	        	fhistory = val;
    	        } else if (key == "chmod") {
    	        	fchmod = val;
    	        }
    	    });
    	}
    });

	$("#dopenfile").html("<b>Download: </b><a style='color: blue' href='" + url + "filemanager/attach/?did=" + did + "&filename=" + encodeURIComponent(fname) + "' id='fdname'>" + fname + "</a>");

	if (fshare) {
		$("#share").attr("checked", "checked");
		$("#shareName").show();
		$(".shfname").text(encodeURIComponent(fshare));
	} else {
		$("#share").removeAttr("checked");
		$("#shareName").hide();
	}

	$("#fid").val(md5);
	$("#fowner").html('' + fowner + '');
	$("#fsize").html('' + fsize + '');
	$("#dfiletext").html('' + ftext + '');
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

function createDirDialog() {
	$('#diradddialog').dialog({
		modal: true,
	    buttons: {
	    	"Create": function() {
	    		createDir();
				$(this).dialog("close");
			},
            "Close": function() {
            	$(this).dialog("close");
            }
		}
	});
}

function createDir() {
    var _dirName = encodeURIComponent($("#fm_dirname").val());
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
        	url: url + 'ajax/filemanager/',
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
        			renderTree();
        			$("#fm_filesystem").html(res);
        		}
        	}
        })
    }
};

function pastFiles() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=moveFiles",
    	success: function(res) {
    		renderTree();
            $("#fm_filesystem").html(res);
            //$("#clip").html("<li style='text-align: center'>пусто</li>");
            $("#countBuffer").removeClass("label-success");
            $("#countBuffer").text("0");
    	}
    });
}

function admin() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=admin",
    	success: function(res) {
    		renderTree();
            $("#fm_filesystem").html(res);
    	}
    });
}

function share(md5) {
	var fname = null;
	var desc = null;
	
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=share&md5=" + md5,
    	dataType: 'json',
    	success: function(res) {
    		$.each(res, function(key, val) {
    			if (key == "fid") {
    				fid = val;
    			} else if (key == "desc") {
    				desc = val;
    			} else if (key == "action") {
    				if (val == "share") {
    					$("#shareName").show();
            			$(".shfname").text(encodeURIComponent(desc));

    					$("#fs_" + fid).show();
    				} else if (val == "unshare") {
    					$("#share").removeAttr("checked");
    	    			$("#shareName").hide();
    	    			
    					$("#fs_" + fid).hide();
    				}
    			}
    		});
    	}
    });
}

function renderTree() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/filemanager/',
    	data: "did=" + did + "&action=getTree",
    	success: function(res) {
    		$("#treestructure").html(res);
    	}
    });
}

function fileRename(name) {
	$("<div title='File Rename'>File name:&nbsp;<input type='text' name='dirname' id='fm_dirname' value='" + name + "' /></div>").dialog({
		modal: true,
	    buttons: {
	    	"OK": function() { 
	    		$.ajax({
	    	    	type: "POST",
	    	    	async: false,
	    	    	url: url + 'ajax/filemanager/',
	    	    	data: "did=" + did + "&action=fileRename&oldname=" + encodeURIComponent(name) + "&newname=" + encodeURIComponent($("#fm_dirname").val()),
	    	    	success: function(res) {
	    	    		$(".fm_file[title=" + name + "] .fname").text($("#fm_dirname").val());
	    	    	}
	    	    });
	    		$(this).dialog("close");
	    	},
	    	"Close": function() { $(this).dialog("close"); }
	    }
	});
}

function dirRename(did) {
	var name = null;
	
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/filemanager/',
    	async: false,
    	data: "action=getDirName&did=" + did,
    	success: function(res) {
    		name = res;
    	}
    });
	$("<div title='Folder Rename'>Folder name:&nbsp;<input type='text' name='dirname' id='fm_dirname' value='" + name + "' /></div>").dialog({
		modal: true,
	    buttons: {
	    	"OK": function() { 
	    		$.ajax({
	    	    	type: "POST",
	    	    	async: false,
	    	    	url: url + 'ajax/filemanager/',
	    	    	data: "action=dirRename&did=" + did + "&name=" + encodeURIComponent($("#fm_dirname").val()),
	    	    	success: function(res) {
	    	    		renderTree();
	    	    		$("#" + did + " .dname").text($("#fm_dirname").val());
	    	    	}
	    	    });
	    		$(this).dialog("close");
	    	},
	    	"Close": function() { $(this).dialog("close"); }
	    }
	});
}

function updUQuota() {
	$.ajax({
    	type: "POST",
    	url: url + 'ajax/users/',
    	data: "action=getUserQuota",
    	success: function(res) {
    		$("#user_quota").html(res);
    	}
    });
}

function userParam() {
	$("#userParam").html('<p style="text-align: center"><img src="' + url + 'img/ajax-loader.gif" alt="ajax-loader.gif" border="0" /></p>');
    var data = "action=getUserQuota";
    $.ajax({
            type: "POST",
            url: url + "ajax/filemanager/",
            data: data,
            success: function(res) {
            	$("#userParam").html(res);
            }
    });
}

function clearBuffer() {
	$.ajax({
        type: "POST",
        url: url + "ajax/filemanager/",
        data: "action=clearBuffer",
        success: function(res) {
        	$("#countBuffer").removeClass("label-success");
        	$("#countBuffer").html("0");
        	$("#clip").html("<li style='text-align: center'>пусто</li>");
        }
	});
}

function fromBuffer() {
	var dir = "";
	var fname = "";
	$.ajax({
        type: "POST",
        dataType: 'json',
        url: url + "ajax/filemanager/",
        data: "action=fromBuffer",
        success: function(res) {
        	$.each(res, function(key, val) {
        		$.each(val, function(file_key, file_val) {
        			if (file_key == "name") {
        				fname = file_val;
        			}
        			
        			if (file_key == "pdirid") {
        				dir = file_val;
        			}
    			});
    			
    			$("#attach_files").append("<div style='margin: 4px; float: left'><input type='hidden' name='attaches[]' value='/" + dir + "/" + fname + "' /><code><img border='0' src='" + url + "img/paper-clip-small.png' alt='attach' style='position: relative; top: 1px; left: 1px' />" + fname + "</code></div>");
        	});     	
        }
	});
}