<ul class="nav nav-list">

<li class="nav-header">Разделы</li>

{% if registry.ui.admin %}
{% if registry.args.0 == "admin" or registry.args.0 == "templates" %}
    <li class="active">
{% else %}
    <li>
{% endif %}
    <a href="{{ registry.uri }}objects/admin/"><img src="{{ registry.uri }}img/g.png" alt="" border="0" /> Управление объектами</a>
</li>
{% endif %}

{% if registry.ui.admin %}
{% if registry.args.0 == "forms" %}
    <li class="active">
{% else %}
    <li>
{% endif %}
    <a href="{{ registry.uri }}objects/forms/"><img src="{{ registry.uri }}img/application-form.png" alt="" border="0" /> Управление формами</a>
</li>
{% endif %}

{% if registry.args.0 == "list" %}
    <li class="active">
{% else %}
    <li>
{% endif %}
    <a href="{{ registry.uri }}objects/"><img src="{{ registry.uri }}img/gear.png" alt="" border="0" /> Просмотр</a>
</li>

</ul>