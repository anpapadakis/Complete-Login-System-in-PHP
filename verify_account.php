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

          if (isset($_GET['action']) && $_GET['action'] == "sendVerificationEmail") {
            if (isset($_GET['email'])) {
              $email = $_GET['email'];

              // Check connection
      				if ($conn->connect_error) {
      					die("Connection failed: " . $conn->connect_error);
      				}

              $stmt = $GLOBALS['conn']->prepare("select VerificationCode from User where Email=?");
              $stmt->bind_param("s",$email);

              if($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                  $stmt->bind_result($VerificationCode);
                  $stmt->fetch();

                  include_once 'functions/email.php';
                  sendVerificationEmail($email,$VerificationCode);
                } else {
                  echo "Email " . $email . " does not exist.";
                }
              } else {
                echo "Error in select query: <i>" . $stmt->error . "</i>";
              }
              $stmt->close();
            }

            exit();
          }

          if (isset($_GET['email']) && !empty($_GET['email']) && isset($_GET['hash']) && !empty($_GET['hash'])) {
            $email = $_GET['email'];
            $hash = $_GET['hash'];

            // Check connection
    				if ($conn->connect_error) {
    					die("Connection failed: " . $conn->connect_error);
    				}

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

              // if($stmt->execute()) {
  						// 	$stmt->store_result();
              //
  						// 	if($stmt->affected_rows > 0) {
              //     echo $row['Verified'];
              //     if ($row['Verified'] == 0) {
              //       $verified = false;
              //       echo "false";
              //     } else {
              //       echo "true";
              //       $verified = true;
              //     }
              //
              //     return $verified;
  						// 	}
              //
  						// } else {
  						// 	echo "Error in sql query: <i>" . $stmt->error . "</i>";
  						// }
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
              echo "<p>The email <b>" . $email . "</b> does not exist in our database.</p>";
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
