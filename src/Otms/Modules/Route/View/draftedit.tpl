<div class="input-append">
	Name: <input type="text" name="bpname" id="bpname" value="{{  route.0.name  }}" style="margin-bottom: 0" /><button type="button" class="btn" onclick="saveBPName()">Save name</button>
</div>

<div class="btn-group" style="margin: 10px 0">
	<a onclick="addStep()" class="btn">+ step in route end</a>
	<a onclick="addRealRoute()" class="btn">in routes</a>
</div>

<div class="newRoute beginRoute alert alert-success">Start</div>
<div id="appendRoute">

<div id="subTable">
	<div class="st_substep">Step</div>
	<div class="st_step">Tasks</div>
	<div class="st_stepaction">Actions</div>
</div>
	
{% for part in steps %}
<div class="newRoute newStep" id="step_{{ part.step_id }}">
	
	<div class="substep"><h4>{{ part.name }}</h4></div>
	
	<div class="step"></div>

	<div class="stepaction" style="overflow: hidden; font-size: 11px">
	{% for action in part.action %}
	<div style="margin-bottom: 10px">
	{{ action.ifdataval }} {{ action.ifcon }} {{ action.ifval }}
	<br />
	<b>Go to:</b> {{ action.gotoval }}
	</div>
	{% endfor %}
	</div>
	
</div>
{% endfor %}
</div>
<div class="newRoute endRoute alert alert-error">End</div>

<!-- SUBSTEP CONTEXT MENU -->
<div class="contextMenu" id="subStepMenu" style="display: none">
	<ul class="cm">
		<li id="substep_addbefore"><i class="icon-plus"></i>&nbsp;step&nbsp;before</li>
		<li id="substep_rename"><i class="icon-pencil"></i>&nbsp;Rename</li>
		<li id="substep_remove"><i class="icon-minus"></i>&nbsp;Delete</li>
	</ul>
</div>

<!-- ACTION CONTEXT MENU -->
<div class="contextMenu" id="actionMenu" style="display: none">
	<ul class="cm">
		<li id="substep_action"><i class="icon-random"></i>&nbsp;Action</li>
	</ul>
</div>

<!-- TASK CONTEXT MENU -->
<div class="contextMenu" id="taskMenu" style="display: none">
	<ul class="cm">
		<li id="task_edit"><i class="icon-pencil"></i>&nbsp;Edit</li>
		<li id="task_remove"><i class="icon-minus"></i>&nbsp;Delete</li>
	</ul>
</div>

<!-- STEP CONTEXT MENU -->
<div class="contextMenu" id="stepMenu" style="display: none">
	<ul class="cm">
		<li id="task_add"><i class="icon-plus"></i>&nbsp;task</li>
	</ul>
</div>

<div title="Rename" id="renameStep" style="display: none"><input type="text" id="sname" /></div>
<div title="Task" id="ftask" style="display: none; text-align: left">{{ formtask }}</div>

<script type="text/javascript">
contextMenu();

$.ajax({
	type: "POST",
	url: '{{ registry.uri }}ajax/route/',
	data: "action=getTasks&rid={{ rid }}",
	dataType: 'json',
	success: function(res) {
		var taskname = "";
		$.each(res, function(key, val) {
			taskname = "No name";
			if (val["task"]) {
				$.each(val["task"], function(param, value) {
					if (param == "taskname") {
						taskname = value;
					}
				});
			}
			$("#step_" + val["step_id"] + " .step").append("<div class='info stask' id='task_" + val["tid"] + "'>" + taskname + "<div>");
		});
		
		taskContext();
	}
});

$(".newStep").each(function(){
	var height = $(this).height();
	$(".substep", this).height(height);
	$(".step", this).height(height);
	$(".stepaction", this).height(height);
});

function taskContext() {
	$(".stask").contextMenu('taskMenu', {
	    bindings: {
	      'task_edit': function(t) {
	    	  var tid = t.id;
	    	  tid = tid.substr(tid.indexOf("_")+1);

	    	  window.location.href = '{{ registry.uri }}route/task/?id=' + tid;
	      },
	      'task_remove': function(t) {
	    	var tid = t.id;
			taskRemoveConfirm(tid);
	      }
	    }
	});
}

