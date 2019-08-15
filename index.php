<?php
session_start();

if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	header('Location: account.php');
}

if (isset($_GET['action'])) {
	if ($_GET['action'] == 'logout') {
		$_SESSION['logged_in'] = false;
	}
}
?>

<?php include 'header.php'; ?>

<div class="container pt-5 pb-4">
	<div class="row justify-content-center">
		<div class="col-12 col-md-6 col-lg-4 col-xl-3">
			<form action="index.php" method="post" class="needs-validation" novalidate>
				<div class="form-group">
					<label for="username">Username</label>
					<input type="text" class="form-control" id="username" placeholder="Enter username or email" name="myusername" required>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control" id="password" placeholder="Enter password" name="mypass" required>
				</div>
				<button type="submit" class="btn btn-info mx-auto d-block w-100">Login</button>
			</form>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-12 col-md-6 col-lg-4 py-3">
			<div class="d-flex justify-content-center">
				<a id="registerBtn" class="btn btn-link" data-toggle="collapse" href="#register" role="button" aria-expanded="false" aria-controls="register">
					Register
				</a>
				<a id="resetBtn" class="btn btn-link" data-toggle="collapse" href="#resetPassword" role="button" aria-expanded="false" aria-controls="resetPassword">
					Forgot Password
				</a>
			</div>

			<div class="d-flex justify-content-center">
				<button id="hideRegisterBtn" class="btn invisible" type="button" data-toggle="collapse" data-target="#register" aria-expanded="false" aria-controls="register">
				</button>

				<button id="hideResetBtn" class="btn invisible" type="button" data-toggle="collapse" data-target="#resetPassword" aria-expanded="false" aria-controls="resetPassword">
				</button>
			</div>

			<div class="collapse" id="register">
				<div class="card">
					<div class="card-header">
						<h5 class="m-0">New User</h5>
					</div>
					<div class="card-body">
						<form action="register.php" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
							<div class="form-group">
								<label for="r_username">Username</label>
								<input type="text" class="form-control" id="r_username" aria-describedby="emailHelp" placeholder="Enter name" name="r_username" required>
							</div>
							<div class="form-group">
								<label for="r_email">Email</label>
								<input type="email" class="form-control" id="r_email" aria-describedby="emailHelp" placeholder="Enter email" name="r_email" required>
							</div>
							<div class="form-group">
								<label for="r_pass">Password</label>
								<input type="password" class="form-control" id="r_pass" placeholder="Enter password" name="r_pass" required>
								<p class="mt-2 text-info show-pass" onclick="showPassword(r_pass);">Show password</p>
							</div>
							<div class="form-group">
								<label for="r_date">Date of Birth</label>
								<input type="date" class="form-control" id="r_date" placeholder="Enter date" name="r_dateofbirth" required>
							</div>
							<div class="form-group">
								<label for="r_photo">Photo</label>
								<input type="file" id="r_photo" name="r_photo" class="w-100">
							</div>
							<button type="submit" class="btn btn-warning mx-auto d-block" name="register">Register</button>
						</form>
					</div>
				</div>
			</div>

			<div class="collapse" id="resetPassword">
				<div class="card">
					<div class="card-header">
						<h5 class="m-0">Password Reset</h5>
					</div>
					<div class="card-body">
						<form action="reset_password.php" method="post" class="needs-validation" novalidate>
							<div class="form-group">
								<label for="reset_email">Email</label>
								<input type="email" class="form-control" id="reset_email" placeholder="Enter email" name="reset_email" required>
							</div>
							<button type="submit" class="btn btn-danger mx-auto d-block" name="rest">Reset Password</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col text-center">

			<?php
			if (!empty($_POST)) {
				include 'functions/db.php';
				$conn = dbConnection();

				// vars
				$username = $password = "";

				// Get post headers
				if($_SERVER["REQUEST_METHOD"] == "POST") {
					if (isset($_POST['myusername'])) {
            $_SESSION['username'] = $_POST['myusername'];
					}

					if (isset($_POST['mypass'])) {
            $_SESSION['password'] = $_POST['mypass'];
					}
				}

				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}

				// Login with username/email and password
				function login() {
					if (!empty($_SESSION['username']) && !empty($_SESSION['password'])) {
						$query = "select Password,Verified from User where (Username='" . $_SESSION['username'] . "' OR Email='" . $_SESSION['username'] . "')";
						$result = $GLOBALS['conn']->query($query);

						// If user exists in database
						if ($result->num_rows > 0) {
							$_SESSION['user_exists'] = true;

							// Validate password
							while($row = $result->fetch_assoc()) {
								if(password_verify($_SESSION['password'], $row['Password'])) {
									$_SESSION['password_validation'] = true;
                  $_SESSION['logged_in'] = true;

									if ($row['Verified'] == 1) {
										$_SESSION['verified_user'] = true;
										echo "<script> window.location.href='account.php' </script>";
									} else {
										$_SESSION['verified_user'] = false;

										echo "User is not verified. Please verify your account. <br>";
										echo "<a href=''>Sent verification email.</a>";
									}

								} else {
                  $_SESSION['logged_in'] = false;
									$_SESSION['password_validation'] = false;

									echo 'Wrong password. Please try again or <a id="resetBtn" class="btn-link" data-toggle="collapse" href="#resetPassword" role="button" aria-expanded="false" aria-controls="resetPassword">reset your password</a>';
								}
							}
						} else {
							$_SESSION['user_exists'] = false;
							echo 'User does not exist. Please <a id="registerBtn" class="btn-link" data-toggle="collapse" href="#register" role="button" aria-expanded="false" aria-controls="register">register</a>';
						}
					} else {
						echo "<strong>Info missing!</strong>";
					}
				}

				login();
				$conn->close();
			}
			?>

		</div>
	</div>
</div>

<?php include 'footer.php'; ?>
