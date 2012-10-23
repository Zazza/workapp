<div style="overflow: hidden" id="tview">
	<ul id="firstSort" class="tviewshow"></ul>
	<ul id="secondSort" class="tviewshow"></ul>
	<ul id="thirdSort" class="tviewshow"></ul>
</div>

<input type="hidden" name="tid" value="{{ fields.0.id }}" />
<input type="hidden" name="ttypeid" value="{{ fields.0.ttypeid }}" />

<script type="text/javascript">
{% for part in fields %}
{% if part.view.x == 1 %}
	$("#firstSort").append('{% include 'kb/formfield.tpl' %}');
{% elseif part.view.x == 2 %}
	$("#secondSort").append('{% include 'kb/formfield.tpl' %}');
{% elseif part.view.x == 3 %}
	$("#thirdSort").append('{% include 'kb/formfield.tpl' %}');
{% else %}
	$("#firstSort").append('{% include 'kb/formfield.tpl' %}');
{% endif %}
{% endfor %}
</script>