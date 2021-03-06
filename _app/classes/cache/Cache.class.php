<?php

namespace cache;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

class Cache {

    private $cacheTime;
    // OBJETO QUE SERÁ CRIADO O CACHE
    private $postId;
    private $type;
    // DADOS DO TEMPLATE
    private $templateData;
    private $templateName;
    private $templateDir = BASE_THEME . DIRECTORY_SEPARATOR . 'templates';
    private $templateFileName;
    // DADOS DO CACHE
    private $data;
    private $cacheName;
    private $cacheDir = BASE_THEME . DIRECTORY_SEPARATOR . 'cache';
    private $cacheFileName;
    //RESULTADOS
    private $result;
    private $error;

    // TABALE DO CACHE NO BANCO
    const tabName = 'cache';

    /**
     * Retorna o resultado do arquivo do cache
     * @return string
     */
    function getResult() {
        return $this->result;
    }

    /**
     * Retorn os possíveis erros
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Retorna o nome do arquivo do cache gerado
     * @return string
     */
    function getCacheName() {
        return $this->cacheName;
    }

    /**
     * Retorna o caminho da pasta do cache
     * @return string
     */
    function getCacheDir() {
        return $this->cacheDir;
    }

    /**
     * Retorna o caminho e o nome do arquivo do cache
     * @return string
     */
    function getCacheFileName() {
        if (!$this->cacheFileName):
            $this->cacheFileName = $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheName;
        endif;
        return $this->cacheFileName;
    }

    /**
     * Passar achave id da tabela no banco de dados
     * @param int $postId
     */
    function setPostId($postId) {
        $this->postId = (int) $postId;
    }

    /**
     * Passar o tipo do cache: Ex: post, category, page, etc...
     * @param string $type
     */
    function setType($type) {
        $this->type = (string) $type;
    }

    /**
     * Passar o caminho do diretorio onde estão os arquivos de template
     * @param string $templateDir
     */
    function setTemplateDir($templateDir) {
        $this->templateDir = (string) $templateDir;
    }

    /**
     * Passar o nome do arquivo da template sem a sua extensão, e, dever ser um arquivo html, ex: meutemplate.html
     * @param string $templateName ex: meutemplate
     */
    function setTemplateName($templateName) {
        $this->templateName = (string) $templateName;
    }

    /**
     * Passar um array com os dados que irão alimentar o template
     * @param array $templateData
     */
    function setTemplateData(array $templateData) {
        $this->templateData = $templateData;
    }

    /**
     * Parra o endereço onde ficarão os arquivos de cache
     * @param string $cacheDir
     */
    function setCacheDir($cacheDir) {
        $this->cacheDir = (string) $cacheDir;
    }

    function setCacheName($cacheName) {
        $this->cacheName = $cacheName;
    }

    /**
     * Inicia o bloco de cache
     * @param int $cacheTime
     * @param string $nameId
     */
    public function init($cacheTime, $nameId = null) {
        $this->cacheTime = (int) $cacheTime;

        if ($this->cacheTime > 0):

            ob_start();

            $this->setCacheName(md5( $_SERVER['REQUEST_URI'] . $nameId) . '.html');

            if (file_exists($this->getCacheFileName()) && ( (filemtime($this->getCacheFileName()) + $this->cacheTime) > time())):                
                $this->result = file_get_contents($this->getCacheFileName());
            else:
                $this->result = false;
            endif;

        endif;
    }

    /**
     * Fecha o bloco de chace
     */
    public function close() {

        if ($this->cacheTime > 0):

            $this->result = ob_get_contents();
            file_put_contents($this->getCacheFileName(), $this->result);

            ob_end_flush();
            
        endif;
    }

    /**
     * Executa o cache do conteudo do posto: cria, mostra ou atualiza o cache se nescessário
     * 
     * @param int $postId id do artigo para o qual será gerado o cache
     * @param string $type o tipo de postagem: post, page, etc...
     * @param string $templateName nome do template.html utilizado para gerar o cache
     * @param array $templateData dados do post que serão passados para o template.html, ex: data['title' => 'my first cache', 'content' => 'hello world']
     * @param boolean $show deixe como true para exibir o conteudo, false para não exibir. Em ambos o cache será armazenado no atributo <b>result</b>
     */
    public function exeCacheObjeto($postId, $type, $templateName, array $templateData, $show = true) {

        $this->setTemplateName($templateName);
        $this->setPostId($postId);
        $this->setType($type);
        $this->setTemplateData($templateData);
        $this->setCacheName("{$this->postId}-{$this->type}-{$this->templateName}.html");

        $this->templateFileName = $this->templateDir . DIRECTORY_SEPARATOR . $this->templateName . '.html';
        $this->getCacheFileName();

        if (!$this->check() || $this->data['cache_status'] == 0 || !file_exists($this->cacheFileName)):           

            $this->creatCacheFile($this->postId, $this->type, $this->templateName);
            if ($show):
                echo $this->result;
            endif;

        else:            

            $this->result = file_get_contents($this->cacheFileName);
            if ($show):
                echo $this->result;
            endif;

        endif;
    }

