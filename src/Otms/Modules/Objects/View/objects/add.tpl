<h3>New object</h3>

<form method="post" action="{{ registry.uri }}objects/add/{{ pname }}/">

{% if email %}
<p style="margin: 10px 0">
<img border="0" src="{{ registry.uri }}img/plus-button.png" alt="" style="vertical-align: middle" />
<a onclick="addEmail('{{ email }}')" style="cursor: pointer; text-decoration: none">
<b>Add email:</b> {{ email }}
</a>
</p>
{% else %}
<p style="margin: 10px 0">
<img border="0" src="{{ registry.uri }}img/plus-button.png" alt="" style="vertical-align: middle" />
<a onclick="addEmail('')" style="cursor: pointer; text-decoration: none">
<b>Add email</b>
</a>
</p>
{% endif %}

<div id="fieldemail"></div>

<div id="fields"></div>

</form>

<script type="text/javascript">
var data = "action=getTemplateFields&id={{ pname }}";
$.ajax({
	type: "POST",
	url: "{{ registry.uri }}ajax/objects/",
	data: data,
	success: function(res) {
        $("#fields").html(res);
	}
});

function addEmail(email) {
	$("#fieldemail").html('<p><b>Email</b></p><p><input type="text" name="email" value="' + email + '" /></p>');
}
</script>