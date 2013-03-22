<div style="overflow: hidden">
<div style="float: left; text-align: center; margin-right: 10px">
	<img class="avatar" id="ava" src="{{ post.avatar }}" alt="avatar" />
	<br />
	{% if post.status %}
	<div style="font-size: 10px; color: green">[online]</div>
	{% else %}
	<div style="font-size: 10px; color: red">[offline]</div>
	{% endif %}
</div>
<div style="float: left">
	<h2 style="margin: 2px 0">{{ post.name }} {{ post.soname }}</h2>
	<h4 style="margin: 5px 0">{{ post.signature }}</h4>
	<h4 style="margin: 5px 0">Group: {{ post.gname }}</h4>

	{% if post.admin %}<span style="margin-left: 10px; font-size: 10px; color: red">administrator</span>{% endif %}
</div>
</div>

<br /><br />

<p><b>Email: </b><a href="mailto: {{ post.email }}" style="outline: none">{{ post.email }}</a></p>
<p><b>ICQ: </b>{{ post.icq }}</p>
<p><b>Skype: </b>{{ post.skype }}</p>
<p><b>Address: </b>{{ post.adres }}</p>
<p><b>Phone: </b>{{ post.phone }}</p>