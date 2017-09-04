<?php

namespace sql;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

/**
 * <b>Update.class.php</b>
 * Classe responsavel por atualizações genericas no banco de dados
 * 
 * @copyright (c) 2017, Francsico S. Filho Sie Webdesing 
 */
class Update extends Conn {

    private $Tabela;
    private $Dados;
    private $Termos;
    private $Places;
    private $Result;

    /** @var PDOStatement */
    private $Update;

    /** @var PDO */
    private $Conn;

    public function ExeUpdate($Tabela, array $Dados, $Termos, $ParseString) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->Termos = (string) $Termos;
        parse_str($ParseString, $this->Places);

        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Update->rowCount();
    }

    /**
     * @param STRING $ParseString = modifica a condição dos paramentros passos
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
        $this->Update = $this->Conn->prepare($this->Update);
    }

    //Cria a sintaxe da query para Prepared Statements
    private function getSyntax() {
        foreach ($this->Dados as $key => $value):
            $Places[] = $key . ' = :' . $key;
        endforeach;

        $Places = implode(', ', $Places);
        $this->Update = "UPDATE {$this->Tabela} SET {$Places} {$this->Termos}";
    }

    //Obtem a Conexão e a syntax, executa a query!
    private function Execute() {
        $this->Connect();

        try {
            $this->Update->execute(array_merge($this->Dados, $this->Places));
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            CiedevErro('<b>Erro ao Atualizar: </b>' . $e->getMessage(), $e->getCode());
        }
    }

}
