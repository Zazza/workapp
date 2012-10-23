{% if registry.ui.admin %}

<hr />

<ul class="nav nav-list">

<li class="nav-header">Переменные:</li>

{% for part in data %}
<h4>Этап: {{ part.step_name }}</h4>

<li style="margin-bottom: 10px">
<h5>Задача: {{ part.task_name }}</h5>

{% for val in part %}

{% if val.name %}
<p><code>$[{{ val.id }}]</code> $: {{ val.name }}</p>
{% endif %}

{% endfor %}

</li>
{% endfor %}

</ul>
{% endif %}