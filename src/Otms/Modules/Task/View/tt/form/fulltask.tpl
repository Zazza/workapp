<div style="overflow: hidden">

<div class="tabs">

<ul>
	<li><a href="#tabs-0">Task</a></li>
	<li><a href="#tabs-1">Main</a></li>
	<li><a href="#tabs-2">Object</a></li>
	<li><a href="#tabs-3">Responsible</a></li>
	<li><a href="#tabs-4">Periods</a></li>
	<li><a href="#tabs-5">Delegate</a></li>
</ul>

<div class="tab_container" style="width: 700px">

<div id="tabs-0" class="tabcont">

	<div class="input-prepend">
		<span class="add-on"><b>Name task:</b></span>
		<input type="text" name="taskname" id="titletask" value="{{ data.0.taskname }}" style="margin-bottom: 0" class="span6" />
	</div>
	
	<!-- attach -->
	<div style="overflow: hidden; margin-top: 10px">
	<div class="alert alert-success" style="float: left; width: 500px">
		<p style="font-weight: bold">Attach files:</p>
		<div style="overflow: hidden; margin-bottom: 10px">
			<button class="btn" onclick="fromBuffer()" style="float: left; margin-right: 5px">
				<i class="icon-trash"></i>
				From buffer
			</button>
			<div style="float: left" id="fa-uploader"></div>
		</div>
		<a class="btn btn-mini btn-info" onclick="flushAttaches()">
			<i class="icon-remove icon-white"></i>
			remove
		</a>
		<div id="attach_files" style="margin-top: 10px"></div>
	</div>
	</div>
	<!-- /attach -->
	
	
	<!-- wysihtml5 -->
	<div style="overflow: hidden; margin-bottom: 10px">
	
	<div id="text_area" style="float: left">
	    <textarea id="wysihtml5" name="textfield" style="width: 600px; height: 250px">{{ data.0.textfield }}</textarea>
	</div>
	
	</div>
	<!-- /wysihtml5 -->

</div>

<div id="tabs-1" class="tabcont">

<div style="margin-bottom: 10px">
	<label class="radio inline">
		<input type="radio" id="secure" name="secure" value="0" {% if not data.0.secure %} checked {% endif %} />
		normal
	</label>
	
	<label class="radio inline">
		<input type="radio" id="secure" name="secure" value="1" {% if data.0.secure %} checked {% endif %} />
		private
	</label>
</div>

<div style="overflow: hidden">

<div style="float: left">
   <label for="select-imp" class="select">Importance:</label>
   <select name="imp" id="select-imp">
    <option value="1" {% if data.0.imp == 1 %}selected="selected"{% endif %}>1</option>
    <option value="2" {% if data.0.imp == 2 %}selected="selected"{% endif %}>2</option>
    <option value="3" {% if data.0.imp == 3 or not data.0.imp %}selected="selected"{% endif %}>3</option>
    <option value="4" {% if data.0.imp == 4 %}selected="selected"{% endif %}>4</option>
    <option value="5" {% if data.0.imp == 5 %}selected="selected"{% endif %}>5</option>
   </select>
</div>

<div style="float: left; margin-left: 30px">
	<label for="ttgid" class="select">Group:</label>
	<select name="ttgid" id="ttgid">
	    {% for part in registry.ttgroups %}
	    <option value="{{ part.id }}" {% if data.0.ttgid == part.id %}selected="selected"{% endif %}>{{ part.name }}</option>
	    {% endfor %}
	</select>
</div>

</div>

</div>

<div id="tabs-2" class="tabcont">

<div style="overflow: hidden; margin-bottom: 10px">
	<a class="btn" style="float: left" onclick="selObject()"><i class="icon-th-list"></i> Select object</a>
	<input value="{{ data.0.selObjHid }}" type="text" id="selObjHid" name="selObjHid" style="width: 50px; float: left; margin-left: 10px" />
</div>



<div id="newObj" style="margin-bottom: 20px; {% if not obj %}display: none{% endif %}">
<div id="selObj" style="margin-top: 10px">{% for part in obj %}{{ part.val }} {% endfor %}</div>
</div>

</div>

<div id="tabs-3" class="tabcont">

<div class="btn-group">
	<a class="btn" onclick="addruser()">
		<i class="icon-user"></i>
		Add responsible
	</a>
	
	<a class="btn" onclick="delRusers()">
	<i class="icon-remove-circle"></i>
	Remove
	</a>
</div>

<p id="addedusers" style="margin: 10px 0"></p>

</div>

<div id="tabs-4" class="tabcont">

<div style="float: left; width: 240px">

