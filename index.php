<?php
namespace Mockup;

$inicio = microtime(true);

require_once 'vendor/autoload.php';

//  $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);

//  die(var_dump($dotenv->safeLoad()));


// $db = new \Mockup\Classes\Database();


// $user = $db->execPrepared(
//   'select * from users where id = :id',
//   [':id' => '1'],
//   \PDO::FETCH_ASSOC // this is optional
// );

// foreach($user as $singleUser)
// {
//   var_dump($singleUser);
// }

// 


$user = new \Mockup\Models\Users();

//print_r($user->getAll());
//print_r($user->getByEmail('admin@admin.com'));
// var_dump($user->updateIpAddress('correo@pedroruizhidalgo.es','127.0.0.1'));

// try
// {
//   var_dump($user->newUser([
//     'ip_address'    =>  '127.0.0.1',
//     'username'      =>  'xxxxxxxn@gmail.com',
//     'password'      =>  password_hash('password',PASSWORD_BCRYPT,['cost'=>12]),
//     'email'         =>  'xxxxxxxn@gmail.com',
//     'active'        =>  1,
//     'first_name'    =>  ucfirst('name'),
//     'last_name'     =>  ucfirst('last name'),
//     'phone'         =>  '+34 111111111'
//   ]));
// } 
// catch(\PDOException $e) 
// {
//   die($e->getMessage().PHP_EOL);
// }

// try
// {
//   var_dump($user->softDelete('carmenmoon@gmail.com'));
// }
// catch(\PDOException $e)
// {
//   die('Error--->' . $e->getMessage().PHP_EOL);
// }

try
{
  var_dump($user->softUndelete('carmenmoon@gmail.com'));
}
catch(\PDOException $e)
{
  die('Error--->' . $e->getMessage().PHP_EOL);
}

$final = microtime(true);
print_r( PHP_EOL.($final-$inicio) . ' microsegundos' . PHP_EOL . PHP_EOL);