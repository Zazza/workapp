<form method="post" action="{{ registry.uri }}objects/edit/{{ vals.0.tid }}/">

<p>
<a class="btn" href="{{ registry.uri }}objects/show/{{ vals.0.id }}/">
	<i class="icon-zoom-in"></i>
	Перейти к объекту
</a>
</p>

<p>
{% if email %}
<a onclick="addEmail('{{ email }}')">
	<img border="0" src="{{ registry.uri }}img/plus-button.png" alt="" style="vertical-align: middle" />
	<b>Добавить email:</b> {{ email }}
</a>

{% else %}
<a onclick="addEmail('')" class="btn">
	<img border="0" src="{{ registry.uri }}img/plus-button.png" alt="" style="vertical-align: middle" />
	<b>Добавить email</b>
</a>
{% endif %}
</p>

<div id="fieldemail"></div>

{% if vals.0.email %}
<p><b>Email</b></p>
<p><input type="text" name="email" value="{{ vals.0.email }}" /></p>
{% endif %}

{{ template }}

<input type="hidden" name="tid" value="{{ vals.0.id }}" />

<input type="submit" name="submit" value="Готово" class="btn" style="margin-top: 20px" />

</form>

<script type="text/javascript">
function addEmail(email) {
	if ($("input[name='email']").height()) {
		$("input[name='email']").val(email);
	} else {
		$("#fieldemail").html('<p><b>Email</b></p><p><input type="text" name="email" value="' + email + '" /></p>');
	}
}
</script>