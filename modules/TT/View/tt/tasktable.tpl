<tr class="dtask" id="t_{{ data.0.id }}" style="cursor: pointer;" onclick="getTask({{ data.0.id }})">
<td>

<div style="float: left; width: 80px;">
			<div style="font-size: 14px; font-weight: bold;">
				№{{ data.0.id }}
			</div>

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
<div>

			<div class="d_arr" style="display: none; float: right;"><img src="{{ registry.uri }}img/arrow.png" alt="arrow" /></div>			

			<h6>{{ data.0.name }}</h6>

			<div style="margin-bottom: 5px;">
				{% if data.0.remote_id != 0 %}
				{{ author.soname }} {{ author.name }} (группа {{ author.gname }})
				{% else %}
				<a style="cursor: pointer" onclick="getUserInfo('{{ author.id }}')">{{ author.soname }} {{ author.name }}</a>
				{% endif %}
			</div>
			
			{{ data.0.text }}
</div>

	
</td>
</tr>