<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>API SAMPLE</title>
</head>
<body>
<?php require_once 'config.php'; ?>
<p><a id="back" href="<?php echo $link; ?>">На главную</a></p>
<?php
	if (isset($_POST["addTask"])) {
		if ($curl = curl_init()) {
			$text = rawurlencode($_POST["task"]);
			
			$dourl = $url;
			if (isset($rall)) {
				$dourl = $dourl . "&rall=1";
			}
			if (isset($ruser)) {
				$str = implode("&ruser[]=", $ruser);
				$dourl = $dourl . "&ruser[]=" . $str;
			}
			if (isset($gruser)) {
				$str = implode("&gruser[]=", $gruser);
				$dourl = $dourl . "&gruser[]=" . $str;
			}

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, "module=task&action=addTask&login=" . $login . "&password=" . $password . "&oid=" . $oid . "&text=" . $text);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$out = curl_exec($curl);
			curl_close($curl);
		}
	}
?>
<form action="<?php echo $link; ?>addtask.php" method="post">
	<textarea name="task" id="taskarea"></textarea>
	<p><input type="submit" value="Создать" name="addTask" /></p>
</form>

</body>
</html>