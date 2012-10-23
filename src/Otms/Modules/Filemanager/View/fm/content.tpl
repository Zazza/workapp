<div id="fmTop">
	<div style="float: left; margin-right: 20px"><b>Current dir: [{{ shPath }}]</b></div>
	<div style="float: left">
		<div id="servSel">
			<div id="fm_sel" class="btn"><img style="vertical-align: middle" src="{{ registry.uri }}/img/filemanager/plus.png" /> Select All</div>
			<div id="fm_unsel" class="btn"><img style="vertical-align: middle" src="{{ registry.uri }}/img/filemanager/minus.png" /> Unselect All</div>
		</div>
	</div>
	<div style="float: right"><b>Size:</b> <span id="fm_total">{{ totalsize }}</span></div>
</div>

<div style="overflow: hidden; margin-bottom: 57px;">

{% set i = 0 %}
{% for part in dirs %}
{% if part.name != ".." %}
{% set i = i + 1 %}

{% if part.close %}{% set opacity = "; opacity: 0.2;" %}{% else %}{% set opacity = "; opacity: 1.0;" %}{% endif %}

<div class="fm_dirs fm_dirs{{ i }}" title="{{ part.name }}" id="d_{{ part.id }}"  style="text-align: center; cursor: pointer">
<div class="fm_unsellabel">
	<div ondblClick="chdir('{{ part.id }}')"><img src="{{ registry.uri }}img/filemanager/ftypes/folder.png" style="width: 50px{{ opacity }}" alt="[DIR]" /></div>
	<div ondblClick="chdir('{{ part.id }}')" class="dname">{{ part.name }}</div>
</div>
</div>
{% else %}
<div class="fm_dirs_up" title="{{ part.name }}" style="text-align: center; cursor: pointer">
	<div ondblClick="chdir('{{ part.pid }}')"><img src="{{ registry.uri }}img/filemanager/ftypes/folder.png" style="width: 50px{{ opacity }}" alt="[DIR]" /></div>
	<div ondblClick="chdir('{{ part.pid }}')">[..]</div>
</div>
{% endif %}
{% endfor %}

<div id="fm_uploadDir">

{% for part in files %}
{% set i = i + 1 %}

{% if part.close %}{% set opacity = "; opacity: 0.2;" %}{% else %}{% set opacity = "; opacity: 1.0;" %}{% endif %}

<div id="fm_file{{ i }}" title="{{ part.name }}" class="fm_file" style="text-align: center; cursor: pointer">
<div class="fm_unsellabel">
	<a class="fm_pre" name="{{ part.name }}" id="fm_filename{{ i }}"><img src="{{ part.ico }}" style="height: 50px{{ opacity }}" alt="[FILE]" /></a>
	<div class="fname">{{ part.shortname }}</div>
	<div class="fullname" style="display: none">{{ part.name }}</div>
	<input type="hidden" class="file_id" value="{{ part.id }}" />
	
	<div style="color: #777">size:&nbsp;{{ part.size }}</div>
	<div id="fs_{{ part.id }}" style="color: green; font-weight: bold; {% if part.share %}display: block{% else %}display: none{% endif %}">Share</div>
</div>
</div>
{% endfor %}
</div>

</div>

<input name="lastIdRow" id="fm_lastIdRow" value="{{ i }}" type="hidden" />
<input name="max" id="fm_max" value="{{ i }}" type="hidden" />

<script type="text/javascript">
$(function(){
	{% if admin %}
	$("#admbtn").removeClass("btn-danger").addClass("btn-success");
	$("#adminFunc").show();
	{% else %}
	$("#admbtn").removeClass("btn-success").addClass("btn-danger");
	$("#adminFunc").hide();
	{% endif %}
});

$("#fm_sel").click(function() {
    $(".fm_unsellabel").removeClass("fm_unsellabel").addClass("fm_sellabel");
});

$("#fm_unsel").click(function() {
    $(".fm_sellabel").removeClass("fm_sellabel").addClass("fm_unsellabel");
});

$(".fm_unsellabel").live("click", function(){
    $(this).removeClass("fm_unsellabel").addClass("fm_sellabel");
});

$(".fm_sellabel").live("click", function(){
    $(this).removeClass("fm_sellabel").addClass("fm_unsellabel");
});

