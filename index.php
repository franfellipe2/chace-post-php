<?php

require './config.php';


$urlRouter = (!empty($_GET['url']) ? explode('/', $_GET['url']) : NULL);

//MONTA A PAGINA
require BASE_THEME .DIRECTORY_SEPARATOR .'header.php';

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

require BASE_THEME .DIRECTORY_SEPARATOR . 'footer.php';
