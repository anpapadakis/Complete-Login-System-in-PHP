<?php
session_start();

if (empty($_SESSION['logged_in'])) {
	header('Location: index.php');
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 text-center">
      <?php

      // if ($_SESSION['user_exists']) {
      //   if ($_SESSION['password'] == true) {
      //     if ($_SESSION['user'] != null) {
      //       echo "Hi " . $_SESSION['user'] . '!';
      //     }
      //   } else {
      //     echo "Wrong password. Please <a href='/php/admin'>try again or reset your password.</a>";
      //   }
      // } else {
      //   echo "User does not exist. Please <a href='/php/admin'>register</a>";
      // }

      ?>
    </div>
  </div>
</div>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <?php
      include 'db.php';
      $conn = dbConnection();

      // vars
      $username = $_SESSION['username'];
      $password = $_SESSION['password'];

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      if (!empty($_SESSION['username'])) {
        $query = "select * from User where (Username='" . $_SESSION['username'] . "' OR Email='" . $_SESSION['username'] . "')";

        $result = $GLOBALS['conn']->query($query);

        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) { ?>

            <form action="register.php" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="form-group d-flex justify-content-center">
                <div class="profile-photo-container">
                  <?php if ($row['Photo'] != '') { ?>
                    <img src="photos/<?php echo $row['Photo']; ?>" alt="" class="profile-photo rounded-circle">
                  <?php } else { ?>
                    <img src="photos/user.png" alt="user" class="profile-photo rounded-circle">
                  <?php } ?>

                  <div class="update-photo">
                    <label>
                      Update
                      <input type="file" id="r_photo" name="r_photo">
                    </label>
                  </div>
                </div>
              </div>
							<div class="form-group">
								<label for="r_username">Username</label>
								<input type="text" class="form-control" id="r_username" placeholder="Enter name" name="r_username" value="<?php echo $row['Username']; ?>" required>
							</div>
							<div class="form-group">
								<label for="r_email">Email</label>
								<input type="email" class="form-control" id="r_email" placeholder="Enter email" name="r_email" value="<?php echo $row['Email']; ?>" required>
							</div>
							<div class="form-group">
								<label for="r_pass">Password</label>
								<input type="password" class="form-control" id="r_pass" placeholder="Enter new password" name="r_pass" required>
								<p class="mt-2 text-info show-pass" onclick="showPassword(r_pass);">Show password</p>
							</div>
              <div class="form-group">
                <label for="r_pass_2">Confirm Password</label>
                <input type="password" class="form-control" id="r_pass_2" placeholder="Confirm password" name="r_pass_2" required>
                <p class="mt-2 text-info show-pass" onclick="showPassword(r_pass_2);">Show password</p>
              </div>
							<div class="form-group">
								<label for="r_date">Date of Birth</label>
								<input type="date" class="form-control" id="r_date" placeholder="Enter date" name="r_dateofbirth" value="<?php echo $row['DateOfBirth']; ?>" required>
							</div>

							<button type="submit" class="btn btn-warning mx-auto d-block mt-4" name="update">Update</button>
						</form>

        <?php
          }
        } else {
          echo 'No Info found.';
        }

      } else {
        echo "<strong>Info missing!</strong>";
      }
      ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
