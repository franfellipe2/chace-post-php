<?php

$postTitle = strip_tags(urldecode($urlRouter[1]));

$read = new sql\Read();
$read->ExeRead('post', 'WHERE post_title LIKE :t ', "t={$postTitle}");

if ($read->getResult()):

    extract($read->getResult()[0]);

    //nome do arquivo cache do post
    $cacheFileName = BASE_THEME . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'article-' . $post_id . '.html';

    //VERIFICA SE TEM CACHE
    $Cache = new sql\Read();
    $Cache->ExeRead('cache', 'WHERE cache_objid = :postid AND cache_type = :type', "postid={$post_id}&type=post");

    if (!$Cache->getResult() || $Cache->getResult()[0]['cache_status'] == 0 || !file_exists($cacheFileName)):

        //MONTA O TEMPLATE
        $dataPost = [
            '{$title}' => $post_title,
            '{$content}' => $post_content,
            '{$data}' => $post_date
        ];
        $template = file_get_contents(BASE_THEME . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'article.html');
        $template = str_replace(array_keys($dataPost), array_values($dataPost), $template);

        //GEREA O CACHE
        file_put_contents($cacheFileName, $template);

        
        if ($Cache->getResult()):
            
            //atualiza o banco
            $upCache = new \sql\Update();
            $upCache->ExeUpdate('cache', array('cache_status' => 1), 'WHERE cache_objid = :postid AND cache_type = :type', "postid={$post_id}&type=post");
        
        else:
            
            //cadastra no banco   
            $dataCache = [
                'cache_objid' => $post_id,
                'cache_type' => 'post',
                'cache_date' => date('Y-m-d H:i:s'),
                'cache_status' => 1
            ];
            $creatCache = new sql\Create();
            $creatCache->ExeCreate('cache', $dataCache);

        endif;
        
        echo ' <<<<< gerou o cahce<hr>';
        //MOSTRA O ARTIGO
        echo $template;
        
    else:
        echo '>>>> pegou do cahce<hr>';
        //SE TIVER O CACHE 
        echo file_get_contents($cacheFileName);
        
    endif;//FIM verificaçãod o cache


endif;
?>