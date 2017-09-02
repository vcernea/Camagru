<?PHP
	session_start();
	$servername = "127.0.0.1";
	$username = "root";
	$passwd = "";
	$dbname = "camagru";
	
	try
	{
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwd);
		$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$checker = $conn->prepare("SELECT * FROM users");
		$checker->execute();
		if ($checker !== false)
		{
			if ($_GET['do'] == 'act')
			{
				foreach ($checker as $vl) {
					if (hash("md5", $vl['reg_date']) . hash("md5", $vl['email']) == $_GET['check'])
					{
						echo "account found!";
						$checker = $conn->prepare("UPDATE users SET active=1 WHERE email=:mail");
						$checker->bindParam(':mail', $vl['email']);
						$checker->execute();
						echo "<br />account activated!";
					}
				}
			}
			if ($_GET['do'] == 'reset')
			{
				foreach ($checker as $vl) {
					if (hash("md5", $vl['email']) . hash("md5", $vl['reg_date']) == $_GET['check'])
					{
						$con = $conn->prepare("UPDATE users SET password=:passwd WHERE email=:mail");
						$con->bindParam(':passwd', hash("md5", $_SESSION['temp_pass']));
						$con->bindParam(':mail', $vl['email']);
						$con->execute();
						unset($_SESSION['temp_pass']);
						echo "Password succesfully changed!";
					}
				}
			}
		}
	}
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	$conn = null;
	header('Location: index.php');
?>