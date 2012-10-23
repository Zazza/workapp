{% if registry.ui.admin %}
<ul class="nav nav-list">

{% if registry.args.0 == "add" or not registry.args.0 %}
<li class="active">
{% else %}
<li>
{% endif %}
    <a href="{{ registry.uri }}route/">
    	<img style="vertical-align: middle" src="{{ registry.uri }}img/g.png" alt="" border="0" />
    	Маршруты
    </a>
</li>

{% if registry.args.1 == "dash" %}
<li class="active">
{% else %}
<li>
{% endif %}
    <a href="{{ registry.uri }}route/draft/">
    	<img style="vertical-align: middle" src="{{ registry.uri }}img/application-form.png" alt="" border="0" />
    	Черновики
    </a>
</li>

</ul>
{% endif %}