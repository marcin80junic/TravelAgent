<?php #logout.php

  session_start();

  if (isset($_SESSION['agent'])) {
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time()-3600, '/', '', 0, 0);
  }

  require("php/login_functions.php");
  redirect_user("register_login.php");

?>
