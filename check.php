<?php
	// Проверка на интернет в приложении "Wi-Fi в метро" происходит путем сравнивания ответа сервера с этой строкой
	print("2fv3bYW6x92V3Y7gM5FfT7Wmh<br>");

	if (!empty($_POST['isAutomatic'])) { // Поддержка версий ниже 1.1-9
		$automatic = $_POST['isAutomatic'];
	} else { // Версии больше и равные 1.1-9
		$automatic = $_POST['automatic'];
	}

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
		"3" => ["success", $connected ? "true" : "false"]
	];

	header("Location: /piwik/piwik.php?"
			. "idsite=6&"
			. "rec=1&"
			. "url=" . urlencode("https://thedrhax.pw/mosmetro/check.php") . "&"
			. "_cvar=" . json_encode($data)
	);
?>

