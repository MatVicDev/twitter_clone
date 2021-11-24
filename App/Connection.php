<?php

namespace App;

class Connection
{
	public static function getDb()
	{
		try {
			$conn = new \PDO('mysql:host=localhost;dbname=twitter_clone;', 'root', 'root');

			return $conn;

		} catch(\PDOException $e) {
			 return 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
		}
	}
}
?>