<?php #login_functions.php


  //function determines the absolute URL and redirects the user
  function redirect_user($page='index.php') {

    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $url = rtrim($url, '/\\');
    $url .= '/' . $page;

    header("Location: $url");
    exit();

  }

  /*
  validates the email and password from the login page, returns either
  true and record from the DB or false and error array to display on login form
  */
  function check_login($dbc, $email='', $pass='') {

    $errors = [];

    if (empty($email)) {
      $errors[] = 'enter your email address or username please';
    } else {
      $email = mysqli_real_escape_string($dbc, trim($email));
    }

    if (empty($pass)) {
      $errors[] = 'enter your password please';
    } else {
      $pass = mysqli_real_escape_string($dbc, trim($pass));
    }

    if (empty($errors)) {
      $query = "SELECT * FROM users WHERE email='$email' AND password=SHA2('$pass', 512)";
      $result = mysqli_query($dbc, $query);

      if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return [true, $row];
      } else {
        $errors[] = "incorrect login details!";
      }
    }

    return [false, $errors];

  }

?>