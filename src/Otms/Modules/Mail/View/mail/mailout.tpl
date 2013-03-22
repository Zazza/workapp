<div class="emailhead">

<div style="padding: 1px 4px">
<span style="margin-right: 10px"><b>Date: </b>
{{ mail.0.timestamp }}
</span>
<span style="margin-right: 10px"><b>Theme:</b> {{ mail.0.subject }}</span>
</div>
<div style="padding: 1px 4px">
<b>Sender:</b>
<a href="mailto: {{ mail.0.email }}">{{ mail.0.email }}</a>
</div>
<div style="padding: 1px 4px">
<b>Addressee:</b>
<a href="mailto: {{ mail.0.email }}">{{ mail.0.to }}</a>
</div>
<div style="padding: 6px 4px;">
{% set i = 0 %}
{% for part in mail %}
{% set i = i + 1 %}
<a style="cursor: pointer; text-decoration: none" onclick="showText('{{ i }}')"><span class="btn btn-mini">{{ part.type }}</span></a>
{% endfor %}
</div>
{% if mail.0.attach %}
<div style="padding: 1px 4px">
{% for part in mail.0.attach %}
<a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}mail/attach/?mid={{ mail.0.id }}&filename={{ part.filename }}&type=out">{{ part.filename }}</a>
{% endfor %}
</div>
{% endif %}
<div style="padding: 1px 4px; overflow: hidden">
<div style="float: right">
	<a class="btn btn-mini" onclick="delMailConfirm()">
		<i class="icon-remove"></i>
		delete
	</a>
</div>
</div>

</div>

{% set i = 0 %}

{% for part in mail %}
{% set i = i + 1 %}
	<iframe style="display: none" class="mailtext" id="text{{ i }}" src="{{ registry.siteName }}{{ registry.uri }}mail/load/?out=1&mid={{ mail.0.id }}&part={{ i }}" frameborder="0" width="100%" height="90%"></iframe>
{% endfor %}

<script type="text/javascript">
var height = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
$(".mailtext").height(height - 180 - $(".emailhead").height());

$(document).keyup(function(e) {
	switch(e.keyCode) {
		case 37: backtolist(); break;
	};
});

showText('1');

function showText(id) {
	$(".mailtext").hide();
	$("#text" + id).show();
};
</script>