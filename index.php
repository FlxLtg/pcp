<?php


// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Entity\Request;
use Entity\User;
use Controllers\IndexController;
use Controllers\UserController;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/entity"), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters

session_start();

$conn = array(
          'dbname' => 'framework_felix',
          'user' => 'felix',
          'password' => '2018',
          'driver' => 'pdo_mysql',
         // 'host' => 'localhost',
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
$class = "Controllers\\" . (isset($_GET['c']) ? ucfirst($_GET['c']) . 'Controller' : 'IndexController');
$target = isset($_GET['t']) ? $_GET['t'] : "index";
$getParams = isset($_GET) ? $_GET : null;
$postParams = isset($_POST) ? $_POST : null;
$request = new Request();
// $params = [
//     "get"  => $getParams,
//     "post" => $postParams,
//     "entityManager" => $entityManager,
//     "path"=> "http://195.154.118.169/felix/tpmvc/"
// ];


if (isset($_SESSION['id'])) {

  $user = $entityManager->getRepository(User::class)->find($_SESSION['id']);
  $request->setUser($user);
//   $params['user']=$user;
}

$request->setEm($entityManager);
$request->setPost($postParams);
$request->setGet($getParams);
$request->setPath("http://195.154.118.169/felix/tpmvc/");

$params = [
    "request" => $request,
]; 
 
if (isset($_SESSION['id'])){ 
  if (class_exists($class, true)) {
    $class = new $class();
    if (in_array($target, get_class_methods($class))) {
        call_user_func_array([$class, $target], $params);
    }
  else {
        call_user_func([$class, "index"]);
    }
  } 
}
else if ($class == "Controllers\IndexController" && in_array($target, get_class_methods($class))) // si c = index et qu'on a un t = methode existante de c
  { 
    $class = new IndexController; // c = index
    call_user_func_array([$class, $target], $params); // c = index et t = la methode existante
  }
else if ($class == "Controllers\UserController" && in_array($target, get_class_methods($class))) // si c = user et qu'on a un t = methode existante de c
{
  $class = new UserController; // c = user
  call_user_func_array([$class, $target], $params); // c = user et t = la methode existante
}
else { // dans tout les autres cas ou c != index et t n'existe pas alors
  $class = new IndexController; // c = index 
  call_user_func_array([$class, "index"], $params); // c = index et t = index
  }




 
?>