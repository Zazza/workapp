<div id="mail{{ mail.0.id }}">

<div class="emailhead">

<div style="padding: 1px 7px">
{% if not task %}
<span style="margin-right: 10px"><b>Дата: </b>
{% if mail.0.date != "0000-00-00 00:00:00" %}
{{ mail.0.date }}
{% else %}
{{ mail.0.timestamp }}
{% endif %}
</span>
{% endif %}
<span style="margin-right: 10px"><b>Тема:</b> {{ mail.0.subject }}</span>
</div>

{% if not task %}
<div style="padding: 1px 7px">

<b>Отправитель:</b>
{% if mail.0.personal %}({{ mail.0.personal }})&nbsp;{% endif %}
<a href="mailto: {{ mail.0.email }}" style="margin-right: 10px">{{ mail.0.email }}</a>

<span style="float: right">
{% if mail.0.contact %}
	<a style="cursor: pointer; margin-right: 2px" onclick="getInfo('{{ mail.0.email }}')"><img src="{{ registry.uri }}img/information-button.png" title="полные данные" alt="info" border="0" style="position: relative; top: 1px" /></a>
{% else %}
	<a onclick="addContact('{{ mail.0.email }}')" style="cursor: pointer; margin-right: 2px" title="добавить контакт"><img src="{{registry.uri }}img/plus-button.png" alt="" border="0" style="position: relative; top: 1px" /></a>
{% endif %}
</span>

</div>
{% endif %}

{% if not task %}
<div style="padding: 1px 7px">
<b>Получатель:</b>
<a href="mailto: {{ mail.0.email }}">{{ mail.0.to }}</a>
</div>
{% endif %}

<div style="padding: 6px 4px;">
{% set i = 0 %}
{% for part in mail %}
{% set i = i + 1 %}
<a style="cursor: pointer; text-decoration: none" onclick="showText('{{ mail.0.id }}', '{{ i }}')"><span class="btn">{{ part.type }}</span></a>
{% endfor %}
</div>

{% if mail.0.attach %}
<div style="padding: 1px 7px">
{% for part in mail.0.attach %}
<a class="attach" style="margin-right: 10px; cursor: pointer" href="{{ registry.uri }}mail/attach/?mid={{ mail.0.id }}&filename={{ part.filename }}">{{ part.filename }}</a>
{% endfor %}
</div>
{% endif %}


<div style="bpadding: 1px 7px;">
<div style="float: right">
	<a class="btn"  href="{{ registry.uri }}mail/sort/?mid={{ mail.0.id }}">
	<i class="icon-random"></i>
		сортировка</a>
	</a>
	
	<a class="btn" onclick="delMailConfirm()">
		<i class="icon-remove"></i>
		удалить
	</a>
</div>

	<a class="btn" href="{{ registry.uri }}mail/compose/?action=reply&mid={{ mail.0.id }}">
		<i class="icon-repeat"></i>
		ответить
	</a>
	
	{% if not isset %}
	<a class="btn" onclick="addTaskFromMail('{{ mail.0.id }}')">
		<i class="icon-plus"></i>
		создать задачу
	</a>
	{% endif %}
</div>


</div>

{% set i = 0 %}

{% for part in mail %}
{% set i = i + 1 %}
	<iframe style="display: none" class="mailtext" id="text{{ i }}" src="{{ registry.siteName }}{{ registry.uri }}mail/load/?mid={{ mail.0.id }}&part={{ i }}" frameborder="0" width="100%" height="90%"></iframe>
{% endfor %}

</div>

<script type="text/javascript">
var height = document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;
$(".mailtext").height(height - 180 - $(".emailhead").height());

$(document).keyup(function(e) {
	switch(e.keyCode) {
		case 37: backtolist(); break;
	};
});

showText('{{ mail.0.id }}', '1');

function showText(mid, id) {
	$("#mail" + mid + " > .mailtext").hide();
	$("#mail" + mid + " > #text" + id).show();
};

function addTaskFromMail(mid) {
    var data = "action=addTaskFromMail&mid=" + mid;
    $.ajax({
    	type: "POST",
    	url: "{{ registry.uri }}ajax/mail/",
    	async: false,
    	data: data,
    	success: function(res) {
    		document.location.href = "{{ registry.uri }}tt/edit/" + res  + "/";
        }
    });
};

function addContact(email) {
    var data = "action=addContact&email=" + email;
    $.ajax({
        type: "POST",
        url: "{{ registry.uri }}ajax/mail/",
        async: false,
        data: data,
        success: function(res) {
                document.location.href = "{{ registry.uri }}objects/";
        }
    });
}
</script>