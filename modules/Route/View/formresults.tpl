<div id="taskResults">
{% for part in form %}
	{% if part.type == 0 %}
		<div style="margin: 10px 0">{{ part.name }} <input type="text" id="k_{{ part.id }}" class="valRes" /></div>
	{% elseif part.type == 1 %}
		<div style="margin: 10px 0">{{ part.name }} <textarea id="k_{{ part.id }}" class="valRes" ></textarea></div>
	{% elseif part.type == 2 %}
		<div style="margin: 10px 0">
		{{ part.name }} 
		<select id="k_{{ part.id }}" class="valRes" >
		{% for val in part.select %}
		<option value="{{ val.id }}">{{ val.val }}</option>
		{% endfor %}
		</select>
		</div>
	{% endif %}
{% endfor %}
</div>