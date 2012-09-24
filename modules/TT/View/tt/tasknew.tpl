<div style="clear: both;">

	<div style="float: left; width: 50px; padding-right: 20px; padding-top: 40px;">
		{% if data.0.mail_id == 0 %}
			<img class="avatar" id="ava" src="{{ author.avatar }}" alt="аватар" />
		{% else %}
			<img class="avatar" id="ava" src="{{ registry.uri }}img/noavatar.gif" alt="аватар" />
		{% endif %}
	</div>
	
	<div style="float: left; width: 479px;">

		<div style="height: 40px; margin: 0 5px; padding: 5px 14px; background-color: #F0F0F0; border-top: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD;">
			<div style="float: left; font-size: 14px; font-weight: bold;">
				<a href="{{ registry.uri }}tt/show/{{ data.0.id }}/">№{{ data.0.id }}</a>
				
				<!-- приоритет -->
				<div style="font-size: 12px; font-weight: normal">
				{% if data.0.imp == 1 %}
				<div style="height: 14px; width: 40px; border: 2px solid #00ffcc; text-align: center"><span style="position:relative; bottom: 2px">1/5</span></div>
				{% elseif data.0.imp == 2 %}
				<div style="height: 14px; width: 40px; border: 2px solid #00ffcc; text-align: center"><span style="position:relative; bottom: 2px">2/5</span></div>
				{% elseif data.0.imp == 3 %}
				<div style="height: 14px; width: 40px; border: 2px solid #ffcc00; text-align: center"><span style="position:relative; bottom: 2px">3/5</span></div>
				{% elseif data.0.imp == 4 %}
				<div style="height: 14px; width: 40px; border: 2px solid #ff0000; text-align: center"><span style="position:relative; bottom: 2px">4/5</span></div>
				{% elseif data.0.imp == 5 %}
				<div style="height: 14px; width: 40px; border: 2px solid #ff0000; text-align: center"><span style="position:relative; bottom: 2px">5/5</span></div>
				{% endif %}
				</div>
				<!-- END приоритет -->
			</div>
			
			<div class="dropdown" style="float: right;">
				<span class="dropdown-toggle" data-toggle="dropdown" href="#" style="cursor: pointer;">
					<img src="{{ registry.uri }}img/information-button.png" border="0" />
					<b class="caret"></b>
				</span>
				<ul class="dropdown-menu">
					<li>
						{% if data.0.close == 1 %}<div class="taskstatus">завершено</div>{% endif %}
						{% if data.0.secure == 1 %}<div class="taskstatus">приватная</div>{% endif %}
						{% if data.0.type == "1" and data.0.expire %}<div class="taskstatus">просроченная</div>{% endif %}
					</li>

					<li>
						{% include "tt/info/period.tpl" %}
					</li>
						
						{% if data.0.chid %}

						Вложенные задачи:

						{% for part in data %}
						<li style="text-align: left; padding: 4px 10px">
						<p class="chtask">
							<a style="color: #048; text-decoration: underline" href="{{ registry.uri }}tt/show/{{ part.chid }}/">№{{ part.chid }}</a>
							{{ part.chname }}
						</p>
						</li>
						{% endfor %}

						{% endif %}
						
						{% if obj %}
						<li>
								Объект

								{% for part in obj %}
								<span style="cursor: pointer; margin: 2px;" onclick="getInfo({{ part.id }})">{{ part.val }}</span>
								{% endfor %}
						</li>
						{% endif %}
						
						{% if data.0.cid %}
						<li>(подзадача для <a href="{{ registry.uri }}tt/{{ data.0.cid }}/">№{{ data.0.cid }}</a>)</li>
						{% endif %}
					</li>
				</ul>
			</div>
			
			<h6 style="padding: 0 60px;"><a style="color: #777;" href="{{ registry.uri }}tt/show/{{ data.0.id }}/">{{ data.0.name }}</a></h6>
		</div>
		
		<div style="padding: 9px 14px; border: 1px solid #DDD;">
			<div style="margin-bottom: 5px;">
				{% if data.0.remote_id != 0 %}
				{{ author.soname }} {{ author.name }} (группа {{ author.gname }})
				{% else %}
				<a style="cursor: pointer" onclick="getUserInfo('{{ author.id }}')">{{ author.soname }} {{ author.name }}</a>
				{% endif %}
				
				<span style="font-size: 11px; color: #777;">{{ data.0.start }}</span>
				
				<span class="dropdown" style="float: right;">
					<span class="dropdown-toggle" data-toggle="dropdown" href="#" style="cursor: pointer;">
						Ответственные
						<b class="caret"></b>
					</span>
					<ul class="dropdown-menu">
						<li>{% include "tt/info/responsible.tpl" %}</li>
					</ul>
				</span>
			</div>

			{{ data.0.text }}
		</div>
		
		<div style="overflow: hidden; padding: 9px 14px; border-bottom: 1px solid #DDD; border-left: 1px solid #DDD; border-right: 1px solid #DDD">
			<!-- кнопки-внизу -->
			{% if type != "draft" %}
			<div style="float: right; font-size: 12px; margin-top: 10px;">
			<a href="{{ registry.uri }}tt/show/{{ data.0.id }}/">
			<img src="{{ registry.uri }}img/user-medium.png" />
			{{ numComments }} {% if newComments > 0 %}<span style="color: green; font-weight: bold">+{{ newComments }}</span>{% endif %}
			</a>
			</div>

			<a class="btn" style="text-decoration: none" href="{{ registry.uri }}tt/history/{{ data.0.id }}/" title="История">
			<img src="{{ registry.uri }}img/clock-history.png" alt="История" border="0" />
			</a>
			
			{% if data.0.close == 0%}
			{% if data.0.spam != 0 %}
			<a class="btn" style="cursor: pointer; text-decoration: none" onclick="spam({{ data.0.id }})" title="отписаться от рассылки по задаче">
			<img src="{{ registry.uri }}img/mail--minus.png" />
			</a>
			{% else %}
			<a class="btn" style="cursor: pointer; text-decoration: none" onclick="spam({{ data.0.id }})" title="подписаться на рассылку по задаче">
			<img src="{{ registry.uri }}img/mail--plus.png" alt="" />
			</a>
			{% endif %}
			{% endif %}
			
			{% if data.0.close == 0 %}
			
			{% if registry.ui.id == data.0.who or registry.ui.admin %}
			<a class="btn" href="{{ registry.uri }}tt/edit/{{ data.0.id }}/" title="Правка">
			<img src="{{ registry.uri }}img/edititem.gif" />
			</a>
			{% endif %}
			
			{% if registry.ui.id == data.0.who or registry.ui.admin %}
			<a class="btn" href="{{ registry.uri }}tt/add/?sub={{ data.0.id }}" title="Создать подзадачу">
			<img src="{{ registry.uri }}img/plus-button.png" />
			</a>
			{% endif %}
			
			{% if not data.0.route %}
				{% if registry.ui.id == data.0.who or registry.ui.admin %}
				<a class="btn" onclick="closeTask({{ data.0.id }})" title="Завершить">
				<img src="{{ registry.uri }}img/inbox-download.png" />
				</a>
				{% endif %}
			{% else %}
				<a class="btn btn-success" onclick="setResult({{ data.0.id }})" title="Продолжить">
				<img src="{{ registry.uri }}img/node-select-next.png" />
				</a>
			{% endif %}
				
			{% endif %}
			
			{% else %}
			<!-- Если черновик -->
			<div class="btn-group" style="margin-top: 20px">
			
			<a class="btn" style="cursor: pointer; text-decoration: none" href="{{ registry.uri }}tt/draftedit/{{ data.0.id }}/">
			<img src="{{ registry.uri }}img/edititem.gif" />
			Правка
			</a>
			
			<a class="btn" style="cursor: pointer; text-decoration: none" onclick="delDraftConfirm({{ data.0.id }})">
			<img src="{{ registry.uri }}img/inbox-download.png" />
			Удалить
			</a>
			
			</div>
			{% endif %}
			<!-- END кнопки-внизу -->
		</div>
		
	</div>
	
</div>

<div style="clear: both; height: 40px;"></div>