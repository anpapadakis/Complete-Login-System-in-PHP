<?php
//session_start();

include 'db.php';
$conn = dbConnection();

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Login with username/email and password
function login() {
  // $query = "select Password,Verified from User where (Username='" . $_SESSION['username'] . "' OR Email='" . $_SESSION['username'] . "')";
  // $result = $GLOBALS['conn']->query($query);
  // echo $query;
  // print_r($result);

  $stmt = $GLOBALS['conn']->prepare("select Password,Verified from User where Username=? or Email=?");
  $stmt->bind_param("ss",$username,$email);
  $username = $_SESSION['username'];
  $email = $_SESSION['username'];

  // If user exists in database
  if($stmt->execute()) {
    $result->store_result();
  if ($result->num_rows > 0) {
    $_SESSION['user_exists'] = true;

    // Validate password
    while($row = $result->fetch_assoc()) {
      if(password_verify($_SESSION['password'], $row['Password'])) {
        $_SESSION['password_validation'] = true;

        if ($row['Verified'] == 1) {
          $_SESSION['logged_in'] = true;
          $_SESSION['verified_user'] = true;
          echo "<script> window.location.href='account.php' </script>";
        } else {
          $_SESSION['verified_user'] = false;

          echo "User is not verified. Please verify your account. <br>";
          echo "<a href=''>Sent verification email.</a>";

          include 'functions/email.php';
          sendVerificationEmail();
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
  echo "Error in select query: <i>" . $stmt->error . "</i>";
}
}


$conn->close();
?>
