<?php  #login.php


  /* validates the email and password from the login page, returns either
  true and record from the DB or false and error array to display on login form */
  function check_login($dbc, $username='', $pass='') {

    $errors = [];

    if (empty($username)) {
    $errors[] = 'enter your username please';
    } else {
      $username = mysqli_real_escape_string($dbc, trim($username));
    }

    if (empty($pass)) {
      $errors[] = 'enter your password please';
    } else {
      $pass = trim($pass);
    }

    //if login credentials were delivered carry on
    if (empty($errors)) {
      $query = "SELECT * FROM users WHERE user_name='$username'";
      $result = mysqli_query($dbc, $query);
      if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (password_verify($pass, $row['password'])) {
          unset($row['password']);
          if ($row['active'] == NULL) {
            return [true, $row];
          } else {
            $errors[] = "account has not been activated!";
          }
        } else {
          $errors[] = "incorrect login details!";
        }
      } else {
        $errors[] = "incorrect login details!";
      }
    }
    return [false, $errors];
  }


  //check for submission
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require("php/includes/config.inc.php");
    require(MYSQL);

    list($check, $data) = check_login($dbconnect, $_POST['email'], $_POST['password']);
    if ($check) {
      session_start();
      foreach($data as $key => $value) {
        if ($key !== "password") {
          $_SESSION[$key] = $data[$key];
        }
      }
      $_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);
      if ($_SESSION['user_level'] === "3") {
        session_regenerate_id();
      }
      redirect_user("index.php");
    }

    else {
      $errors = $data;
      include("register_login.php");
    }
    mysqli_close($dbconnect);
  }

 ?>
