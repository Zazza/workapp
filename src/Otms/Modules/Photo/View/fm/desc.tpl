{% if desc %}
<div style="overflow: hidden;">
	{% for part in desc %}
	<span style="margin-right: 3px;" class="label" id="pdesc_{{ part.id }}">
		<a style="cursor: pointer; color: white;" onmouseover="overAXIS({{ part.x1 }}, {{ part.y1 }}, {{ part.x2 }}, {{ part.y2 }}, {{ part.width }})" onmouseout="outAXIS()" onclick="setDescConfirm('{{ part.desc }}')">{{ part.desc }}</a>
		{% if registry.auth %}<span><a style="cursor: pointer; color: #FC7;" onclick="delDesc({{ part.id }})">x</a></span>{% endif %}
	</span>
	{% endfor %}
</div>
{% endif %}

{% if tags %}
<div style="overflow: hidden;"><b>Tags:</b>
	{% for part in tags %}
	<span style="margin-right: 3px;" class="label" id="ptag_{{ part.id }}">
		<a style="cursor: pointer; color: white;" onclick="setTagConfirm('{{ part.tag }}')">{{ part.tag }}</a>
		{% if registry.auth %}<span><a style="cursor: pointer; color: #FC7;" onclick="delTag({{ part.id }})">x</a></span>{% endif %}
	</span>
	{% endfor %}
</div>
{% endif %}