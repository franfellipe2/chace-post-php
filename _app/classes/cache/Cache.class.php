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
    private $fileCacheName;
    private $fileCacheBase = BASE_THEME . DIRECTORY_SEPARATOR . 'cache';

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

    /**
     * Executa o cache: cria, ou atualiza se nescessário
     * retorna o conteudo do cache
     */
    public function exeCacheObjeto($objId, $type, $templateName) {
        $this->objId = (int) $objId;
        $this->type = (string) $type;

        $this->fileCacheName = $this->fileCacheBase . DIRECTORY_SEPARATOR . '$templateName-' . $objId . '-' . $type . '.html';

        if (!$this->check() || $this->data['cache_status'] == 0 || !file_exists($this->fileCacheName)):
            $this->creatCacheFile($this->objId, $this->type, $templateName);
        else:

        endif;
    }

    public function creatCacheFile($objId, $type, $templateName, $templateData) {
        $this->objId = (int) $objId;
        $this->type = (string) $type;
        $this->fileCacheName = $this->fileCacheName ? $this->fileCacheName : $this->fileCacheBase . DIRECTORY_SEPARATOR . '$templateName-' . $objId . '-' . $type . '.html';

        //CADASTRA NO BANCO        
        $this->creatDB($this->objId, $this->type);

        //MONTA O TEMPLATE
//        $template = file_get_contents(BASE_THEME . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'article.html');
//        $template = str_replace(array_keys($dataPost), array_values($dataPost), $template);
        //GEREA O CACHE
        //file_put_contents($this->fileCacheName, $template);
    }

    /**
     * Cria o arquivo de cahce
     * 
     * @param int $objId id do objeto que será criado o chace
     * @param string $type tipo de arquivo do cahce, ex: post, category ou outro
     */
    public function creatDB($objId, $type) {
        if ($this->check()):
            $this->upCache(array('cache_status' => 1, 'cache_date' => date('Y-m-d')));
        else:
//            $data = [
//                'cache_objid' => $this->objId,
//                'cache_type' => $this->type,
//                'cache_date' => date('Y-m-d'),
//                'cache_status' => 1
//            ];
//            $creatCache = new \sql\Create();
//            $creatCache->ExeCreate(self::tabName, $data);
        endif;
    }

    /**
     * Atualiza o status do cache no banco de dados
     * @param type $intStatus
     */
    private function upCache($data = array()) {
        $up = new \sql\Update();
        $up->ExeUpdate(self::tabName, $data, 'WHERE cache_objid = :id AND cache_type = :type', "id={$this->objId}&type={$this->type}");
        $msg = 'Atualização realizada com sucesso!';
    }

    /**
     * Verifica se existe cache
     * @return boolean
     */
    private function check() {

        if ($this->data):
            return true;
        else:
            return $this->readDB();
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

}
