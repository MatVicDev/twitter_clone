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

		$_SESSION['pesquisarPor'] = $pesquisarPor;

		$usuarios = array();

		if($pesquisarPor != '') {
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id_usuario', $_SESSION['id_usuario']);
			$usuarios = $usuario->getAll();
		}

		$this->view->seguir_usuario = $usuarios;

		$this->render('quemSeguir');
	}

	public function acao()
	{
		$this->validarAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id_usuario', $_SESSION['id_usuario']);

		if($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguindo);
		} else if($acao == 'deixar_de_seguir') {
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
		}
		header('Location: /quem_seguir?pesquisarPor='.$_SESSION['pesquisarPor']);
	}

	public function delete()
	{
		$this->validarAutenticacao();

		$tweet = isset($_GET['tweet']) ? $_GET['tweet'] : '';

		$usuario = Container::getModel('Tweet');
		$usuario->__set('id_tweet', $tweet);
		$usuario->deletarTweet();

		header('Location: /timeline');
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