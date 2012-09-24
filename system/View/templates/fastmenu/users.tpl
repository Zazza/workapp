<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="text-shadow: none;">
		<i class="icon-user"></i>
		<span id="onlineUsers" class="label" title="online"></span>/<span id="allUsers" class="label" title="всего"></span>
		
		<b class="caret" style="border-bottom-color: #0088CC; border-top-color: #0088CC"></b>
	</a>

	<ul class="dropdown-menu unclicked">
		<div id="bottomUsers">
			<div id="listUser"></div>
			<div id="bottomBU">
				<div style="padding-bottom: 3px">
					<i class="icon-refresh" style="cursor: pointer" id="updUB" title="refresh"></i>
					<input type="hidden" name="rall" value="off" />
				</div>
				<div class="btn-group">
					<span class="btn" id="rallbu"><i class="icon-th"></i>выбрать всех</span>
					<span class="btn" id="addtaskbu"><i class="icon-user"></i>создать задачу</span>
					<span class="btn" id="addchatbu"><i class="icon-comment"></i>создать чат</span>
				</div>
			</div>
		</div>
	</ul>
</li>

<div title="Правка" id="editCat" style="display: none"><input type="text" id="catname" style="width: 150px" /></div>

<script type="text/javascript">
$(document).ready(function(){
	
	var height = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
	$("#bottomUsers").css("max-height", (height - 150));
	
	setUlist({{ ulist }});

	$("#updUB").click(function() {
		var data = "action=getUserList";
		$.ajax({
			type: "POST",
			url: "{{ registry.uri }}ajax/users/",
			data: data,
			dataType: 'json',
			success: function(res) {
				setUlist(res);
			}
		});
	});
});

function setUlist(res) {
	$.each(res, function(key, val) {
		if (key == "onlineUsers") {
			$("#onlineUsers").text(val);
			if (val > 0) { 
				$("#onlineUsers").addClass("label-success");
			} else {
				$("#onlineUsers").removeClass("label-success");
			}
		} else if (key == "allUsers") {
			$("#allUsers").text(val);
		} else if (key == "listUsers") {
			$("#listUser").html(val);
		}
	});
}
</script>