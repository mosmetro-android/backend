<?php
	// Версии большие и равные 1.6-29
	if (!empty($_POST['ssid'])) {
		$ssid = $_POST['ssid'];
	} else {
		$ssid = "MosMetro_Free";
	}

	if (!empty($_POST['version'])) {
		$version = $_POST['version'];
	} else {
		exit();
	}

	$connected = $_POST['connected'];

	$data = [
		"1" => ["version", $version],
		"2" => ["ssid", $ssid],
		"3" => ["success", $connected ? "true" : "false"],
		"4" => ["domain", $_SERVER['HTTP_HOST']]
	];

	header("Location: /piwik/piwik.php?"
			. "idsite=6&"
			. "rec=1&"
			. "url=" . urlencode("https://thedrhax.pw/mosmetro/check.php") . "&"
			. "new_visit=1&"
			. "_cvar=" . urlencode(json_encode($data))
	);
?>

