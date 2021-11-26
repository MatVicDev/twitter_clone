<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{
	private $id_tweet;
	private $fk_id_usuario;
	private $tweets;
	private $data;

	public function __get($attr)
	{
		return $this->$attr;
	}

	public function __set($attr, $value)
	{
		$this->$attr = $value;
	}

	// Salvar
	public function salvar()
	{
		$query = 'INSERT INTO tb_tweets(fk_id_usuario, tweets)VALUES(:FK_ID_USUARIO, :TWEETS)';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':FK_ID_USUARIO', $this->__get('fk_id_usuario'));
		$stmt->bindValue(':TWEETS', $this->__get('tweets'));
		$stmt->execute();

		return $this;
	}

	// Recuperar
	public function getAll()
	{
		$query = '
			SELECT 
				t.id_tweet, t.fk_id_usuario, u.nome, t.tweets, 
				DATE_FORMAT(t.data, "%d/%m/%Y %H:%i") as data
			FROM 
				tb_tweets as t
			LEFT JOIN 
				tb_usuarios as u 
			ON
				(t.fk_id_usuario = u.id_usuario)
			WHERE 
				t.fk_id_usuario = :FK_ID_USUARIO
			OR
				t.fk_id_usuario IN (
					SELECT 
						id_usuario_seguindo
					FROM
						tb_seguidores
					WHERE
						fk_id_usuario = :FK_ID_USUARIO
					)
			ORDER BY
				t.data DESC';

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':FK_ID_USUARIO', $this->__get('fk_id_usuario'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	// Deletar tweet
	public function deletarTweet()
	{
		$query = 'DELETE FROM tb_tweets WHERE id_tweet = :ID_TWEET';
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':ID_TWEET', $this->__get('id_tweet'));
		$stmt->execute();

		return true;
	}
}
?>