<?php

function failsafe_retriever($url) {
	$content = file_get_contents($url);
	
	if (!($content === FALSE)) {
		apc_store("failsafe-" . $url, $content, 0);
	}
	
	return apc_fetch("failsafe-" . $url);
}

function cached_retriever($url, $ttl) {
	if (!apc_exists($url)) {
		apc_store($url, failsafe_retriever($url) , $ttl);
	}
	
	return apc_fetch($url);
}

?>
