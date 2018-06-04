<?php 

use \Hcode\Page;

/* Index Site */
$app->get('/', function() {
    
	$page = new Page();

	$page->setTpl("index");

});

 ?>