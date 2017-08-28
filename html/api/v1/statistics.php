<?php
    define(__ROOT__, __DIR__ . "/../..");

    require_once __ROOT__ . "/config.example.php";
    if (file_exists(__ROOT__ . "/config.php")) {
        require_once __ROOT__ . "/config.php";
    }

    $data = [
        "version" => $_POST["version"],
        "ssid" => $_POST["ssid"],
        "success" => $_POST["success"],
        "domain" => $_SERVER["HTTP_HOST"],
    ];

    if (!empty($_POST["provider"])) {
        $data["provider"] = $_POST["provider"];
    }
    if (!empty($_POST["p"])) {
        $data["provider"] = $_POST["p"];
    }

    if (!empty($_POST["captcha"])) {
        $data["captcha"] = $_POST["captcha"];
    }

    if (!empty($_POST["captcha_image"])) {
        $path = $pref_captcha['save_dir'];
        $date = date($pref_captcha['date_format']);

        mkdir($path, 755, true);

        file_put_contents(
            $path . '/' . $_POST["captcha_code"] . '-' . $date . '.png',
            base64_decode($_POST["captcha_image"])
        );
    }

    // ------------------------------------------------------------------
    // Write statistics to InfluxDB
    // ------------------------------------------------------------------

    if ($pref_stat["enabled"]) {
        require_once __ROOT__ . '/vendor/autoload.php';

        $client = new InfluxDB\Client(
            $pref_stat["influx_host"],
            $pref_stat["influx_port"]
        );

        $database = $client->selectDB(
            $pref_stat["influx_port"]
        );

        if (!$database->exists()) {
	        $database->create(
                new InfluxDB\Database\RetentionPolicy(
                    'test', $pref_stat["influx_retention"], 2, true
                )
            );
        }

        $points = [
	        new InfluxDB\Point(
		        'hit', // name
                1, // value
		        $data, // tags
                [], // metrics
		        date('U')
	        ),
        ];

        $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);
    }
?>

