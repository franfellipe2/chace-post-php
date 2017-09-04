<?php

namespace sql;

//protege contra o acesso direto ao arquivo
if (!defined('BASE_URL')):
    header('location: 404');
    die;
endif;

/**
 * Conn.class.php [ CONEXAO ]
 * Classe abstrata de conexão com o banco de dados utilizando o padrão sinlgeton.
 * Retorna um objeto PDO pelo método estático getConn().
 * 
 * @copyright (c) 2017, Francisco S. Filho SIEWEBDESING
 */
class Conn {

    private static $Host = HOST;
    private static $User = USER;
    private static $Pass = PASS;
    private static $Dbsa = DBSA;

    /** @var PDO */
    private static $Connect = null;

    public function __construct() {
        self::Conectar();
    }

    /**
     * Conecta ao banco de dados com o pattern singleton
     * Retorna um objeto PDO!
     */
    private static function Conectar() {
        try {
            if (self::$Connect == null):
                $dsn = 'mysql:host=' . self::$Host . ';dbname=' . self::$Dbsa;
                $options = [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];
                self::$Connect = new \PDO($dsn, self::$User, self::$Pass, $options);
            endif;
        } catch (PDOException $e) {
            CiedevPhpErro($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            die;
        }

        self::$Connect->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return self::$Connect;
    }

    /** Retorna um objeto PDO Singleton Pattern. */
    public static function getConn() {
        return self::Conectar();
    }

}
