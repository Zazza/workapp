<div style="overflow: hidden;">
	<div style="float: left; margin-bottom: 20px">
		add:&nbsp;<input type="text" id="name" name="name" style="width: 150px; margin-right: 20px" />
		<input type="button" value="Add" onclick="addDataField()" />
	</div>
	
	<div style="float: right">
		<a class="btn" onclick="rmAllDataConfirm()">
			<i class="icon-trash"></i>
			Delete all
		</a>
		
		<a class="btn" href="{{ registry.uri }}settings/datatypes/">
			<i class="icon-arrow-left"></i>
			Back
		</a>
	</div>
</div>

<div id="litree"></div>

<div title="Правка" id="editDataCat" style="display: none">
    <input type="text" id="datacatname" style="width: 150px" />
<div>

<script type="text/javascript">
$(document).ready(function(){
    renderDataTree();
})

function renderDataTree() { 
    var data = "action=getDataTree&id=" + {{ id }};
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
            $("#litree").html(res);
            $("#structure").treeview();
		}
	})
}

function addDataField() {
	$("#litree").html('<img src="{{ registry.uri }}img/ajaxCheckMail.gif" alt="ajax-loader.gif" border="0" />');
    var data = "action=addDataField&id=" + {{ id }} + "&name=" + $("#name").val();
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
            renderDataTree();
		}
	})
}

function delDataCat(id) {
    $('<div title="Delete">Really delete?<div>').dialog({
		modal: true,
	    buttons: {
            "Yes": function() {
                delDataCatOK(id);
                $(this).dialog("close");
            },
			"No": function() {
                 $(this).dialog("close");
            }
		},
		width: 200,
        height: 140
	});
}

function delDataCatOK(id) {
    var data = "action=delDataCat&id=" + id;
    $.ajax({
    	type: "POST",
    	url: url + "ajax/objects/",
    	data: data,
		success: function(res) {
            renderDataTree();
		}
    })
}

function editDataCat(id) {
    var data = "action=getDataCatName&id=" + id;
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
            $("#datacatname").val(res);
		}
	});
    
    $("#editDataCat").dialog({
		modal: true,
	    buttons: {
            "Готово": function() {
                var data = "action=editDataCat&id=" + id + "&name=" + $("#datacatname").val();
            	$.ajax({
            		type: "POST",
            		url: url + "ajax/objects/",
            		data: data,
            		success: function(res) {
                        renderDataTree();
            		}
            	});
                
                $(this).dialog("close");
            }
		},
		width: 210,
        height: 160
	});
}

function rmAllDataConfirm() {
	$('<div title="Delete">Really delete?<div>').dialog({
		modal: true,
	    buttons: {
            "Yes": function() {
            	rmAllData();
                $(this).dialog("close");
            },
			"No": function() {
                 $(this).dialog("close");
            }
		},
		width: 200,
        height: 140
	});
}

function rmAllData() {
	var data = "action=rmAllData&did=" + {{ id }};
    $.ajax({
    	type: "POST",
    	url: url + "ajax/objects/",
    	data: data,
		success: function(res) {
            renderDataTree();
		}
    })
}
</script>