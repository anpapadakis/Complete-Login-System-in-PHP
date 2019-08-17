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
          include_once 'functions/db.php';
          $conn = dbConnection();

					// Check connection
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					}

					// If not verified, send verification email
          if (isset($_GET['action']) && $_GET['action'] == "sendVerificationEmail") {
            if (isset($_GET['user'])) {
              $user = $_GET['user'];

              $stmt = $GLOBALS['conn']->prepare("select Email,VerificationCode from User where Username=? or Email=?");
              $stmt->bind_param("ss",$user,$user);

              if($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                  $stmt->bind_result($Email,$VerificationCode);
                  $stmt->fetch();

                  include_once 'functions/email.php';
                  sendVerificationEmail($Email,$VerificationCode);
                } else {
                  echo "User " . $user . " does not exist.";
                }
              } else {
                echo "Error in select query: <i>" . $stmt->error . "</i>";
              }
              $stmt->close();
            }

            exit();
          }

					// Send verification email after register
          if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
            $email = $_GET['email'];
            $hash = $_GET['hash'];

            function userVerified($email) {
              $stmt = $GLOBALS['conn']->prepare("select Verified from User where Email = ?");
  						$stmt->bind_param("s",$email);

              if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->affected_rows > 0) {
                  $user_exists = true;

                  $stmt->bind_result($Verified);

                  while ($stmt->fetch()) {
                    if ($Verified == 0) {
                      $verified = false;
                    } else {
                      $verified = true;
                    }
                  }
                }

                return $verified;
              } else {
  							echo "Error in sql query: <i>" . $stmt->error . "</i>";
  						}

              $stmt->close();
            }

            function userExists($email) {
              $stmt = $GLOBALS['conn']->prepare("select ID from User where Email=?");
              $stmt->bind_param("s",$email);

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
            }

            //Check if email exists
            if (userExists($email)) {
              // Check if user is already verified
              if (userVerified($email)) {
                echo "User is already verified.<br>";
                echo "Go to <a href='index.php'>login page</a>.";
              } else {
                $stmt = $GLOBALS['conn']->prepare("update User set Verified = ? where Email = ? and VerificationCode = ? and Verified = 0");

                $verified = 1;
                $stmt->bind_param("iss",$verified,$email,$hash);

                if($stmt->execute()) {
                  if($stmt->affected_rows > 0) {
                    echo "<p>The email <b>" . $email . "</b> has been verified successfully.</p>";
                    echo "<p>Go to <a href='index.php'>login page</a></p>.";
                  }
                } else {
                  echo "<p>Error in sql query: <i>" . $stmt->error . "</i></p>";
                }
              }

            } else {
              echo "<p>The email <b>" . $email . "</b> does not exist in the database.</p>";
              echo "<p>Please <a href='index.php'>register</a>.</p>";
            }

            $conn->close();

          } else {
            echo "<script> window.location.href='index.php' </script>";
          }
        ?>

      </div>
    </div>
  </div>

<?php include 'footer.php'; ?>
