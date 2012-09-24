<h2>Настройка уведомлений</h2>

<p>Выберите, о каких событиях получать уведомления:</p>

<p id="saveNoticeSuccess" style="display: none; margin: 10px 0; color: green">Настройки сохранены!</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="task" name="task" {% if registry.ajax_notice_sets.task %}checked="checked"{% endif %} />
	задачи
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="com" name="com" {% if registry.ajax_notice_sets.com %}checked="checked"{% endif %} />
	комментарии
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="mail" name="mail" {% if registry.ajax_notice_sets.mail %}checked="checked"{% endif %} />
	почта
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="obj" name="obj" {% if registry.ajax_notice_sets.obj %}checked="checked"{% endif %} />
	объекты
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="info" name="info" {% if registry.ajax_notice_sets.info %}checked="checked"{% endif %} />
	база знаний
	</label>
</p>

<p style="padding-top: 20px"><input type="button" value="Сохранить" onclick="saveNotice()" /></p>

<script type="text/javascript">
function saveNotice() {
	if ($("#task").attr('checked')) { var task = 1; } else { var task = 0; };
	if ($("#com").attr('checked')) { var com = 1; } else { var com = 0; };
	if ($("#mail").attr('checked')) { var mail = 1; } else { var mail = 0; };
	if ($("#obj").attr('checked')) { var obj = 1; } else { var obj = 0; };
	if ($("#info").attr('checked')) { var info = 1; } else { var info = 0; };
	
	var data = "action=saveNotice&task=" + task + "&com=" + com + "&mail=" + mail + "&obj=" + obj + "&info=" + info;
	$.ajax({
		type: "POST",
		url: "{{ registry.uri }}ajax/dashboard/",
		data: data,
		success: function(res) {
			$("#saveNoticeSuccess").show();
		}
	});
}
</script>