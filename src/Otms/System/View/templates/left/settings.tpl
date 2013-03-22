<ul class="nav nav-list">
{% if registry.ui.admin %}
<li><a href="{{ registry.uri }}settings/mail/"><i class="icon-envelope"></i> Mail</a></li>
<li><a href="{{ registry.uri }}settings/datatypes/"><i class="icon-cog"></i> Data type</a></li>
<li><a href="{{ registry.uri }}settings/ttgroups/"><i class="icon-th-large"></i> Project</a></li>
{% endif %}
</ul>