    /**
     * Cadastra no banco os dados do arquivo de cache
     * 
     * @param int $postId id do post que será gerado o chace
     * @param string $type tipo de postagem, ex: post, category ou outro
     */
    public function creatDB() {
        if ($this->check()):
            $this->upCache(array('cache_status' => 1, 'cache_date' => date('Y-m-d H:i:s')));
        else:
            $this->data = [
                'cache_postid' => $this->postId,
                'cache_type' => $this->type,
                'cache_date' => date('Y-m-d H:i:s'),
                'cache_status' => 1
            ];
            $creatCache = new \sql\Create();
            $creatCache->ExeCreate(self::tabName, $this->data);
            if (!$creatCache->getResult()):
                $this->error = 'Erro ao cadastrar cache!';
            endif;
        endif;
    }

    /**
     * Reseta o cache, assim quando o usuário acessar a pagina do artigo será criado um novo arquivo de cache 
     * @param type $postId
     * @param type $type
     */
    static public function reset($postId) {
        $res = new Cache;
        $res->setPostId($postId);

        //Se tiver um cache, reseta; senão, não faz dada
        if ($res->check()):
            $res->upCache(array('cache_status' => 0));
        endif;
    }

    /**
     * cria ou atualiza o arquivo e o banco do cache
     */
    private function creatCacheFile() {

        //CRIA OU ATUALIZA O BANCO
        $this->creatDB($this->postId, $this->type);

        //MONTA O TEMPLATE
        $this->mountFileContent();
    }

    /**
     * Atualiza o status do cache no banco de dados
     * @param array $intStatus
     */
    private function upCache(array $data) {
        $up = new \sql\Update();
        $up->ExeUpdate(self::tabName, $data, 'WHERE cache_postid = :id', "id={$this->postId}");
    }

    /**
     * Delata os arquivos do cache e os dados do cache no banco
     * 
     * @param int $postId id chave primaria da tabela do objeto no banco, ex: post_id
     * @param string $type tipo do objeto/tabela, ex: posts
     */
    static public function delCachePost($postId, $type) {
        $cache = new Cache();
        $cache->setPostId($postId);
        $cache->setType($type);

        //DELETAR O BANCO
        $delDB = new \sql\Delete();
        $delDB->ExeDelete(self::tabName, 'WHERE cache_postid = :id', "id={$cache->postId}");

        //DELETAR OS ARQUIVOS        
        $cache->cacheFileName = $cache->cacheDir . DIRECTORY_SEPARATOR . "{$cache->postId}-{$cache->type}-*.html";

        foreach (glob($cache->cacheFileName) as $file):
            //deleta o arquivo
            unlink($file);
        endforeach;
    }

    /**
     * Verifica se existe cache no banco
     * @return boolean
     */
    private function check() {
        if ($this->data):
            return true;
        else:
            $check = $this->readDB();
            return $check;
        endif;
    }

    /**
     * Le os dados do cahce no banco e armazena no atributo <b>data</b>
     * @return boolean
     */
    private function readDB() {

        $readCach = new \sql\Read();
        $readCach->ExeRead(self::tabName, 'WHERE cache_postid = :id', "id={$this->postId}");

        if ($readCach->getResult()):
            //caputra os dados do banco
            $this->data = $readCach->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Passa os dados para o template
     */
    private function mountFileContent() {
        //Pega os valores
        $keys = explode(' ', '{$' . implode('} {$', array_keys($this->templateData)) . '}');
        $vals = array_values($this->templateData);

        //Monta o template
        $getTemplate = file_get_contents($this->templateFileName);
        $mountTemplate = str_replace($keys, $vals, $getTemplate);

        //GEREA O CACHE
        file_put_contents($this->cacheFileName, $mountTemplate);
        $this->result = file_get_contents($this->cacheFileName);
    }

    public function __destruct() {
        
    }

}
