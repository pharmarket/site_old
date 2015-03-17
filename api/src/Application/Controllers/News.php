<?php

namespace Application\Controllers;

class News extends \Library\Controller\Controller{

	public function __construct(){
		$this->setLayout("news");
		$this->addScript("validationFormulaire");
	}

	public function indexAction(){

		$news 		= new \Application\Models\News("localhost");
		$where = "1 ORDER BY `update` DESC";
		$resultNews = $news->fetchAll($where);
		$title		= !empty($resultNews)?"All News" : "News not Found";

		$this->setDataView(array("news" 	 => $resultNews,
								 "pageTitle" => $title));
	}

	public function readAction($id=0){

		$news  		 = new \Application\Models\News("localhost");
		$resultNews  = $news->findByPrimary($id);
		$title 		 = (!empty($resultNews))?$resultNews[0]->titre:"News not Found";
		
		/** 
		  *if (!empty($resultNews)){
		  *	$title = $resultNews[0]->titre;
		  * }else{
		  * $title = "News not Found";
		  * }
		*/

		$this->setDataView(array("news" 	 => $resultNews,
								 "pageTitle" => $title));
	}

	public function readPreviousAction($id=0){

		$news = new \Application\Models\News("localhost");
		//recherche de l'id de la news lu
		$resultNews = $news->findByPrimary($id);
		$idNews = $resultNews[0]->id;

		//recherche de l'id de la news précédente
		$where = "`id`<'" . $idNews . "' ORDER BY id DESC LIMIT 1 " ;
		$newsPrevious   = $news->fetchAll($where,$fields="*");
		$idPreviousNews = $newsPrevious[0]->id; 

		//envoi de l'id au controleur gérant l'affichage d'un news
		if(!empty($idPreviousNews)){
			header("location: " . LINK_ROOT . "news/read/" . $idPreviousNews);
			die();
		}else{
			header("location: " . LINK_ROOT . "news/read/" . $idNews);
			die();
		}
	}

	public function readNextAction($id=0){
		
		$news = new \Application\Models\News("localhost");
		//recherche de la date de la news lu
		$resultNews = $news->findByPrimary($id);
		$idNews = $resultNews[0]->id;

		//recherche de l'id de la news suivante
		$where = "`id`>'" . $idNews . "' ORDER BY id ASC limit 1" ;
		$newsNext = $news->fetchAll($where,$fields="*");
		$idNextNews = $newsNext[0]->id;

		//envoi de l'id au controleur gérant l'affichage des news
		if(!empty($idNextNews)){
			header("location: " . LINK_ROOT . "news/read/" . $idNextNews);
			die();
		}else{
			header("location: " . LINK_ROOT . "news/read/" . $idNews);
			die();
		}
	}

	public function ajoutAction(){

		$this->addScript("validationFormulaire");

		//pour acceder à la vue d'ajout de news, l'utilisateur doit être connecté
		//si pas connecté redirigé vers la vue d'authentification
		if (empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		if (!empty($_POST)){

			if(!isset($_POST['titre']) || empty($_POST['titre'])){
				$error['news'] = "Erreur d'enregistrement (titre ou contenu invalide)! Veuillez recommencer votre saisie";
			}

			if(!isset($_POST['contenu']) || empty($_POST['contenu'])){
				$error['news'] = "Erreur d'enregistrement (titre ou contenu invalide)! Veuillez recommencer votre saisie";
			}

			if(empty($error['news'])){
				$data = array("titre"   => $_POST['titre'],
						      "contenu" => $_POST['contenu']);

				$news = new\Application\Models\News("localhost");
				$resultNews = $news->insert($_POST);
				$_SESSION['news']['ajoutSuccess'] = "News ajoutée";
				header("location: " . LINK_ROOT . "news/index");
			}else{
				$this->setDataView(array("erreur" 	 => $error,
										 "pageTitle" => "Ajout news"));
			}

		}else{
			$this->setDataView(array("pageTitle" => "Ajout news"));
		}
	}

	public function suppressionAction($id=0){

		//pour acceder à la vue de suppression de news, l'utilisateur doit être connecté
		//si pas connecté redirigé vers la vue d'authentification
		if (empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		$news = new\Application\Models\News("localhost");
		$news->deleteByPrimary($id);
		$_SESSION['news']['suppressionSuccess'] = "News supprimée";
		header("location: " . LINK_ROOT . "news/index");
		die();
	}

	public function updateAction($id=0){

		//pour acceder à la vue de de mise a jour de news, l'utilisateur doit être connecté
		//si pas connecté redirigé vers la vue d'authentification
		if (empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		$news = new \Application\Models\News("localhost");
		$resultNews  = $news->findByPrimary($id);
		$title 		 = (!empty($resultNews))?$resultNews[0]->titre:"News not Found";
		$this->setDataView(array("news" 	 => $resultNews,
								 "pageTitle" => $title));
		
		if(!empty($_POST)){

			/** 
			  *if (!empty($resultNews)){
			  *	$title = $resultNews[0]->titre;
			  * }else{
			  * $title = "News not Found";
			  * }
			*/


			if(!isset($_POST['titre']) || empty($_POST['titre'])){
				$error['news'] = "Erreur de mise à jour (titre ou contenu invalide)! Veuillez recommencer votre saisie";
			}

			if(!isset($_POST['contenu']) || empty($_POST['contenu'])){
				$error['news'] = "Erreur de mise à jour (titre ou contenu invalide)! Veuillez recommencer votre saisie";
			}

			if (empty($error['news'])) {
				$where = "id=" . $_POST['id'];
				$data=array('id'=>$_POST['id'], 'titre'=> $_POST['titre'], 'contenu' => $_POST['contenu']);
				$news->update($where, $data);
				$_SESSION['news']['updateSuccess'] = "News mis à jour";
				header("location: " . LINK_ROOT . "news/index");
			}else{
				$this->setDataView(array("erreur" => $error));
			}
		}
	}
}