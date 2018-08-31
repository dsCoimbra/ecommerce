<?php 


use \Hcode\PageAdmin;
use \Hcode\Model\User;

/* Index Administrativo */
$app->get('/admin/', function() {

	User::verifyLogin();
    
	$page = new PageAdmin();

	$page->setTpl("index");

});

/* Index Login */
$app->get('/admin/login', function() {
    
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

/* Função Login */
$app->post('/admin/login', function() {
    
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});

/* Função Sair */
$app->get('/admin/logout', function() {
    
	User::logout();

	header("Location: /admin/login");
	exit;
});

/* Index Usuários */
$app->get('/admin/users', function() {
    
    User::verifyLogin();

    $search = (isset($_GET['search'])) ? $_GET['search']:"";

    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

    if ($search != ''){

    	$pagination = User::getPageSearch($search, $page, 1);

    } else {

    	$pagination = User::getPage($page);

    }

    
    $pages = [];

    for($x = 0; $x < $pagination['pages']; $x++)
    {
    	array_push($pages, [
    		'href'=>'/admin/users?'.http_build_query([
    			'page'=>$x+1,
    			'search'=>$search
    		]),
    		'text'=>$x+1
    	]);
    }

	$page = new PageAdmin();

	$page->setTpl("users", array(
		"users"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages
	));
});

/* Pagina esqueci a senha */
$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");

});

/* Função esqueci a senha */
$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

/* Pagina enviado link */
$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");

});

/* Página reset senha */
$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

/* Função reset senha */
$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

 ?>