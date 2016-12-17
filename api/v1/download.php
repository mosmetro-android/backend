<?php
    $ROOT = __DIR__ . '/../..';

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';

    function fail() {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    if (empty($_GET['branch']))
        fail();

    $branch = $_GET['branch'];
    $url = $branches[$branch]['url'];

    if (empty($url))
        fail();

    $query = "INSERT INTO mosmetro_update_stat(branch)" .
             " VALUES ('" . $branch . "')";
    mysqli_query($mysqli, $query);

    $apk = cached_retriever($url, 0);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename="
                . "\"MosMetro-" . $branch . "-signed.apk\"");
    header("Content-Length: " . strlen($apk));

    echo $apk;
    exit();
?>
