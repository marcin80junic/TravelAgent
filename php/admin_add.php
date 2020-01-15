<?php  #admin_add.php

  //make sure that table name has been specified
  if (isset($_REQUEST['table'])) {
    if (!empty($_REQUEST['table'])) {
      $table_name = $_GET['table'];
    } else {
      echo '<p>table name is undefined!</p>';
      exit();
    }
  } else {
    echo '<p>table name not received!</p>';
    exit();
  }

  //set up the script
  require("../../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");
  set_current_data($table_name);

  //if form has been submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //perform form validation and print errors if any
    require("form_validation.php");
    if (!empty($edit_errors)) {
      foreach($edit_errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }

  }

  //create a sticky form
  echo "hello!";
