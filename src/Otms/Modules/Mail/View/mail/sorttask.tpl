<!-- tabs -->
{{ formtask }}
<!-- /tabs -->

<div id="usersDialog" title="Choice users" style="text-align: left"></div>

<script type="text/javascript">
$(".startdate").datepicker({
    dayName: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    monthNamesShort: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
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