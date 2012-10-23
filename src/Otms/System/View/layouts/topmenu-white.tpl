<div class="navbar navbar-fixed-top" id="topContent">
	<div class="navbar-inner">
		<div class="container" style="width: auto; padding: 0 10px; min-width: 750px">
			<ul class="nav" style="width: 166px; border-right: 1px solid #EEE; padding-left: 34px; margin-bottom: 1px;">
				<div class="brand" href="{{ registry.uri }}" style="padding: 5px 15px;">
				<img src="{{ registry.uri }}img/logo-small-black.png" alt="logo" border="0" />
				</div>
			</ul>
			
			<div class="nav-collapse" style="margin-bottom: 0px;">
				<ul class="nav">
					{% for part in bottomPanel %}
					{{ part }}
					{% endfor %}
				</ul>
				
				<ul class="nav">
					{% for part in bottomPanelRight %}
					{{ part }}
					{% endfor %}
				</ul>
			</div>

			<!-- PROFILE -->
				<ul class="nav pull-right" style="padding: 2px 0 0 40px;">
				
					<li class="dropdown">
						<a  onclick="userParam()" class="dropdown-toggle" data-toggle="dropdown" href="#" style="padding: 0">
							<div style="margin-right: 15px; float: left">
								<img class="avatar" style="border: 1px solid #555; max-height: 30px; max-width: 30px" src="{{ registry.ui.avatar }}" alt="аватар" />
							</div>
							<div style="float: left; margin: 1px 15px 0 0; line-height: 16px; font-size: 11px;">
								<b>{{ registry.ui.name }}<br />{{ registry.ui.soname }}</b>
							</div>
							
							<b class="caret" style="float: left; margin: 17px 17px 0 0"></b>
						</a>
						
						<ul class="dropdown-menu" style="text-align: left;">
							<li><div id="userParam"></div></li>
							<li><a href="{{ registry.uri }}profile/"><i class="icon-user"></i> Профиль</a></li>
							<li><a href="{{ registry.uri }}logout/" title="выход"><i class="icon-off"></i> выход</a></li>
						</ul>
					</li>

				</ul>
			<!-- /PROFILE -->
			
			<form action="{{ registry.uri }}find/objects/" method="post" name="form_find" class="navbar-search form-search pull-right">
			<div class="input-prepend input-append" style="margin-top: 3px;">
				<input type="text" name="find" id="in_find" class="input-medium span3" style="height: 16px;" />
				<button type="submit" class="btn btn-small" style="margin-top: 0;" name="button_find" id="but_find"><i class="icon-large icon-search"></i></button>
			</div>
			</form>
		</div>
	</div>
</div>