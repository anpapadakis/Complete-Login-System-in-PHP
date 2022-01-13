<?php
session_start();

if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	header('Location: account.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 col-md-6 col-lg-4 col-xl-3">
			<form action="reset_password.php" method="post" class="needs-validation" novalidate>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control" id="password" placeholder="Enter password" name="mypass" required>
				</div>
				<div class="form-group">
					<label for="password2">Confirm Password</label>
					<input type="password" class="form-control" id="password2" placeholder="Confirm password" name="mypass2" required>
				</div>
				<button type="submit" class="btn btn-info mx-auto d-block w-100" name="submit">Reset</button>
			</form>
		</div>
	</div>

	<div class="row mt-4">
		<div class="col text-center">

			<?php
			require_once 'inc/db.php';
			$conn = dbConnection();

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			if (isset($_GET['result']) && $_GET['result'] == 'success') {
				echo "Your password has been updated!<br> <a href='index.php'>Login</a>";
				exit;
			}

			// Get hash code from url
			if (empty($_POST)) {
				if (isset($_GET['hash']) && !empty($_GET['hash'])) {
					$_SESSION['hash'] = $_GET['hash'];
				} else {
					die("Hash is missing.");
				}
			}

			if (isset($_POST['submit'])) {
				if (empty($_POST['mypass']) || empty($_POST['mypass2'])) {
					die("Info missing!");
				}

				$password = $_POST['mypass'];
				$password_2 = $_POST['mypass2'];

				if ($password != $password_2) {
					die("Passwords don't match. Please try again.");
				}

				// Get email from database according to hash code
				$first_query = "select Email from User where ResetPasswordHash = ? limit 1";
				$stmt = $conn->prepare($first_query);
				$stmt->bind_param('s', $resetPasswordHash);
				$resetPasswordHash = $_SESSION['hash'];

				if ($stmt->execute()) {
					$result = $stmt->get_result();

					if ($result->num_rows > 0) {
						$row = $result->fetch_assoc();
						$_SESSION['email'] = $row['Email'];
					} else {
						echo "The user does not exist in our database.";
					}

					$stmt->close();
				} else {
					echo "Error in sql query: <i>" . $stmt->error . "</i>";
				}

				// Update password according to Email
				$second_query = "update User set Password = ? where Email = ?";
				$stmt = $conn->prepare($second_query);
				$stmt->bind_param('ss', $password, $email);

				$password = password_hash($password, PASSWORD_BCRYPT);
				$email = $_SESSION['email'];

				if ($stmt->execute()) {
					$stmt->get_result();

					if ($stmt->affected_rows > 0) {
						header('Location: reset_password.php?result=success');
					} else {
						echo "The user does not exist in our database.";
					}

					$stmt->close();
				} else {
					echo "Error in sql query: <i>" . $stmt->error . "</i>";
				}
			}

			$conn->close();
			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>