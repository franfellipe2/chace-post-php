<?php

namespace sql;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;
/**
 * <b>Delete.class.php</b>
 * Classe responsavel por Deletar genericamente no banco de dados
 * 
 * @copyright (c) 2017, Francsico S. Filho Sie Webdesing 
 */
class Delete extends Conn {

    private $Tabela;
    private $Termos;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Delete;

    /** @var PDO */
    private $Conn;

    /**
     * <b>ExecDelete: </b> Executa o delete
     * 
     * @param type $Tabela = Nome da tabela onde os valores serão deletados
     * @param type $Termos = Condição do delete, ex: id = :id
     * @param type $ParseString = valores da termos passados via Prepared Statments, ex: id=1
     */
    public function ExeDelete($Tabela, $Termos, $ParseString) {
        $this->Tabela = (string) $Tabela;
        $this->Termos = (string) $Termos;
        // grava os dados como array em $this->Places
        parse_str($ParseString, $this->Places);

        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Delete->rowCount();
    }

    /**
     * @param STRING $ParseString = modifica a condição dos paramentros
     */
    public function setPlaces($ParseString) {
        $this->getSyntax();
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
        $this->Delete = $this->Conn->prepare($this->Delete);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        foreach ($this->Places as $key => $value):
            $Places[] = $key . ' = :' . $key;
        endforeach;

        $Places = implode(', ', $Places);
        $this->Delete = "DELETE FROM {$this->Tabela} {$this->Termos}";
    }

    //Obtem a Conexão e a syntax, executa a query!
    private function Execute() {
        $this->Connect();

        try {            
            $this->Delete->execute($this->Places);
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            CiedevErro('<b>Erro ao Deletar: </b>' . $e->getMessage(), $e->getCode());
        }
    }

}