<div>
   <p><label for="type" class="select">Periodic:</label></p>
   <select id="type" name="type">
    <option value="0" {% if data.0.type == 0 %}selected="selected"{% endif %}>Without conditions</option>
    <option value="1" {% if data.0.type == 1 %}selected="selected"{% endif %}>Once</option>
    <option value="2" {% if data.0.type == 2 %}selected="selected"{% endif %}>Repeat</option>
   </select>
</div>

</div>

<div style="margin-left: 260px" id="advDeadline">
    <div id="global" style="height: 65px; overflow: hidden"></div>
        
    <div id="noiter" style="display: none; overflow: hidden">
        <div style="overflow: hidden">
        <p><b>duration:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="lifetime_noiter" value="{{ data.0.lifetime_noiter }}" />
            </div>
  
            <div style="float: left; width: 200px; clear: none; padding: 0 2px">
            <select name="timetype_noiter">
                <option value="min" {% if data.0.timetype_noiter == "min" %}selected="selected"{% endif %}>minutes</option>
                <option value="hour" {% if data.0.timetype_noiter == "hour" %}selected="selected"{% endif %}>hours</option>
                <option value="day" {% if data.0.timetype_noiter == "day" or not data.0.imetype_noiter %}selected="selected"{% endif %}>days</option>
            </select>
            </div>
		</div>
    </div>
    
    <div id="iter" style="display: none; overflow: hidden">
    	<div style="overflow: hidden">
        	<p><b>Repeat each:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="itertime" value="{{ data.0.itertime }}" />
            </div>
            
            <div style="float: left; width: 200px; clear: none; padding: 0 2px">
            <select name="timetype_itertime">
                <option value="day" {% if data.0.timetype_itertime == "day" or not data.0.timetype_itertime %}selected="selected"{% endif %}>days</option>
                <option value="month" {% if data.0.timetype_itertime == "month" %}selected="selected"{% endif %}>months</option>
            </select>
            </div>
        </div>
        
        <div style="overflow: hidden">    
        <p><b>duration:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="lifetime_iter" value="{{ data.0.lifetime_iter }}" />
            </div>
            
            <div style="float: left; clear: none; padding: 0 2px">
            <select name="timetype_iter">
                <option value="day" {% if data.0.timetype_iter == "day" %}selected="selected"{% endif %}>days</option>
            </select>
            </div>
        </div>
    </div>
</div>

</div>

<div id="tabs-5" class="tabcont">
<p>Assign authorship of task to other user</p>

<div class="btn-group">
	<a class="btn" onclick="delegate()">
	<i class="icon-user"></i>
	replace author
	</a>
	
	<a class="btn" onclick="delDelegate()">
	<i class="icon-remove-circle"></i>
	Remove
	</a>
</div>

<p id="delegateuser" style="margin: 10px 0">{{ data.0.delegate_user }}</p>

<div id="usersDelegateDialog" title="Select user" style="text-align: left"></div>

</div>

</div>

</div>

</div>

<div id="usersDialog" title="Select users" style="text-align: left"></div>

<script type="text/javascript">
createUploaderFA();

$('#wysihtml5').wysihtml5();

//$(function(){
	changeType();
	
	$("#type").change(function(){
		changeType();
	});
	
	$(".tabs").tabs({
		{% if obj %}
		selected: 1
		{% elseif data.0.startdate and data.0.starttime == "00:00:00" %}
		selected: 3
		{% elseif issRusers %}
		selected: 2
		{% else %}
		selected: 0
		{% endif %}
	});
//});

{% for part in data.0.attach %}
$("#attach_files").append("<div style='margin: 4px; float: left'><input type='hidden' name='attaches[]' value='/{{ part.pdirid }}/{{ part.filename }}' /><code><img border='0' src='{{ registry.uri }}img/paper-clip-small.png' alt='attach' style='position: relative; top: 1px; left: 1px' />{{ part.filename }}</code></div>");
{% endfor %}

{% for part in issRusers %}
$("#addedusers").append('{{ part.desc }}');
{% endfor %}

function flushAttaches() {
	$("#attach_files").html('');
};

$(".startdate").datepicker({
    dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    monthNamesShort: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    firstDay: 1,
    dateFormat: 'yy-mm-dd'
});

function delRusers() {
    $("#addedusers").text("");
};

function delDelegate() {
    $("#delegateuser").text("");
};

function changeType() {
    var type = $("#type").val();

    if (type == "0") {
        $("#global").show();
        $("#noiter").hide();
        $("#iter").hide();
    } else if (type == "1") {
        $("#global").hide();
        $("#noiter").show();
        $("#iter").hide();
    } else {
        $("#global").hide();
        $("#noiter").hide();
        $("#iter").show()
    }
}
</script>
