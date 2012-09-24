<form method="post" action="{{ registry.uri }}settings/ttgroups/edit/{{ id }}/">

<div style="margin-bottom: 50px">
<h3>Редактирование проекта</h3>
<p><b>Название проекта</b></p>
<p><input name='group' type='text' size='60' value="{{ name }}" /></p>
<p><input class="btn btn-info" name='submit_group' type='submit' value='Готово' /></p>
</div>

</form>