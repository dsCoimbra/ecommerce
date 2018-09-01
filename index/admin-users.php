<?php 

use \Hcode\Model\User;
use \Hcode\PageAdmin;

/* Index Criação de Usuario */

$app->get("/admin/users/:iduser/password", function($iduser){

    User::verifyLogin();

    $user = new User;

    $user->get((int)$iduser);

    $page = new PageAdmin();

    $page->setTpl('users-password',[
        "user"=>$user->getValues(),
        "msgError"=>User::getError(),
        "msgSuccess"=>User::getSuccess()
    ]);
});

$app->post("/admin/users/:iduser/password", function($iduser){

    User::verifyLogin();

    if (!isset($_POST['despassword']) || $_POST['despassword'] === ''){

        User::setError("Preencha a nova senha.");
        header("Location: /admin/users/$iduser/password");
        exit;

    }

    if (!isset($_POST['despassword-confirm']) || $_POST['despassword-confirm'] === ''){

        User::setError("Preencha a confirmação nova senha.");
        header("Location: /admin/users/$iduser/password");
        exit;
        
    }

    if ($_POST['despassword']!== $_POST['despassword-confirm']){

        User::setError("Senhas diferentes.");
        header("Location: /admin/users/$iduser/password");
        exit;

    }

    $user = new User;

    $user->get((int)$iduser);

    $user->setPassword(User::getPasswordHash($_POST['despassword']));

    User::setSuccess("Senha alterada com sucesso.");
    header("Location: /admin/users/$iduser/password");
    exit;

});
$app->get('/admin/users/create', function() {
    
    User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");


});

/* Função Criação de Usuario */
$app->post('/admin/users/create', function() {
    
    User::verifyLogin();

    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit;

});

/* Função Delete */
$app->get('/admin/users/:iduser/delete', function($iduser) {
    
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $user->delete();

    header("Location: /admin/users");

    exit;

});

/* Index Edição de Usuário */
$app->get('/admin/users/:iduser', function($iduser) {
    
    User::verifyLogin();
    
	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});

/* Função Edição de Usuário */
$app->post('/admin/users/:iduser', function($iduser) {
    
    User::verifyLogin();

    $user = new User();

    $user->get((int)$iduser);

    $_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

    $user->setData($_POST);

    $user->update();

    header("Location: /admin/users");
    exit;
});

 ?>