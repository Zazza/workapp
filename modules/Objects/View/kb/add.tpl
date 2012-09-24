<p>Название: <input type="text" name="title" id="title" style="width: 300px" /></p>

<!-- wysihtml5 -->
<div style="overflow: hidden; margin-bottom: 10px">

	<div id="text_area" style="float: left">
		<textarea id="wysihtml5" name="textfield" style="width: 700px; height: 300px">{{ post.textfield }}</textarea>
	</div>

</div>
<!-- /wysihtml5 -->

<p>Теги: <input type="text" name="tags" id="tags" style="width: 300px" /></p>

<div style="width: 500px">
	<input type="submit" data-inline="true" onclick="addAINote()" name="submit" value="Добавить" />
</div>

<script type="text/javascript">
	$('#wysihtml5').wysihtml5();
	
	function addAINote() {
	    var data = "action=addAdvancedNote&title=" + $("#title").val() + "&text=" + $("#wysihtml5").val() + "&tags=" + $("#tags").val();
		$.ajax({
			type: "POST",
			url: "{{ registry.uri }}ajax/tt/",
			data: data,
			success: function(res) {
				if (res != "false") { 
	            	document.location.href = "{{ registry.uri }}objects/kb/";
				} else {
					$('<div title="Уведомление">Заполнены не все поля!</div>').dialog();
				}
			}
		});
	}
</script>