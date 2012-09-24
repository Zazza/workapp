<form action="{{ registry.uri }}objects/" method="post" name="form_list_obj">

<div id="objl">
{% for part in list %}
<p><a href="{{ registry.uri }}objects/sub/{{ part.id }}/">{{ part.name }}</a></p>
{% endfor %}
</div>

<input name="move_confirm" id="move_confirm" type="hidden" value="no" />
<input name="tName" id="tName" type="hidden" value="" />
<input name="tTypeName" id="tTypeName" type="hidden" value="" />

</form>
