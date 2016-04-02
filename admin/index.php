<?php
	include '../config.php';

	if (!empty($_GET['show_log'])) {
		$query = "SELECT * FROM `mosmetro_report` WHERE id=" . $_GET['show_log'];
		$res = mysqli_query($mysqli, $query);
		$row = mysqli_fetch_array($res);
		
		exit(nl2br($row['log']));
	}
	
	if (!empty($_GET['delete_log'])) {
		$query = "DELETE FROM `mosmetro_report` WHERE id=" . $_GET['delete_log'];
		$res = mysqli_query($mysqli, $query);
	}
	
	// Показать последний $interval (MySQL), поделенный по $index (field)
	function get_data ($mysqli, $interval, $index) {
		$query = "SELECT `date`
					FROM `mosmetro_stat`
					WHERE `date` > DATE_SUB(NOW(), INTERVAL " . $interval . ")";
		
		if (!empty($_GET['automatic']))
		    $query = $query . " AND `automatic` = " . $_GET['automatic'];
		if (!empty($_GET['version']))
		    $query = $query . " AND `version` = '" . $_GET['version'] . "'";
	    if (!empty($_GET['connected']))
		    $query = $query . " AND `connected` = " . $_GET['connected'];
		if (!empty($_GET['ssid']))
		    $query = $query . " AND `ssid` = '" . $_GET['ssid'] . "'";
		
		$query = $query . " ORDER BY `id` ASC";
		$res = mysqli_query($mysqli, $query);
	
		$data = array();
		while($row = mysqli_fetch_array($res)) {
			$date = date_parse($row['date']);
			$id = $date[$index];
			if (empty($data[$id])) $data[$id] = 0;
			$data[$id]++;
		}
		
		return $data;
	}
	
	function gen_plot ($mysqli, $interval, $index) {
		$data = get_data($mysqli, $interval, $index);
		$sum = 0; foreach ($data as &$field) {$sum += $field; $field = $field / 100;}
		echo $interval . " by " . $index . "s: " . $sum . " / 100" . "<br>";
		echo "<img src='plot/plot.php?type=line&data=" . urlencode(serialize($data)) . "' /><br>";
	}
?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css" media="all" />
	<title>Статистика MosMetro</title>
	
</head>

<body>

<div class="content">
    <?php include 'elements/stats.php'; ?>
</div>

<div class="content">
    <?php include 'elements/releases.php'; ?>
</div>

</body>
</html>
