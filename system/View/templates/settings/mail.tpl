<form method="post" action="{{ registry.uri }}settings/mail/">

{% if err %}
<div style="width: 150px; padding: 6px 3px; margin: 10px 0; border: 1px solid red; background-color: #FDD">
Заполнены не все поля!
</div>
{% endif %}

<div style="overflow: hidden">

<p style="font-size: smaller">SMTP сервер для отправки почтовых сообщений</p>

<p>Email</p>
<p><input type="text" name="email" id="email" value="{{ post.email }}" /></p>

<div>
	<p style="margin-bottom: 10px"><b>Исходящая почта</b></p>
	
	<p>Сервер</p>
	<p><input type="text" name="server" id="server" value="{{ post.server }}" /></p>
	
	<p>Аутентификация</p>
	<p>
		<select name="auth" id="auth">
			<option value="0" {% if post.auth == "0" %}selected="selected"{% endif %}>Не требуется</option>
			<option value="1" {% if post.auth == "1" %}selected="selected"{% endif %}>Задать логин и пароль</option>
		</select>
	</p>
	
	<div id="auth_param">
		<p>Логин</p>
		<p><input type="text" name="login" id="login" value="{{ post.login }}" /></p>
		<p>Пароль</p>
		<p><input type="password" name="password" id="password" value="{{ post.password }}" /></p>
	</div>
	
	<p>Порт</p>
	<p><input type="text" name="port" id="port" value="{{ post.port }}" /></p>
	<p>SSL</p>
	<p>
		<select name="ssl" id="ssl">
			<option value="notls" {% if post.ssl == "notls" %}selected="selected"{% endif %}>NO SSL</option>
			<option value="ssl" {% if post.ssl == "ssl" %}selected="selected"{% endif %}>SSL</option>
		</select>
	</p>
</div>

</div>

<div style="margin-top: 20px"><input type="submit" name="submit" value="Готово" /></div>

</form>

<script type="text/javascript">
	if ({{ post.auth }} == "0") { $("#auth_param input").attr("disabled", "disabled"); };
	if ({{ post.auth }} == "1") { $("#auth_param input").removeAttr("disabled"); };

	$("#auth").change(function() {
		if ($("#auth").val() == "0") { $("#auth_param input").attr("disabled", "disabled"); };
		if ($("#auth").val() == "1") { $("#auth_param input").removeAttr("disabled"); };
	});
</script>