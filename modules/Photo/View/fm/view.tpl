<div id="fsRes" style="display: none;">

<div style='min-height: 105px; margin-top: 5px; overflow: hidden;'>
<div style='float: left;'>
<p><b>Filename: </b><span id="pFname"></span></p>
<p style='padding: 4px 0;'>
	<a style='cursor: pointer; padding-right: 7px;' onclick='$.fancybox.prev();' title='prev'><img src='{{ registry.uri }}img/arrow_left2.png' alt='left' /></a>
	<a style='cursor: pointer; padding-right: 14px;' onclick='$.fancybox.next();' title='next'><img src='{{ registry.uri }}img/arrow_right2.png' alt='right' /></a>
	<a style='cursor: pointer; padding-right: 14px;' onclick='slidePlay()' title='slideshow'><img id='slideimg' src='{{ registry.uri }}img/hourglass2.png' alt='play' /></a>
	<a style='cursor: pointer; padding-right: 7px;' onclick='outAXIS()' title='erase selected'><img src='{{ registry.uri }}img/location.png' alt='erase crop' /></a>
	{% if registry.auth %}
	<a style='cursor: pointer; padding-right: 7px;' onclick='favorite()' title='add favorite'><img src='{{ registry.uri }}img/star.png' alt='favorite' /></a>
	{% endif %}
</p>
<div id='ajaxdesc'></div>
</div>

<div id="fsResText" style="float: right;">
	<p style="text-align: right;">
		<a style="cursor: pointer; font-weight: bold; color: black; text-decoration: underline;" onclick="showNotes()">Notes (<span id="numNotes"></span>)</a>
	</p>

	{% if registry.auth %}
	<input type="text" id="pdesc" />
	<input type="button" class="btn" value="Save" onclick="saveSort()" style="position: relative; bottom: 5px;" />
	{% endif %}
	
	<input type="hidden" id="x1" />
	<input type="hidden" id="y1" />
	<input type="hidden" id="x2" />
	<input type="hidden" id="y2" />
	<input type="hidden" id="md5" />
</div>

</div>

</div>