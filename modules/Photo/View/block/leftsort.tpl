<ul class="nav nav-list">

{% if fav %}
<li style="margin-bottom: 10px;"><span class="alert alert-info">Favorites</span></li>
{% elseif sel %}
<li style="margin-bottom: 10px;"><span class="alert alert-info">Sort changed</span></li>
{% endif %}
<li><a onclick="resetSort()" class="btn"><i class="icon-remove-circle"></i> Reset</a></li>

</ul>
<script type="text/javascript">
function resetSort() {
	$.ajax({
    	type: "POST",
    	url: '{{ registry.uri }}ajax/photo/',
    	data: "action=delSort",
    	success: function(res) {
    		window.location.href = "{{ registry.siteName }}{{ registry.uri }}fm/";
    	}
    });
}
</script>