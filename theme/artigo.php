<?php

$postTitle = strip_tags(htmlspecialchars(urldecode($urlRouter[1])));

$read = new sql\Read();
$read->ExeRead('post', 'WHERE post_title LIKE :t ', "t={$postTitle}");

if ($read->getResult()):    
    
    $cachePost = new cache\Cache();
    $cachePost->exeCacheObjeto($read->getResult()[0]['post_id'], 'post', 'article', $read->getResult()[0]);

endif;


?>