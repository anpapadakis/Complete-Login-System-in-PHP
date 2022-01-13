<?php
session_start();

if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	header('Location: account.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row">
		<div class="col text-center">

			<?php
			require_once 'inc/functions.php';

			// If not verified, send verification email
			if (isset($_GET['action']) && $_GET['action'] == "sendVerificationEmail") {
				if (isset($_GET['user'])) {
					$user = $_GET['user'];

					$stmt = $GLOBALS['conn']->prepare("select Email,VerificationCode from User where Username=? or Email=?");
					$stmt->bind_param("ss", $user, $user);

					if ($stmt->execute()) {
						$stmt->store_result();

						if ($stmt->num_rows > 0) {
							$stmt->bind_result($Email, $VerificationCode);
							$stmt->fetch();

							include_once 'inc/email.php';
							sendVerificationEmail($Email, $VerificationCode);
						} else {
							echo "User " . $user . " does not exist.";
						}
					} else {
						echo "Error in select query: <i>" . $stmt->error . "</i>";
					}

					$stmt->close();
					$GLOBALS['conn']->close();
				}

				exit();
			}

			// Send verification email after register
			if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
				$email = $_GET['email'];
				$hash = $_GET['hash'];

				//Check if email exists
				if (!userExists(null, $email)) {
					die("<p>The email <b>" . $email . "</b> does not exist in the database.</p> <p>Please <a href='index.php'>register</a>.</p>");
				}

				// Check if user is already verified
				if (userVerified($email)) {
					die("User is already verified. <br> Go to <a href='index.php'>login page</a>.");
				}

				$stmt = $GLOBALS['conn']->prepare("update User set Verified = ? where Email = ? and VerificationCode = ? and Verified = 0");
				$stmt->bind_param("iss", $verified, $email, $hash);

				$verified = 1;

				if ($stmt->execute()) {
					if ($stmt->affected_rows > 0) {
						echo "<p>The email <b>" . $email . "</b> has been verified successfully.</p>";
						echo "<p>Go to <a href='index.php'>login page</a></p>";
					}
				} else {
					echo "<p>Error in sql query: <i>" . $stmt->error . "</i></p>";
				}

				$GLOBALS['conn']->close();
			} else {
				echo "<script> window.location.href='index.php' </script>";
			}
			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>