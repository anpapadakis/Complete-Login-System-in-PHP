<!DOCTYPE html>
<html lang="en">
<head>
	<title>Admin Panel</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
	<link rel="stylesheet" href="polyfill/html5-simple-date-input-polyfill.css" />
	<link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container-fluid p-0 bg-light border-bottom">
    <div class="container">
      <div class="row">
        <nav class="navbar navbar-expand-lg navbar-light w-100 justify-content-between">
          <a class="navbar-brand" href="/">Admin</a>
          <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item active">
                <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
              </li>
            </ul>
          </div> -->

          <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) { ?>
            <div class="d-flex flex-column align-items-center">
              <div class="username">
                <?php
                  if (isset($_SESSION['username'])) {
                    echo $_SESSION['username'];
                  }
                ?>
              </div>
              <a href="index.php?action=logout" class="logout">Logout</a>
            </div>
          <?php } ?>
        </nav>
      </div>

    </div>

  </div>
