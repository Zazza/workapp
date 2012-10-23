<ul id="structure" class="filetree"></ul>

<script type="text/javascript">
    {% for part in tree %}
        $("#structure").append("<li id='pid{{ part.id }}'><span class='folder'>&nbsp;{{ part.val }}&nbsp;<a style='cursor: pointer' onclick='editDataCat(\"{{ part.id }}\")' title='правка'><img src='{{ registry.uri }}img/highlighter-small.png' alt='правка' /></a>&nbsp;<a style='cursor: pointer' onclick='delDataCat(\"{{ part.id }}\")' title='удалить'><img src='{{ registry.uri }}img/minus-small.png' alt='удалить' /></a></span></li>");
    {% endfor %}
</script>