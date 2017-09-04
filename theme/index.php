<?php
//protege contra acesso direto
if (!defined('BASE_URL')):
    die;
endif;

$read = new sql\Read();
$read->ExeRead('post', 'ORDER BY post_date DESC');
?>
<section class="list-posts">
    <h1>Lista de artigos</h1>
    <?php
    if ($read->getResult()):
        foreach ($read->getResult() as $post):
            extract($post);
            ?>
            <article>
                <h1>
                    <a href="artigo/<?= urlencode($post_title);?>" title="">
                        <?= $post_title; ?>
                    </a>
                </h1>
                <p><?= $post_content; ?></p>
                <time><?= $post_date; ?></time>
            </article>

            <?php
        endforeach;
    endif;
    ?>
</section>