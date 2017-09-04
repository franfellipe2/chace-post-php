<?php

require 'config.php';

use sql\Read;
use sql\Create;
use sql\Update;

$readPost = new Read;
$readPost->ExeRead('post');

$tplName = 'article'; //nome do arquivo sem extenção
$tplFile = file_get_contents("templates/{$tplName}.html"); //nome e caminho do arquivo

ob_start();

foreach ($readPost->getResult() as $post):
    //cria o nome de arquivo do cache
    $cacheFileName = 'cache' . DIRSP . "$tplName-" . $post['post_id'] . '.html';

    //Le a tabela de cache
    $readCache = new Read();
    $readCache->ExeRead('cache', 'WHERE cache_objid = :postid AND cache_type = :cache_type', "postid={$post['post_id']}&cache_type=post");

    //Verifica o cache no banco de dados
    if (!$readCache->getResult() || $readCache->getResult()[0]['cache_status'] == 0):
        //Grava as informações de cache no banco
        $creatCache = new Create();
        $creatCache->ExeCreate('cache', array('cache_objid' => $post['post_id'], 'cache_type' => 'post', 'cache_date' => date('Y-m-d H:i:s'), 'cache_status' => 1));
    endif;

    //verifica se existe o arquivo de cache
    if (file_exists($cacheFileName)):
        echo file_get_contents($cacheFileName);
    else:
        //CRIA O ARQUIVO DE CACHE
       
        $dataTpl = [
            '{$title}' => $post['post_title'],
            '{$content}' => $post['post_content'],
            '{$data}' => $post['post_date'],
            '{$cache_date}' => date('d/m/Y H:i:s')
        ];
        $html = str_replace(array_keys($dataTpl), array_values($dataTpl), $tplFile);
        $Cache = file_put_contents($cacheFileName, $html);
        
        //MOSTRA O TAMPLATE
         echo 'Cria template';
        echo $html;
        echo '<hr><hr><hr>';
        
        //ATUALIZA O CACHE NO BANCO DE DADOS
        $upCache = new Update();
        $upCache->ExeUpdate('cache', array( 'cache_date' => date('Y-m-d H:i:s') ), 'WHERE cache_objid = :postid AND cache_type = :cache_type', "postid={$post['post_id']}&cache_type=post");
    endif;

endforeach;

ob_end_flush();
