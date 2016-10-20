<?php
    $ROOT = __DIR__ . '/../..';

    include $ROOT . '/config.php';
    include $ROOT . '/lib/branches.php';
    
    if (!empty($_GET['branch'])) {
        $branch = $_GET['branch'];
        $url = $branches[$branch]['url'];
        
        if (empty($url)) {
            $result = array();
            $result['success'] = False;
            $result['status'] = 'not found';
            echo json_encode($result);
            exit();
        }
        
        $apk = cached_retriever($url, 0);
        
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; "
                . "filename=\"MosMetro-" . $branch . "-signed.apk\"");
        header("Content-Length: " . strlen($apk));
        
        echo $apk;
        exit();
    }
?>
