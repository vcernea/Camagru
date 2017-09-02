<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="css/style4.css">
	<script type="text/javascript" src="functions.js"></script>
</head>
<body>
	<div class="top-bar">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="gallery.php">My gallery</a></li>
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
			
			$checker = $conn->prepare("SELECT * FROM photos ORDER BY id DESC");
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
			}
		
		?>
		<!--<div class="picture">-->
		<!--	<img src="images/1.png"></img>-->
		<!--	<div class="comments">-->
		<!--		<p class="comment_author">Somewho: </p>-->
		<!--		<p class="comment_content">Just a comment</p>-->
		<!--	</div>-->
		<!--</div>-->
	</div>
		<!--<input type="submit" class="tab" name="submit" value="SHOWMORE">-->
</body>