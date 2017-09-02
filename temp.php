<?php
	$servername = "127.0.0.1";
	$username = "root";
	$password = "";
	
	try {
		$conn = new PDO("mysql:host=$servername;dbname=camagru", $username, $password);
		// $conn->exec("DROP TABLE `photos`");
			$sql = "CREATE TABLE likes(
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				image_id INT(6),
				author_login VARCHAR(30) NOT NULL
				)";
				
		// use exec() because no results are returned
		$conn->exec($sql);
		echo "Database created successfully<br>";
	}
	
	catch(PDOException $e) {
		echo $sql . "<br>" . $e->getMessage();
	}
	
	$conn = null;
?>