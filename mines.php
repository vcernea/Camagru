<?PHP
	session_start();
	if ($_SESSION['login'] == "")
		header('Location: login.php');
	
	$servername = "127.0.0.1";
	$username = "root";
	$passwd = "";
	$dbname = "camagru";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwd);
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		// echo $SESSION['login'];
	if ($_POST['submit'])
	{
		$checker = $conn->prepare("INSERT INTO comments (comment_author, comment_content, photo_id)
									VALUES(:login, :content, :photo_id)");
		$checker->bindParam(':login', $_SESSION['login']);
		$checker->bindParam(':content', $_POST['comm']);
		$checker->bindParam(':photo_id', $_POST['img_id']);
		$checker->execute();
		header("location: mines.php"); //asta eu am adaugat
	}
	else if ($_POST['like_it'])
	{
		$is_here = 0;
		$sql = 'SELECT * FROM `likes` WHERE author_login=:id';
		$checker = $conn->prepare($sql);
		$checker->bindParam(':id', $_SESSION['login']);
		$checker->execute();
		foreach ($checker as $val)
		{
			if ($val['image_id'] == $_POST['img_id'])
			{
				$is_here = 1;
			}
		}
		if ($is_here == 1)
		{
			echo "You already liked it!";
			header('Location: mines.php');
		}
		else
		{
			$checker = $conn->prepare("INSERT INTO likes (image_id, author_login)
										VALUES(:id, :login)");
			$checker->bindParam(':id', $_POST['img_id']);
			$checker->bindParam(':login', $_SESSION['login']);
			$checker->execute();
			echo "Liked!";
			header('Location: mines.php');
		}
	}
	else if ($_POST['dislike'])
	{
		$is_here = 0;
		$sql = 'SELECT * FROM `likes` WHERE author_login=:id';
		$checker = $conn->prepare($sql);
		$checker->bindParam(':id', $_SESSION['login']);
		$checker->execute();
		foreach ($checker as $val)
		{
			if ($val['image_id'] == $_POST['img_id'])
			{
				$is_here = 1;
			}
		}
		if ($is_here == 1)
		{
			$sql = 'DELETE FROM `likes` WHERE author_login=:id AND image_id=:img';
			$checker = $conn->prepare($sql);
			$checker->bindParam(':id', $_SESSION['login']);
			$checker->bindParam(':img', $_POST['img_id']);
			$checker->execute();
			header('Location: mines.php');
		}
		else
		{
			echo "You haven't liked this photo yet.";
			header('Location: mines.php');
		}
	}
	else if ($_POST['delete'])
	{
		$is_here = 0;
		$sql = 'DELETE FROM `photos` WHERE id=:id';
		$checker = $conn->prepare($sql);
		$checker->bindParam(':id', $_POST['img_id']);
		$checker->execute();
		header('Location: mines.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="css/style4.css">
</head>
<body>
	<div class="top-bar">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="mines.php">My gallery</a></li>
			<li style="float:right"><a class="active" href="logout.php">Logout</a></li>
		</ul>
	</div>
	<div class="instant-gallery">
		<?PHP
			$servername = "127.0.0.1";
			$username = "root";
			$passwd = "";
			$dbname = "camagru";
			$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwd);
			$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$checker = $conn->prepare("SELECT * FROM photos WHERE author_login=:log ORDER BY id DESC");
			$checker->bindParam(':log', $_SESSION['login']);
			$checker->execute();
			foreach ($checker as $val) {
				echo '<div class="picture"><img src=' . $val['image_path'] . '></img>';
				$checker2 = $conn->prepare("SELECT * FROM comments");
				$checker2->execute();
				$checker3 = $conn->prepare("SELECT COUNT(*) FROM likes WHERE image_id=:img_id");
				$checker3->bindParam(':img_id', $val['id']);
				$checker3->execute();
				foreach ($checker2 as $val2) {
					if ($val2['photo_id'] == $val['id'])
					{
						echo '<br>' . '<spanstyle="float: left; margin: 0; font-size: 13px;">' . $val2['comment_author'] . ": " . $val2['comment_content'] . '</span>' . '<span style="float: right; margin: 0; font-size: 12px;">' . $val2['reg_date'] . '</span>';
					}
				}
				echo '<form action="mines.php" class="last_form" method="post">' . '<input type="hidden" name="img_id" value=' . $val['id'] . '>' . '<input class="input" name="comm" type="text" maxlenght="255">' . '<input type="submit" class="tab" name="submit" value="comm_it">' . '
			<input type="submit" value="' . $checker3->fetchColumn() . ' likes | Like"' . 'name="like_it">' . '<input type="submit" value="Dislike" name="dislike">' . '<input type="submit" value="Delete" name="delete">' . '</form>' . '</div>';
			}
		
		?>
	</div>
		<!--<div class="picture">-->
		<!--	<img src="images/1.png"></img>-->
		<!--	<div class="comments">-->
		<!--		<p class="comment_author">Somewho: </p>-->
		<!--		<p class="comment_content">Just a comment</p>-->
		<!--	</div>-->
		<!--</div>-->
		<!--<input type="submit" class="tab" name="submit" value="SHOWMORE">-->
</body>
</html>