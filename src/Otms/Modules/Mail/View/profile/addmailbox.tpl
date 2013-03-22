<form method="post" action="{{ registry.uri }}mail/boxes/add/">

{% if err %}
<div style="width: 150px; padding: 6px 3px; margin: 10px 0; border: 1px solid red; background-color: #FDD">
All fields are filled not!
</div>
{% endif %}

<div style="margin-bottom: 20px">
	<p><b>Mailbox</b></p>
	<p><input type="text" name="email" value="{{ post.email }}" /></p>
</div>

<div style="height: 50px; margin: 5px 0 20px 0">
	{% if not post.clear %}
	<div style="margin-bottom: 10px"><label class="checkbox"><input type="checkbox" name="clear" id="clear" />Leave mails on server</label></div>	
	{% else %}
	<div style="margin-bottom: 10px"><label class="checkbox"><input type="checkbox" name="clear" id="clear" checked="checked" />Leave mails on server</label></div>
	{% endif %}
	<div id="div_clear_days">Delete mails in: <input name="clear_days" id="clear_days" type="text" style="width: 30px" value="{{ post.clear_days }}" /> days</div>
</div>

<div style="overflow: hidden">
<div style="float: left; margin-right: 100px">
	<p style="margin-bottom: 10px"><b>Incoming mail</b></p>
	
	<p>Server</p>
	<p><input type="text" name="in_server" id="in_server" value="{{ post.in_server }}" /></p>
	<p>Login</p>
	<p><input type="text" name="in_login" id="in_login" value="{{ post.in_login }}" /></p>
	<p>Password</p>
	<p><input type="password" name="in_password" id="in_password" value="{{ post.in_password }}" /></p>
	<p>Protocol</p>
	<p>
		<select name="in_protocol" id="in_protocol">
			<option {% if post.in_protocol == "POP3" %}selected="selected"{% endif %}>POP3</option>
			<option {% if post.in_protocol == "IMAP" %}selected="selected"{% endif %}>IMAP</option>
		</select>
	</p>
	<p>Port</p>
	<p><input type="text" name="in_port" id="in_port" value="{{ post.in_port }}" /></p>
	<p>SSL</p>
	<p>
		<select name="in_ssl" id="in_ssl">
			<option value="notls" {% if post.in_ssl == "notls" %}selected="selected"{% endif %}>NO SSL</option>
			<option value="ssl" {% if post.in_ssl == "ssl" %}selected="selected"{% endif %}>SSL</option>
		</select>
	</p>
</div>

<div style="float: left">
	<p style="margin-bottom: 10px"><b>Outgoing mail</b></p>
	
	<p>Server</p>
	<p><input type="text" name="out_server" id="out_server" value="{{ post.out_server }}" /></p>
	
	<p>Authentification</p>
	<p>
		<select name="out_auth" id="out_auth">
			<option value="0" {% if post.out_auth == "0" %}selected="selected"{% endif %}>Not required</option>
			<option value="1" {% if post.out_auth == "1" %}selected="selected"{% endif %}>As for incoming mail</option>
			<option value="2" {% if post.out_auth == "2" %}selected="selected"{% endif %}>Set login and password</option>
		</select>
	</p>
	
	<div id="out_auth_param">
		<p>Login</p>
		<p><input type="text" name="out_login" id="out_login" value="{{ post.out_login }}" /></p>
		<p>Password</p>
		<p><input type="password" name="out_password" id="out_password" value="{{ post.out_password }}" /></p>
	</div>
	
	<p>Port</p>
	<p><input type="text" name="out_port" id="out_port" value="25" value="{{ post.out_port }}" /></p>
	<p>SSL</p>
	<p>
		<select name="out_ssl" id="out_ssl">
			<option value="notls" {% if post.out_ssl == "notls" %}selected="selected"{% endif %}>NO SSL</option>
			<option value="ssl" {% if post.out_ssl == "ssl" %}selected="selected"{% endif %}>SSL</option>
		</select>
	</p>
</div>
</div>

<!--  SIGNATURE -->
<div style="overflow: hidden; margin-top: 30px">
<h3>Signature</h3>
<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

<div id="text_area" style="float: left">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px">
    ___<br /><p>Best regards, {{ registry.ui.name }} {{ registry.ui.soname }}</p>
	</textarea>
</div>

</div>
<!-- /wysihtml5 -->
</div>
<!--  /SIGNATURE -->

<div style="margin-top: 20px"><input type="submit" class="btn" name="submit" value="Add" /></div>

</form>

<script type="text/javascript">
	$('#wysihtml5').wysihtml5();

	if ($("#clear").attr('checked')) {
		$("#div_clear_days").hide();
	} else {
		if ($("#in_protocol").val() == "IMAP") {
			$("#div_clear_days").show();
		}
	}
	
	$("#clear").change(function() {
		if ($("#clear").attr('checked')) {
			$("#div_clear_days").hide();
		} else {
			if ($("#in_protocol").val() == "IMAP") {
				$("#div_clear_days").show();
			}
		}
	});

	$("#in_port").val("110");

	var out_auth = '{{ post.out_auth }}';
	if (out_auth == "0") { $("#out_auth_param input").attr("disabled", "disabled"); };
	if (out_auth == "1") { $("#out_auth_param input").attr("disabled", "disabled"); };
	if (out_auth == "2") { $("#out_auth_param input").removeAttr("disabled"); };

	$("#in_protocol").change(function() {
		if ($("#in_protocol").val() == "POP3") {
			$("#in_port").val("110");
			$("#div_clear_days").hide();
			$("#clear_days").val("0");
		};
		if ($("#in_protocol").val() == "IMAP") {
			$("#in_port").val("143");
			if (!$("#clear").attr('checked')) {
				$("#div_clear_days").show();
			}
		};
	});

	$("#out_auth").change(function() {
		if ($("#out_auth").val() == "0") { $("#out_auth_param input").attr("disabled", "disabled"); };
		if ($("#out_auth").val() == "1") { $("#out_auth_param input").attr("disabled", "disabled"); };
		if ($("#out_auth").val() == "2") { $("#out_auth_param input").removeAttr("disabled"); };
	});
</script>