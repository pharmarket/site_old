<?php

namespace Application\Controllers;

class User extends \Library\Controller\Controller{

	public function __construct(){
		$this->setLayout("user");
	}

	public function indexAction(){

		header("location: " . LINK_ROOT . "user/login");
		die();
	}

	public function loginAction(){
		
		$this->addScript("validationFormulaire");

		//pour accéder à la vue login l'utilisatoir ne doit pas être connecté
		//si connecté il est redirigé vers la page d'accueil
		if (!empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT);
			die();
		}

		if(!empty($_POST)){

			if((!isset($_POST['emailAddress'])) || !preg_match("/^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/", $_POST['emailAddress']))
			 	$error['emailAddress']  = "Vérifier la saisie de votre email";

			if((!isset($_POST['password'])) || !preg_match("/^[a-z0-9_-]{8,16}$/", $_POST['password']))
			  	$error['password']  = "Vérifier la saisie de votre mot de passe";


			if(empty($error['emailAddress']) && empty($error['password'])){
				// Verification du mail et mot de passe dans la base de donnée
				$password = md5($_POST['password']);
				$where = "emailAddress = '" . $_POST['emailAddress']  . "'" . " AND password = '" . $password . "'";
				$user = new \Application\Models\User("localhost");
				$identification = $user->fetchAll($where, $fields="*");

				if($identification){
					// Mise en session de l'email de l'utilisateur et redirection
					$_SESSION['user']['id'] 		  = $identification[0]->id;
					$_SESSION['user']['emailAddress'] = $identification[0]->emailAddress;
					$_SESSION['user']['secondName']	  = $identification[0]->secondName;
					$_SESSION['user']['firstName']	  = $identification[0]->firstName;
					header("location: " . LINK_ROOT);
					die();
				}else{
					$error['authentification'] = "Erreur d'authentification ! Email et/ou mot de passe incorrecte(s)";
					$this->setDataView(array("erreur" 	 => $error,
											 "pageTitle" => "Authentification"));
				}

			}else{
				$this->setDataView(array("erreur" => $error,
									     "pageTitle"   => "Authentification"));
			}
		}else{
			$this->setDataView(array("pageTitle" => "Authentification"));
		}

