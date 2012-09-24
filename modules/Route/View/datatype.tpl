<select id="ifval">
{% for part in data %}
<option>{{ part.val }}</option>
{% endfor %}
</select>