<h3>Информация:</h3>

<p>Название: <input type="text" name="title" id="title" style="width: 300px" /></p>

<div id="text_area">
    <textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px"></textarea>
</div>

<p>Теги: <input type="text" name="tags" id="tags" style="width: 300px" /></p>

<div style="clear: both; padding-top: 10px"><input type="button" onclick="addAI()" value="Добавить" /></div>

<script type="text/javascript">
$('#wysihtml5').wysihtml5();

function addAI() {
    var data = "action=addAdvanced&id={{ oid }}&title=" + $("#title").val() + "&text=" + $("#wysihtml5").val() + "&tags=" + $("#tags").val();
	$.ajax({
		type: "POST",
		url: url + "ajax/tt/",
		data: data,
		success: function(res) {
            document.location.href = "{{ registry.uri }}objects/show/{{ oid }}/";
		}
	})
}
</script>