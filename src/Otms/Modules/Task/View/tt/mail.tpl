<div id="mail{{ mail.0.id }}">

<div class="emailhead">

<div style="background-color: #DFE4EA; border-bottom: 1px solid #FFF; padding: 4px 7px">
<span style="margin-right: 10px"><b>Theme:</b> {{ mail.0.subject }}</span>
</div>

<div style="background-color: #DFE4EA; padding: 6px 4px; border-bottom: 1px solid #FFF">
{% set i = 0 %}
{% for part in mail %}
{% set i = i + 1 %}
<a data-role="button" data-inline="true" style="cursor: pointer; text-decoration: none" onclick="showText('{{ mail.0.id }}', '{{ i }}')"><span class="btn">{{ part.type }}</span></a>
{% endfor %}
</div>

{% if mail.0.attach %}
<div style="background-color: #DFE4EA; border-bottom: 1px solid #FFF; padding: 4px 7px">
{% for part in mail.0.attach %}
<a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}mail/attach/?mid={{ mail.0.id }}&filename={{ part.filename }}">{{ part.filename }}</a>
{% endfor %}
</div>
{% endif %}

</div>

{% set i = 0 %}

{% for part in mail %}
{% set i = i + 1 %}
	<iframe style="display: none" class="mailtext" id="text{{ i }}" src="{{ registry.siteName }}{{ registry.uri }}mail/load/?mid={{ mail.0.id }}&part={{ i }}" frameborder="0" width="100%" height="90%"></iframe>
{% endfor %}

</div>

<script type="text/javascript">
showText('{{ mail.0.id }}', '1');

function showText(mid, id) {
	$("#mail" + mid + " > .mailtext").hide();
	$("#mail" + mid + " > #text" + id).show();
};
</script>