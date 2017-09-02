<?php
	session_start();
	session_unset();
	$servername = "127.0.0.1";
	$username = "root";
	$password = "";
	
	try {
		$conn = new PDO("mysql:host=$servername", $username, $password);
		
		
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$conn->exec("DROP DATABASE IF EXISTS camagru");
		$sql = "CREATE DATABASE camagru";
		$conn->exec($sql);
		
		$conn = new PDO("mysql:host=$servername;dbname=camagru", $username, $password);
		$sql = "CREATE TABLE users (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				username VARCHAR(30) NOT NULL,
				firstname VARCHAR(30) NOT NULL,
				lastname VARCHAR(30) NOT NULL,
				password VARCHAR(255) NOT NULL,
				active INT(1) UNSIGNED,
				email VARCHAR(50),
				reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
				)";
				
		// use exec() because no results are returned
		$conn->exec($sql);
		
		$sql = "CREATE TABLE photos(
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				image_path VARCHAR(255),
				author_login VARCHAR(30) NOT NULL,
				reg_date TIMESTAMP
				)";
				
		// use exec() because no results are returned
		$conn->exec($sql);
		
		$sql = "CREATE TABLE likes(
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				image_id INT(6),
				author_login VARCHAR(30) NOT NULL
				)";
				
		// use exec() because no results are returned
		$conn->exec($sql);
		
		$sql = "CREATE TABLE comments(
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				comment_author VARCHAR(255),
				comment_content VARCHAR(255),
				photo_id INT(6),
				reg_date TIMESTAMP
				)";
				
		// use exec() because no results are returned
		$conn->exec($sql);
		
		echo "Database created successfully<br>";
		header("Refresh: 2; url=./../singup.php");
	}
	
	catch(PDOException $e) {
		echo $sql . "<br>" . $e->getMessage();
	}
	
	$conn = null;
?>