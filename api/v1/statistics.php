<?php
    $data = [
        "1" => ["version", $_POST["version"]],
        "2" => ["ssid", $_POST["ssid"]],
        "3" => ["success", $_POST["success"]],
        "4" => ["domain", $_SERVER["HTTP_HOST"]],
    ];

    header("Location: "
        . "/piwik/piwik.php?"
        . "idsite=6&"
        . "rec=1&"
        . "url=" . urlencode("https://thedrhax.pw/mosmetro/check.php") . "&"
        . "new_visit=1&"
        . "_cvar=" . urlencode(json_encode($data))
    );
?>

