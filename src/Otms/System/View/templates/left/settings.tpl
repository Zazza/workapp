<ul class="nav nav-list">
{% if registry.ui.admin %}
<li><a href="{{ registry.uri }}settings/mail/"><i class="icon-envelope"></i> Почта</a></li>
<li><a href="{{ registry.uri }}settings/datatypes/"><i class="icon-cog"></i> Типы данных</a></li>
<li><a href="{{ registry.uri }}settings/ttgroups/"><i class="icon-th-large"></i> Проекты</a></li>
{% endif %}
</ul>