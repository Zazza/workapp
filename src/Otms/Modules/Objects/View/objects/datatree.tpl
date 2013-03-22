<ul id="structure" class="filetree"></ul>

<script type="text/javascript">
    {% for part in tree %}
        $("#structure").append("<li id='pid{{ part.id }}'><span class='folder'>&nbsp;{{ part.val }}&nbsp;<a style='cursor: pointer' onclick='editDataCat(\"{{ part.id }}\")' title='edit'><img src='{{ registry.uri }}img/highlighter-small.png' alt='edit' /></a>&nbsp;<a style='cursor: pointer' onclick='delDataCat(\"{{ part.id }}\")' title='delete'><img src='{{ registry.uri }}img/minus-small.png' alt='delete' /></a></span></li>");
    {% endfor %}
</script>