<?PHP
	session_start();
	if ($_SESSION['login'])
		header('Location: index.php');
?>
<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" href="css/style2.css" type="text/css" />
		<link href="https://fonts.googleapis.com/css?family=Bitter|Raleway" rel="stylesheet">
	</head>
	<body>
		<div class="LogBox">
			<div class="ourform">
				<p class="reg-log" style="margin-bottom: 48px" >Register</p>
				<form action="singup.php" method="post">
						<p class="pas-log">Username:</p>
						<input class="input" name="login" type="text" maxlength="32">
						<p class="pas-log">First name:</p>
						<input class="input" name="firstname" type="text" maxlength="32">
						<p class="pas-log">Last name:</p>
						<input class="input" name="lastname" type="text" maxlength="32">
						<p class="pas-log">Password:</p>
						<input class="input" name="password" type="password" maxlength="32">
						<p class="pas-log">Email:</p>
						<input class="input" name="email" type="email" maxlength="50">
					
					<br />
					<br />
						<input type="submit" class="tab" name="submit" value="Register">
					<br />
					<br />
					<a href="login.php">Already have an account? Login!</a>
				</form>
			</div>
		</div>
	</body>
</html>
<?PHP
	if (isset($_POST['submit']))
	{
		if (isset($_POST['login'])) 
		{
			$login = $_POST['login'];
			if ($login == '') { unset($login); }
		}
		if (isset($_POST['firstname'])) 
		{
			$firstname = $_POST['firstname'];
			if ($firstname == '') { unset($firstname); }
		}
		if (isset($_POST['lastname'])) 
		{
			$lastname = $_POST['lastname'];
			if ($lastname == '') { unset($lastname); }
		}
		if (isset($_POST['password']))
		{
			$passwd = hash("md5", $_POST['password']);
			if ($passwd =='') { unset($passwd);}
		}
		if (isset($_POST['email']))
		{
			$email = $_POST['email'];
			if ($email =='') { unset($email);}
		}
		
		if (empty($login) or empty($passwd) or empty($email) or empty($firstname) or empty($lastname)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
		{
			exit ("sorry, please fill empty boxes");
		}
		
		//если логин и пароль введены, то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
		$login = stripslashes($login);
		$login = htmlspecialchars($login);
		$passwd = stripslashes($passwd);
		$passwd = htmlspecialchars($passwd);
		
		//удаляем лишние пробелы
		$login = trim($login);
		$passwd = trim($passwd);
		$email = trim($email);
		$actv = 0;
		
		
		$servername = "127.0.0.1";
		$username = "root";
		$password = "";
		$dbname = "camagru";
		
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$checker = $conn->prepare("SELECT * FROM users");
		$checker->execute();
		try
		{
			foreach ($checker as $vl) {
				if ($vl['username'] == $login)
				{
					echo "<center><p>Oops, username already exists</p></center>";
					goto ll;
				}
			}
			
			
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$checker = $conn->prepare("INSERT INTO users (username, firstname, lastname, password, email, active)
			VALUES (:logn, :fnm, :lnm, :pwd, :emil, :activ)");
			$checker->bindParam(':logn', $login);
			$checker->bindParam(':fnm', $firstname);
			$checker->bindParam(':lnm', $lastname);
			$checker->bindParam(':pwd', $passwd);
			$checker->bindParam(':emil', $email);
			$checker->bindParam(':activ', $actv);
			$checker->execute();
			
			
			$checker = $conn->prepare("SELECT reg_date FROM users WHERE username = :name");
			$checker->bindParam(':name', $_POST['login']);
			$checker->execute();
			foreach ($checker as $val) {
				if ($val['reg_date'])
				{
					$generatedhash = hash("md5", $val['reg_date']);
				}
			}
			$generatedhash = $generatedhash . hash("md5", $email);
			//$_SERVER['SERVER_NAME']
			$addres = "/camagru/activate.php?do=act&check=" . $generatedhash;
			$message = "<p style='padding-left: 4em;'>Dear $firstname</p><br />" . "Please verify your Camagru account. Just click on the link:<br />" . "<a href=" . $addres . ">" . $addres . "</a>" . "<br /><br /><br />" . "<p style='text-align: center'>Thank You!</p>";
		//	mail($email, "Camagru account verification", $message);
			echo $message;
			
			
			echo "New record created successfully";
			// header("Location: index.php");
		}
		catch(PDOException $e)
		{
			echo $sql . "<br>" . $e->getMessage();
		}
		ll :
		$conn = null;
	}
?>