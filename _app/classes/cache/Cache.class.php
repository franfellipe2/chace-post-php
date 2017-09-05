<?php

namespace cache;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

class Cache {

    // CHECK-LIST ----------------------------
    # + zerar o cache na atualizacao do artigo
    # + verificar se existe cache
    # + cadastrar no banco
    # + atualizar o cahce
    # + deletar o banco e os aquivos de cache
    // ---------------------------------------
    // OBJETO QUE SERÁ CRIADO O CACHE
    private $objId;
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
    private $result;

    // TABALE DO CACHE NO BANCO
    const tabName = 'cache';

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
        return $this->cacheFileName;
    }

    /**
     * Retorna o resultado do arquivo do cache
     * @return string
     */
    function getResult() {
        return $this->result;
    }

    /**
     * Passar achave id da tabela no banco de dados
     * @param int $objId
     */
    function setObjId($objId) {
        $this->objId = (int) $objId;
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
     * Executa o cache: cria, ou atualiza se nescessário
     * retorna o conteudo do cache
     */
    public function exeCacheObjeto($objId, $type, $templateName, array $templateData) {

        $this->setTemplateName($templateName);
        $this->setObjId($objId);
        $this->setType($type);
        $this->setTemplateData($templateData);
        $this->setCacheName("{$this->objId}-{$this->type}-{$this->templateName}.html");

        $this->templateFileName = $this->templateDir . DIRECTORY_SEPARATOR . $this->templateName . '.html';
        $this->cacheFileName = $this->cacheDir . DIRECTORY_SEPARATOR . $this->cacheName;

        if (!$this->check() || $this->data['cache_status'] == 0 || !file_exists($this->cacheFileName)):
                
            echo '<br><<<<<---- gerou cache<br>';            
            $this->creatCacheFile($this->objId, $this->type, $this->templateName);
            
        else:
            
            $this->result = file_get_contents($this->cacheFileName);
            echo '<br>>>>>>---- pegou cache<br>';
            
        endif;
    }

    /**
     * Cria o arquivo de cahce
     * 
     * @param int $objId id do objeto que será criado o chace
     * @param string $type tipo de arquivo do cahce, ex: post, category ou outro
     */
    public function creatDB() {
        if ($this->check()):
            $this->upCache(array('cache_status' => 1, 'cache_date' => date('Y-m-d H:i:s')));
        else:
            $this->data = [
                'cache_objid' => $this->objId,
                'cache_type' => $this->type,
                'cache_date' => date('Y-m-d H:i:s'),
                'cache_status' => 1
            ];
            $creatCache = new \sql\Create();
            $creatCache->ExeCreate(self::tabName, $this->data);
            if ($creatCache->getResult()):
                echo '<br>Cadastrou no banco<br>';
            endif;
        endif;
    }

    /**
     * Reseta o cache, assim quando o usuário acessar a pagina do artigo será criado um novo arquivo de cache 
     */
    public function reset($objId, $type) {
        $this->objId = (int) $objId;
        $this->type = (string) $type;
        
        //Se tiver um cache, reseta; senão, não faz dada
        if ($this->check()):
            $this->upCache(array('cache_status' => 0));
        endif;
    }

    /**
     * cria ou atualiza o arquivo e o banco de cache
     */
    private function creatCacheFile() {

        //CRIA OU ATUALIZA O BANCO
        $this->creatDB($this->objId, $this->type);

        //MONTA O TEMPLATE
        $this->mountFileContent();
    }

    /**
     * Atualiza o status do cache no banco de dados
     * @param type $intStatus
     */
    private function upCache(array $data) {
        $up = new \sql\Update();
        $up->ExeUpdate(self::tabName, $data, 'WHERE cache_objid = :id AND cache_type = :type', "id={$this->objId}&type={$this->type}");
    }

    /**
     * Delata os arquivos do cache e os dados do cache no banco
     * 
     * @param int $objId id chave primaria da tabela do objeto no banco, ex: post_id
     * @param string $type tipo do objeto/tabela, ex: posts
     */
    static public function delCacheObjeto($objId, $type) {
        $cache = new Cache();
        $cache->setObjId($objId);
        $cache->setType($type);

        //DELETAR O BANCO
        $delDB = new \sql\Delete();
        $delDB->ExeDelete(self::tabName, 'WHERE cache_objid = :id AND cache_type = :type', "id={$cache->objId}&type={$cache->type}");

        //DELETAR OS ARQUIVOS        
        $cacheFileName = $cache->cacheDir . DIRECTORY_SEPARATOR . "{$cache->objId}-{$cache->type}-*.html";

        foreach (glob($cacheFileName) as $file):
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
     * Le os dados do cahce no banco
     */
    private function readDB() {

        $readCach = new \sql\Read();
        $readCach->ExeRead(self::tabName, 'WHERE cache_objid = :id AND cache_type = :type', "id={$this->objId}&type={$this->type}");

        if ($readCach->getResult()):
            //atualiza
            $this->data = $readCach->getResult()[0];
            return true;
        else:
            return false;
        endif;
    }

    /**
     * Passa os dados do banco para o template
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
