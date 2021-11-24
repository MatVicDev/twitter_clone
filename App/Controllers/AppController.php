<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
	public function timeline()
	{
		session_start();

		// Se o usuário foi autenticado
		if(!empty($_SESSION['id_usuario']) && !empty($_SESSION['nome'])) {
			$this->render('timeline');
		} else {
			header('Location: /?login=erro');
		}
	}
}
?>