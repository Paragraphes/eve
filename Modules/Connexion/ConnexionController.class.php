<?php
namespace Modules\Connexion;

if (!defined("EVE_APP"))
	exit();

class ConnexionController extends \Library\BackController{
	public function executeIndex(\Library\HTTPRequest $request){
		$this->page->addVar('title', CONNEXION_TITLE);
	
		if($request->existPost('login')){
			$connexion = new \Modules\Connexion\Entities\log(array(
																'login' => $request->dataPost('login'),
																'password' => $request->dataPost('password')
															));
			
			$connexionManager = $this->managers->getManagersOf('log');
				
			$userId = $connexionManager->checkAcces($connexion, $this->app->user()->getSessId(), $this->app->user()->getIP());
			
			if($userId > 0){
				$userManager = $this->managers->getManagersOf('user', 0);
				
				$user = $userManager->get($userId);
				$sub =  $userManager->getSubUser($user->id());
				
				if ($user->reference_user() == -1 || $user->reference_user() == 0) {
					$this->app->user()->setId($userId);
					$this->app->user()->setAttribute("reference_for", 0);
					$this->app()->user()->setAttribute("base_id", -1);
					if (count($sub)) {
						$this->app->user()->setAttribute("reference_for", -1);
						$this->app()->user()->setAttribute("base_id", $userId);
						$this->app()->user()->setAttribute("listeSubUser", array_merge(array($user), $sub));
					}
				} else {
						$this->app()->user()->setAttribute("base_id", -1);
					$this->app->user()->setAttribute("reference_for", $user->reference_user());
					$this->app->user()->setId($userId);
				}
				
				$this->app->user()->setAuthenticated(true);
				$this->app->user()->setAdmin($user->admin());
				$this->app->user()->setAttribute("login", $request->dataPost('login'));
				$this->app->user()->setAttribute("nom", $user->nom());
				$this->app->user()->setAttribute("prenom", $user->prenom());
				$this->app->user()->setAttribute("email", $user->email());
				$this->app->user()->setAttribute("entreprise", $user->entreprise());
				
				$this->app->httpResponse()->redirect($this->page->getVar("root") . $request->extendUri());
			}else{
				switch($userId){
					case -1:
						$this->app->user()->setFlash(ERROR_TO_MUTCH_CO);
						break;
					case 0:
					default:
						$this->app->user()->setFlash(ERROR_LOGIN);
				}
			}
			
		}else{
			$connexion = new \Modules\Connexion\Entities\log;
		}
		
		$form = new \Library\FormBuilder($connexion);
		
		$this->page->addVar("form", $form->build('./Modules/Connexion/Form/Connexion.xml', $this->page->getVar("root") . $request->extendUri()));
	}
}

?>