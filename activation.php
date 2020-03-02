<?php #php/activation.php

  //set up the page
  require("php/includes/config.inc.php");
  $page_title = "Account activation";
  include("templates/header.php");

  //only get request are to be proccessed
  if ($_SERVER['REQUEST_METHOD'] === "GET") {

    //after basic validation activate the account
    if (isset($_GET['x'], $_GET['y'])
        && filter_var($_GET['x'], FILTER_VALIDATE_EMAIL)
        && strlen($_GET['y']) == 32) {

      require(MYSQL);
      $email = mysqli_real_escape_string($dbconnect, $_GET['x']);
      $code = mysqli_real_escape_string($dbconnect, $_GET['y']);
      $query = "UPDATE users SET active=NULL WHERE email='$email'
                AND active='$code'";
      $result = _execute_query($dbconnect, $query);

      //print customized message
      if (mysqli_affected_rows($dbconnect) == 1) {
        echo "<h3>Your account is now active. You may login!</h3>";
      } else {
        echo "<p>Your account could not be activated. Please re-check the link
              or contact system administrator</p>";
      }
      mysqli_close($dbconnect);
    }

    //if x or y dont exist or in inproper format redirect user
    else {
      ob_end_clean();
      redirect_user();
      exit();
    }
  }

  include("templates/footer.html");

 ?>
