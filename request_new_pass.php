<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 text-center">

			<?php
			require_once 'inc/db.php';
			$conn = dbConnection();

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			if (isset($_POST['reset']) && isset($_POST['reset_email'])) {

				if (empty($_POST['reset_email'])) {
					die("Email is missing.");
				}

				$query = "update User set ResetPasswordHash = ? where Email = ?";
				$stmt = $GLOBALS['conn']->prepare($query);

				$stmt->bind_param('ss', $resetPasswordHash, $email);
				$email = $_POST['reset_email'];
				$resetPasswordHash = md5(rand(0, 1000));

				if ($stmt->execute()) {
					$stmt->get_result();

					if ($stmt->affected_rows > 0) {
						include 'inc/reset_pass_email.php';
						resetPasswordEmail($email, $resetPasswordHash);
					} else {
						echo "The email <b>" . $email . "</b> does not exist in the database.";
					}
				} else {
					echo "Error in sql query: <i>" . $stmt->error . "</i>";
				}
			}

			$stmt->close();
			$conn->close();
			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>