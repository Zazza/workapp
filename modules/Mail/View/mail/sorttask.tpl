<!-- tabs -->
{{ formtask }}
<!-- /tabs -->

<div id="usersDialog" title="Выбор пользователей" style="text-align: left"></div>

<script type="text/javascript">
$(".startdate").datepicker({
    dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
    dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
    monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    firstDay: 1,
    dateFormat: 'yy-mm-dd'
});

function delRusers() {
    $("#addedusers").text("");
}

{% if issRusers %}
{% for part in issRusers %}
	$("#addedusers").append('{{ part.desc }}');
{% endfor %}
{% endif %}
</script>