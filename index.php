<?php
require './config.php';

//INICIA O CACHE
$cachePage = new cache\Cache();
$cachePage->Init(3);

//verifica se existe cahce
if ($cachePage->getResult()):
    
    //mostra o cache
    echo $cachePage->getResult();

// Redenriza o conteudo
else:    
    
    $urlRouter = (!empty($_GET['url']) ? explode('/', $_GET['url']) : NULL);

    //MONTA A PAGINA
    require BASE_THEME . DIRECTORY_SEPARATOR . 'header.php';

    //VERIFICA QUAL ARQUIVO INCLUIR
    if (!$urlRouter):

        require BASE_THEME . DIRECTORY_SEPARATOR . 'index.php';

    elseif (file_exists(BASE_THEME . DIRECTORY_SEPARATOR . $urlRouter[0] . '.php')):

        require BASE_THEME . DIRECTORY_SEPARATOR . $urlRouter[0] . '.php';

    elseif (file_exists(BASE_THEME . DIRECTORY_SEPARATOR . $urlRouter[0] . DIRECTORY_SEPARATOR . $urlRouter[1] . '.php')):

        require BASE_THEME . DIRECTORY_SEPARATOR . $urlRouter[0] . DIRECTORY_SEPARATOR . $urlRouter[1] . '.php';

    else:

        require BASE_THEME . DIRECTORY_SEPARATOR . '404.php';

    endif;

    require BASE_THEME . DIRECTORY_SEPARATOR . 'footer.php';
    
endif;

//FINALIZA E CRIA O CACHE
$cachePage->Close();
