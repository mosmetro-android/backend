<?php
    define(__ROOT__, __DIR__ . "/../..");

    require_once __ROOT__ . "/config.example.php";
    if (file_exists(__ROOT__ . "/config.php")) {
        require_once __ROOT__ . "/config.php";
    }

    $data = [
        "version.code" => preg_filter("/^(.*)-/", "", $_POST["version"]),
        "version.name" => preg_filter("/-(\d+)$/", "", $_POST["version"]),
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

    if (!empty($_POST["captcha_image"]) && $pref_captcha["save"]) {
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
        require_once __ROOT__ . '/lib/influxdb.php';

        $influx = new InfluxClient($pref_stat["influxdb_dsn"], $pref_stat["influxdb_retention"]);
        $influx->add('', 'hit', 1, $data, []);
        $influx->add('hit.version.code.', $data["version.code"], 1, $data, []);
        $influx->add('hit.version.name.', $data["version.name"], 1, $data, []);
        $influx->add('hit.success.', $data["success"], 1, $data, []);
        $influx->add('hit.ssid.', $data["ssid"], 1, $data, []);
        $influx->add('hit.provider.', $data["provider"], 1, $data, []);
        $influx->add('hit.domain.', $data["domain"], 1, $data, []);
        $influx->add('hit.captcha.', $data["captcha"], 1, $data, []);
        $influx->write();
    }
?>

