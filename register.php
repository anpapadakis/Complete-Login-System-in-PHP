<!DOCTYPE html>
<html>
<head>
	<title>Admin Panel</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="/php/admin">Admin</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item active">
					<a class="nav-link" href="/php/admin">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Link</a>
				</li>
			</ul>
		</div>
	</nav>

	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 text-center">
				
				<?php

				include 'db.php';
				$conn = dbConnection();

				// vars
				$username = $email = $password = $date = "";
				$photos_path = "photos/";

				if($_SERVER["REQUEST_METHOD"] == "POST") {
					if (isset($_POST['r_username'])) {						
						$username = $_POST['r_username'];
					}

					if (isset($_POST['r_email'])) {
						$email = $_POST['r_email'];
					}

					if (isset($_POST['r_pass'])) {
						$password = $_POST['r_pass'];
					}

					if (isset($_POST['r_dateofbirth'])) {
						$date = $_POST['r_dateofbirth'];
					}	

					if (isset($_FILES['r_photo'])) {
						// $photo = addslashes(file_get_contents($_FILES['r_photo']['name']));
						$photo = $_FILES['r_photo']['name'];

						$photos_path .= $_FILES['r_photo']['name'];

						// $photo = $_FILES['r_photo']['tmp_name'];

						if(move_uploaded_file($_FILES['r_photo']['tmp_name'],$photos_path)) {
							echo "moved";
							$photo_saved = 1;
						} else {
							echo "not moved";
							$photo_saved = 0;
						}						
					}
				}


				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}

				if (userExists()) {
					echo "<strong>User already exists!</strong><br>";
					echo "<a href='/php/admin'>Go back to login</a>";
				} else {
					if (!empty($GLOBALS['username']) && !empty($GLOBALS['email']) && !empty($GLOBALS['password']) && $photo_saved) {

						$stmt = $conn->prepare("insert into User (Username,Email,Password,DateOfBirth,Photo) values (?,?,?,?,?)");
						$stmt->bind_param("sssss",$username,$email,$password,$date,$photo);

						$username = $GLOBALS['username'];
						$email = $GLOBALS['email'];
						$password = $GLOBALS['password'];
						$date = $GLOBALS['date'];
						$photo = $GLOBALS['photo'];

						$password = password_hash($password,PASSWORD_BCRYPT);
						
						if($stmt->execute()) {
							$stmt->store_result();

							if($stmt->affected_rows > 0) {
								echo "<strong>Registration success!</strong><br>";
								echo "<a href='/php/admin'>Go back to login</a>";
							}
							
						} else {
							echo "Error in insert query: <i>" . $stmt->error . "</i>";
						}

						$stmt->close();

					} else {
						echo "<strong>Info missing!</strong>";
					}
				}

				function userExists() {
					$stmt = $GLOBALS['conn']->prepare("select * from User where Username=? or Email=?");
					$stmt->bind_param("ss",$username,$email);

					$username = $GLOBALS['username'];
					$email = $GLOBALS['email'];					

					if($stmt->execute()) {
						$stmt->store_result();

						if ($stmt->num_rows > 0) {		
							return true;
						} else {
							return false;
						}
					} else {
						echo "Error in select query: <i>" . $stmt->error . "</i>";
					}

					$stmt->close();					
				}

				$conn->close();

				?>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>