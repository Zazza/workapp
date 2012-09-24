<div style="overflow: hidden">

<div class="tabs">

<ul>
	<li><a href="#tabs-1">Главные</a></li>
	<li><a href="#tabs-2">Объект</a></li>
	<li><a href="#tabs-3">Ответственные</a></li>
	<li><a href="#tabs-4">Сроки</a></li>
	<li><a href="#tabs-5">Делегировать</a></li>
</ul>

<div class="tab_container" style="width: 700px">

<div id="tabs-1" class="tabcont">

<div style="margin-bottom: 10px">
	<label class="radio inline">
		<input type="radio" id="secure" name="secure" value="0" {% if not data.0.secure %} checked {% endif %} />
		обычная
	</label>
	
	<label class="radio inline">
		<input type="radio" id="secure" name="secure" value="1" {% if data.0.secure %} checked {% endif %} />
		приватная
	</label>
</div>

<div style="overflow: hidden">

<div style="float: left">
   <label for="select-imp" class="select">Важность:</label>
   <select name="imp" id="select-imp">
    <option value="1" {% if data.0.imp == 1 %}selected="selected"{% endif %}>1</option>
    <option value="2" {% if data.0.imp == 2 %}selected="selected"{% endif %}>2</option>
    <option value="3" {% if data.0.imp == 3 or not data.0.imp %}selected="selected"{% endif %}>3</option>
    <option value="4" {% if data.0.imp == 4 %}selected="selected"{% endif %}>4</option>
    <option value="5" {% if data.0.imp == 5 %}selected="selected"{% endif %}>5</option>
   </select>
</div>

<div style="float: left; margin-left: 30px">
	<label for="ttgid" class="select">Проект / группа задач:</label>
	<select name="ttgid" id="ttgid">
	    {% for part in registry.ttgroups %}
	    <option value="{{ part.id }}" {% if data.0.gid == part.id %}selected="selected"{% endif %}>{{ part.name }}</option>
	    {% endfor %}
	</select>
</div>

</div>

</div>

<div id="tabs-2" class="tabcont">


<a class="btn" onclick="selObject()"><i class="icon-th-list"></i> Выбрать объект</a>
<div id="newObj" style="margin-bottom: 20px; {% if not obj %}display: none;{% endif %}">
<div id="selObj" style="margin-top: 10px">

{% for part in obj %}
<p><b>{{ part.field }}:</b>&nbsp;{{ part.val }}</p>
{% endfor %}

</div>
<input type="hidden" id="selObjHid" name="selObjHid" value="{% if obj %}{{ obj.0.id }}{% endif %}" />
</div>


</div>

<div id="tabs-3" class="tabcont">

<div class="btn-group">
	<a class="btn" onclick="addruser()">
		<i class="icon-user"></i>
		добавить ответственных
	</a>
	
	<a class="btn" onclick="delRusers()">
	<i class="icon-remove-circle"></i>
	Очистить
	</a>
</div>

<p id="addedusers" style="margin: 10px 0"></p>

</div>

<div id="tabs-4" class="tabcont">

<div style="float: left; width: 240px">

<div>
   <p><label for="type" class="select">Периодичность:</label></p>
   <select id="type" name="type">
    <option value="0" {% if data.0.type == 0 %}selected="selected"{% endif %}>Без условий</option>
    <option value="1" {% if data.0.type == 1 %}selected="selected"{% endif %}>Один раз</option>
    <option value="2" {% if data.0.type == 2 %}selected="selected"{% endif %}>Повторять</option>
   </select>
</div>

</div>

<div style="margin-left: 260px" id="advDeadline">
    <div id="global" style="overflow: hidden">
        	<p><b>Начиная с:</b></p>
        	
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="startdate_global" class="startdate" value="{{ data.0.startdate }}" />
            </div>
            
            <div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="starttime_global" class="starttime" value="{{ data.0.starttime }}" />
            </div>
    </div>

    <div id="noiter" style="display: none; overflow: hidden">
    	<div style="overflow: hidden">
        <p><b>Начиная с:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="startdate_noiter" class="startdate" value="{{ data.0.startdate }}" />
            </div>
            
            <div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="starttime_noiter" class="starttime" value="{{ data.0.starttime }}" />
            </div>
        </div>
        
        <div style="overflow: hidden">
        <p><b>продолжительность:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="lifetime_noiter" value="{{ data.0.deadline }}" />
            </div>
  
            <div style="float: left; width: 200px; clear: none; padding: 0 2px">
            <select name="timetype_noiter">
                <option value="min" {% if data.0.deadline_date == "минут" %}selected="selected"{% endif %}>минут</option>
                <option value="hour" {% if data.0.deadline_date == "часов" %}selected="selected"{% endif %}>часов</option>
                <option value="day" {% if data.0.deadline_date == "дней" or not data.0.deadline_date %}selected="selected"{% endif %}>дней</option>
            </select>
            </div>
		</div>
    </div>
    
    <div id="iter" style="display: none; overflow: hidden">
    	<div style="overflow: hidden">
        	<p><b>Повторять каждые:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="itertime" value="1" value="{{ data.0.iteration }}" />
            </div>
            
            <div style="float: left; width: 200px; clear: none; padding: 0 2px">
            <select name="timetype_itertime">
                <option value="day" {% if data.0.timetype_iteration == "day" or not data.0.timetype_iteration %}selected="selected"{% endif %}>дней</option>
                <option value="month" {% if data.0.timetype_iteration == "month" %}selected="selected"{% endif %}>месяцев</option>
            </select>
            </div>
        </div>
        
        <div style="overflow: hidden">        
        <p><b>Начиная с:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="startdate_iter" class="startdate" value="{{ data.0.startdate }}" />
            </div>
            
            <div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="starttime_iter" class="starttime" value="{{ data.0.starttime }}" />
            </div>
        </div>
        
        <div style="overflow: hidden">    
        <p><b>продолжительность:</b></p>
        	<div style="float: left; clear: none; padding: 0 2px">
            <input type="text" style="width: 90px" name="lifetime_iter" value="{{ data.0.deadline }}" />
            </div>
            
            <div style="float: left; clear: none; padding: 0 2px">
            <select name="timetype_iter">
                <option value="day" {% if data.0.deadline_date == "дней" %}selected="selected"{% endif %}>дней</option>
            </select>
            </div>
        </div>
    </div>
</div>

</div>

<div id="tabs-5" class="tabcont">
<p>Назначить авторство задачи другому пользователю</p>

<div class="btn-group">
	<a class="btn" onclick="delegate()">
	<i class="icon-user"></i>
	сменить автора
	</a>
	
	<a class="btn" onclick="delDelegate()">
	<i class="icon-remove-circle"></i>
	Очистить
	</a>
</div>

<p id="delegateuser" style="margin: 10px 0"></p>

<div id="usersDelegateDialog" title="Выбрать пользователя" style="text-align: left"></div>

</div>

</div>

</div>

</div>

<script type="text/javascript">
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

$(".startdate").datepicker({
    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
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
