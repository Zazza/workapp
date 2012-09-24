<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<link rel="icon" type="image/gif" href="{{ registry.uri }}favicon.png" />
<meta name="description" content="workapp" />
<meta name="keywords" content="workapp" />
<link href="{{ registry.uri }}css/bootstrap.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{ registry.uri }}js/bootstrap.min.js"></script>
<title>workapp</title>
<style>
@charset "utf-8";
/* CSS Document */
html, body {
    font-family: Verdana, sans-serif;
    font-size: 11px;
    font-style: normal;
    line-height: normal;
    font-weight: normal;
    font-variant: normal;
    text-align: center;
    background-color: #FFF;
    margin: 0;
    padding: 0;
    color: black;
}

.p {
	overflow: hidden;
	height: 30px;
}

.pn {
    float: left;
    font-size: 14px;
    margin-top: 2px;
    text-align: left;
    width: 80px;
}

#dl {
    margin: 0 auto;
    display: table;
}
</style>
</head>
<body>

<div style="margin: 100px auto 0">

<img alt="logo" src="{{ registry.uri }}img/logo-big.png" style="border: none; padding: 8px 20px 10px" />


<div style="color: red; padding: 10px 4px; width: 200px; height: 20px; margin: 0 auto">
{% if err %}Неверный логин/пароль{% endif %}
</div>


<div class="container">
<div id="dl">
<div class="well" style="padding: 19px 20px 5px">
<form action="{{ registry.uri }}" method="post" style="margin-bottom: 0">

<div class="p control-group {% if err %}error{% endif %}">
	<div class="pn">Логин</div>
	<div style="float: left"><input id="" type="text" name="login" /></div>
</div>
<div class="p control-group {% if err %}error{% endif %}">
	<div class="pn">Пароль</div>
	<div style="float: left"><input type="password" name="password" /></div>
</div>
<div class="p" style="text-align: right; margin-top: 10px">
<input class="btn btn-info" type="submit" name="submit" value="Войти" />
</div>
</form>
</div>
</div>
</div>

</div>

</body>
</html>
