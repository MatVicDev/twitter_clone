<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action
{
	public function autenticar()
	{
		$usuario = Container::getModel('Usuario');

		// Recebendo dados do formulário
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		$usuario->autenticar(); // Autenticação

		// Se o usuário for autenticado corretamente
		if(!empty($usuario->__get('id_usuario')) && !empty($usuario->__get('nome'))) {
			session_start(); // Iniciando uma sessão

			$_SESSION['id_usuario'] = $usuario->__get('id_usuario');
			$_SESSION['nome'] = $usuario->__get('nome');

			header('Location: /timeline');
		} else {
			header('Location: /?login=erro');
		}
	}

	public function sair() // Quando o usuário resolvr sair
	{
		session_start();
		session_destroy();
		header('Location: /');
	}
}
?>