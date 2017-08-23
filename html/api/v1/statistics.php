<?php
    define(__ROOT__, __DIR__ . "/../..");

    if (file_exists(__ROOT__ . "/config.php")) {
        require_once __ROOT__ . "/config.php";
    } else {
        require_once __ROOT__ . "/config.example.php";
    }

    $data = [
        "1" => ["version", $_POST["version"]],
        "2" => ["ssid", $_POST["ssid"]],
        "3" => ["success", $_POST["success"]],
        "4" => ["domain", $_SERVER["HTTP_HOST"]],
    ];

    if (!empty($_POST["provider"])) {
        $data["5"] = ["provider", $_POST["provider"]];
    }
    if (!empty($_POST["p"])) {
        $data["5"] = ["provider", $_POST["p"]];
    }

    if (!empty($_POST["captcha"])) {
        $data["6"] = ["captcha", $_POST["captcha"]];
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

?>

