<h2>Настройка уведомлений</h2>

<p>Выберите, о каких событиях получать уведомления:</p>

<p id="saveNoticeSuccess" style="display: none; margin: 10px 0; color: green">Настройки сохранены!</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="task" name="task" {% if not registry.ajax_notice_sets.task %}checked="checked"{% endif %} />
	задачи
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="com" name="com" {% if not registry.ajax_notice_sets.com %}checked="checked"{% endif %} />
	комментарии
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="mail" name="mail" {% if not registry.ajax_notice_sets.mail %}checked="checked"{% endif %} />
	почта
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="obj" name="obj" {% if not registry.ajax_notice_sets.obj %}checked="checked"{% endif %} />
	объекты
	</label>
</p>

<p style="margin: 0">
	<label class="checkbox inline">
	<input type="checkbox" id="info" name="info" {% if not registry.ajax_notice_sets.info %}checked="checked"{% endif %} />
	база знаний
	</label>
</p>

<p style="padding-top: 20px"><input type="button" value="Сохранить" onclick="saveNotice()" /></p>

<script type="text/javascript">
function saveNotice() {
	if ($("#task").attr('checked')) { var task = 0; } else { var task = 1; };
	if ($("#com").attr('checked')) { var com = 0; } else { var com = 1; };
	if ($("#mail").attr('checked')) { var mail = 0; } else { var mail = 1; };
	if ($("#obj").attr('checked')) { var obj = 0; } else { var obj = 1; };
	if ($("#info").attr('checked')) { var info = 0; } else { var info = 1; };
	
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