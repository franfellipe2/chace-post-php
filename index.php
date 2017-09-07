<?php

require './config.php';

///////////////////////////////////////////////////////////////////////////////
$pr = 'http' . (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on" ? 's' : '') . '://';
$PageCurrent = $pr . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$cacheTime = 60;
$cacheDir = BASE_THEME . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
$cacheName = md5($PageCurrent);
$cahceFileName = $cacheDir . $cacheName . '.html';

if (file_exists($cahceFileName) && (filemtime($cahceFileName) + $cacheTime > time())):
    echo '<br> ----->| Pegou o cache da pagina<br>';
    echo file_get_contents($cahceFileName);
    die;
else:
    echo '<br> |<----- Gerou o cache da pagina<br>';
endif;

echo $cahceFileName;


//////////////////////////////////////////////////////////////////////////////

ob_start();

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

////////////////////////////////////////////////////////////
// Pega o conteudo da pagina
$content = ob_get_contents();
//cria o chace
file_put_contents($cahceFileName, $content);


////////////////////////////////////////////////////////////

ob_end_flush();
