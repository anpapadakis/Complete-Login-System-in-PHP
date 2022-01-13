<?php
session_start();

if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	header('Location: account.php');
}

if (!isset($_POST['register'])) {
	header('Location: account.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 text-center">

			<?php
			include 'inc/functions.php';

			$username = $email = $password = $password_2 = $date = "";
			$photos_path = "photos/";

			if (isset($_POST['r_username'])) {
				$username = $_POST['r_username'];
			}

			if (isset($_POST['r_email'])) {
				$email = $_POST['r_email'];
			}

			if (isset($_POST['r_pass'])) {
				$password = $_POST['r_pass'];
			}

			if (isset($_POST['r_pass_2'])) {
				$password_2 = $_POST['r_pass_2'];
			}

			if (isset($_POST['r_dateofbirth'])) {
				$date = $_POST['r_dateofbirth'];
			}

			if (isset($_FILES['r_photo']) && $_FILES['r_photo']['size'] > 0) {
				if (uploadPhoto($_FILES['r_photo'])) {
					$photo_saved = 1;
					$photo = $_FILES['r_photo']['name'];
				} else {
					$photo_saved = 0;
				}
			} else {
				// default photo will be assigned
				$photo = "";
				$photo_saved = 0;
			}

			/* Start validations */
			if (empty($GLOBALS['username']) || empty($GLOBALS['email']) || empty($GLOBALS['password']) || empty($GLOBALS['password_2']) || empty($GLOBALS['date'])) {
				die("Info missing!");
			}

			if ($password != $password_2) {
				die("Passwords don't match. Please try again.<br> <a href='index.php'>Register</a>");
			}

			validateDateOfBirth($date, 'register');
			/* Finish validations */


			// Create a hash code
			$verification_code = md5(rand(0, 1000));

			// Check if user exists already
			if (userExists($username, $email)) {
				die("<p>User already exists.</p> <a href='index.php'>Go back to login</a>");
			}

			// Register user if does not exist
			$query = "insert into User (Username,Email,Password,DateOfBirth,Photo,VerificationCode) values (?,?,?,?,?,?)";
			$stmt = $conn->prepare($query);
			$stmt->bind_param("ssssss", $username, $email, $password, $date, $photo, $verification_code);

			$password = password_hash($password, PASSWORD_BCRYPT);

			if ($stmt->execute()) {
				$stmt->store_result();

				if ($stmt->affected_rows > 0) {
					echo "<strong>You have been registered successfully!</strong><br>";

					include 'inc/email.php';
					sendVerificationEmail($email, $verification_code);
				}
			} else {
				echo "Error in insert query: <i>" . $stmt->error . "</i>";
			}

			$stmt->close();
			$conn->close();

			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>