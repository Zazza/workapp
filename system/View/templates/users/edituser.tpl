<form method="post" action="{{ registry.uri }}users/edituser/{{ post.uid }}/">

{% if err %}
{% for part in err %}
<p style="color: red">{{ part }}</p>
{% endfor %}
{% endif %}

<div style="margin-bottom: 50px">
<h3>Правка данных пользователя</h3>

<p><b>Логин</b></p><p><input name='login' type='text' size='60' value="{{ post.login }}" /></p>

<p><b>Имя</b></p><p><input name='name' type='text' size='60' value="{{ post.name }}" /></p>
<p><b>Фамилия</b></p><p><input name='soname' type='text' size='60' value="{{ post.soname }}" /></p>
<p><b>Подпись</b></p><p><input name='signature' type='text' size='60' value="{{ post.signature }}" /></p>
<p><b>Email</b></p><p><input name='email' type='text' size='60' value="{{ post.email }}" /></p>

<div class="well">
<p style="margin: 7px 0">
    <label class="radio inline"><input name='priv' value="admin" type='radio' {% if post.priv == "admin" %} checked {% endif %} />&nbsp;<b>Администратор</b></label>
    <label class="radio inline"><input name='priv' value="null" type='radio' {% if post.priv == FALSE  %} checked {% endif %} />&nbsp;<b>Обычный пользователь</b></label>
</p>

<p style="margin: 7px 0"><b>Группа</b>&nbsp;
<select name="gid">
{% for part in group %}
<option value="{{ part.sid }}" {% if post.gid == part.sid %} selected="selected" {% endif %}>{{ part.sname }}</option>
{% endfor %}
</select>
</p>

<div class="form-inline">
<b>Квота</b>&nbsp;
<input class="span1" type="text" name="quota_val" value="{{ post.quota_val }}">
<select class="span1" name="quota_unit">
<option value="mb" {% if post.quota_unit == "mb" %}selected="selected"{% endif %}>Mb</option>
<option value="gb" {% if post.quota_unit == "gb" %}selected="selected"{% endif %}>Gb</option>
</select>
</div>
</div>

<div class="well">
<p style="margin: 7px 0"><b>Почтовые уведомления</b>&nbsp;
    <p><label class="checkbox inline"><input name="notify" type="checkbox" {% if post.notify %} checked {% endif %} />&nbsp;включено</label></p>
</p>

<p style="margin: 7px 0"><b>Время уведомления о задачах на день</b>&nbsp;
    <p><input type="text" name="time_notify" value="{{ post.time_notify }}" style="width: 50px; text-align: center" /></p>
</p>

<p style="margin: 7px 0"><b>Дублировать задачи в другую копию TTW:</b>
	<p><label class="checkbox inline"><input name="email_for_task" type="checkbox" {% if post.email_for_task %} checked {% endif %} />&nbsp;включено (не ставить галочку, если не ясно, что это такое!)</label></p>
</p>
</div>

<p><b>Пароль</b></p><p><input name='pass' type='password' value="{{ post.pass }}" /></p>

<p style="margin-top: 20px"><input name='edituser' type='submit' value='Готово' /></p>
</div>

</form>