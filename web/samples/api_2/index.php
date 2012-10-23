<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<title>API SAMPLE</title>
</head>
<body>

<div id="newtask"><a href="addtask.php">Новая задача</a></div>

<?php
	require_once 'config.php';
	
	if($curl = curl_init()) {
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "module=task&action=getTaskList&login=" . $login . "&password=" . $password . "&oid=" . $oid);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($curl);

		curl_close($curl);
		
		$out = json_decode($out, true);
		
		foreach($out as $val) {
			$id = $val["id"];
			$text = $val["text"];
			$open_data = $val["open_data"];
			$close_data = $val["close_data"];
			$author = $val["author"];
			$numComments = $val["numComments"];
			$newComments = $val["newComments"];
			
			if ($close_data == "0000-00-00 00:00:00") {
			?>
				<div class="otask">
					<div class="tid"><a href="task.php?tid=<?php echo $id; ?>">№<?php echo $id; ?></a></div>
					<div class="tright"><div class="ttext"><?php echo $text; ?></div>
						<div class="tsub"><?php echo $author; ?></div><div class="tsub"><?php echo $open_data; ?></div>
						<div class="tcom">Комментарии: <?php echo $numComments; ?> [<?php echo $newComments; ?>]</div>
					</div>
				</div>
			<?php
			} else {
				?>
				<div class="ctask">
					<div class="tid"><a href="task.php?tid=<?php echo $id; ?>">№<?php echo $id; ?></a></div>
					<div class="tright"><div class="ttext"><?php echo $text; ?></div>
						<div class="tsub"><?php echo $author; ?></div><div class="tsub"><?php echo $open_data; ?></div>
						<div class="tclose">Закрыта: <?php echo $close_data; ?></div>
						<div class="tcom">Комментарии: <?php echo $numComments; ?> [<?php echo $newComments; ?>]</div>
					</div>
				</div>
				<?php
			}
		}
	}
?>

</body>
</html>