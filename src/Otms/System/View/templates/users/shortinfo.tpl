<div style="overflow: hidden; margin-bottom: 10px" class="ul" id="userid_{{ user.uid }}" onclick="usersel({{ user.uid }})">

<!-- HIDDEN ELEMENTS -->
<input type="hidden" name="ruser[]" value="{{ user.uid }}" />
<input type="hidden" name="sid" value="{{ user.gid }}" />

	<div style="float: left">
		<img src="{{ user.avatar }}" alt="avatar" style="{% if status %}border-color: green;{% else %}border-color: red;{% endif %} border-width: 1px; border-style: solid; padding: 2px; width: 30px; margi
n: 10px 10px 0 0" />
	</div>

	<div style="margin-left: 65px; margin-top: 2px">
		<div style="color: black; text-align: left">{{ user.name }}&nbsp;{{ user.soname }}</div>
		<div style="padding-top: 4px" class="btn-group">
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="info" onclick="getUserInfo({{ user.uid }})"><i class="icon-info-sign"></i></a>
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="tasks" href="{{ registry.uri }}users/tasks/{{ user.uid }}/"><i class="icon-tasks"></i></a>
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="calendar" href="{{ registry.uri }}calendar/?uid={{ user.uid }}"><i class="icon-calendar"></i></a>
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="message" onclick="sendMsg({{ user.uid }})"><i class="icon-envelope"></i></a>
						
			{% if registry.ui.admin %}
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="edit" href="{{ registry.uri }}users/edituser/{{ user.uid }}/"><i class="icon-pencil"></i></a>
			<a class="btn btn-mini" style="clear: none; display: inline; padding: 0 6px 1px" title="delete" onclick="delUserConfirm({{ user.uid }})"><i class="icon-remove"></i></a>
			{% endif %}
		</div>
	</div>
</div>