<?php 

session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;


$app = new Slim();

$app->config('debug', true);

require_once("index/site.php");
require_once("index/admin.php");
require_once("index/admin-users.php");
require_once("index/admin-categories.php");
require_once("index/admin-products.php");

$app->run();

 ?>