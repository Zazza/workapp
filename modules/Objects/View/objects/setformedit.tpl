<h3>Форма</h3>

<form method="post" action="{{ registry.uri }}objects/setform/edit/?oaid={{ oaid }}">

	<div style="overflow: hidden" id="tview">
		<ul id="firstSort" class="tviewshow"></ul>
		<ul id="secondSort" class="tviewshow"></ul>
		<ul id="thirdSort" class="tviewshow"></ul>
	</div>
	
	<input type="hidden" name="tid" value="{{ fields.0.id }}" />
	<input type="hidden" name="ttypeid" value="{{ fields.0.ttypeid }}" />
	
	<input type="submit" name="submit" value="Изменить" />

</form>

<script type="text/javascript">
{% for part in fields %}
{% if part.view.x == 1 %}
	$("#firstSort").append('{% include 'objects/objectfieldedit.tpl' %}');
{% elseif part.view.x == 2 %}
	$("#secondSort").append('{% include 'objects/objectfieldedit.tpl' %}');
{% elseif part.view.x == 3 %}
	$("#thirdSort").append('{% include 'objects/objectfieldedit.tpl' %}');
{% else %}
	$("#firstSort").append('{% include 'objects/objectfieldedit.tpl' %}');
{% endif %}
{% endfor %}
</script>