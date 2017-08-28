<?php

define(__ROOT__, __DIR__ . "/..");

require_once __ROOT__ . "/config.example.php";
if (file_exists(__ROOT__ . "/config.php")) {
    require_once __ROOT__ . "/config.php";
}

require_once __ROOT__ . "/lib/cache.php";

if ($pref_cache['flush_enabled']) {
    if ($_POST['password'] == $pref_cache['flush_password']) {
        $cache = new CacheConnection;
        $cache->flush();
    }
}

?>
