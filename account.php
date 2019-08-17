<?php
session_start();

if (empty($_SESSION['logged_in'])) {
	header('Location: index.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <?php
      include 'functions/db.php';
      $conn = dbConnection();

			// Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      // vars
      $username = $_SESSION['username'];
      $password = $_SESSION['password'];

			if (empty($username) || empty($password)) {
				die("Info missing.");
			}

        $query = "select * from User where (Username='" . $_SESSION['username'] . "' OR Email='" . $_SESSION['username'] . "')";

        $result = $GLOBALS['conn']->query($query);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) { ?>

            <form action="update_profile.php" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="form-group d-flex justify-content-center">
								<?php
								// if ($row['Photo'] != '') {
								// 	$photo = "photos/" . $row['Photo'];
								// } else {
								// 	$photo = "photos/user.png";
								// }

								$photo = $row['Photo'] != '' ? "photos/" . $row['Photo'] : "photos/user.png";
								?>

                <div class="profile-photo" style="background-image: url('<?php echo $photo; ?>')">
                  <div class="update-photo">
                    <label>
                      Update
                      <input type="file" id="r_photo" name="r_photo">
                    </label>
                  </div>
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
								<input type="password" class="form-control" id="pass" placeholder="Enter current password" name="pass" required>
								<p class="mt-2 text-info show-pass">Show password</p>
							</div>
							<div class="form-group">
								<label for="new_pass">New Password</label>
								<input type="password" class="form-control" id="new_pass" placeholder="Enter new password" name="new_pass" required>
								<p class="mt-2 text-info show-pass">Show password</p>
							</div>
              <div class="form-group">
                <label for="new_pass_2">Confirm Password</label>
                <input type="password" class="form-control" id="new_pass_2" placeholder="Confirm password" name="new_pass_2" required>
                <p class="mt-2 text-info show-pass">Show password</p>
              </div>
							<div class="form-group">
								<label for="dateofbirth">Date of Birth</label>
								<input type="date" class="form-control" id="dateofbirth" placeholder="Enter date" name="dateofbirth" value="<?php echo $row['DateOfBirth']; ?>" required>
							</div>

							<input type="hidden" name="r_id" value="<?php echo $row['ID']; ?>">
							<button type="submit" class="btn btn-warning mx-auto d-block mt-4" name="update">Update</button>
						</form>

        <?php
          }
        } else {
          echo 'No Info found.';
        }
      ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
