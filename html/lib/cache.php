<?php

class CacheConnection {

    function __construct() {
        $this->$_con = new Memcache;
        $this->$_con->addServer('memcached', 11211);
    }

    function __destruct() {
        $this->$_con->close();
    }

    function flush() {
        $this->$_con->flush();
    }

    function failsafe_retriever($url) {
        ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 
        $content = file_get_contents($url);

        if (!($content === FALSE)) {
            $this->$_con->set("failsafe-" . $url, $content, 0);
        }

        return $this->$_con->get("failsafe-" . $url);
    }

    function cached_retriever($url, $ttl) {
        if (!($this->$_con->get($url) !== false)) {
            $this->$_con->set($url, $this->failsafe_retriever($url) , $ttl);
        }

        return $this->$_con->get($url);
    }

}

?>
