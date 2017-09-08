# Introdução

Sistema de cache php em 2 níveis:
* 1- Nivel da pagina: faz o cache do conteudo da pagina inteira ou parcial com tempo de expiração
* 2- Nível de post: Gera e mantem o cache de uma postagem enquanto o seu conteudo não seja alterado


## Como usar


### Cache da postagem
* Requisitos:
Este sistema de cache foi projetado para trabalhar com com o recurso de templates.
Para funcionar é preciso criar arquivos templates.html em uma pasta 'templates'

Exemplo article.html:
<article>
    <h1>{$post_title}</h1>
    <p>{$post_content}</p>
</article>

```php
    
    $arrPostData = [
        'post_title' => 'My first cache',
        'post_content' => 'Hello World!'
    ];

    $cachePost = new cache\Cache();
    $cachePost->exeCacheObjeto($post_id, 'post', 'article', $arrPostData );
```


### Cache da pagina


```php
//Inicia o bloco do cache
$cachePage = new cache\Cache();
//Define o tempo de expiração em segundos
$cachePage->Init(60);
```
```
<!-- Exemplo de conteudo -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8"/>
<title>HTML5 – Estrutura básica</title>
</head>
<body>
    Minha pagina
</body>
</html>
```

```php
//Fecha o bloco de cache
$cachePage->Close();
```