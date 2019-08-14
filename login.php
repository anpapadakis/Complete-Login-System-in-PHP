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
					<a class="nav-link" href="/php/">Home <span class="sr-only">(current)</span></a>
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
				$username = $password = "";

				// Get post headers
				if($_SERVER["REQUEST_METHOD"] == "POST") {
					if ($_POST['myusername']) {
						$username = $_POST['myusername'];
					}

					if ($_POST['mypass']) {
						$password = $_POST['mypass'];
					}
				}

				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}

				// User Login with username / email and password
				function login() {
					if (!empty($GLOBALS['username']) && !empty($GLOBALS['password'])) {

						// $query = "select * from User where (Username='" . $GLOBALS['username'] . "' OR Email='" . $GLOBALS['username'] . "') and Password='" . $GLOBALS['password'] . "'";

						$query = "select Password from User where (Username='" . $GLOBALS['username'] . "' OR Email='" . $GLOBALS['username'] . "')";

						$result = $GLOBALS['conn']->query($query);

						if ($result == TRUE) {
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									if(password_verify($GLOBALS['password'], $row['Password'])) {
										echo "<strong>Hi " . $GLOBALS['username'] ."!</strong>";
									} else {
										echo "<strong>User does not exist!</strong><br>";
										echo "<a href='/php/admin'>Register now</a>";
									}
								}
							}
						} else {
							echo "<strong>Error in sql query:</strong> <i>" . $query . "</i><br>";
							echo $query->error;
						}





						// if ($result == TRUE) {
						// 	if ($result->num_rows > 0) {
						// 		// echo "<script> window.location = 'dashboard.php';</script>";
						// 		echo "<strong>User exists!</strong>";
						// 	} else {
						// 		echo "<strong>User does not exist!</strong>";
						// 	}
						// } else {
						// 	echo "<strong>Error in sql query:</strong> <i>" . $query . "</i><br>";
						// 	echo $query->error;
						// }
					} else {
						echo "<strong>Info missing!</strong>";
					}
				}

				login();

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
