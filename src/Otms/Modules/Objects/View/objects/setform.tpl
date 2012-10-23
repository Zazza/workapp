<h3>Форма</h3>

<form method="post" action="{{ registry.uri }}objects/setform/?oid={{ oid }}&fid={{ fid }}">

<div id="fields"></div>

<input type="submit" name="submit" value="Добавить" />

</form>

<script type="text/javascript">
var data = "action=getFormFields&id={{ fid }}";
$.ajax({
	type: "POST",
	url: "{{ registry.uri }}ajax/objects/",
	data: data,
	success: function(res) {
        $("#fields").html(res);
	}
});
</script>