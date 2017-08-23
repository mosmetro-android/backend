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
        $cache = new CacheConnection;
        $json = $cache->cached_retriever("https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/" . $name . "/lastSuccessfulBuild/api/json", 30*60);
        $jenkins = json_decode($json, true);

        //$artifact = "";
        foreach ($jenkins['artifacts'] as $i) {
            if (strpos($i['fileName'], 'signed') !== FALSE) {
                if (strpos($i['fileName'], 'MosMetro') !== FALSE) {
                    $artifact = $i;
                    break;
                }
            }
        }

        $branches[$name]['version'] = "" . 0;
        $branches[$name]['build'] = "" . $jenkins['actions'][1]['buildsByBranchName']['origin/' . $name]['buildNumber'];
        $branches[$name]['by_build'] = "" . 1;
        $branches[$name]['url'] = "https://local.thedrhax.pw/jenkins/job/MosMetro-Android/branch/" . $name . "/lastSuccessfulBuild/artifact/"
           . $artifact['relativePath'];

        // Generate update message
        $message = "Сборка " . $name . "-#" . $branches[$name]['build'] . ":";
        $commits = $jenkins["changeSet"]["items"];
        if (count($commits) == 0) {
            $message = $message . " ¯\_(ツ)_/¯";
        } else {
            foreach ($jenkins["changeSet"]["items"] as $commit) {
                $message = $message . "\n* " . $commit["msg"];
            }
        }
        $branches[$name]['message'] = $message;

        $branches[$name]['downloads'] = "" . 0;
    }

    function get_branch_list_from_jenkins(&$branches) {
        $cache = new CacheConnection;
        $json = $cache->cached_retriever("https://local.thedrhax.pw/jenkins/job/MosMetro-Android/api/json", 30*60);
        $jenkins = json_decode($json, true);

        foreach ($jenkins["jobs"] as $branch) {
            $name = $branch["name"];
            if ($branch["color"] != "disabled" && empty($branches[$name])) {
                add_branch_from_jenkins($branches, $name);
            }
        }
    }

    get_branch_list_from_jenkins($branches);

    // ------------------------------------------------------------------

    // Подменяем ветку beta на play, если последняя новее

    if ($branches['play']['version'] >= $branches['beta']['version'])
	    $branches['beta'] = $branches['play'];

    // ------------------------------------------------------------------
    
    // Получаем количество скачиваний через внутреннюю систему обновления

    foreach (array_keys($branches) as $branch) {
        if ($branches[$branch]['by_build'] == "1") {
            $version = $branches[$branch]['build'];
        } else {
            $version = $branches[$branch]['version'];
        }
        $query = "SELECT COUNT(*) FROM `mosmetro_update_stat` WHERE `branch` = \"" . $branch . "\" AND `version` = " . $version . ";";
        $result = mysqli_query($mysqli, $query);
        $branches[$branch]['downloads'] = mysqli_fetch_array($result)[0];
    }

    // ------------------------------------------------------------------

    // Режим отладки
    if (!empty($_GET['debug'])) print_r($branches);
?>
