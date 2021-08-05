<?php namespace Mockup\Models;



class Users extends \Mockup\Classes\Database
{

  private $defaultGroupId = 2;

  function __construct()
  {
    parent::__construct();
  }

  public function getAll(): array
  {
    return $this->execPrepared('select * from users',[]);
  }

  public function getByEmail($email): array
  {
    return $this->execPrepared(
      'select * from users where email = :email',
      [':email' => $email]
    );
  }

  public function updateIPAddress(string $email, string $ip): bool
  {
    return $this->execPrepared(
      'update users set ip_address = :ip_address, updated_at = :updated_at where email = :email',
      [':ip_address' => $ip, ':email' => $email, ':updated_at' => time()]
    ) === [] ? true : false;
  }

  public function newUser(array $user): bool 
  {
    try
    {
      $this->connection->beginTransaction();
      
      $newUser = $this->execPrepared(
        <<<INSERTQUERY
          INSERT INTO users(
            ip_address,
            username,
            password,
            email,
            activation_selector,
            activation_code,
            forgotten_password_selector,
            forgotten_password_code,
            forgotten_password_time,
            remember_selector,
            last_login,
            active,
            first_name,
            last_name,
            grantplace,
            phone,
            created_at,
            updated_at,
            deleted_at) VALUES (
              :ip_address,
              :username,
              :password,
              :email,
              :activtion_selector,
              :activation_code,
              :forgotten_password_selector,
              :forgotten_password_code,
              :forgotten_password_time,
              :remember_selector,
              :last_login,
              :active,
              :first_name,
              :last_name,
              :grantplace,
              :phone,
              :created_at,
              :updated_at,
              :deleted_at)
        INSERTQUERY,
        [
          ':ip_address' =>                  $user['ip_address'] ?? null,
          ':username' =>                    $user['username'] ?? null,
          ':password' =>                    password_hash($user['password'],PASSWORD_BCRYPT,['cost'=>12]) ?? null,
          ':email' =>                       $user['email'] ?? null,
          ':activtion_selector' =>          $user['activtion_selector'] ?? null,
          ':activation_code' =>             $user['activation_code'] ?? null,
          ':forgotten_password_selector' => $user['forgotten_password_selector'] ?? null,
          ':forgotten_password_code' =>     $user['forgotten_password_code'] ?? null,
          ':forgotten_password_time' =>     $user['forgotten_password_time'] ?? null,
          ':remember_selector' =>           $user['remember_selector'] ?? null,
          ':last_login' =>                  $user['last_login'] ?? null,
          ':active' =>                      $user['active'] ?? null,
          ':first_name' =>                  $user['first_name'] ?? null,
          ':last_name' =>                   $user['last_name'] ?? null,
          ':grantplace' =>                  $user['grantplace'] ?? null,
          ':phone' =>                       $user['phone'] ?? null,
          ':created_at' =>                  time(),
          ':updated_at' =>                  time(),
          ':deleted_at' =>                  null,
        ]
      );
      if($this->rowCount !== 1) return false;
      $newUserGroup = $this->execPrepared(
        <<<INSERTQUERY
          INSERT INTO users_groups (user_id, group_id, created_at, updated_at)  VALUES (:firstParam, :secondParam, :thirdParam, :fourthParam)
        INSERTQUERY,
        [
          ':firstParam'     =>  $this->lastInsert,
          ':secondParam'    =>  $this->defaultGroupId,
          ':thirdParam'     =>  time(),
          ':fourthParam'    =>  time()
        ]
      );
      $this->connection->commit();
      return ($this->rowCount === 1);
    } 
    catch(\PDOException $e)
    {
      $this->connection->rollback();
      die($e->getMessage());
    }
  }

  public function softDelete($email): bool
  {
    $this->execPrepared(
      'update users set deleted_at = :time where email=:email and isnull(deleted_at)',
      [':time' => time(), ':email'=>$email]
    );
    return ($this->rowCount===1);
  }

  public function softUndelete($email): bool
  {
    $this->execPrepared(
      'update users set deleted_at = :time where email=:email and not isnull(deleted_at)',
      [':time'=> null, ':email'=>$email]
    );
    return ($this->rowCount===1);
  }

  public function hardDelete(string $email): bool
  {
    //
    // mind the user_groups row deletion is auto because the 
    // foreing key deletes on cascade according to its definition
    //
    try
    {
      $this->connection->beginTransaction();
      $id = $this->getByEmail($email)[0]['id'];

      $this->execPrepared("delete from users where id = :firstParam",[':firstParam' => $id]);

      $this->connection->commit();
      return ($this->rowCount === 1);
    }
    catch (\PDOException $e)
    {
      print $e->getMessage();
      return false;
    }
  }
}