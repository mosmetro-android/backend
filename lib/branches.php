<?php
   
    $query = "SELECT * FROM `mosmetro_release` ORDER BY `id` DESC";
    $result = mysqli_query($mysqli, $query);
        
    $branches = array();
    while ($row = mysqli_fetch_array($result)) {
        $branch = $row['branch'];
        
        if (empty($branches[$branch])) {
            $branches[$branch] = array();
            
            $branches[$branch]['version'] = $row['version'];
            $branches[$branch]['build'] = $row['build'];
            $branches[$branch]['by_build'] = $row['by_build'];
            $branches[$branch]['url'] = $row['url'];
            $branches[$branch]['message'] = $row['message'];
        }
    }
    
    $json = file_get_contents("https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/master/lastSuccessfulBuild/api/json?pretty=true");
    $jenkins = json_decode($json, true);
    
    $branches['master']['version'] = 0;
    $branches['master']['build'] = $jenkins['actions'][2]['buildsByBranchName']['origin/master']['buildNumber'];
    $branches['master']['by_build'] = 1;
    $branches['master']['url'] = "https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/master/lastSuccessfulBuild/artifact/" . $jenkins['artifacts'][0]['relativePath'];
    $branches['master']['message'] = "Сборка #" . $branches['master']['build'] . " (" . date("d.m.y H:m:s", $jenkins['timestamp'] / 1000) . ") ветки master. Об изменениях вы можете узнать из репозитория GitHub (ссылка в настройках приложения).";

?>
