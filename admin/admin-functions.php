<?php

/**
 * Pega a area via $_GET['area'] do arquivo a ser incluido
 * @return string nome e caminho do arquivo a ser incluido
 */
function get_area() {

    $default = 'post/index.php';

    if (empty($_GET['area'])):
        return $default;
    elseif (file_exists($_GET['area']) && !is_dir($_GET['area'])):
        return $_GET['area'];
    else:
        return $default;
    endif;
}

/**
 * Pega o titulo da pagina
 * @return string
 */
function get_title() {
    switch (get_area()):
        //PAINEL PRINCIAPL
        case 'painel.php':
            $title = 'Area administrativa ';
            break;
        //POSTS
        case 'post/create.php':
            $title = 'Criar artigos';
            break;
        case 'post/update.php':
            $title = 'Atualizar artigos';
            break;
        case 'post/index.php':
            $title = 'Gerenciar artigos';
            break;
        //USUARIOS
        case 'user/create.php':
            $title = 'Criar artigos';
            break;
        case 'user/update.php':
            $title = 'Atualizar artigos';
            break;
        case 'user/index.php':
            $title = 'Gerenciar artigos';
            break;
    endswitch;
    return $title . ' | ' . ADMIN_TITLE_PAINEL;
}
