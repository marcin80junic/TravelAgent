<?php

  //declare variables
  $reg_errors = $edit_errors = $reg_data = $news = [];
  $email = $pass = "";
  $email_unique = $privacy = $newsletter = false;

  //validate all form fields and record all errors
  if(isset($_POST['f_name'])) {
    if(!empty($_POST['f_name'])) {
      $reg_data[] = mysqli_real_escape_string($dbconnect, trim($_POST['f_name']));
    } else {
      $edit_errors[] = $reg_errors[] = "Fill in first name please!";
    }
  }

  if(isset($_POST['l_name'])) {
    if(!empty($_POST['l_name'])) {
      $reg_data[] = mysqli_real_escape_string($dbconnect, trim($_POST['l_name']));
    } else {
      $edit_errors[] = $reg_errors[] = "Fill in last name please!";
    }
  }

  if (isset($_POST['email'])) {
    if (!empty($_POST['email'])) {
      $reg_data[] = $email = mysqli_real_escape_string($dbconnect, trim($_POST['email']));
      $email_unique = users_is_unique_email($dbconnect, $email);
      if (!$email_unique) {
        $reg_errors[] = "this email address is already in our database!";
      }
      if (isset($_POST['orig_email']) && $_POST['orig_email'] != $email) {
        if (!$email_unique) {
          $edit_errors[] = "this email address is already in our database!";
        }
      }
    } else {
      $edit_errors[] = $reg_errors[] = "fill in an email address please!";
    }
  }

  if(isset($_POST['password'])) {
    if(!empty($_POST['password'])) {
      $reg_data[] = $pass = mysqli_real_escape_string($dbconnect, $_POST['password']);
      if(strlen($pass) < 8) {
        $edit_errors[] = $reg_errors[] = "password must be AT LEAST 8 characters long";
      }
    } else {
      $reg_errors[] = "fill in password field please!";
    }
  }

  if(isset($_POST['confirm_password'])) {
    if(!empty($_POST['confirm_password'])) {
      $conf_pass = mysqli_real_escape_string($dbconnect, $_POST['confirm_password']);
      if($conf_pass != $pass) {
        $edit_errors[] = $reg_errors[] = "password confirmation DOESN'T match!";
      }
    }
  }

  if(isset($_POST['mobile'])) {
    $mobile = trim($_POST['mobile']);
    $reg_data[] = $mobile;
    if($mobile != "" && !is_numeric($mobile)) {
      $edit_errors[] = $reg_errors[] = "mobile number must contain digits only (no spaces)!";
    }
  }

  if(isset($_POST['privacy'])) {
    $privacy = true;
  }

  if(isset($_POST['newsletter'])) {
    $newsletter = ($_POST['newsletter'] == "Yes")? true: false;
  }

  if(isset($_POST['edit_newsletter'])) {
    $newsletter = $_POST['edit_newsletter'];
  }

  foreach(NEWSLETTER_DATA as $value) {
    if(isset($_POST[$value])) {
      $news[] = ($_POST[$value] == "false")? 0: 1;
    }
  }

?>
