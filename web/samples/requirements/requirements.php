<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Проверка</title>
<style type="text/css">
div {
    padding: 4px 8px;
    margin: 2px 4px;
}; 
</style>
</head> 
<body>

<h3>Зависимости</h3>

<?php
$flag = TRUE;

if (version_compare(phpversion(), '5.1.6', '<') == true) {
    echo "<div style='color: red'>PHP version must be > 5.1</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>PHP version satisfy the requirements</div>";
}

if (!class_exists('PDO')) {
    echo "<div style='color: red'>PDO disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>PDO enabled</div>";
}

if(!extension_loaded('pdo_mysql')) {
    echo "<div style='color: red'>pdo_mysql disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>pdo_mysql enabled</div>";
}

if (!extension_loaded('memcache')) {
    echo "<div style='color: red'>memcache disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>memcache enabled</div>";
}

if(!extension_loaded('mbstring')) {
    echo "<div style='color: red'>mbstring disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>mbstring enabled</div>";
}

$amods = apache_get_modules();
$mod_rewrite = FALSE;
foreach($amods as $part) {
    if ($part == "mod_rewrite") {
        $mod_rewrite = TRUE;
    }
}

if (!$mod_rewrite) {
    echo "<div style='color: red'>mod_rewrite disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>mod_rewrite enabled</div>";
}

if (!function_exists('json_encode')) {
    echo "<div style='color: red'>json disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>json enabled</div>";
}

if (!extension_loaded('curl')) {
    echo "<div style='color: orange'>curl disabled</div>";
    $flag = FALSE;
} else {
    echo "<div style='color: green'>curl enabled</div>";
}
?>

</body>
</html>