<?php
    $ROOT = __DIR__ . '/../..';

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';
    
    $result = array();
    
    foreach (array_keys($branches) as $branch) {
        $branches[$branch]['message'] = nl2br($branches[$branch]['message']);
        $branches[$branch]['url'] = "http://wi-fi.metro-it.com/"
                . "api/v1/download.php?branch=" . $branch;
    }

    echo json_encode($branches);
?>
