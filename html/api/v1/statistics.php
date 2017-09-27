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

    if (!empty($_POST["bssid"])) {
        $data["mac.oui"] = substr($_POST["bssid"], 0, 8);
        $data["mac.nic"] = substr($_POST["bssid"], 9);
    }

    if (!empty($_POST["segment"])) {
        $data["segment"] = $_POST["segment"];
    }

    // ------------------------------------------------------------------
    // Write statistics to StatsD
    // ------------------------------------------------------------------

    require_once __ROOT__ . '/vendor/autoload.php';

    function escape_str($input) {
        if (empty($input))
            return "null";
        else
            return str_replace(array('.', ':'), "-", $input);
    }

    if ($pref_stat["enabled"]) {
        $statsd = new League\StatsD\Client();
        $statsd->configure($pref_stat);
        $statsd->increment('success.' . escape_str($data['success']));
        $statsd->increment('version.code.' . escape_str($data['version.code']));
        $statsd->increment('version.name.' . escape_str($data['version.name']));
        $statsd->increment('ssid.' . escape_str($data['ssid']));
        $statsd->increment('provider.' . escape_str($data['provider']));
        $statsd->increment('domain.' . escape_str($data['domain']));
        $statsd->increment('captcha.' . escape_str($data['captcha']));
        $statsd->increment('segment.' . escape_str($data['segment']));
        $statsd->increment('mac.' . escape_str($data['mac.oui']) . '.' . escape_str($data['mac.nic']));
    }
?>

