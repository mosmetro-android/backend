<?php
    define(__ROOT__, __DIR__ . "/../..");
    $DOMAIN = "https://" . $_SERVER['HTTP_HOST'];

    require_once __ROOT__ . '/lib/branches.php';

    foreach (array_keys($branches) as $branch) {
        $branches[$branch]['url'] = $DOMAIN . "/api/v1/download.php?branch=" . $branch;
    }

    echo json_encode($branches);
?>
