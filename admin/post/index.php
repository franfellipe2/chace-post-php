<?php
//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

//DELETA ARTIGO
$delId = filter_input(INPUT_GET, 'delid', FILTER_VALIDATE_INT);
$delTitle = filter_input(INPUT_GET, 't');

$confirmDel = null;
$sucess = null;

if ($delId):
    $confirm = filter_input(INPUT_GET, 'confirm', FILTER_VALIDATE_BOOLEAN);
    if (!$confirm):
        $confirmDel = BASE_ADMIN . '/?area=post/index.php&delid=' . $delId . '&t=' . $delTitle . '&confirm=true';
    else:
        $del = new sql\Delete();
        $del->ExeDelete('post', 'WHERE post_id = :delid', "delid={$delId}");
        if ($del->getRowCount()):

            cache\Cache::delCacheObjeto($delId, 'post');

            $sucess = "Artigo deletado com sucesso!: <b>{$delTitle}</b>";
        endif;
    endif;
endif;

$read = new sql\Read();
$read->ExeRead('post', 'ORDER BY post_date DESC');
?>
<section class="manage-posts">
    <div class="msg">
        <?php
        if ($sucess):
            echo '<span class="trigger acept">';
            echo $sucess;
            echo '</span>';
        endif;
        if ($confirmDel):
            echo '<span class="trigger alert">';
            echo 'Você deseja realmente deletar?: <b>' . $delTitle . '</b>';
            echo '<span class="btn-group">';
            echo "<br><br> <a class='btn acept' href='" . BASE_ADMIN . "/?area=post/index.php'>NÃO</a>";
            echo " <a class='btn error' href='{$confirmDel}'>SIM</a>";
            echo '</span>';
            echo '</span>';
        endif;
        ?>
    </div>
    <header><h1>Gerenciar artigos</h1></header>
    <?php
    if (!$read->getResult()):
        echo '<p>Ainda não existem artigos cadastrados!</p>';
    else:
        foreach ($read->getResult() as $post):
            extract($post);
            ?>
            <article>
                <header>
                    <h1><?= $post_title; ?></h1>
                </header>
                <p><?= $post_title; ?></p>
                <footer>
                    <time datetime="<?= $post_date; ?>"><?= date('d/m/Y H:i:s', strtotime($post_title)); ?></time>
                    <a class="action edit"href="<?= BASE_ADMIN . '/?area=post/update.php&id=' . $post_id; ?>">Editar</a>
                    <a class="action del"href="<?= BASE_ADMIN . '/?area=post/index.php&delid=' . $post_id . '&t=' . urlencode($post_title); ?>">Remover</a>
                </footer>
            </article>
            <?php
        endforeach;
    endif;
    ?>
</section>

