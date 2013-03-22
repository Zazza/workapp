<ul class="nav nav-list">

<li class="nav-header">Sections</li>

{% if registry.args.1 == "add" %}
    <li class="active">
{% else %}
    <li>
{% endif %}
    <a href="{{ registry.uri }}objects/kb/add/"><img src="{{ registry.uri }}img/document-plus.png" alt="" border="0" /> Add</a>
</li>

{% if not registry.args.1 %}
    <li class="active">
{% else %}
    <li>
{% endif %}
    <a href="{{ registry.uri }}objects/kb/"><img src="{{ registry.uri }}img/information-button.png" alt="" border="0" /> View</a>
</li>

</ul>