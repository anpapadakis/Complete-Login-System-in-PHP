<?php
session_start();

if (empty($_SESSION['logged_in'])) {
	header('Location: index.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
	<div class="row justify-content-center">
		<?php
		if ( isset($_GET['result']) && !empty($_GET['result'])) { ?>
			<div class="col-12 text-center py-3">
				<?php
				if ($_GET['result'] == "success") { ?>
					<p class="text-success">Your account updated successfully.</p>
				<?php
				} else if ($_GET['result'] == "fail") { ?>
					<p class="text-danger">Update failed.</p>
				<?php
				} ?>
			</div>
		<?php
		} ?>

		<div class="col-12 col-sm-8 col-md-6 col-lg-4">
			<?php
			require_once 'inc/db.php';
			$conn = dbConnection();

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			$username = $_SESSION['username'];
			$password = $_SESSION['password'];

			if (empty($username) || empty($password)) {
				die("Info missing.");
			}

			$query = "select * from User where Username=? or Email=?";
			$stmt = $conn->prepare($query);
			$stmt->bind_param("ss", $username,$username);

			if ($stmt->execute()) {
				$result = $stmt->get_result();

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) { ?>

						<form action="update_account.php" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
							<div class="form-group d-flex flex-column align-items-center justify-content-center">
								<?php
								$photo = $row['Photo'] != '' ? "photos/" . $row['Photo'] : "photos/user.png";
								?>

								<div class="profile-photo" style="background-image: url('<?php echo $photo; ?>')">
									<div class="update-photo">
										<label>
											Update
											<input type="file" id="updatePhoto" name="photo" accept=".jpg, .jpeg, .png">
										</label>
									</div>
								</div>

								<div id="photoUploaded" class="py-2 text-success">
									Photo uploaded
								</div>
							</div>

							<div class="form-group">
								<label for="username">Username</label>
								<input type="text" class="form-control" id="username" placeholder="Enter name" name="username" value="<?php echo $row['Username']; ?>" required>
							</div>

							<div class="form-group">
								<label for="email">Email</label>
								<input type="email" class="form-control" id="email" placeholder="Enter email" name="email" value="<?php echo $row['Email']; ?>" required>
							</div>

							<div class="form-group">
								<label for="pass">Current Password</label>
								<input type="password" class="form-control" id="pass" placeholder="Enter current password" name="pass">
								<p class="mt-2 text-info show-pass">Show password</p>
							</div>

							<div class="form-group">
								<label for="new_pass">New Password</label>
								<input type="password" class="form-control" id="new_pass" placeholder="Enter new password" name="new_pass">
								<p class="mt-2 text-info show-pass">Show password</p>
							</div>

							<div class="form-group">
								<label for="new_pass_2">Confirm Password</label>
								<input type="password" class="form-control" id="new_pass_2" placeholder="Confirm password" name="new_pass_2">
								<p class="mt-2 text-info show-pass">Show password</p>
							</div>

							<div class="form-group">
								<label for="dateofbirth">Date of Birth</label>
								<input type="date" class="form-control" id="dateofbirth" name="dateofbirth" value="<?php echo $row['DateOfBirth']; ?>" required>
							</div>

							<div class="form-row justify-content-center">
								<div class="col-12 col-md-6">
									<button type="submit" class="btn btn-warning d-block mt-4 w-100" name="update">Update</button>
								</div>
							</div>

							<div class="form-row justify-content-center">
								<div class="col-12 col-md-6">
									<button id="deleteAccount" type="submit" class="btn btn-danger d-block mt-4 w-100" name="delete">Delete</button>
								</div>
							</div>

							<input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
						</form>

					<?php
					}

				} else {
					echo 'User does not exist in our database.';
				}

			} else {
				echo "Error in sql query <br> <b>" . $stmt->error . "</b>";
			}

			$stmt->close();
			$conn->close();
			?>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>
