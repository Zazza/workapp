<div style="margin-bottom: 20px">
	add:&nbsp;<input type="text" id="name" name="name" style="width: 150px; margin-right: 20px" />
	<input type="button" value="Add" onclick="addTree()" />
</div>

<div id="litree"></div>

<script type="text/javascript">
$(document).ready(function(){
    renderTree();
})

function renderTree() {
    var data = "action=getTree&pid=" + {{ registry.args.2 }};
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/users/",
		data: data,
		success: function(res) {
            $("#litree").html(res);            
            $("#structure").treeview();
		}
	})
}

function addTree() {
    var data = "action=addTree&pid=" + {{ registry.args.2 }} + "&name=" + $("#name").val();
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/users/",
		data: data,
		success: function(res) {
            renderTree();
		}
	})
}

function delCat(id) {
    $('<div title="Delete">Really delete?<div>').dialog({
		modal: true,
	    buttons: {
            "Yes": function() {
                delCatOK(id);
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

function delCatOK(id) {
    var data = "action=delCat&id=" + id;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/users/",
    	data: data,
		success: function(res) {
            renderTree();
		}
    })
}

function editCat(id) {
    var data = "action=getCatName&id=" + id;
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/users/",
		data: data,
		success: function(res) {
            $("#catname").val(res);
		}
	});
    
    $("#editCat").dialog({
		modal: true,
	    buttons: {
            "Готово": function() {
                var data = "action=editCat&id=" + id + "&name=" + $("#catname").val();
            	$.ajax({
            		type: "POST",
            		url: "{{ registry.uri }}ajax/users/",
            		data: data,
            		success: function(res) {
                        renderTree();
            		}
            	});
                
                $(this).dialog("close");
            }
		},
		width: 200,
        height: 140
	});
}
</script>