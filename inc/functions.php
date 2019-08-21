<?php
//session_start();

include 'db.php';
$conn = dbConnection();

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function login() {
  $stmt = $GLOBALS['conn']->prepare("select Password,Verified from User where Username=? or Email=?");
  $stmt->bind_param("ss",$_SESSION['username'],$_SESSION['username']);

  if ($stmt->execute()) {
    $result = $stmt->get_result();

    // If user exists in database
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
            exit();

          } else {
            $_SESSION['verified_user'] = false;

            echo "User is not verified. Please verify your account. <br>";
            echo "<a href='verify_account.php?action=sendVerificationEmail&user=" . $_SESSION['username'] ."'>Send verification email.</a>";
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

  $stmt->close();
  $GLOBALS['conn']->close();
}

function userExists($username,$email) {
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
  $GLOBALS['conn']->close();
}

function checkCurrentPassword($username,$email,$password) {
  $stmt = $GLOBALS['conn']->prepare("select Password from User where Username=? or Email=?");
  $stmt->bind_param("ss",$username,$email);

  if ($stmt->execute()) {
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $db_password = $row['Password'];

      if (password_verify($password,$db_password)) {
        return  true;
      } else {
        return false;
      }
    } else {
      echo "User not found.";
    }
  } else {
    echo "Error in select query: <i>" . $stmt->error . "</i>";
  }

  $stmt->close();
  $GLOBALS['conn']->close();
}

function uploadPhoto() {
  $photos_path = "photos/";
  // $photo = addslashes(file_get_contents($_FILES['r_photo']['name']));
  $photo = $_FILES['photo']['name'];

  $photos_path .= $_FILES['photo']['name'];

  // $photo = $_FILES['r_photo']['tmp_name'];

  if (move_uploaded_file($_FILES['photo']['tmp_name'], $photos_path)) {
    //echo "moved";
    $photo_saved = 1;
  } else {
    //echo "not moved";
    $photo_saved = 0;
  }
}

function validateDateOfBirth($date) {
  $today = date("Y-m-d");
  $diff = date_diff(date_create($date), date_create($today));

  if ($diff->format('%y%') < 18) {
    die("<strong>Your age must be at least 18 years old.</strong> <br> <a href='index.php'>Register</a>");
  }
}

?>
