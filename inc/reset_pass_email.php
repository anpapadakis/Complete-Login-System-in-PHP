<?php
  function resetPasswordEmail($email,$resetPasswordHash) {
    //echo $email . ' ' . $hash;

    $to = $email;
    $subject = "Reset your password";
    $message = "
    <html>
    <head>
    <title>Reset password for " . $email . "</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>

    <style>
      .container {
        width: 100%;
        max-width: 600px;
        margin: auto;
        padding: 20px;
        text-align: center;
        background-color: #eeeff1;
      }

      a.btn {
        padding: 10px 15px;
        background-color: #17a2b8;
        color: #fff!important;
        text-decoration: none;
        font-weight: 600;
        margin-top: 25px;
        display: inline-block;
      }
    </style>
    </head>
    <body>
    <div class='container'>
    <p>Click the button below to reset your password.</p>
    <a href='https://projects.anpapadakis.com/php/admin/reset_password.php?hash=" . $resetPasswordHash . "'
    class='btn'>Reset</a>
    </div>
    </body>
    </html>";

    // echo $message;
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8";
    $headers .= "From: <webmaster@admin.com>";

    if(mail($to,$subject,$message,$headers)) {
      echo "An email has been sent to <b>" . $email . "</b> in order to reset your password.<br>" ;
      echo "<a href='index.php'>Login</a>";
    } else {
      echo "Email has not been sent. Please contact admin.";
    }
  }
?>
