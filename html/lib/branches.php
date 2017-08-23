<?php
    define(__ROOT__, __DIR__ . "/..");
    require_once __ROOT__ . "/lib/cache.php";

    // ------------------------------------------------------------------
    // Source: Jenkins CI
    // Branches: all except disabled, 'play' and 'beta'
    // ------------------------------------------------------------------

    function jenkins_branch($project_url, $name) {
        $cache = new CacheConnection;
        $result = array();

        $last_build_url = $project_url . "/branch/" . $name . "/lastSuccessfulBuild";

        $jenkins = json_decode(
            $cache->failsafe_retriever(
                $last_build_url . "/api/json"
            ), true
        );

        // Looking for correct artifact
        foreach ($jenkins['artifacts'] as $i) {
            if (strpos($i['fileName'], 'MosMetro') !== FALSE) { // ^MosMetro-
                if (strpos($i['fileName'], 'signed') !== FALSE) { // -signed.apk$
                    $artifact = $i; break;
                }
            }
        }
        
        $result['version'] = "" . 0;
        $result['build'] = "" . $jenkins['number'];
        $result['by_build'] = "" . 1;
        $result['url'] = $last_build_url . "/artifact/" . $artifact['relativePath'];
        $result['filename'] = "MosMetro-" . $name . "-b" . $jenkins['number'] . ".apk";

        // Generate update message
        $message = "Сборка " . $name . "-#" . $result['build'] . ":";
        $commits = $jenkins["changeSet"]["items"];
        if (count($commits) == 0) {
            $message .= " ¯\_(ツ)_/¯";
        } else {
            foreach ($commits as $commit) {
                $message .= "\r\n* " . $commit["msg"];
            }
        }
        $result['message'] = $message;

        return array($name => $result);
    }

    function get_branches_from_jenkins($project_url) {
        $cache = new CacheConnection;
        $result = array();

        $jenkins = json_decode(
            $cache->failsafe_retriever(
                $project_url . "/api/json"
            ), true
        );

        foreach ($jenkins["jobs"] as $branch) {
            $name = $branch["name"];

            if ($branch["color"] != "disabled") {
                if (!in_array($name, ["play", "beta"])) {
                    $result += jenkins_branch($project_url, $name);
                }
            }
        }

        return $result;
    }

    // ------------------------------------------------------------------
    // Source: GitHub
    // Branches: play, beta
    // ------------------------------------------------------------------

    function add_release_from_github($name, $release) {
        $result = array();

        $result['version'] = $release["tag_name"];
        $result['build'] = 0;
        $result['by_build'] = "" . 0;
        $result['url'] = $release["assets"][0]["browser_download_url"];
        $result['message'] = $release["body"];
        $result['filename'] = "MosMetro-" . $name . "-v" . $release["tag_name"] . ".apk";

        return array($name => $result);
    }

    function get_branches_from_github($repo) {
        $cache = new CacheConnection;
        $result = array();

        $releases = json_decode(
            $cache->failsafe_retriever(
                "https://api.github.com/repos/" . $repo . "/releases"
            ), true
        );

        // Looking for 'beta' (latest) and 'play' (latest stable)
        $beta = $releases[0];
        if ($beta["prerelease"]) {
            foreach ($releases as $release) {
                if (!$release["prerelease"]) {
                    $play = $release;
                    break;
                }
            }
        } else {
            $play = $beta;
        }

        $result += add_release_from_github("beta", $beta);
        $result += add_release_from_github("play", $play);

        return $result;
    }

    // ------------------------------------------------------------------

    $cache = new CacheConnection;

    if (!$cache->exists("branches")) {
        $branches = array();

        $branches += get_branches_from_jenkins(
            "https://local.thedrhax.pw/jenkins/job/MosMetro-Android"
        );
        $branches += get_branches_from_github(
            "mosmetro-android/mosmetro-android"
        );

        $cache->set("branches", $branches, 30*60);
    } else {
        $branches = $cache->get("branches");
    }

    if (!empty($_GET['debug'])) {
        print(json_encode($branches, JSON_PRETTY_PRINT));
    }

?>
