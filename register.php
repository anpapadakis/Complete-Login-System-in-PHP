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
			include 'functions/db.php';
			$conn = dbConnection();

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			// vars
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
				// $photo = addslashes(file_get_contents($_FILES['r_photo']['name']));
				$photo = $_FILES['r_photo']['name'];

				$photos_path .= $_FILES['r_photo']['name'];

				// $photo = $_FILES['r_photo']['tmp_name'];

				if(move_uploaded_file($_FILES['r_photo']['tmp_name'],$photos_path)) {
					//echo "moved";
					$photo_saved = 1;
				} else {
					//echo "not moved";
					$photo_saved = 0;
				}
			} else {
				// default photo will be assigned
				$photo = "";
				$photo_saved = 1;
			}

			/* Start validations */
			if (empty($GLOBALS['username']) || empty($GLOBALS['email']) || empty($GLOBALS['password']) || empty($GLOBALS['password_2']) || empty($GLOBALS['date'])) {
				die("Info missing!");
			}

			if ($password != $password_2) {
				die("Passwords don't match. Please try again.<br> <a href='index.php'>Register</a>");
			}

			$today = date("Y-m-d");
			$diff = date_diff(date_create($date), date_create($today));

			if($diff->format('%y%') < 18){
				die("<strong>You must be at least 18 years old.</strong> <br> <a href='index.php'>Register</a>");
			}
			/* Finish validations */


			// Create a hash code
			$verification_code = md5(rand(0,1000));

			// Check if user exists already
			if (userExists()) {
				echo "<strong>User already exists.</strong><br>";
				echo "<a href='/php/admin'>Go back to login</a>";
			} else {
				$query = "insert into User (Username,Email,Password,DateOfBirth,Photo,VerificationCode) values (?,?,?,?,?,?)";
				$stmt = $conn->prepare($query);
				$stmt->bind_param("ssssss",$username,$email,$password,$date,$photo,$verification_code);

				$password = password_hash($password,PASSWORD_BCRYPT);

				if($stmt->execute()) {
					$stmt->store_result();

					if($stmt->affected_rows > 0) {
						echo "<strong>Registration success!</strong><br>";

						include 'functions/email.php';
						sendVerificationEmail($email,$verification_code);
					}

				} else {
					echo "Error in insert query: <i>" . $stmt->error . "</i>";
				}

				$stmt->close();
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

<?php include 'footer.php'; ?>
