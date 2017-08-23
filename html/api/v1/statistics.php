<?php

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

/*    if (!empty($_POST["captcha_image"])) {
        $path = "/tmp/mosmetro-captcha";
        $date = date("H:i:s-d.m.y");
        mkdir($path, 755, true);
        file_put_contents(
            $path . '/' . $_POST["captcha_code"] . '-' . $date . '.png',
            base64_decode($_POST["captcha_image"])
        );
    } */

/*
    header("Location: "
        . "/piwik/piwik.php?"
        . "idsite=6&"
        . "rec=1&"
        . "url=" . urlencode("https://thedrhax.pw/mosmetro/check.php") . "&"
        . "new_visit=1&"
        . "_cvar=" . urlencode(json_encode($data))
    );
*/

?>

