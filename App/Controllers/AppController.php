<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
	public function timeline()
	{
		$this->validarAutenticacao();

		$tweet = Container::getModel('Tweet');

		$tweet->__set('fk_id_usuario', $_SESSION['id_usuario']);

		$this->view->tweets = $tweet->getAll();

		$this->render('timeline');
	}

	public function tweet()
	{
		$this->validarAutenticacao();
			
		$tweet = Container::getModel('Tweet');
		$tweet->__set('tweets', $_POST['tweet']);
		$tweet->__set('fk_id_usuario', $_SESSION['id_usuario']);

		$tweet->salvar();

		header('Location: /timeline');
	}

	public function quemSeguir()
	{
		$this->validarAutenticacao();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

		$usuarios = array();

		if($pesquisarPor != '') {
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuarios = $usuario->getAll();
		}

		$this->view->seguir_usuario = $usuarios;

		$this->render('quemSeguir');
	}

	public function validarAutenticacao() // Confirma se o usuário foi autenticado
	{
		session_start();

		if(!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '')
			header('Location: /?login=erro');
	}
}
?>