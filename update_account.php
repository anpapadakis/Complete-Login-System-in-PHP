<?php
session_start();
// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('log_errors_max_len', 0);
// ini_set('error_log', 'errors.log');



if (empty($_SESSION['logged_in'])) {
  header('Location: index.php');
}

require_once 'inc/functions.php';
?>

<?php include 'header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
      <?php
      include_once 'inc/db.php';
      $conn = dbConnection();

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      if (isset($_POST['submit'])) {
        // vars
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
          // uploadPhoto();
          $photos_path = "photos/";
          // $photo = addslashes(file_get_contents($_FILES['r_photo']['name']));
          $photo = $_FILES['photo']['name'];

          $photos_path .= $_FILES['photo']['name'];

          // $photo = $_FILES['r_photo']['tmp_name'];

          if(move_uploaded_file($_FILES['photo']['tmp_name'],$photos_path)) {
            $photo_saved = 1;
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

              if (!checkCurrentPassword($username,$email,$current_password)) {
                header("Location: account.php?result=wrong_pass");
                exit();
              }
            } else {
              die("Please enter the new password. <br> <a href='account.php'>Account</a>");
            }
          } else {
            die("Please enter the current password. <br> <a href='account.php'>Account</a>");
          }
        }

        validateDateOfBirth($date,'update');
        //
        // $today = date("Y-m-d");
        // $diff = date_diff(date_create($date), date_create($today));
        //
        // if($diff->format('%y%') < 18){
        //   die("<strong>You must be at least 18 years old.</strong>");
        // }
        /* Finish validations */

        // if (!empty($password)) {
        //   $query = "update User set Username=?,Email=?,Password=?,DateOfBirth=?,Photo=? where ID=?";
        //   $stmt = $conn->prepare($query);
        //   $stmt->bind_param("sssssi",$username,$email,$password,$date,$photo,$id);
        //   $password = password_hash($password,PASSWORD_BCRYPT);
        // } else {
        //   $query = "update User set Username=?,Email=?,DateOfBirth=?,Photo=? where ID=?";
        //   $stmt = $conn->prepare($query);
        //   $stmt->bind_param("ssssi",$username,$email,$date,$photo,$id);
        //   $id = $id+1;
        // }

        // $query = "update User set Username=?,Email=?,DateOfBirth=?,Photo=? where ID=?";
        // $stmt = $conn->prepare($query);
        // $stmt->bind_param("ssssi",$username,$email,$date,$photo,$id);

        $query = "update User set Username=?,Email=?,DateOfBirth=?";
        $params_types = 'sss';
        $params_values = array($username,$email,$date);

        if (!empty($password)) {
          $query .= ",Password=?";
          $params_types .= 's';
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
          //$stmt->store_result();
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