		if (isset($_SESSION['successInscription'])){
			$this->setDataView(array("msg" => $_SESSION['successInscription']));
			unset($_SESSION['successInscription']);
		}
	}

	public function logoutAction($id = 'deconnexion'){
		session_unset();
		if ($id=="deconnexion"){
			header("location: " . $_SERVER['HTTP_REFERER']);
			die();
		}
	}

	public function inscriptionAction(){

		$this->addScript("validationFormulaire");

		//pour accéder à la vue inscription l'utilisatoir ne doit pas être connecté
		//si connecté il est redirigé vers la page d'accueil
		if (!empty($_SESSION['user'])) {
			header("location: " . $_SERVER['HTTP_REFERER']);
			die();
		}

		if(!empty($_POST)){

			//Vérification des champs saisis par l'utilisateur			
			if((!isset($_POST['secondName'])) || empty($_POST['secondName']))
				$error['secondName'] = "Vérifier la saisie de votre prénom";

			if((!isset($_POST['firstName'])) || empty($_POST['firstName']))
			   	$error['firstName']  = "Vérifier la saisie de votre nom";

			if((!isset($_POST['emailAddress'])) || !preg_match("/^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/", $_POST['emailAddress']))
			 	$error['emailAddress']  = "Vérifier la saisie de adresse email";

			if((!isset($_POST['password'])) || !preg_match("/^[a-z0-9_-]{8,16}$/", $_POST['password']))
			  	$error['password']  = "Vérifier la saisie de votre mot de passe (doit contenir entre 8 et 16 caractères composés de lettres ou chiffres)";

			if ($_POST['passwordConfirm']!= $_POST['password'])
				$error['passwordConfirm'] = "Le mot de passe de confirmation doit ête identique au mot de passe";

			//vérification mail n'existe pas en base de donnée
			$user = new \Application\Models\User("localhost");
			$testMail = $user->fetchAll($where=1, $fields="*");

			for ($i=0;$i<count($testMail);$i++) {
				foreach ($testMail[$i] as $key => $value) {
					if ($key == 'emailAddress' && $value == $_POST['emailAddress']) {
						$error['existEmailAdress'] = "L'adresse mail saisi existe déjà dans notre base de donnée";
						break;
					}
				}
			}

			//si aucune erreur création du compte en base de donnée
			if(empty($error)){
				//$user = new \Application\Models\User("localhost");
				$_POST['password'] = md5($_POST['password']);
				array_pop($_POST);

				try {
				$user->insert($_POST);
				$_SESSION['successInscription'] = "Votre compte a été crée. Vous pouvez vous connecter.";

				}catch(Execption $e){
					var_dump($e->getMessage());
				}

				header("location: " . LINK_ROOT . "user/login");
				die();
			//sinon on passe un tableau d'erreur à la vue
			}else{
				$this->setDataView(array("erreur"    => $error,
										 "pageTitle" => "Inscription"));
			}

		}else{
			$this->setDataView(array("pageTitle" => "Inscription"));
		}
	}

	public function updateAction(){

		$this->addScript("validationFormulaire");

		//pour accéder à la vue de mise à jour l'utilisatoir  doit être connecté
		//si pas connecté il est redirigé vers la vue de connexion
		if(empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		//recupération des informations de l'utilisateur connecté
		$user = new \Application\Models\User("localhost");

		if(empty($_POST)){
		$id=$_SESSION['user']['id'];
		$resultUser  = $user->findByPrimary($id);

		$secondName   = $resultUser[0]->secondName;
		$firstName    = $resultUser[0]->firstName;
		$emailAddress = $resultUser[0]->emailAddress;
		
		$this->setDataView(array("secondName"   => $secondName,
								 "firstName"    => $firstName,
								 "emailAddress" => $emailAddress,
								 "pageTitle"    => "Mise à jour profile"));
		
		}else{

			//Vérification des champs saisis par l'utilisateur			
			if((!isset($_POST['secondName'])) || empty($_POST['secondName']))
				$error[] = "Prénom non valide";

			if((!isset($_POST['firstName'])) || empty($_POST['firstName']))
			   	$error[] = "Nom non valide";

			if((!isset($_POST['emailAddress'])) || !preg_match("/^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,3}$/", $_POST['emailAddress']))
			 	$error[] = "Adresse mail non valide";

			//Vérification que l'email eventuellement mis à jour n'existe pas déja en base de donnée
			if($_POST['emailAddress']!=$_SESSION['user']['emailAddress']) {

				$testMail = $user->fetchAll($where=1, $fields="*");
				for ($i=0;$i<count($testMail);$i++) {
					foreach ($testMail[$i] as $key => $value) {
						if ($key == 'emailAddress' && $value == $_POST['emailAddress']) {
							$error['existEmailAdress'] = "Adresse mail saisi déja existante dans notre base de donnée";
							break;
						}
					}
				}
			}

			if(!empty($error)) {

				$this->setDataView(array("secondName" 	=> $_POST['secondName'],
										 "firstName" 	=> $_POST['firstName'],
										 "emailAddress" => $_POST['emailAddress'],
										 "erreur" 		=> $error,
								 		 "pageTitle"	=> "Mise à jour profile"));

			}else{

				//mise à jour des informations dans la base de donnée
				$where = "id=" . $_SESSION['user']['id'];
				$data=array('id' => $_SESSION['user']['id'], 'secondName' => $_POST['secondName'], 'firstName' => $_POST['firstName'], 'emailAddress' => $_POST['emailAddress']);
				$user->update($where, $data);
				//mise à jour des information de l'utilisateur en session pour mettre à jour le message de bienvenue
				$_SESSION['user']['secondName']   = $_POST['secondName'];
				$_SESSION['user']['firstName']	  = $_POST['firstName'];
				$_SESSION['user']['emailAddress'] = $_POST['emailAddress'];
				//mise en session d'un message de confirmation de mise à jour
				$_SESSION['user']['successUpdate'] = "Votre compte a été mis à jour";
				header("location: " . LINK_ROOT . "user/update");
				die();
				
			}
		}
	}

	public function passwordAction(){

		$this->addScript("validationFormulaire");

		//si utilisateur connecté il est redirigé vers la page d'accueil
		if(!empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		if(!empty($_POST)){

			//vérification de la saisi du mal par l'utilisateur
			if(!preg_match("/^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/", $_POST['emailAddress']))
			$error['emailAddress']  = "Vérifier la saisie de votre email";


			if(empty($error['emailAddress'])){

				//vérification que l'adresse mail est présent dans la base de données
				$user = new \Application\Models\User("localhost");
				$testMail = $user->fetchAll($where=1, $fields="*");
				$mailPresent = false;

				for ($i=0;$i<count($testMail);$i++) {
					foreach ($testMail[$i] as $key => $value) {
						if ($key == 'emailAddress' && $value == $_POST['emailAddress']) {
							$mailPresent = true;
						}
					}
				}

				if ($mailPresent){
					//création d'un nouveau mot de passe
					//initialisation de la variable
					$mdp 	  = "";
					$longueur = 8;
					
					//caractères possibles lors de la génétation du mot de passe
					$possible 	 = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
					$longueurMax = strlen($possible);

					if ($longueur > $longueurMax) {
						$longueur = $longueurMax;
					}

					//initialisation d'un cmpteur
					$i = 0;

					//ajoute un caractère aléatoire à $mdp jusqu'à ce que $longeur soit atteint
					while ($i < $longueur) {
						//choix de manière aléatoire d'un caractère 
						$caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);
						//vérification que le caractère choisi n'est pas déja présent dans $mdp
						if (!strstr($mdp, $caractere)){
							$mdp .= $caractere;
							$i++;
						}
					}

					//mise à jour du mot de passe dans la base de donnée
					$where ="emailAddress='" . $_POST['emailAddress'] . "'";
					$data = array('password' => md5($mdp));
					$user->update($where, $data);

			        //envoi de l'e-mail avec le nouveau mot de passe
			        $mail  = $_POST['emailAddress'];
			        $sujet = "Renouvellement de votre mot de passe";
			        $message = "Votre nouveau mot de passe est : " . $mdp;
			        mail($mail, $sujet, $message);
			      	header("location: " . LINK_ROOT . "user/login");
			    }else{
			    	$this->setDataView(array("mail" 	 => "Mail inexistant dans notre base de donnée !!",
			    							 "pageTitle" => "Renouvellement mdp"));
			    }
			}else{
				$this->setDataView(array("erreur"    => $error,
										 "pageTitle" => "Renouvellement mdp"));
			}

		}else{
			$this->setDataView(array("pageTitle" => "Renouvellement mdp"));
		}
	}

	public function suppressionAction(){

		$this->addScript("validationFormulaire");		

		//pour accéder à la vue login l'utilisatoir ne doit pas être connecté
		//si connecté il est redirigé vers la page d'accueil
		if (empty($_SESSION['user'])) {
			header("location: " . LINK_ROOT . "user/login");
			die();
		}

		if(!empty($_POST)){
			$user = new \Application\Models\User("localhost");
			$where = "emailAddress = '" . $_SESSION['user']['emailAddress'] . "'";
			//mot de passe enregistré en base de donnée
			$testUser = $user->fetchAll($where, $fields="*");
			$passwordBddUser = $testUser[0]->password;
			//mot de passe saisi
			$passwordSaisi = md5($_POST['password']);

			//vérification mot de passe saisi par l'utilisateur
			if ($passwordBddUser == $passwordSaisi) {
				$user->delete($where);
				$this->logoutAction("suppression");
				header("location: " . LINK_ROOT);
				die();
			}else{
				$this->setDataView(array("password"  => "Le mot de passe saisi n'est pas valide !!",
										 "pageTitle" => "Suppression compte"));
			}

		}else{
			$this->setDataView(array("pageTitle" => "Suppression compte"));
		}		
	}
}