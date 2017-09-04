<?php

namespace sql;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

/**
 * <b>Read.class.php</b>
 * Classe responsavel por fazer a leitura no banco de dados
 * 
 * @copyright (c) 2017, Francsico S. Filho Sie Webdesing 
 */
class Read extends Conn {

    private $Select;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Read;

    /** @var PDO */
    private $Conn;

    /**
     * <b>ExeRead()</b>
     * Executar a leitura
     * 
     * @param STRING $Tabela = nome da tabela à ser consultada
     * @param STRING $Termos = condiçoes da consulta
     * @param STRING $ParseString = utilizada para montar a pdo Prepare Statements
     * 
     * EXEMPLO:
     * $Talbela = 'usuario';
     * $Termos = 'WHRE usuario_nome = :name AND usuario_idade = :idade';
     * $ParseString = 'name=pedro&idade=25';
     */
    public function ExeRead($Tabela, $Termos = null, $ParseString = null) {
        if (!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        $this->Select = "SELECT * FROM {$Tabela} {$Termos}";
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Read->rowCount();
    }

    /**
     * <b>FullRead: </b> Faz uma busca completa
     * 
     * @param STRING $Query = passar a query completa ex: SELECT * FROM tabela WHERE campo = :condicao
     * @param $ParseString = passar as parseStrings ex: condicao=valor
     */
    public function FullRead($Query, $ParseString = null) {
        $this->Select = (string) $Query;
        if (!empty($ParseString)):
            parse_str($ParseString, $this->Places);
        endif;
        $this->Execute();
    }

    /**
     * @param STRING $ParseString = modifica a condição dos paramentros passos
     */
    public function setPlaces($ParseString) {
        parse_str($ParseString, $this->Places);
        $this->Execute();
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */
    // Obtem o PDO e prepara a query
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($this->Select);
        $this->Read->setFetchMode(\PDO::FETCH_ASSOC);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        if ($this->Places):
            foreach ($this->Places as $vinculo => $valor):
                //tratar o termo LIMIT e OFFSET como int
                if ($vinculo == 'limit' || $vinculo == 'offset'):
                    $valor = (int) $valor;
                endif;
                $this->Read->bindValue(":{$vinculo}", $valor, (is_int($valor) ? \PDO::PARAM_INT : \PDO::PARAM_STR));
            endforeach;
        endif;
    }

    //Obtem a Conexão e a syntax, executa a query!
    private function Execute() {
        $this->Connect();
        try {
            $this->getSyntax();
            $this->Read->execute();
            $this->Result = $this->Read->fetchAll();
        } catch (PDOException $e) {
            $this->Result = null;
            CiedevErro('<b>Erro ao Ler: </b>' . $e->getMessage(), $e->getCode());
        }
    }

}
