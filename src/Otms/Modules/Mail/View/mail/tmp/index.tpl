<h2>{{ mailbox }}</h2>

<div id="mailbut">
<div class="btn-group">
	<button class="btn" onclick="setRead()">
		<i class="icon-ok icon-gray"></i>
		Пометить все как прочитанные
	</button>

	{% if not obj %}
	<button class="btn" onclick="clearFolderConfirm()">
		<i class="icon-trash icon-gray"></i>
		Очистить папку
	</button>
	{% endif %}
	
	<button class="btn" onclick="delMailsConfirm()">
		<i class="icon-remove icon-gray"></i>
		Удалить помеченные
	</button>
</div>
</div>


<div id="mailerhead">

{% for mail in mails %}
{% if mail.id != 0 %}

<div class="piecemail {% if not mail.read %}newmail{% endif %}" id="msg{{ mail.id }}" style="overflow: hidden; border-bottom: 1px solid #EEE; padding: 2px 4px; cursor: pointer">

<div style="float: left; width: 50px; margin-top: 17px"><input type="checkbox" name="smid" class="smid" value="{{ mail.id }}" /></div>

<div style="float: left; width: 50px; margin: 13px 0 0 15px; padding-left: 20px">
{% if mail.attach %}
<img border="0" src="{{ registry.uri }}img/paper-clip-small.png" alt="attach" />
{% else %}
&nbsp;
{% endif %}
</div>

<div class="selmail" style="margin-left: 70px" onclick="getMail('msg{{ mail.id }}')">

<div style="overflow: hidden; color: #048">

<div style="float: left; margin-right: 20px">
{% if mail.date != "0000-00-00 00:00:00" %}
{{ mail.date }}
{% else %}
{{ mail.timestamp }}
{% endif %}
</div>
<div style="float: left; overflow-x: hidden">{% if mail.personal %}{{ mail.personal }}{% else %}{{ mail.email }}{% endif %}</div>
</div>

<div style="margin: 5px 0 0 50px">
	{{ mail.subject }}
</div>

<div style="margin: 5px 0 0 50px">
	{{ mail.content }}
</div>


</div>

</div>


{% endif %}
{% endfor %}

</div>


<!-- CONTEXT MENU -->
<div class="contextMenu" id="mailMenu" style="display: none">
	<ul class="cm">
		<li id="mm_repeat"><i class="icon-repeat"></i> Ответить</li>
		<li id="mm_task"><i class="icon-plus"></i> + задача</li>
		<li id="mm_sort"><i class="icon-random"></i> Сортировка</li>
		<li id="mm_remove"><i class="icon-remove"></i> Удалить</li>
	</ul>
</div>

<script type="text/javascript">
{% if registry.get.folder %}
var fid = {{ registry.get.folder }};
{% else %}
var fid = "main";
{% endif %}

$(".piecemail").contextMenu('mailMenu', {
    bindings: {
      'mm_repeat': function(t) {
    	  var mid = t.id;
    	  var mid = mid.substr(3);
    	  window.location.href = "{{ registry.uri }}mail/compose/?action=reply&mid=" + mid;
      },
      'mm_task': function(t) {
    	  var mid = t.id;
    	  var mid = mid.substr(3);
    	  addTaskFromMail(mid);
      },
      'mm_sort': function(t) {
    	  var mid = t.id;
    	  var mid = mid.substr(3);
    	  window.location.href = "{{ registry.uri }}mail/sort/?mid=" + mid;
      },
      'mm_remove': function(t) {
    	  //var mid = t.id;
    	  //var mid = mid.substr(3);
    	  delMailConfirm();
      }
    }
});

$(document).keyup(function(e) {
	switch(e.keyCode) {
		case 46: delMailConfirm(); break;
		case 38: showUp(); break;
		case 40: showDown(); break;
		case 39: getMail($('.itemhover').attr('id')); break;
	};
});

$(".piecemail").click(function(){
	$(".piecemail").removeClass("itemhover");
	$(this).addClass("itemhover");
});

$(".selmail").mouseover(function(){
	$(this).css("background-color", "#F0F3F5");
});
$(".selmail").mouseout(function(){
	$(this).css("background-color", "transparent");
});

