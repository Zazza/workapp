<ul id="treestructure" class="filetree" style="margin-left: 10px;">
	<ul>
		<li>
			<span class='folder' title='d_0'><a class="tbranch" href="{{ registry.uri }}filemanager/?id=0">Upload</a></span>
			{{ tree }}
		</li>
	</ul>
</ul>

<script type="text/javascript">
$("#treestructure").treeview({
	persist: "location",
	collapsed: false,
	persist: "cookie"
});
</script>