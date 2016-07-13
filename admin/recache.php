<html>
    <head>
        <title></title>
        <!--<meta http-equiv="refresh" content="2;url=/mosmetro/admin">-->
    </head>
    
    <body>
        <?php
            include __DIR__ . '/../config.php';
            
            echo '<h2>Перед очисткой</h2>';
            
            print_r(apc_cache_info());
            
            apc_clear_cache();

            include __DIR__ . '/../lib/branches.php';
            
            foreach (array_keys($branches) as $branch) {
				cached_retriever($branches[$branch]['url'], 30*60);
			}
			
			echo '<h2>После очистки</h2>';
            
            print_r(apc_cache_info());
        ?>
        
        <p><a href="index.php">Готово</a></p>
    </body>
</html>
