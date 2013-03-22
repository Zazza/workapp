<li class="nav-header">Quota:</li>

<div style="padding: 3px 0 1px 0; text-align: left">
	{{ now }} of {{ quota }}
</div>
<div style="border: 2px solid #555; height: 10px; width: 150px">
	<div style="overflow: hidden; text-align: left; width: {{ percent }}%">
	<img src="{{ registry.uri }}{{ registry.path.modules }}FM/img/quota.png" style="position: relative; top: -7px; height: 10px;" />
	</div>
</div>