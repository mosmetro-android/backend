<html>
    <head>
        <title></title>
        <meta http-equiv="refresh" content="2;url=/mosmetro/admin">
    </head>
    
    <body>
        <?php
            include __DIR__ . '/../config.php';
    
            $branch = $_POST['branch'];
            $version = $_POST['version'];
            $build = $_POST['build'];
            $by_build = $_POST['by-build'];
            $message = $_POST['message'];
            
            if (!empty($_POST['by_build'])) {
            	$by_build = $_POST['by_build'];
            } else {
            	$by_build = 0;
            }
            
            $path = "/var/www/mosmetro/releases";
            $filename = "MosMetro-" . $branch . "-v" . $version . "-b" . $build . ".apk";
            $url = "http://thedrhax.pw/mosmetro/releases/" . $filename;
            
            if (move_uploaded_file($_FILES['apk']['tmp_name'], $path . "/" . $filename)) {
                $query = "INSERT INTO mosmetro_release(branch, version, build, by_build, url, message) VALUES ('" . $branch . "', " . $version . ", " . $build . ", " . $by_build . ", '" . $url . "', '" . $message . "')";
                mysqli_query($mysqli, $query);
                echo "OK";
            } else {
                echo "Failed";
            }
        ?>
    </body>
</html>
