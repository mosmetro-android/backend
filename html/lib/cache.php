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

    function get($name) {
        return $this->$_con->get($name);
    }

    function exists($name) {
        return $this->get($name) !== false;
    }

    function set($name, $value, $ttl) {
        $this->$_con->set($name, $value, $ttl);
    }

    function failsafe_retriever($url) {
        ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 
        $content = file_get_contents($url);

        if (!($content === FALSE)) {
            $this->set("failsafe-" . $url, $content, 0);
        }

        return $this->get("failsafe-" . $url);
    }

    function cached_retriever($url, $ttl) {
        if (!$this->exists($url)) {
            $this->set($url, $this->failsafe_retriever($url) , $ttl);
        }

        return $this->get($url);
    }

}

?>
