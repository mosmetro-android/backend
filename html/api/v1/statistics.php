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
        require_once __ROOT__ . '/vendor/autoload.php';

        function null_to_str($input) {
            if (empty($input))
                return "null";
            else
                return $input;
        }

        $timestamp = date('U');
        $points = [
            new InfluxDB\Point(
                'hit',
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.version.code.' . null_to_str($data["version.code"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.version.name.' . null_to_str($data["version.name"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.success.' . null_to_str($data["success"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.ssid.' . null_to_str($data["ssid"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.provider.' . null_to_str($data["provider"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.domain.' . null_to_str($data["domain"]),
                1, $data, [], $timestamp
            ),
            new InfluxDB\Point(
                'hit.captcha.' . null_to_str($data["captcha"]),
                1, $data, [], $timestamp
            ),
        ];

        $database = \InfluxDB\Client::fromDSN(
            $pref_stat["influxdb_dsn"]
        );

        if (!$database->exists()) {
            $database->create(
                new InfluxDB\Database\RetentionPolicy(
                    'test', $pref_stat["influxdb_retention"], 2, true
                )
            );
        }

        $database->writePoints($points, InfluxDB\Database::PRECISION_SECONDS);
    }
?>

