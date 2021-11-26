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

	// Buscar por um ou vários usuários no DB
	public function getAll()
	{
		$query = '
		SELECT 
			u.id_usuario, u.nome, u.email,
			(
				SELECT
					count(*)
				FROM
					tb_seguidores as s
				WHERE
					s.fk_id_usuario = :ID_USUARIO AND s.id_usuario_seguindo = u.id_usuario
			) as seguindo_sn
		FROM 
			tb_usuarios as u
		WHERE 
			nome 
		LIKE :NOME AND id_usuario != :ID_USUARIO';

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':NOME', '%'.$this->__get('nome').'%');
		$stmt->bindValue(':ID_USUARIO', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function seguirUsuario($id_usuario_seguindo)
	{
		$query = 'INSERT INTO tb_seguidores(fk_id_usuario, id_usuario_seguindo) VALUES(:FK_ID_USUARIO, :ID_USUARIO_SEGUINDO)';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':FK_ID_USUARIO', $this->__get('id_usuario'));
		$stmt->bindValue(':ID_USUARIO_SEGUINDO', $id_usuario_seguindo);
		$stmt->execute();

		return true;
	}

	public function deixarSeguirUsuario($id_usuario_seguindo)
	{
		$query = 'DELETE FROM tb_seguidores WHERE fk_id_usuario = :FK_ID_USUARIO AND id_usuario_seguindo = :ID_USUARIO_SEGUINDO';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':FK_ID_USUARIO', $this->__get('id_usuario'));
		$stmt->bindValue(':ID_USUARIO_SEGUINDO', $id_usuario_seguindo);
		$stmt->execute();

		return true;
	}

	// Nome do usuário
	public function getNomeUsuario()
	{
		$query = 'SELECT nome FROM tb_usuarios WHERE id_usuario = :ID_USUARIO';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':ID_USUARIO', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	// Total de tweets
	public function getTotalTweets()
	{
		$query = 'SELECT count(*) as total_tweets FROM tb_tweets WHERE fk_id_usuario = :ID_USUARIO';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':ID_USUARIO', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	// Total de pessoas que o usuário está seguindo
	public function getTotalSeguindo()
	{
		$query = 'SELECT count(*) as total_seguindo FROM tb_seguidores WHERE fk_id_usuario = :ID_USUARIO';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':ID_USUARIO', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

	// Total de seguidores
	public function getTotalSeguidores()
	{
		$query = 'SELECT count(*) as total_seguidores FROM tb_seguidores WHERE id_usuario_seguindo = :ID_USUARIO';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':ID_USUARIO', $this->__get('id_usuario'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}
?>