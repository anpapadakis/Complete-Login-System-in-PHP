<?php
  function sendVerificationEmail($email,$hash) {
    //echo $email . ' ' . $hash;

    $to = $email;
    $subject = "Verify your account";
    $message = "
    <html>
    <head>
    <title>Verify account for " . $email . "</title>
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
    <p>Please verify your account by cicking the button below</p>
    <a href='https://projects.anpapadakis.com/php/admin/verify_account.php?email=" . $email . "&hash=" . $hash . "'
    class='btn'>Verify</a>
    </div>
    </body>
    </html>";

    // echo $message;
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8";
    $headers .= "From: <webmaster@admin.com>";

    if(mail($to,$subject,$message,$headers)) {
      echo "A verification email has been sent to <b>" . $email . "</b>. Please verify your account.<br>" ;
      // echo "Go to <a href='index.php'>login page</a>";
    } else {
      echo "Verification email has not been sent. Please contact admin.";
    }
  }
?>
