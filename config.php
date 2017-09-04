<?php
//DEFINIÇÕES DO BANCO
define('HOST', 'localhost');
define('USER', 'root');
define('PASS', '');
define('DBSA', 'proj_cachephp');

//CAMINHOS
define('BASE_URL', 'http://localhost/projetos/cachephp');
define('BASE_DIR', __DIR__);
define('BASE_THEME', BASE_DIR . DIRECTORY_SEPARATOR . 'theme');
define('BASE_THEME_URI', BASE_URL.'/theme');


spl_autoload_register(function($className) {
    $fileName = BASE_DIR . DIRECTORY_SEPARATOR . '_app' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $className . '.class.php';    
    if (file_exists($fileName)) {
        require_once $fileName;
    }
});


// TRATAMENTO DE ERROS ######################
// Css Constantes: Mensagens de Erro;
define('CIEDEV_ERROR_ACCEPT', 'accept');
define('CIEDEV_ERROR_INFO', 'infor');
define('CIEDEV_ERROR_ALERT', 'alert');
define('CIEDEV_ERROR_ERROR', 'error');

// CiedevErro :: Exibe erros lançados :: Front
function CiedevErro($ErrMgs, $ErrNo, $ErrDie = null) {
    $CssClass = (
            $ErrNo == E_USER_NOTICE ? CIEDEV_ERROR_INFO :
            (
            $ErrNo == E_USER_WARNING ? CIEDEV_ERROR_ALERT :
            (
            $ErrNo == E_USER_ERROR ? CIEDEV_ERROR_ERROR : $ErrNo
            ) ) );

    echo "<p class='trigger {$CssClass}'>{$ErrMgs}  <span class='ajax_close'></span></p>";

    if ($ErrDie):
        die;
    endif;
}
