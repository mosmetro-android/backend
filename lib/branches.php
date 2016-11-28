<?php
    include __DIR__ . '/../config.php';
    include "cache.php";
   
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
            $branches[$branch]['downloads'] = 0;
        }
    }
    
    // Получаем информацию о бранче master из Jenkins-CI
    function add_branch_from_jenkins(&$branches, $name) {
        $json = cached_retriever("https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/" . $name . "/lastSuccessfulBuild/api/json", 30*60);
        $jenkins = json_decode($json, true);
    
        $branches[$name]['version'] = "" . 0;
        $branches[$name]['build'] = "" . $jenkins['actions'][1]['buildsByBranchName']['origin/' . $name]['buildNumber'];
        $branches[$name]['by_build'] = "" . 1;
        $branches[$name]['url'] = "https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/" . $name . "/lastSuccessfulBuild/artifact/"
           . $jenkins['artifacts'][0]['relativePath'];
        $branches[$name]['message'] = "Сборка #" . $branches[$name]['build'] . " (" . date("d.m.y H:m:s", $jenkins['timestamp'] / 1000)
            . ") ветки " . $name . ". Об изменениях вы можете узнать из репозитория GitHub (ссылка в настройках приложения).";
        $branches[$name]['downloads'] = "" . 0;
    }

    add_branch_from_jenkins($branches, "master");
    add_branch_from_jenkins($branches, "experimental");

    // ------------------------------------------------------------------

    // Подменяем ветку beta на play, если последняя новее

    if ($branches['play']['version'] >= $branches['beta']['version'])
	$branches['beta'] = $branches['play'];

    // ------------------------------------------------------------------
    
    // Получаем количество скачиваний через внутреннюю систему обновления
    
    $query = "SELECT * FROM `mosmetro_update_stat` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `id` DESC";
    $result = mysqli_query($mysqli, $query);
    
    while ($row = mysqli_fetch_array($result)) {
        $branches[$row['branch']]['downloads']++;
    }
    
    // ------------------------------------------------------------------

    // Режим отладки
    if (!empty($_GET['debug'])) print_r($branches);
?>