function getCol() {
	var col = 0;
	for (i = 1; i <= parseInt($("#fm_max").val()); i++) {
    	if ($(".fm_dirs" + i + " > div").attr("class") == "fm_sellabel") {
    		col++;
    	};
    	
        if ($("#fm_file" + i + " > div").attr("class") == "fm_sellabel") {
        	col++;
        }
    };
    
    return col;
}

function delmany() {
    for (i = 1; i <= parseInt($("#fm_max").val()); i++){
    	if ($(".fm_dirs" + i + " > div").attr("class") == "fm_sellabel") {
    		deldir($(".fm_dirs" + i + "").attr("id"), $(".fm_dirs" + i + "").attr("title"));
    	};
    	
        if ($("#fm_file" + i + " > div").attr("class") == "fm_sellabel") {
            del($("#fm_filename" + i + "").attr("name"), "fm_file" + i);
        }
    };
    
    update();
};

function restore() {
	var selfiles = "";
    for (i = 1; i <= parseInt($("#fm_max").val()); i++) {
    	if ($(".fm_dirs" + i + " > div").attr("class") == "fm_sellabel") {
    		var did = $(".fm_dirs" + i + "").attr("id");
    		selfiles += "&dir[]" + "=" + did.substr(2);
    	};
    	
        if ($("#fm_file" + i + " > div").attr("class") == "fm_sellabel") {
            selfiles += "&file[]" + "=" + encodeURIComponent($("#fm_filename" + i + "").attr("name"));
        }
    };

	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/filemanager/',
		data: "did={{ curdir }}&action=restore&" + selfiles,
		success: function(res) {
			$("#fm_filesystem").html(res);
		}
	});
}

function delmanyrealConfirm() {
	$('<div title="Remove">You really want to remove files without restoration possibility?</div>').dialog({
		buttons: {
			"Yes": function() { delmanyreal(); $(this).dialog("close"); },
			"No": function() { $(this).dialog("close"); }
		},
		width: 350,
		height: 200
	});
};

function delmanyreal() {
    for (i = 1; i <= parseInt($("#fm_max").val()); i++){
    	if ($(".fm_dirs" + i + " > div").attr("class") == "fm_sellabel") {
    		deldirReal($(".fm_dirs" + i + "").attr("id"));
    	};
    	
        if ($("#fm_file" + i + " > div").attr("class") == "fm_sellabel") {
            delReal($("#fm_filename" + i + "").attr("name"), "fm_file" + i);
        }
    };
    
    update();
}

function copyFiles() {
    var selfiles = "";
    for (i = 1; i <= parseInt($("#fm_max").val()); i++) {
    	if ($(".fm_dirs" + i + " > div").attr("class") == "fm_sellabel") {
    		var did = $(".fm_dirs" + i + "").attr("id");
    		selfiles += "&dir[]" + "=" + did.substr(2);
    	};
    	
        if ($("#fm_file" + i + " > div").attr("class") == "fm_sellabel") {
            selfiles += "&file[]" + "=" + $("#fm_file" + i + " > div > .file_id").val();
        }
    };

	$.ajax({
		type: "POST",
		dataType: 'json',
		url: '{{ registry.uri }}ajax/filemanager/',
		data: "did={{ curdir }}&action=copyFiles&" + selfiles,
		success: function(res) {
			$.each(res, function(key, val) {
				if (key == "count") {
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

$(".fm_file").contextMenu('fileMenu', {
    bindings: {
      'rm_open': function(t) {
		window.location.href = "{{ registry.uri }}filemanager/attach/?did={{ curdir }}&filename=" + encodeURIComponent(t.title);
      },
      'rm_rename': function(t) {
		fileRename(t.title); 
      },
      'rm_main': function(t) {
		getfInfo(t.title, 0); 
      },
      'rm_history': function(t) {
        getfInfo(t.title, 1); 
      },
      'rm_right': function(t) {
		getfInfo(t.title, 2); 
      }
    }
});

$(".fm_dirs").contextMenu('dirMenu', {
    bindings: {
      'rd_open': function(t) {
		var id = t.id; 
		id = id.substr(2);
		chdir(id);
      },
      'rd_rename': function(t) {
		dirRename(t.id);
      },
      'rd_right': function(t) {
		shDirRight(t.id);
      }
    }
});

$(".fm_pre").live("dblclick", function(){
	window.location.href = "{{ registry.uri }}filemanager/attach/?did={{ curdir }}&filename=" + encodeURIComponent($(this).attr("name"));
});
</script>
