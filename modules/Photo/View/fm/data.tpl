<div>

<label class="checkbox">

<div style="margin-right: 10px">
	<input type="checkbox" name="fruser[]" id="user{{ data.uid }}" value="{{ data.uid }}" class="fg{{ data.gid }} fcusers" />
</div>

<div><b>{{ data.login }}</b></div>

</label>

<span class="mode umode gparent_mode_{{ data.gid }}" id="umode_{{ data.uid }}">
	<label class="radio" style="margin-right: 5px"><input type="radio" name="mode_u_{{ data.uid }}" value="1" /> Read</label>
	<label class="radio"><input type="radio" name="mode_u_{{ data.uid }}" value="2" /> Write</label>
</span>

</div>
