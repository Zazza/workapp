{% for mail in mails %}
<div class="piecemail newmail" id="{{ mail.id }}" onclick="getMail('{{ mail.id }}')" style="overflow: hidden; border-bottom: 1px solid #EEE; padding: 2px 4px; font-size: 10px; cursor: pointer">
<div style="float: left; width: 130px">{{ mail.date }}</div>
<div style="float: left; width: 200px; overflow-x: hidden">{% if mail.personal %}{{ mail.personal }}{% else %}{{ mail.mailbox }}@{{ mail.host }}{% endif %}</div>
<div style="float: left; width: 20px">
{% if mail.attach %}
<img border="0" src="{{ registry.uri }}img/paper-clip-small.png" alt="attach" />
{% else %}
&nbsp;
{% endif %}
</div>
<div style="margin-left: 364px">{{ mail.subject }}</div>
</div>
{% endfor %}

<script type="text/javascript">
$(".piecemail").click(function(){
	$(".piecemail").removeClass("itemhover");
	$(this).addClass("itemhover");
	$(this).removeClass("newmail");
});
</script>