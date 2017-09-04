<?php
//protege contra acesso direto
if (!defined('BASE_URL')):
    die;
endif;

//CAMPOS
//'post_title'
//'post_content'
//'post_desc'
//'post_lastviews'
//'post_date'
//'post_update'

$post = filter_input_array(INPUT_POST);

if (!empty($post['send'])):

    unset($post['send']);
    $post = array_map('htmlspecialchars', $post);
    var_dump($post);
    $creat = new sql\Create();
    $creat->ExeCreate('post', $post);
    if ($creat->getResult()):
        header('location: ' . BASE_ADMIN . '/?area=post/update.php&id=' . $creat->getResult() . '&c=true');
    endif;
    echo '<hr>';
endif;
?>
<article>
    <header>
        <h1>Criar artigo</h1>
        <h2>Area utilizada para o cadastro de artigos do site</h2>
    </header>
    <form action="" method="post">
        <input type="text" name="post_title" value="" placeholder="titulo" required="required"/>
        <input type="text" name="post_desc" value="" placeholder="descrição" required="required"/>
        <textarea name="post_content" required="required"></textarea>
        <input type="submit" value="cadastrar" name="send">    
    </form>
</article>