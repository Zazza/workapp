<form method="post" action="{{ registry.uri }}route/draft/edit/?id={{ task.rid }}">
<input type="hidden" name="tid" value="{{ task.tid }}" />
{{ formtask }}

<div class="well" style="margin-top: 10px">
<h3>Result:</h3>
{% include "result.tpl" %}
</div>

<input type="submit" name="submit" class="btn" value="Done" />
</form>