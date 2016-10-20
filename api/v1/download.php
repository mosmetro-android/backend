<?php
    $ROOT = __DIR__ . '/../..';

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';

    function fail() {
        $result = array();
        $result['success'] = False;
        $result['status'] = 'branch not found';
        echo json_encode($result);
        exit();
    }

    if (empty($_GET['branch'])
        fail();

    $branch = $_GET['branch'];
    $url = $branches[$branch]['url'];

    if (empty($url))
        fail();

    $apk = cached_retriever($url, 0);

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename="
                . "\"MosMetro-" . $branch . "-signed.apk\"");
    header("Content-Length: " . strlen($apk));

    echo $apk;
    exit();
?>
