<?php
namespace Mockup\Classes;

use \Dotenv\Dotenv;

class Database
{
  /**
   * @author Pedro Ruiz Hidalgo
   * 
   * RET_MODE describes the mode to return data
   * 
   * \PDO::FETCH_ASSOC -> returns a named array hash
   * \PDO::FETCH_BOTH -> returns an array with nimeric index AND named hash,
   * 
   * more information and modes: https://www.php.net/manual/en/pdostatement.fetchall.php
   * 
   * every child of this class must call to constructor as parent::__construct(); in its own constructor
   */
  
  
  const RET_MODE = \PDO::FETCH_ASSOC;
  
  protected $data = [];
  protected $connection;
  protected $rowCount;
  protected $lastInsert;


  function __construct(bool $persistent = true)
  {
    $direnv = dirname(dirname(dirname(dirname(__FILE__))));

    $this->data = Dotenv::createImmutable($direnv)->safeload();
    
    try
    {
      $this->connection = new \PDO(
          $this->data['database.dsn'],
          $this->data['database.user'],
          $this->data['database.password'],
          [
            \PDO::ATTR_PERSISTENT => $persistent,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
          ]
        );
    }
    catch (\PDOException $e)
    {
     throw new \Exception("¡Error!: " . $e->getMessage());
    }
  }

  public function execPrepared(string $query, array $params = [], int $fetchStyle = self::RET_MODE): ?array
  {
    $stmt = $this->connection->prepare($query, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
    if( $params !==[] ) $stmt->execute($params);
    $this->rowCount   = $stmt->rowCount();
    $this->lastInsert = $this->connection->lastInsertId();
    return $stmt->fetchAll($fetchStyle) ?? null;
  }
}