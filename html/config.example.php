<?php

    /* Captcha collection settings */
	$pref_captcha = array(
        "save" => false,
        "save_dir" => "/tmp/mosmetro-captcha",
        "date_format" => "d.m.y-H.i.s",
    );

    /* Cache preferences */
    $pref_cache = array(
        // Send POST to /flush.php to clear branch cache
        // and redownload all latest builds
        // Example: curl -d "password=..." https://server/flush.php
        "flush_enabled" => false,
        "flush_password" => "",
    );

    /* Statistics */
    $pref_stat = array(
        "enabled" => "true",
        "influxdb_dsn" => "influxdb://influxdb:8086/mosmetro";
        "influxdb_retention" => "1d",
    );

?>

