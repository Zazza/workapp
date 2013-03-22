<h2>{{ mailbox }}</h2>

<div id="mailbut">
<div class="btn-group">
	<button class="btn" onclick="clearFolderConfirm()">
		<i class="icon-trash icon-gray"></i>
		Clear folder
	</button>
	
	<button class="btn" onclick="delMailsConfirm()">
		<i class="icon-remove icon-gray"></i>
		Delete marked
	</button>
</div>
</div>

<div id="mailerhead">

{% for mail in mails %}
{% if mail.id != 0 %}

<div class="piecemail" id="msg{{ mail.id }}" style="overflow: hidden; border-bottom: 1px solid #EEE; padding: 2px 4px; cursor: pointer">

<div style="float: left; width: 50px; margin-top: 17px"><input type="checkbox" name="smid" class="smid" value="{{ mail.id }}" /></div>

<div style="float: left; width: 50px; margin: 13px 0 0 15px; padding-left: 20px">
{% if mail.attach %}
<img border="0" src="{{ registry.uri }}img/paper-clip-small.png" alt="attach" />
{% else %}
&nbsp;
{% endif %}
</div>

<div class="selmail" style="margin-left: 70px" onclick="getMailOut('msg{{ mail.id }}')">

<div style="overflow: hidden; color: #048">

<div style="float: left; margin-right: 20px">
{% if mail.date != "0000-00-00 00:00:00" %}
{{ mail.date }}
{% else %}
{{ mail.timestamp }}
{% endif %}
</div>
<div style="float: left; overflow-x: hidden">{% if mail.personal %}{{ mail.personal }}{% else %}{{ mail.to }}{% endif %}</div>
</div>

<div style="margin: 5px 0 0 50px">{{ mail.subject }}</div>

</div>

</div>

{% endif %}
{% endfor %}

</div>

<span class="btn btn-small" id="mailman" style="margin-bottom: 10px; display: none" onclick="backtolist()">
	<img alt="back" src="{{ registry.uri }}img/back.png" style="vertical-align: middle">
</span>

<div id="mail_body" style="display: none"></div>

<script type="text/javascript">
$(document).keyup(function(e) {
	switch(e.keyCode) {
		case 46: delMailConfirm(); break;
		case 38: showUp(); break;
		case 40: showDown(); break;
		case 39: getMailOut($('.itemhover').attr('id')); break;
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

function backtolist() {
	$("#mailerhead").show();
	$("#mail_body").hide();
	$("#mailbut").show();
	$("#mailman").hide();
}

function getMailOut(mid) {
	mid = mid.substr(3, mid.length - 3);
    var data = "action=getMailOut&mid=" + mid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
    		$("#mailerhead").hide();
        	$("#mailbut").hide();
        	$("#mail_body").show();
        	$("#mailman").show();
        	$("#mail_body").html(res);
        }
    });
};

function delMailsConfirm() {
	$('<div title="Delete selected mails">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"Yes": function() { delSelected(); $(this).dialog("close"); },
			"No": function() { $(this).dialog("close"); }
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

function clearFolderConfirm() {
	$('<div title="Folder cleaning">Really delete all mails in folder?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { clearFolder(); $(this).dialog("close"); }
		},
		width: 280
	});
}

function clearFolder() {
    var data = "action=clearFolder&fid=out";
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

function delMails(json) {
	var data = "action=delMailsOut&json=" + json;
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

function delMailConfirm() {
	$('<div title="Deleting mail">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"Yes": function() { delMailOut(); $(this).dialog("close"); },
			"No": function() { $(this).dialog("close"); }
		},
		width: 280
	});
}

function delMailOut() {
	var strmid = $('.itemhover').attr('id');
	mid = strmid.substr(3, strmid.length - 3);

    var data = "action=delMailOut&mid=" + mid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {    		
    		var next_id = $("div#" + strmid).next().attr("id");

    		$('.itemhover').hide();
    		$('.itemhover').remove();
    		
    		if ($("div#" + next_id).length) {
    			$("div#" + next_id).addClass("itemhover");

    			mid = $("div#" + mid).next().attr('id');
    			getMailOut(next_id);
    		} else {
    			backtolist();
    		}
        }
    });
};

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