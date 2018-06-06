<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model
{
	//Listando todas as categorias
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

	}

	//Salvando criações e edições
	public function save()
	{
		$sql = new Sql();

		$result = $sql->select("Call sp_categories_save(:idcategory, :descategory)", array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()
		));

		$this->setData($result[0]);
	}

	//Puxando dados de uma determinda categoria
	public function get($idcategory)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [':idcategory'=>$idcategory]);

		$this->setData($results[0]);

		Category::updateFile();
	}

	//Excluindo uma determinada categoria
	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [':idcategory'=>$this->getidcategory()]);

		Category::updateFile();
	}

	//Atualizando dados dinamicamenteno site
	public static function updateFile()
	{

		$categories = Category::listAll();

		$html = [];

		foreach ($categories as $row) {
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));
	}

	//Coletando produtos relacionado e não relacionados com a categoria
	public function getProducts($related = true)
	{

		$sql = new Sql();

		if ($related === true){

			return $sql->select("SELECT * FROM tb_products WHERE idproduct IN (SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory)", [":idcategory"=>$this->getidcategory()]);
		}else{
			return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN (SELECT a.idproduct FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct WHERE b.idcategory = :idcategory)", [":idcategory"=>$this->getidcategory()]);
		}
	}


	//Criando a páginação
	public function getProductsPage($page = 1, $itemsPerPage = 8)
	{
		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("SELECT SQL_CALC_FOUND_ROWS  * FROM tb_products a INNER JOIN tb_productscategories b On a.idproduct = b.idproduct INNER JOIN tb_categories c ON c.idcategory = b.idcategory Where c.idcategory = :idcategory LIMIT $start, $itemsPerPage", [
			":idcategory"=>$this->getidcategory()]);

		$resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultsTotal[0]["nrtotal"],
			'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}

	//Vinculando produto com a categoria
	public function addProduct(Product $product)
	{
		
		$sql = new Sql();

		$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES (:idcategory, :idproduct)", [
			":idcategory"=>$this->getidcategory(),
			":idproduct"=>$product->getidproduct()
		]);


	}

	//Desvinculando produto com a categoria
	public function removeProduct(Product $product)
	{
		
		$sql = new Sql();

		$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory and idproduct = :idproduct", [
			":idcategory"=>$this->getidcategory(),
			":idproduct"=>$product->getidproduct()
		]);

	}

}

 ?>