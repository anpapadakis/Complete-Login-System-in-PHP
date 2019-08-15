<?php
  function sendVerificationEmail($email,$hash) {
    //echo $email . ' ' . $hash;

    $to = $email;
    $subject = "Verify your account";
    $message = "
    <html>
    <head>
    <title>Verify account for " . $email . "</title>

    <style>
      .container {
        padding: 20px 15px;
        text-align: center;
      }

      a.btn {
        padding: 10px 15px;
        background-color: #17a2b8;
        color: #fff;
        width: auto;
        text-decoration: none;
        font-weight: 600;
        margin-top: 25px;
        display: inline-block;
      }
    </style>
    </head>
    <body>
    <div class='container'>
    <p>Please verify your account by cicking the button below</p>
    <a href='https:\/\/projects.anpapadakis.com/php/admin/verify_account.php?email=" . $email . "&hash=" . $hash . "'
    class='btn'>Verify</a>
    </div>
    </body>
    </html>";

    // echo $message;
    $headers = "Content-type:text/html;charset=UTF-8";
    $headers .= "From: <webmaster@admin.com>";

    if(mail($to,$subject,$message,$headers)) {
      echo "A verification email has been sent to " . $email . ". Please verify your account.<br>" ;
      echo "Go to <a href='index.php'>login page</a>";
    } else {
      echo "Verification email has not been sent. Please contact admin.";
    }
  }
?>
