<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/jquery-ui.css">
  <link rel="stylesheet" href="css/theme.css">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/my.css">
  <script src="js/jquery-3.4.1.js"></script>
  <script src="js/popper.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/init.js"></script>
  <title><?php $page_title = isset($page_title)? $page_title: "Europe Travel Experts";
          echo "$page_title"; ?></title>
</head>
<body class="d-flex flex-column">
  <div class="wrapper container-fluid flex-fill px-5 pb-5 pt-3">
    <h1 class="text-white text-center">Europe Travel Experts<br><small>make your holidays come true...</small></h1>
    <div class="px-0 border border-primary rounded-lg">
      <nav class="navbar navbar-expand-sm sticky-top bg-info shadow-sm py-0">
        <ul id="navigation" class="navbar-nav">
          <li id="index" class="nav-item px-2 py-1">
            <a class="nav-link text-white" href="index.php">Home</a>
          </li>
          <li id="hot-deals" class="nav-item px-2 py-1">
            <a class="nav-link text-white font-weight-bolder" href="">Hot Deals!</a>
          </li>
          <li id="holidays" class="nav-item px-2 py-1">
            <a class="nav-link text-white" href="">Holidays</a>
          </li>

          <?php
            if (isset($_SESSION['agent']) && ($_SESSION['agent'] === sha1($_SERVER['HTTP_USER_AGENT']))) {
              echo '<li id="logout" class="nav-item px-2 py-1">
                      <a class="nav-link text-white" href="logout.php">Logout</a>
                    </li>';
            } else {
              echo '<li id="register" class="nav-item px-2 py-1">
                     <a class="nav-link text-white" href="register_login.php">Register/Login</a>
                   </li>';
            }
           ?>

        </ul>
        <ul id="navigation" class="navbar-nav ml-auto">

          <?php
            if (isset($_SESSION['agent']) && ($_SESSION['agent'] === sha1($_SERVER['HTTP_USER_AGENT']))) {
              if (isset($_SESSION['email']) && ($_SESSION['email'] === "admin")) {
                echo '<li id="admin" class="nav-item px-2 py-1">
                        <a class="nav-link text-white" href="admin.php">admin</a>
                      </li>';
              } else {
                echo '<li id="account" class="nav-item px-2 py-1">
                        <a class="nav-link text-white" href="account_settings.php">My Account</a>
                      </li>';
              }
            }
           ?>

        </ul>
      </nav>

      <div class="flex-grow-1 text-center p-5">
