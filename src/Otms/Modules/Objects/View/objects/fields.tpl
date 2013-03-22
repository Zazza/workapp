<div style="overflow: hidden" id="tview">
	<ul id="firstSort" class="tviewshow"></ul>
	<ul id="secondSort" class="tviewshow"></ul>
	<ul id="thirdSort" class="tviewshow"></ul>
</div>

<input type="hidden" name="tid" value="{{ fields.0.id }}" />
<input type="hidden" name="ttypeid" value="{{ fields.0.ttypeid }}" />

<input type="submit" name="submit" value="Add" style="margin-top: 20px" />

<script type="text/javascript">
{% for part in fields %}
{% if part.view.x == 1 %}
	$("#firstSort").append('{% include 'objects/objectfield.tpl' %}');
{% elseif part.view.x == 2 %}
	$("#secondSort").append('{% include 'objects/objectfield.tpl' %}');
{% elseif part.view.x == 3 %}
	$("#thirdSort").append('{% include 'objects/objectfield.tpl' %}');
{% else %}
	$("#firstSort").append('{% include 'objects/objectfield.tpl' %}');
{% endif %}
{% endfor %}
</script>