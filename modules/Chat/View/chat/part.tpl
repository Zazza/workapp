<div id="chatuser{{ data.id }}" style="overflow: hidden; margin-bottom: 10px">
	
	<div style="float: left">
		<a style="cursor: pointer" onclick="private('{{ data.login }}')">
			<img class="avatar" style="border: 1px solid #555; max-height: 40px; max-width: 40px" src="{{ data.avatar }}" alt="аватар" />
		</a>
	</div>
	
	<div style="margin: 4px 0 0 60px; font-size: 12px">
		<a style="cursor: pointer" onclick="private('{{ data.login }}')">
			<b>{{ data.name }}<br />{{ data.soname }}</b>
		</a>
	</div>

</div>

<script type="text/javascript">
function private(name) {
	$("#message").val("/private " + name + " ");
	$("#message").focus();
}
</script>