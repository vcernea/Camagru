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
		$checker = $conn->prepare("SELECT * FROM photos WHERE id=:id");
		$checker->bindParam(':id', $_POST['img_id']);
		$checker->execute();
		foreach ($checker as $val)
		{
			$is_here = $val['author_login'];
		}
		$checker = $conn->prepare("SELECT * FROM users WHERE username=:id");
		$checker->bindParam(':id', $is_here);
		$checker->execute();
		foreach ($checker as $val)
		{
			$is_here = $val['email'];
		}
		$message = "You just got a new comment!";
		//	mail($is_here, "Camagru password reset", $message);
		echo $message;
		header("location: index.php"); //asta eu am adaugat
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
			header('Location: index.php');
		}
		else
		{
			$checker = $conn->prepare("INSERT INTO likes (image_id, author_login)
										VALUES(:id, :login)");
			$checker->bindParam(':id', $_POST['img_id']);
			$checker->bindParam(':login', $_SESSION['login']);
			$checker->execute();
			echo "Liked!";
			header('Location: index.php');
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
			header('Location: index.php');
		}
		else
		{
			echo "You haven't liked this photo yet.";
			header('Location: index.php');
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="css/style13.css">
	<script type="text/javascript" src="functions.js"></script>
</head>
<body>
	<div class="top-bar">
		<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="mines.php">My gallery</a></li>
			<li style="float:right"><a class="active" href="logout.php">Logout</a></li>
		</ul>
	</div>
	<div class="content">
		<div class="camera">
			<video id="video">Video stream not available.</video>
			<button id="startbutton">Take Photo</button>
		</div>
		<canvas id="canvas"></canvas>
		<img style="display: none;" id="photo" alt="The screen capture will appear in this box.">
		<br>
		<div>
			<img onclick="set_effect(0)" class="effect_img" src="effects/img0.png"></img>
			<img onclick="set_effect(1)" class="effect_img" src="effects/cat.png"></img>
			<img onclick="set_effect(2)" class="effect_img" src="effects/img1.png"></img>
			<img onclick="set_effect(3)" class="effect_img" src="effects/img2.png"></img>
		</div>
		<br>
		<br>
		<form id="photo_go" name="fooorm" action="save_pic.php" method="post" enctype="multipart/form-data">
			<p>Upload your photo</p>
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input id="f" type="hidden" name="f">
			<input id="eff" type="hidden" name="eff" value="0">
			<input type="submit" value="Upload Image" name="on"  class="btn btn-primary btn-block btn-large">
		</form>
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
				echo '<form action="index.php" class="last_form" method="post">' . '<input type="hidden" name="img_id" value=' . $val['id'] . '>' . '<input class="input" name="comm" type="text" maxlenght="255">' . '<input type="submit" class="tab" name="submit" value="comm_it">' . '
			<input type="submit" value="' . $checker3->fetchColumn() . ' likes | Like"' . 'name="like_it">' . '<input type="submit" value="Dislike" name="dislike">' . '</form>' . '</div>';
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
<script id="jsbin-javascript">
(
	function() {
		var streaming	= false,
			video		= document.querySelector('#video'),
			canvas		= document.querySelector('#canvas'),
			f			= document.querySelector('#f'),
			photo		= document.querySelector('#photo'),
			startbutton	= document.querySelector('#startbutton'),
			width = 460,
			height = 0;
		navigator.getMedia = ( navigator.getUserMedia ||
							 navigator.webkitGetUserMedia ||
							 navigator.mozGetUserMedia ||
							 navigator.msGetUserMedia);
		navigator.getMedia(
			{
				video: true,
				audio: false
			},
		function(stream) {
			if (navigator.mozGetUserMedia) {
			video.mozSrcObject = stream;
			} else {
			var vendorURL = window.URL || window.webkitURL;
			video.src = vendorURL.createObjectURL(stream);
			}
			video.play();
		},
		function(err) {
			console.log("An error occured! " + err);
		}
	);
	video.addEventListener('canplay', function(ev){
		if (!streaming) {
			height = video.videoHeight / (video.videoWidth/width);
			video.setAttribute('width', width);
			video.setAttribute('height', height);
			canvas.setAttribute('width', width);
			canvas.setAttribute('height', height);
			streaming = true;
		}
	}, false);
	function takepicture() {
		canvas.width = width;
		canvas.height = height;
		canvas.getContext('2d').drawImage(video, 0, 0, width, height);
		var data = canvas.toDataURL('image/png');
		photo.setAttribute('src', data);
		f.setAttribute('value',data);
		 // document.getElementById('fileToUpload').value = canvas.toDataURL('image/png');
		document.forms[0].submit();
	}

	startbutton.addEventListener('click', function(ev){
		takepicture();
	}, false);
})();
</script>
</html>
