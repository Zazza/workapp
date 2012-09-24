<ul class="nav nav-list">

<li class="nav-header">Папки</li>

{% if not registry.args and not registry.get.folder %}<li class="active">{% else %}<li>{% endif %}
<a href="{{ registry.uri }}mail/">
<img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-receive.png" alt="" border="0" />
Входящие
[<span id="main" {% if registry.mainCount > 0 %}class="bolder"{% endif %}>{{ registry.mainCount }}</span>/<span id="allMain">{{ registry.allCount }}</span>]
</a>
</li>

{% if folders %}
{% for key, part in folders %}

{% if registry.get.folder == part.id %}<li class="active">{% else %}<li>{% endif %}
	<a href="{{ registry.uri }}mail/?folder={{ part.id }}">
	<img src="{{ registry.uri }}img/left/folder-small.png" alt="" /> {{ part.folder }}
	[<span id="{{ part.id }}" {% if part.count > 0 %}class="bolder"{% endif %}>{{ part.count }}</span>/<span id="all{{ part.id }}">{{ part.all }}</span>]
	</a>
</li>

{% endfor %}
{% endif %}

{% if registry.args.0 == "send" %}<li class="active">{% else %}<li>{% endif %}
    <a href="{{ registry.uri }}mail/send/">
    <img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail-send.png" alt="" border="0" />
    Отправленные</a>
</li>

<li class="nav-header">Разделы</li>

{% if registry.args.0 == "folder" %}<li class="active">{% else %}<li>{% endif %}
    <a href="{{ registry.uri }}mail/folder/"><img style="vertical-align: middle" src="{{ registry.uri }}img/left/folder-plus.png" alt="" border="0" />
    Управление папками</a>
</li>

{% if registry.args.0 == "sort" %}<li class="active">{% else %}<li>{% endif %}
    <a href="{{ registry.uri }}mail/sort/"><img style="vertical-align: middle" src="{{ registry.uri }}img/left/sort.png" alt="" border="0" />
    Сортировка писем</a>
</li>

{% if registry.args.0 == "boxes" %}<li class="active">{% else %}<li>{% endif %}
    <a href="{{ registry.uri }}mail/boxes/"><img style="vertical-align: middle" src="{{ registry.uri }}img/left/mail.png" alt="" border="0" />
    Почтовый ящики</a>
</li>

</ul>