<?php #logout.php

  session_start();
  require("php/includes/config.inc.php");

  if (isset($_SESSION['agent'])) {
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time()-3600, '/', '', 0, 0);
  }

  redirect_user("register_login.php");

?>
