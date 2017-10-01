<?php
    define(__ROOT__, __DIR__ . "/../..");

    require_once __ROOT__ . '/lib/branches.php';

    function fail() {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    function js_redirect($url) {
        echo "<script>";
        echo "window.location.replace(\"" . $url . "\")";
        echo "</script>";
        echo "<a href=\"" . $url . "\">";
        echo "Нажмите сюда, если перенаправление не сработало";
        echo "</a>";
    }

    $cache = new CacheConnection;

    // ------------------------------------------------------------------
    // Module download helper
    // Grabs links to modules from GitHub API
    // ------------------------------------------------------------------

    $modules = array( // name => GitHub repo
        "captcha_recognition" => "mosmetro-android/module-captcha-recognition",
    );

    if (!empty($_GET['module'])) {
        if (!empty($modules[$_GET['module']])) {
            $name = $modules[$_GET['module']];

            if (!$cache->exists($name)) {
                $releases = get_branches_from_github($name);
                $cache->set($name, $releases['play']['url'], 2*60*60);
            }

            js_redirect($cache->get($name));
            exit();
        } else {
            fail();
        }
    }

    // ------------------------------------------------------------------

    if (empty($_GET['branch'])) {
        fail();
    }

    $branch = $_GET['branch'];

    if (empty($branches[$branch]['filename'])) {
        fail();
    }

    function download($branches) {
        // Create /releases
        if (!file_exists(__ROOT__ . '/releases')) {
            mkdir(__ROOT__ . '/releases', 0777, true);
        }

        // Remove all previous releases
        array_map('unlink', glob(__ROOT__ . "/releases/*") ?: []);

        // Download new releases
        foreach (array_keys($branches) as $branch) {
            file_put_contents(
                __ROOT__ . "/releases/" . $branches[$branch]['filename'],
                file_get_contents($branches[$branch]['url'])
            );
        }
    }

    // Use locks to avoid downloading corrupted file
    $flush_lock = fopen("/tmp/flush.lock", "w+");
    if (!file_exists(__ROOT__ . "/releases/" . $branches[$branch]['filename'])) {
        if (flock($flush_lock, LOCK_EX)) {
            download($branches);
            flock($flush_lock, LOCK_UN);
        }
    }
    flock($flush_lock, LOCK_SH);
    flock($flush_lock, LOCK_UN);
    fclose($flush_lock);

    js_redirect("/releases/" . $branches[$branch]['filename']);

    // ------------------------------------------------------------------
    // Write statistics to StatsD
    // ------------------------------------------------------------------

    require_once __ROOT__ . '/vendor/autoload.php';
    require_once __ROOT__ . "/config.example.php";
    if (file_exists(__ROOT__ . "/config.php")) {
        require_once __ROOT__ . "/config.php";
    }

    if ($pref_stat["enabled"]) {
        $statsd = new League\StatsD\Client();
        $statsd->configure($pref_stat);
        $statsd->increment('update.' . $branch . '.' . $branches[$branch][
            $branches[$branch]['by_build'] == "1" ? 'build' : 'version'
        ]);
    }
?>
