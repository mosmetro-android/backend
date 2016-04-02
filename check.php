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

	$version = $_POST['version'];
	$connected = $_POST['connected'];

	$query = "INSERT INTO mosmetro_stat(version, automatic, connected, ssid)" .
		" VALUES ('" . $version . "', " . $automatic . ", " . $connected . ", '" . $ssid . "')";
	mysqli_query($mysqli, $query);
?>