function setRead() {
    var data = "action=setRead&fid=" + fid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
    		$(".piecemail").removeClass("newmail");
    		$("#" + fid).removeClass("bolder").html("0");
        }
    });
}

function clearFolderConfirm() {
	$('<div title="Очистка папки">Действительно удалить все письма в папке?</div>').dialog({
		modal: true,
	    buttons: {
			"Нет": function() { $(this).dialog("close"); },
			"Да": function() { clearFolder(); $(this).dialog("close"); }
		},
		width: 280
	});
}

function clearFolder() {
    var data = "action=clearFolder&fid=" + fid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
    		$(".piecemail").hide();
    		$("#" + fid).removeClass("bolder").html("0");
        }
    });
}

function getMail(mid) {
	mid = mid.substr(3, mid.length - 3);
    var data = "action=getMail&mid=" + mid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	dataType: 'json',
    	success: function(res) {
            $.each(res, function(key, val) {
                if (key == "new") {
                    if (val) {
                        if ('{{ registry.get.folder }}' != "") {
                            var count = $("span#{{ registry.get.folder }}").text();
                			$("span#{{ registry.get.folder }}").text(count - 1);
                			if (count == 1) { $("span#{{ registry.get.folder }}").removeClass("bolder"); };
                        } else {
                        	var count = $("span#main").text();
                        	$("span#main").text($("span#main").text() - 1);
                			if (count == 1) { $("span#main").removeClass("bolder"); };
                        }
                    }
                } else if (key == "data") {
//                	$("#mailerhead").hide();
//                	$("#mailbut").hide();
                	$("#mail_body").show();
                	$("#mailman").show();
                	
                	$("#mail_body").html(val);

                	$("div#msg" + mid).removeClass("newmail");
                }
            });
        }
    });
};

function delMailConfirm() {
	$('<div title="Удаление письма">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Да": function() { delMail(); $(this).dialog("close"); },
			"Нет": function() { $(this).dialog("close"); }
		},
		width: 280
	});
}

function delMail() {
	var strmid = $('.itemhover').attr('id');
	mid = strmid.substr(3, strmid.length - 3);

    var data = "action=delMail&mid=" + mid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {    		
    		var next_id = $("div#" + strmid).next().attr("id");

    		$('.itemhover').hide();
    		$('.itemhover').remove();

    		if ('{{ registry.get.folder }}' != "") {
    			var count = $("span#all{{ registry.get.folder }}").text();
    			$("span#all{{ registry.get.folder }}").text(count - 1);
    		} else {
    			var count = $("span#allMain").text();
            	$("span#allMain").text($("span#allMain").text() - 1);
    		}
    		
    		if ($("div#" + next_id).length) {
    			$("div#" + next_id).addClass("itemhover");

    			mid = $("div#" + mid).next().attr('id');
    			getMail(next_id);
    		} else {
    			backtolist();
    		}
        }
    });
};

function delMailsConfirm() {
	$('<div title="Удалить выделенные письма">Удалить?</div>').dialog({
		modal: true,
	    buttons: {
			"Да": function() { delSelected(); $(this).dialog("close"); },
			"Нет": function() { $(this).dialog("close"); }
		},
		width: 280
	});
}

function delSelected() {
	var formData = new Array(); var i = 0;
   	$(".smid:checkbox:checked").each(function(n){
   		val = this.value;

   		formData[i] = ['"' + i + '"', '"' + val + '"'].join(":");

   		i++;
   	});

   	var json = "{" + formData.join(",") + "}";

   	delMails(json);
}

function delMails(json) {
	var data = "action=delMails&json=" + json;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
    		$(".smid:checkbox:checked").each(function(n){
    			val = this.value;
    			
    			$("#msg" + val).hide();
    		});
    	}
    });
}

function showUp() {
	var mid = $('.itemhover').attr('id');

	if ($("div#" + mid).prev().length) {
		$('.itemhover').removeClass("itemhover");
		$("div#" + mid).prev().addClass("itemhover");
	};
};

function showDown() {
	var mid = $('.itemhover').attr('id');

	if ($("div#" + mid).next().length) {
		$('.itemhover').removeClass("itemhover");
		$("div#" + mid).next().addClass("itemhover");
	};
};
</script>