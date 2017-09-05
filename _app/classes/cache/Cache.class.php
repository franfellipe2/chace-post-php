<?php

namespace cache;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

class Cache {

    private $objId;
    private $type;
    private $data;
    private $templateDir = BASE_THEME . DIRECTORY_SEPARATOR . 'templates';
    private $templateName;
    private $templateData;
    private $cacheDir = BASE_THEME . DIRECTORY_SEPARATOR . 'cache';
    private $cacheName;
    private $result;

    const tabName = 'cache';

    #zerar o cache na atualizacao do artigo -no
    #verificar se existe cache -no
    #cadastrar no banco -no
    #deletar o banco e os aquivos de cache -no
    #atualizar o cahce -no

    /**
     * Reseta o cache, assim quando o usuário acessar a pagina do artigo será criado um novo arquivo de cache     * 
     */
    public function reset($objId, $type) {
        $this->objId = (int) $objId;
        $this->type = (string) $type;

        if ($this->check()):
            $this->upCache(array('cache_status' => 0));
        endif;
    }

    public function objeto() {
        
    }

    /**
     * Executa o cache: cria, ou atualiza se nescessário
     * retorna o conteudo do cache
     */
    public function exeCacheObjeto($objId, $type, $templateName, array $templateData) {
        $this->objId = (int) $objId;
        $this->type = (string) $type;
        $this->templateName = $this->templateDir . DIRECTORY_SEPARATOR . $templateName . '.html';
        $this->templateData = $templateData;
        $this->cacheName = $this->cacheDir . DIRECTORY_SEPARATOR . "$templateName-" . $this->objId . '-' . $this->type . '.html';

        if (!$this->check() || $this->data['cache_status'] == 0 || !file_exists($this->cacheName)):
            echo '<<<<<---- gerou cache';
            $this->creatCacheFile($this->objId, $this->type, $templateName);
        else:
            $this->result = file_get_contents($this->cacheName);
            echo '>>>>>---- pegou cache';
        endif;
    }

    /**
     * Cria o arquivo de cahce
     * 
     * @param int $objId id do objeto que será criado o chace
     * @param string $type tipo de arquivo do cahce, ex: post, category ou outro
     */
    public function creatDB($objId, $type) {
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
        $getTemplate = file_get_contents($this->templateName);
        $mountTemplate = str_replace($keys, $vals, $getTemplate);
        
        //GEREA O CACHE
        file_put_contents($this->cacheName, $mountTemplate);
        $this->result = file_get_contents($this->cacheName);
    }

}
