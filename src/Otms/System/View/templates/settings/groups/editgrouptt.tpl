<form method="post" action="{{ registry.uri }}settings/ttgroups/edit/{{ id }}/">

<div style="margin-bottom: 50px">
<h3>Project editing</h3>
<p><b>Project name</b></p>
<p><input name='group' type='text' size='60' value="{{ name }}" /></p>
<p><input class="btn btn-info" name='submit_group' type='submit' value='Done' /></p>
</div>

</form>