 <h3>Правка вида шаблона</h3>
 
<ul class="nav nav-tabs">
	<li><a href="{{ registry.uri }}objects/templates/edit/{{ tid }}/">Шаблон</a></li>
	<li class="active"><a href="{{ registry.uri }}objects/templates/editview/{{ tid }}/">Вид</a></li>
</ul>

<div style="overflow: hidden" id="tview">
	<ul id="firstSort" class="otherlist"><p style="text-align: center">1 колонка</p></ul>
	<ul id="secondSort" class="otherlist"><p style="text-align: center">2 колонка</p></ul>
	<ul id="thirdSort" class="otherlist"><p style="text-align: center">3 колонка</p></ul>
</div>

<div id="tViewSave" title="Сохранение" style="display: none">
    <p style="text-align: center">
        <img src="{{ registry.uri }}img/ajax-loader.gif" alt="ajax-loader.gif" border="0" />
    </p>
</div>

<script type="text/javascript">
$(function(){
{% for part in post %}
{% if part.view.x == 1 %}
	$("#firstSort").append('{% include 'objects/templatelifield.tpl' %}');
{% elseif part.view.x == 2 %}
	$("#secondSort").append('{% include 'objects/templatelifield.tpl' %}');
{% elseif part.view.x == 3 %}
	$("#thirdSort").append('{% include 'objects/templatelifield.tpl' %}');
{% else %}
	$("#firstSort").append('{% include 'objects/templatelifield.tpl' %}');
{% endif %}
{% endfor %}

	$(".ui-state-default").resizable({
		stop: function(event, ui) {
			updateSize($(this).attr("id"));
		}
	});

	$(".otherlist").sortable({
		connectWith: '.otherlist',
		tolerance: 'pointer',
		stop: function(event, ui) {
			updateSortable();
		}
	});
});

function updateSize(fid) {
	var w = $("#" + fid).width(); 
	var h = $("#" + fid).height();
	fid = fid.substr(2);
	
	var data = "action=setTemplateViewSize&tid=" + {{ tid }} + "&fid=" + fid + "&w=" + w + "&h=" + h;
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/objects/",
		data: data
	});
}

function updateSortable() {
	$("#tview .otherlist li").each(function(n){
		var pid = $(this).parent().attr("id");
		var w = $(this).width();
		var h = $(this).height();
		var fid = $(this).attr('id');
		
		var x = 0;
		if (pid == "firstSort") {
			x = 1;
		} else if (pid == "secondSort") {
			x = 2;
		} else if (pid == "thirdSort") {
			x = 3;
		}
	    var y = $(this).prevAll().length + 1;
		fid = fid.substr(2);
		
		$("#tViewSave").dialog({ modal: true });
		var data = "action=setTemplateViewXY&tid=" + {{ tid }} + "&fid=" + fid + "&x=" + x + "&y=" + y;
		$.ajax({
			type: "POST",
			url: "{{ registry.uri }}ajax/objects/",
			data: data,
			success: function(res) {
				$("#tViewSave").dialog("close");
			}
		});
	});
}
</script>