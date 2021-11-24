<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action
{
	public function index()
	{
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : ''; // Verifica se o usuário foi autenticado
		$this->render('index');
	}

	public function inscreverse()
	{
		$this->view->usuario = array(
			'nome' => '',
			'email' => '',
			'senha' => '',
		);
		$this->view->erroCadastro = false;
		$this->render('inscreverse');
	}

	public function registrar()
	{
		// Receber os dados do formulário
		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', $_POST['senha']);

		// Verifica se é um usuário válido e se o usuário já foi cadastrado
		if($usuario->validarCadastro() && count($usuario->getEmailUsuario()) == 0) { 
			
			$usuario->salvar(); // Salva o registro no banco de dados

			$this->render('cadastro');

		} else {
			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha'],
			);
			$this->view->erroCadastro = true;
			$this->render('inscreverse');
		}

	}
}
?>