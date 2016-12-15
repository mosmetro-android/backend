<?php
    $ROOT = __DIR__ . '/../..';
    $DOMAIN = "http://" . $_SERVER['HTTP_HOST'];

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';

    $result = array();

    foreach (array_keys($branches) as $branch) {
        $branches[$branch]['message'] = $branches[$branch]['message'];
        $branches[$branch]['url'] = $DOMAIN . "/api/v1/download.php?branch=" . $branch;
    }

    echo json_encode($branches);
?>
