{% if err %}
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
{% endif %}


<form method="post" action="{{ registry.uri }}mail/compose/">

<div class="input-prepend">
<span class="add-on"><b>To whom:</b></span>
<input name="to" type="text" class="span6" style="margin-bottom: 0" value="{{ post.to }}" />
</div>

<div class="input-prepend">
<span class="add-on"><b>Theme:</b></span>
<input name="subject" type="text" class="span6" style="margin-bottom: 0" value="{{ post.subject }}" />
</div>

<!-- attach -->
<div style="overflow: hidden; margin-top: 10px">
<div class="alert alert-success" style="float: left; width: 500px">
	<p style="font-weight: bold">Attached files:</p>
	<a class="btn btn-mini btn-info" onclick="flushAttaches()">
		<i class="icon-remove icon-white"></i>
		clear
	</a>
	<div id="attach_files" style="margin-top: 10px"></div>
</div>
</div>
<!-- /attach -->

<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

<div id="text_area" style="float: left">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px">{{ post.textfield }}</textarea>
</div>

</div>
<!-- /wysihtml5 -->

<p><b>Mailbox for sending:</b></p>
<select name="mailbox" id="mailbox">
{% if post.email %}
	{% for mailbox in mailboxes %}
	<option value="{{ mailbox.id }}" {% if post.email == mailbox.name %}selected="selected"{% endif %}>{{ mailbox.name }}</option>
	{% endfor %}
{% else %}
	{% for mailbox in mailboxes %}
	<option value="{{ mailbox.id }}" {% if mailbox.default %}selected="selected"{% endif %}>{{ mailbox.name }}</option>
	{% endfor %}
{% endif %}
</select>

<p style="margin-top: 30px">
<input type="submit" class="btn" name="submit" value="Write" />
</p>

</form>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

sign($("#mailbox option:selected").val());

$("#mailbox").change(function(){
	sign($("#mailbox option:selected").val());
});
</script>