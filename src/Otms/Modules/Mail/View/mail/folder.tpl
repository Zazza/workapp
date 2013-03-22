{% if err %}
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
{% endif %}

<form method="post" action="{{ registry.uri }}mail/folder/" style="margin-bottom: 20px" class="form-inline">
<p>New folder name:</p>
<p>
	<input type="text" name="folder" />
	<input type="submit" class="btn" name="submit" value="Create" />
</p>
</form>

{% if folders %}
{% for key, part in folders %}

{% if registry.get.folder == part.folder %}
<p style="margin-top: 2px" class="sellmenu">
{% else %}
<p style="margin-top: 2px" class="lmenu">
{% endif %}
	<span style="margin-right: 10px" class="mail_folder">{{ part.folder }}</span>
	<a title="edit" href="{{ registry.uri }}mail/folder/?id={{ part.id }}"><img style="vertical-align: middle; margin-right: 10px" src="{{ registry.uri }}img/edititem.gif" alt="правка" border="0" /></a>
	<a onclick="delMailDirConfirm('{{ part.id }}')" style="cursor: pointer; text-decoration: none" title="delete"><img style="position: relative; top: 3px" border="0" src="{{ registry.uri }}img/delete.png" alt="" /></a>
</p>

{% endfor %}
{% endif %}