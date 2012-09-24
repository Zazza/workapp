<form method="post" action="{{ registry.uri }}users/editgroup/{{ registry.args.1 }}/">

<div style="margin-bottom: 50px">
<h3>Редактирование группы</h3>
<p><b>Имя группы</b></p>
<p><input name='group' type='text' size='60' value="{{ gname }}" /></p>
<p style="margin-top: 20px"><input name='editgroup' type='submit' value='Готово' /></p>
</div>

</form>