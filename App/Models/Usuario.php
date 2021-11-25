<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{
	private $id_usuario;
	private $nome;
	private $email;
	private $senha;

	public function __get($attr)
	{
		return $this->$attr;
	}

	public function __set($attr, $value)
	{
		$this->$attr = $value;
	}

	// Salvar usuário no banco de dados
	public function salvar()
	{
		$query = 'INSERT INTO tb_usuarios(nome, email, senha)VALUES(:NOME, :EMAIL, :SENHA)';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':NOME', $this->__get('nome'));
		$stmt->bindValue(':EMAIL', $this->__get('email'));
		$stmt->bindValue(':SENHA', $this->__get('senha'));
		$stmt->execute();

		return $this;
	}

	// Validar um cadastro
	public function validarCadastro()
	{
		$valido = true;

		if(strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha')) < 3)
			$valido = false;

		return $valido;
	}

	// Verificar se o usuário já foi cadastrado
	public function getEmailUsuario()
	{
		$query = 'SELECT nome, email FROM tb_usuarios WHERE email = :EMAIL';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':EMAIL', $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Autenticar um usuário
	public function autenticar()
	{
		$query = 'SELECT id_usuario, nome, email FROM tb_usuarios WHERE email = :EMAIL AND senha = :SENHA';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':EMAIL', $this->__get('email'));
		$stmt->bindValue(':SENHA', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		if($usuario['id_usuario'] != '' && $usuario['nome'] != '') {
			$this->__set('id_usuario', $usuario['id_usuario']);
			$this->__set('nome', $usuario['nome']);
		}

		return $this;
	}

	public function getAll()
	{
		$query = 'SELECT id_usuario, nome, email FROM tb_usuarios WHERE nome LIKE :NOME';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':NOME', '%'.$this->__get('nome').'%');
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}
?>