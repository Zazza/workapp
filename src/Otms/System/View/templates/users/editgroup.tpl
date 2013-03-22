<form method="post" action="{{ registry.uri }}users/editgroup/{{ registry.args.1 }}/">

<div style="margin-bottom: 50px">
<h3>Edit group</h3>
<p><b>Group name</b></p>
<p><input name='group' type='text' size='60' value="{{ gname }}" /></p>
<p style="margin-top: 20px"><input name='editgroup' type='submit' value='Done' /></p>
</div>

</form>