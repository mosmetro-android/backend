<?php
    include 'config.php';
    include 'lib/branches.php';
    
    if (!empty($_GET['download'])) {
        $branch = $_GET['download'];
        $url = $branches[$branch]['url'];
        
        if (empty($url)) {
           echo "Not found"; return;
        }
        
        $query = "INSERT INTO mosmetro_update_stat(branch)" .
		" VALUES ('" . $branch . "')";
	mysqli_query($mysqli, $query);
        
        $apk = cached_retriever($url, 0);
        
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"MosMetro-" . 
                $branch . "-signed.apk\"");
        header("Content-Length: " . strlen($apk));
        
        echo $apk; return;
    }
    
    foreach (array_keys($branches) as $branch) {
        echo "<branch id=\"" . $branch . "\">";
        
        echo "<key id=\"version\">" . $branches[$branch]['version'] . "</key>";  
        echo "<key id=\"build\">" . $branches[$branch]['build'] . "</key>";
        echo "<key id=\"by_build\">" . $branches[$branch]['by_build'] . "</key>";
        echo "<key id=\"url\">http://wi-fi1.metro-it.com/update.php?download=" . $branch . "</key>";
        echo "<key id=\"message\">" . nl2br($branches[$branch]['message']) . "</key>";
        
        echo "</branch>";
    }

?>
