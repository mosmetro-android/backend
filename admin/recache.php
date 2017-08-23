<html>
    <head>
        <title></title>
        <meta http-equiv="refresh" content="10; URL=/mosmetro/admin">
    </head>

    <body>
        <?php
            include __DIR__ . '/../config.php';
            include __DIR__ . '/../lib/branches.php';

            $cache = new CacheConnection;
            $cache->flush();
            foreach (array_keys($branches) as $branch) {
                $cache->cached_retriever($branches[$branch]['url'], 30*60);
            }
        ?>
    </body>
</html>
