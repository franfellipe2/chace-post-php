<?php
//protege contra acesso direto
if (!defined('BASE_URL')):
    die;
endif;

$post = filter_input_array(INPUT_POST); //dados do formulario
$getId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); //id do artigo
$getCreat = filter_input(INPUT_GET, 'c', FILTER_VALIDATE_BOOLEAN); //verifica se o post acabou de ser criado

if (!$getId):
    header('location: ' . BASE_ADMIN . '/');
endif;

$msg = $getCreat ? 'Artigo cadastrado com sucesso! Se quiser, continue Editando...' : null;

if (!empty($post['send'])):
    unset($post['send']);

    //ATUALIZA O ARTIGO    
    $post = array_map('htmlspecialchars', array_map('htmlspecialchars_decode', $post));

    $up = new sql\Update();
    $up->ExeUpdate('post', $post, 'WHERE post_id = :id ', "id={$getId}");
    $msg = 'Atualização realizada com sucesso!';

    //ZERA O CACHE
    $cache = new cache\Cache();
    $cache->reset($getId, 'post');

else:
    //LE O ARTIGO
    $read = new sql\Read();
    $read->ExeRead('post', 'WHERE post_id = :id', "id={$getId}");
    if ($read->getResult()):
        $post = $read->getResult()[0];
    else:
        $msg = "Opssss! Este post de <b>id {$getId}</b> não existe no sistema ou foi deletado.";
    endif;
endif;

extract($post);
?>
<article>    
    <header>
        <h1>Editar artigo</h1>
        <h2><?= $post_title; ?></h2>
        <?php
        if ($msg):
            echo '<div style="color:green">' . $msg . '</div><hr>';
        endif;
        ?>
    </header>
    <form action="" method="post">
        <input type="text" name="post_title" value="<?= $post_title; ?>" placeholder="titulo" required="required"/>
        <input type="text" name="post_desc" value="<?= $post_desc; ?>" placeholder="descrição" required="required"/>
        <textarea name="post_content" required="required"><?= $post_content; ?></textarea>
        <input type="submit" value="Atualizar" name="send">    
    </form>
</article>