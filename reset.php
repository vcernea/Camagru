<?PHP
	session_start();
	if ($_SESSION['login'])
		header('Location: index.php');
?>
<html>
<head>
	<title>Reset Password</title>
	<link rel="stylesheet" type="text/css" href="css/style2.css">
</head>

<body>
	<div class="LogBox">
		<div class="ourform">
			<p class="reg-log">Reset Password</p>
			<br />
			<form action="reset.php" method="post">
				<p class="pas-log">Login:</p>
				<input class="input" name="login" type="text" maxlength="32">
				<p class="pas-log">New password:</p>
				<input class="input" name="password" type="password" maxlength="32">
				<br />
				<br />
				<input type="submit" class="tab" name="submit" value="ENTER">
				<br />
				<br />
				<a href="singup.php">No account? Register!</a>
			</form>
		</div>
	</div>
</body>
</html>
<?PHP
	session_start();
	if (isset($_POST['submit']))
	{
		if (isset($_POST['login']))
		{
			$login = $_POST['login'];
			if ($login == '') { unset($login);}
		}
		if (isset($_POST['password']))
		{
			$password = hash("md5", $_POST['password']);
			if ($password =='') { unset($password);}
		}
		if (empty($login) or empty($password))
			echo "<center><p>Please, fill all boxes</p></center>";
		$login = stripslashes($login);
		$login = htmlspecialchars($login);
		$password = stripslashes($password);
		$password = htmlspecialchars($password);
	
		$login = trim($login);
		$password = trim($password);
		
		
		$servername = "127.0.0.1";
		$username = "root";
		$passwd = "";
		$dbname = "camagru";
		try
		{
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwd);
			$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$checker = $conn->prepare("SELECT * FROM users WHERE username = :name");
			$checker->bindParam(':name', $login);
			$checker->execute();
			if ($checker->rowCount() != 1)
			{
				echo "<center><p style='font-size: 18px; font-weight: bold;'>Oops, wrong username</p></center>";
			}
			if ($checker !== false)
			{
				foreach ($checker as $val) {
					$generatedhash = hash("md5", $val['email']) . hash("md5", $val['reg_date']);
				}
				echo $generatedhash . "<br>";
				$_SESSION['temp_pass'] = $_POST['password'];
				$message = "<p style='padding-left: 4em;'>Dear $firstname</p><br />" . "Click on the link to reset your password:<br />" . "https://camagru-vcernea.c9users.io/activate.php?do=reset&check=" . $generatedhash . "<br /><br /><br />" . "<p style='text-align: center'>Thank You!</p>";
				//	mail($email, "Camagru password reset", $message);
				echo $message;
			}
		}
		catch(PDOException $e)
		{
			echo '{"error":{"text":'. $e->getMessage() .'}}';
		}
		$conn = null;
	}
?>