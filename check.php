<?php
	// Проверка на интернет в приложении "Wi-Fi в метро" происходит путем сравнивания ответа сервера с этой строкой
	print("2fv3bYW6x92V3Y7gM5FfT7Wmh<br>");

	include 'config.php';

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

//	$query = "INSERT INTO mosmetro_stat(version, automatic, connected, ssid)" .
//		" VALUES ('" . $version . "', " . $automatic . ", " . $connected . ", '" . $ssid . "')";
//	mysqli_query($mysqli, $query);

	$data = [
		"1" => ["version", $version],
		"2" => ["ssid", $ssid],
		"3" => ["success", $connected ? "true" : "false"]
	];

	header("Location: https://thedrhax.pw/piwik/piwik.php?"
			. "idsite=6&"
			. "rec=1&"
			//. "token_auth=a2176dc663545fb23a95b6aa9d7e6765&"
			//. "cip=" . $_SERVER['REMOTE_ADDR'] . "&"
			. "url=" . urlencode("https://thedrhax.pw/mosmetro/check.php") . "&"
			. "_cvar=" . json_encode($data)
	);
?>

