{% for key, subgroups in listUsers %}
<div style="overflow: hidden">
	<div style="float: left" class="pgbu {% if registry.users_sets.gr[key] == "1" %}minusbu{% else %}plusbu{% endif %}">&nbsp;</div>
	<div style="float: left" class="tpgbu">{{ key }}</div>
</div>
<div class="pubu" name="{{ key }}" {% if registry.users_sets.gr[key] != "1" %}style="display: none"{% endif %}>
{% for key, val in subgroups %}
<div style="overflow: hidden">
	<div style="float: left" class="gbu {% if registry.users_sets.sub[key] == "1" %}minusbu{% else %}plusbu{% endif %}">&nbsp;</div>
	<div style="float: left" class="tgbu">{{ key }}</div>
	<div style="float: left; cursor: pointer" class="selgbu">[+/-]</div>
	<input type="hidden" name="gruser" value="off" />
</div>
<div class="ubu" name="{{ key }}" {% if registry.users_sets.sub[key] != "1" %}style="display: none"{% endif %}>
	{% for user in val %}
		{{ user }}
	{% endfor %}
</div>
{% endfor %}
</div>
{% endfor %}

<script type="text/javascript">
$(".pgbu").click(function() {
	var key = $(this).next().text();
	$(".pubu[name='" + key + "']").slideToggle();
	
	if ($(this).attr('class') == "pgbu minusbu") {
		setSet($(this).next().text(), "gr", "0");
		$(this).removeClass("minusbu");
		$(this).addClass("plusbu");
	} else if ($(this).attr('class') == "pgbu plusbu") {
		setSet($(this).next().text(), "gr", "1");
		$(this).removeClass("plusbu");
		$(this).addClass("minusbu");
	}
});

$(".gbu").click(function() {
	var key = $(this).next().text();
	$(".ubu[name='" + key + "']").slideToggle();
	
	if ($(this).attr('class') == "gbu minusbu") {
		setSet($(this).next().text(), "sub", "0");
		$(this).removeClass("minusbu");
		$(this).addClass("plusbu");
	} else if ($(this).attr('class') == "gbu plusbu") {
		setSet($(this).next().text(), "sub", "1");
		$(this).removeClass("plusbu");
		$(this).addClass("minusbu");
	}
});

$(".selgbu").on("click", function(){
	var key = $(this).prev().text();
	
	if ($("input[name=rall]").val() == "off") {
		if ($(this).next().val() == "off") {
			$(".ubu[name='" + key + "'] .ul").addClass("userSel");
			var sid = $(".ubu[name='" + key + "'] > .ul input[name='sid']").val();
			$(this).next().val(sid);
		} else {
			$(".ubu[name='" + key + "'] .ul").removeClass("userSel");
			$(this).next().val("off");
		}
	}
});

function setSet(val, type, b) {
	var data = "action=setSet&key=bu&val=" + val + "&bool=" + b + "&type=" + type;
	$.ajax({
		type: "POST",
		url: url + "ajax/users/",
		data: data
	});
}
</script>
