<div style="margin-bottom: 20px">

добавить:&nbsp;<input type="text" id="name" name="name" style="width: 150px; margin-right: 20px" />

<input type="button" value="Добавить" onclick="addTree()" />

</div>

<div id="litree"></div>

<div title="Правка" id="editCat" style="display: none">
    <input type="text" id="catname" style="width: 150px" />
<div>

<script type="text/javascript">
$(document).ready(function(){
    renderTree();
})

function renderTree() {
    var data = "action=getTree&id=" + {{ id }};
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

function addTree() {
    var data = "action=addTree&id=" + {{ id }} + "&name=" + $("#name").val();
	$.ajax({
		type: "POST",
		url: url + "ajax/objects/",
		data: data,
		success: function(res) {
            renderTree();
		}
	})
}

function delCat(id) {
    $('<div title="Удаление">Действительно удалить?<div>').dialog({
		modal: true,
	    buttons: {
            "Да": function() {
                delCatOK(id);
                $(this).dialog("close");
            },
			"Нет": function() {
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
    	url: url + "ajax/objects/",
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
		url: url + "ajax/objects/",
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
            		url: url + "ajax/objects/",
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