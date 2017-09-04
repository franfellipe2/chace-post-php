<?php

namespace sql;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

/**
 * <b>Create.class.php</b>
 * Classe resonsável por cadastros genéricos no banco de dados!
 * 
 * @copyright (c) 2017, Francisco S. Filho Sie Webdesing
 */
class Create extends Conn {

    private $Tabela;
    private $Dados;
    private $Result;

    /** @var PDOStatement */
    private $Create;

    /** @var PDO */
    private $Conn;

    /**
     * <b>ExeCreate:</b> Executa um cadastro simplificado no banco de dados utilizando prepared statements.
     * Basta informar o nome da tabela e um array atribuitivo com o nome das coluna e valor!
     * <br>
     * Exemplo: $tabela = 'Usuários'; $dados = ['name' => 'Marcos', 'idade' => '38' ].
     * 
     * @param STRING $Tabela = nome da tabela que vai receber os cadastros
     * @param ARRAY $Dados = Informe um array atribuitivo. (Nome da coluna => Valor ).
     */
    public function ExeCreate($Tabela, array $Dados) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->getSyntax();
        $this->Execute();
    }

    /**
     * @return o ultimo id inserido
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * ****************************************
     * *********** PRIVATE METHODS ************
     * ****************************************
     */

    /**
     * Conectar ao banco de dados
     */
    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Create = $this->Conn->prepare($this->Create);
    }

    /**
     * Responsavel por montar a query do cadastro     
     */
    private function getSyntax() {
        //verifica se é multimpos registros
        if (isset($this->Dados[0]) && is_array($this->Dados[0])):
            /** Campos */
            $Fileds = implode(', ', array_keys($this->Dados[0]));
            /** Chave utilizada ppara atribuir os valores */
            $Places = ':' . implode(', :', array_keys($this->Dados[0]));
        else:

            $Fileds = implode(', ', array_keys($this->Dados));
            $Places = ':' . implode(', :', array_keys($this->Dados));

        endif;
        $this->Create = "INSERT INTO {$this->Tabela} ({$Fileds}) VALUES ({$Places})";
    }

    /**
     * Executa o cadastro
     */
    private function Execute() {
        $this->Connect();

        try {
            if (isset($this->Dados[0]) && is_array($this->Dados[0])):
                foreach ($this->Dados as $row):
                    $this->Create->execute($row);
                    $this->Result[] = $this->Conn->lastInsertId();
                endforeach;
            else:
                $this->Create->execute($this->Dados);
                $this->Result = $this->Conn->lastInsertId();
            endif;
        } catch (PDOException $e) {
            $this->Result = null;
            CiedevErro("<b>Erro ao cadastrar: </b> {$e->getMessage()}", $e->getCode());
        }
    }

}
