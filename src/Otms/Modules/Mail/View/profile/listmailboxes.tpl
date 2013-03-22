<div style="overflow: hidden; margin-bottom: 20px">
		<a class="btn" href="{{ registry.uri }}mail/boxes/add/">
		<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-plus.png" alt="" border="0" />
		Add mailbox
		</a>
</div>

<div>
{% for part in mailboxes %}
<div style="margin-bottom: 10px" id="mb_{{ part.id }}">
	<a style="cursor: pointer" onclick="delMailboxConfirm('{{ part.email }}', '{{ part.id }}')" title="delete"><img style="vertical-align: middle" src="{{ registry.uri }}img/delete.png" alt="delete" border="0" /></a>
	<a href="{{ registry.uri }}mail/boxes/?email={{ part.email }}">{{ part.email }}</a>
	{% if part.default %}
	<span style="color: green; margin-left: 30px">default</span>
	{% else %}
	<span style="margin-left: 30px"><a style="cursor: pointer; color: blue" onclick="setDefault('{{ part.email }}')">assign default</a></span>
	{% endif %}
</div>
{% endfor %}
</div>

<script type="text/javascript">
function delMailboxConfirm(email, id) {
	$('<div title="Deleting mailbox">Delete?</div>').dialog({
		modal: true,
	    buttons: {
			"No": function() { $(this).dialog("close"); },
			"Yes": function() { delMailbox(email, id); $(this).dialog("close"); }
		},
		width: 280
	});
}

function delMailbox(email, id) {
    var data = "action=delMailbox&email=" + email;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
        	$("#mb_" + id).hide();
        }
    });
}

function setDefault(email) {
    var data = "action=setDefault&email=" + email;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	data: data,
    	success: function(res) {
    		document.location.href = document.location.href;
        }
    });
}
</script>