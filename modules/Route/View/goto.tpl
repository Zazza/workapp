<div style="margin: 10px">
<div><b>Условие: </b>{{ ifdataval }} {{ ifcon }} {{ ifval }}</div>
<div><b>Действие: </b>{{ gotoval }}</div>

<input type="hidden" name="ifdata[]" value="{{ ifdata }}" />
<input type="hidden" name="ifcon[]" value="{{ ifcon }}" />
<input type="hidden" name="ifval[]" value="{{ ifval }}" />
<input type="hidden" name="goto[]" value="{{ goto }}" />

<a class="delStepAction btn btn-mini">удалить</a>
</div>