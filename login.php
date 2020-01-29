<?php  #login.php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require("../../../xxsecure/dbconnect.php");
    require("php/login_functions.php");

    list($check, $data) = check_login($dbconnect, $_POST['email'], $_POST['password']);

    if ($check) {
      session_start();
      foreach($data as $key => $value) {
        if ($key !== "password") {
          $_SESSION[$key] = $data[$key];
        }
      }
      $_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);
      if ($_SESSION['f_name'] === "admin") {
        session_regenerate_id();
      }
      redirect_user('index.php');
    }

    else {
      $errors = $data;
    }

    mysqli_close($dbconnect);

  }

  include("register_login.php");

 ?>
