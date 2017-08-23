<?php
    $ROOT = __DIR__ . '/../..';

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';

    function fail() {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    function github_get_link_to_release($repo, $project) {
        $cache = new CacheConnection;
        $github_api_url = "https://api.github.com/repos/" . $repo . "/" . $project . "/releases/latest";
        $github_api_json = $cache->cached_retriever($github_api_url, 30*60);
        $github_api = json_decode($github_api_json, true);
        return $github_api['assets'][0]['browser_download_url'];
    }

    if (!empty($_GET['module']) && $_GET['module'] == "captcha_recognition") {
        header('Location: ' . github_get_link_to_release("mosmetro-android", "module-captcha-recognition"));
        exit();
    }

    if (empty($_GET['branch']))
        fail();

    $branch = $_GET['branch'];
    $url = $branches[$branch]['url'];
    if ($branches[$branch]['by_build'] == "1") {
        $version = $branches[$branch]['build'];
    } else {
        $version = $branches[$branch]['version'];
    }

    if (empty($url))
        fail();

    $query = "INSERT INTO mosmetro_update_stat(branch, version)" .
             " VALUES ('" . $branch . "', '" . $version . "')";
    mysqli_query($mysqli, $query);

    $cache = new CacheConnection;
    $apk = $cache->cached_retriever($url, 0);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename="
                . "\"MosMetro-" . $branch . "-signed.apk\"");
    header("Content-Length: " . strlen($apk));

    echo $apk;
    exit();
?>
