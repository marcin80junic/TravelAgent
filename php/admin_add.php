<?php  #admin_add.php

  //make sure that table name has been specified
  if (isset($_REQUEST['table'])) {
    if (!empty($_REQUEST['table'])) {
      $table_name = $_REQUEST['table'];
    } else {
      echo '<p>table name is undefined!</p>';
      exit();
    }
  } else {
    echo '<p>table name not received!</p>';
    exit();
  }

  //set up the script
  require("../../../../xxsecure/dbconnect.php");
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
    //if no errors carry on with insertion
    else {
      if ($table_name === "users") {
        $result_insert = users_insert($dbconnect, $reg_data);
        report_query($dbconnect);
        if($result_insert && isset($_POST['newsletter'])) {
          $sign_up_result = newsletter_sign_up($dbconnect, $email);
          if ($sign_up_result) {
            echo '<p>successfully signed up for a newsletter</p>';
          } elseif (mysqli_error($dbconnect)) {
            echo '<p>MySql Error: '.mysqli_error($dbconnect).'</p>';
          } else {
            echo '<p>Your email address is already receiving a newsletter</p>';
          }
        }
      }
      elseif ($table_name === "newsletter") {
        if (is_email_unique($dbconnect, $table_name, $email)) {
          $sign_up_result = newsletter_insert($dbconnect, $email, $reg_news);
          report_query($dbconnect);
        }
      }
      elseif ($table_name === "holidays") {
        $result_insert = holidays_insert($dbconnect, $reg_holid);
        report_query($dbconnect);
      }
      close_script($dbconnect);
    }

  }

  //create a sticky form
  echo '<form id="decision" enctype="multipart/form-data" action="admin_add.php" method="post">
          <p>Please fill in details for new '.$table_name.' record';

  $current_data = ignore_values($current_data);
  create_table_form($table_name, $current_data, $current_type);

  echo '<br><button id="create" type="submit" name="yes" value="yes">Create</button>
        <button id="cancel" name="cancel" value="cancel" class="ml-2">Cancel</button>
      </form>';

  ?>
