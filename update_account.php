<?php
session_start();

if (empty($_SESSION['logged_in'])) {
	header('Location: index.php');
}

require_once 'inc/functions.php';
?>

<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row justify-content-center">
		<div class="col-12 col-md-6 col-lg-4 text-center">
			<?php
			include_once 'inc/db.php';
			$conn = dbConnection();

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			// Delete account
			if (isset($_POST['delete'])) {
				if (isset($_POST['id'])) {
					$id = intval($_POST['id']);
				}

				$query = "delete from User where ID=?";
				$stmt = $conn->prepare($query);
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$stmt->get_result();

				if ($stmt->affected_rows > 0) {
					session_unset();
					session_destroy();

					header("Location: index.php?delete_account=success");
					die;
				}
			}

			// Update account
			if (isset($_POST['update'])) {
				$username = $email = $password = $password_2 = $date = "";
				$photos_path = "photos/";

				if (isset($_POST['username'])) {
					$username = $_POST['username'];
				}

				if (isset($_POST['email'])) {
					$email = $_POST['email'];
				}

				if (isset($_POST['pass'])) {
					$current_password = $_POST['pass'];
				}

				if (isset($_POST['new_pass'])) {
					$password = $_POST['new_pass'];
				}

				if (isset($_POST['new_pass_2'])) {
					$password_2 = $_POST['new_pass_2'];
				}

				if (isset($_POST['dateofbirth'])) {
					$date = $_POST['dateofbirth'];
				}

				if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
					if (uploadPhoto($_FILES['photo'])) {
						$photo_saved = 1;
						$photo = $_FILES['photo']['name'];
					} else {
						$photo_saved = 0;
					}
				} else {
					// default photo will be assigned
					$photo = "";
					$photo_saved = 0;
				}

				if (isset($_POST['id'])) {
					$id = intval($_POST['id']);
				}

				/* Start validations */
				if (empty($GLOBALS['username']) || empty($GLOBALS['email']) || empty($GLOBALS['date'])) {
					die("Nothing changed.");
					header("Location: account.php");
				}

				if (!empty($current_password) || !empty($password) || !empty($password_2)) {
					if (!empty($current_password)) {
						if (!empty($password) && !empty($password_2)) {
							if ($password != $password_2) {
								die("Passwords don't match. Please try again. <br> <a href='account.php'>Account</a>");
							}

							if (!checkCurrentPassword($username, $email, $current_password)) {
								die("Your current password is wrong. <br> <a href='account.php'>Account</a>");
							}
						} else {
							die("Please enter the new password. <br> <a href='account.php'>Account</a>");
						}
					} else {
						die("Please enter the current password. <br> <a href='account.php'>Account</a>");
					}
				}

				validateDateOfBirth($date, 'update');

				// Check for changes
				if (empty($password) && !$photo_saved) {
					$query = "select ID from User where Username=? and Email=? and DateOfBirth=?";
					$stmt = $conn->prepare($query);
					$stmt->bind_param('sss', $username, $email, $date);
					$stmt->execute();
					$stmt->get_result();

					if ($stmt->affected_rows > 0) {
						$_SESSION['username'] = $username;
						$_SESSION['email'] = $email;
						header("Location: account.php?result=success");
						exit;
					}
				}

				// Update account
				$query = "update User set Username=?,Email=?,DateOfBirth=?";
				$params_types = 'sss';
				$params_values = array($username, $email, $date);

				if (!empty($password)) {
					$query .= ",Password=?";
					$params_types .= 's';
					$password = password_hash($password, PASSWORD_BCRYPT);
					array_push($params_values, $password);
				}

				if ($photo_saved) {
					$query .= ",Photo=?";
					$params_types .= 's';
					array_push($params_values, $photo);
				}

				$query .= " where ID=?";
				$params_types .= 'i';
				array_push($params_values, $id);

				$stmt = $conn->prepare($query);
				$stmt->bind_param($params_types, ...$params_values);

				if ($stmt->execute()) {
					$stmt->get_result();

					if ($stmt->affected_rows > 0) {
						$_SESSION['username'] = $username;
						$_SESSION['email'] = $email;
						header("Location: account.php?result=success");
					} else {
						header("Location: account.php?result=fail");
					}
				} else {
					die("Error is sql query " . $stmt->error);
				}
			}

			$stmt->close();
			$conn->close();
			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>