function saveBPName() {
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=savebpname&rid={{ rid }}&name=" + $("#bpname").val()
	});
}

function addStep(){
	var step_id = 0; var tid = 0;
	
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=addstep&rid={{ rid }}",
		dataType: 'json',
		async: false,
		success: function(res) {
			$.each(res, function(key, val) {
				if (key == "step_id") {
					step_id = val;
				} else if (key == "tid") {
					tid = val;
				}
			});
		}
	});

	$("#appendRoute").append('<div class="newRoute newStep" id="step_' + step_id + '"><div class="substep">New step</div><div class="step"></div></div>');
	$("#step_" + step_id + " .step").append("<div class='info stask' id='task_" + tid + "'>Empty task<div>");
	contextMenu();
	taskContext();
}

function subStepRemove(id) {
	var step_id = id.substr(id.indexOf("_")+1);
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=stepremove&step_id=" + step_id
	});
	
	$("#" + id).hide();
	$("#" + id).remove();
}

function subStepRename(id) {
	var step_id = id.substr(id.indexOf("_")+1);
	$("#sname").val($("#" + id + " .substep").text());
	$("#renameStep").dialog({
	    buttons: {
	    	"Rename": function() {
				$("#" + id + " .substep").text($("#sname").val());
				
				$.ajax({
					type: "POST",
					url: '{{ registry.uri }}ajax/route/',
					data: "action=steprename&step_id=" + step_id + "&name=" + $("#sname").val()
				});
				
				$(this).dialog("close");
			},
			"Close": function() { $(this).dialog("close"); }
		},
		width: 300,
		height: 200
	});
}

function taskRemoveConfirm(tid) {
	$("<div title='Delete task'>You really want to delete task?</div>").dialog({
	    buttons: {
	    	"Yes": function() {
	    		taskRemove(tid);
				$(this).dialog("close");
			},
			"No": function() { $(this).dialog("close"); }
		},
		width: 250,
		height: 150
	});
}

function taskRemove(tid) {
	var id = tid.substr(tid.indexOf("_")+1);
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=delTask&tid=" + id
	});
	
	$("#" + tid).hide();
	$("#" + tid).remove();
}

function taskAdd(id) {
	step_id = id.substr(id.indexOf("_")+1);
	
	var new_tid = 0;
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=addTask&rid={{ rid }} + &step_id=" + step_id,
		async: false,
		success: function(res) {
			new_tid = res;
		}
	});

	$("#" + id + " .step").append("<div class='info stask' id='task_" + new_tid + "'>Empty task<div>");
	
	taskContext();
}

function addbefore(id) {
	step_id = id.substr(id.indexOf("_")+1);
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=addstepbefore&rid={{ rid }}&step_id=" + step_id,
		success: function(res) {
			window.location.href = '{{ registry.uri }}route/draft/edit/?id={{ rid }}';
		}
	});
}

function contextMenu() {
	$(".substep").contextMenu('subStepMenu', {
		menuStyle: {
			width: '120px'
		},
	    bindings: {
	      'substep_addbefore':  function(t) {
	    	  var id = $(t).parent().attr("id");
	    	  addbefore(id);
	      },
	      'substep_rename': function(t) {
	    	var id = $(t).parent().attr("id");
			subStepRename(id);
	      },
	      'substep_remove': function(t) {
			var id = $(t).parent().attr("id");
			subStepRemove(id);
	      }
	    }
	});
	$(".step").contextMenu('stepMenu', {
	    bindings: {
	      'task_add': function(t) {
	    	var id = $(t).parent().attr("id");
			taskAdd(id);
	      }
	    }
	});
	$(".stepaction").contextMenu('actionMenu', {
	    bindings: {
	    	'substep_action': function(t) {
		    	var id = $(t).parent().attr("id");
		    	id = id.substr(id.indexOf("_")+1);

		    	window.location.href = '{{ registry.uri }}route/action/?id=' + id;
			}
	    }
	});
}

function addRealRoute() {
	$.ajax({
		type: "POST",
		url: '{{ registry.uri }}ajax/route/',
		data: "action=addRealRoute&rid={{ rid }}",
		async: false,
		success: function(res) {
			 window.location.href = '{{ registry.uri }}route/';
		}
	});
}
</script>
