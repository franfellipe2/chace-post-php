<?php
//ARQUIVO DE CONFIGURÇÃO
require '..' . DIRECTORY_SEPARATOR . 'config.php';
require 'admin-config.php';
ob_start();
?>
<!doctye html>
<html>
    <head>        
        <title><?php echo get_title(); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="admin.css"/>
    </head>
    <body>
        <?php require 'template-parts' . DIRECTORY_SEPARATOR . 'header.php'; ?>
        <main class="page-content">
            <?php require get_area(); ?>
        </main>
        <footer>

        </footer>
    </body>
</html>
<?php ob_end_flush(); ?>




