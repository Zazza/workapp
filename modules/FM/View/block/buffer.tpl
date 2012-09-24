<li class="dropdown">
	<a onclick="getBuffer()" class="dropdown-toggle" data-toggle="dropdown" href="#" style="text-shadow: none;">
		<i class="icon-trash"></i>
		<span id="countBuffer" class="label {% if count > 0 %}label-success{% endif %}">{{ count }}</span>
		<b class="caret" style="border-bottom-color: #0088CC; border-top-color: #0088CC"></b>
	</a>

	<ul class="dropdown-menu"><div id="clip"></div></ul>
</li>