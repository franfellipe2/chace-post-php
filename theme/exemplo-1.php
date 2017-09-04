<?php

require 'config.php';

use sql\Read;
use sql\Create;
use sql\Update;

$readPost = new Read;
$readPost->ExeRead('post');

$tplName = 'article';
$tpl = file_get_contents("templates/{$tplName}.html");
$result = array();

ob_start();

foreach ($readPost->getResult() as $post):
    //cria o nome do cache do artigo
    $cacheFileName = 'cache' . DIRSP . "$tplName-" . $post['post_id'] . '.html';

    $readCache = new Read();
    $readCache->ExeRead('cache', 'WHERE cache_objid = :postid AND cache_type = :cache_type', "postid={$post['post_id']}&cache_type=post");

    //verifica se existe um arquivo de cache
    if (!$readCache->getResult()):
        $creatCache = new Create();
        $creatCache->ExeCreate('cache', array('cache_objid' => $post['post_id'], 'cache_type' => 'post', 'cache_date' => date('Y-m-d H:i:s', time() + 60)));
    elseif (strtotime($readCache->getResult()[0]['cache_date']) < time()):
        $upCache = new Update();
        $upCache->ExeUpdate('cache', array('cache_date' => date('Y-m-d H:i:s', time() + 60)), 'WHERE cache_objid = :postid AND cache_type = :cache_type', "postid={$post['post_id']}&cache_type=post");
    endif;

    if (strtotime($readCache->getResult()[0]['cache_date']) > time()):
        echo file_get_contents($cacheFileName);
    else:
        //cria o arquivo de cache
        echo 'Cria template<hr>';
        $dataTpl = [
            '{$title}' => $post['post_title'],
            '{$content}' => $post['post_content'],
            '{$data}' => $post['post_date'],
            '{$cache_date}' => date('d/m/Y H:i:s')
        ];
        $html = str_replace(array_keys($dataTpl), array_values($dataTpl), $tpl);
        $Cache = file_put_contents($cacheFileName, $html);
        echo $html;
    endif;

endforeach;

ob_end_flush();
