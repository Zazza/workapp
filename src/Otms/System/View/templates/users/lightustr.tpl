{% if not notcheckall %}
<p style="font-size: 11px"><label class="checkbox"><input type="checkbox" name="rall" value="1" id="rall" />Select all</label></p>
{% endif %}

<div id="us">
	<ul id="structure" class="filetree">{{ list }}</ul>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#us > #structure").treeview({
		persist: "location",
		collapsed: true
    });
    
    $('#rall').click(function(){
		if ($("#rall").attr("checked")) {
			$('#structure input:checkbox:enabled').each(function(){this.disabled = !this.disabled});
			$('#structure input:checkbox').removeAttr("checked");
		} else {
			$('#structure input:checkbox:disabled').each(function(){this.disabled = !this.disabled});
			$('#structure input:checkbox').removeAttr("checked");
		}
		
		$('#rall').removeAttr("disabled");
	});

	$('.gruser').click(function(){
		$('input.g' + $(this).val() + ':checkbox').each(function(){this.disabled = !this.disabled});
		$('#rall').removeAttr("checked");
	});

	$('.cusers').click(function(){
		$('#rall').removeAttr("checked");
	});
});
</script>