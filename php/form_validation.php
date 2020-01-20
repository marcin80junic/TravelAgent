<?php

  //declare variables
  $reg_errors = $edit_errors = $reg_data = $edit_data = $reg_news = $edit_news = [];
  $email = $pass = "";
  $email_unique = $privacy = false;

  //validate all form fields and record all errors
  if(isset($_POST['f_name'])) {
    if(!empty($_POST['f_name'])) {
      $f_name = mysqli_real_escape_string($dbconnect, trim($_POST['f_name']));
      $reg_data[] = $f_name;
      if (isset($_POST['orig_f_name'])) {
        if ($_POST['orig_f_name'] !== $f_name) {
          $edit_data['f_name'] = $f_name;
        }
      }
    } else {
      $edit_errors[] = $reg_errors[] = "Fill in first name please!";
    }
  }

  if(isset($_POST['l_name'])) {
    if(!empty($_POST['l_name'])) {
      $l_name = mysqli_real_escape_string($dbconnect, trim($_POST['l_name']));
      $reg_data[] = $l_name;
      if (isset($_POST['orig_l_name'])) {
        if ($_POST['orig_l_name'] !== $l_name) {
          $edit_data['l_name'] = $l_name;
        }
      }
    } else {
      $edit_errors[] = $reg_errors[] = "Fill in last name please!";
    }
  }

  if (isset($_POST['email'])) {
    if (!empty($_POST['email'])) {
      $email = mysqli_real_escape_string($dbconnect, trim($_POST['email']));
      $reg_data[] = $reg_news['email'] = $email;
      $table_name = isset($table_name)? $table_name: "users";
      $email_unique = is_email_unique($dbconnect, $table_name, $email);
      if (!$email_unique) {
        $reg_errors[] = "this email address is already in our database!";
      }
      if (isset($_POST['orig_email']) && ($_POST['orig_email'] != $email)) {
        if (!$email_unique) {
          $edit_errors[] = "this email address is already in our database!";
        } else {
          $edit_data['email'] = $email;
          $edit_news['email'] = $email;
        }
      }
    } else {
      $edit_errors[] = $reg_errors[] = "fill in an email address please!";
    }
  }

  if(isset($_POST['password'])) {
    if(!empty($_POST['password'])) {
      $pass = mysqli_real_escape_string($dbconnect, trim($_POST['password']));
      $reg_data[] = $pass;
      if (isset($_POST['orig_password'])) {
        if ($_POST['orig_password'] !== $pass) {
          $edit_data['password'] = $pass;
        }
      }
      if (strlen($pass) < 8) {
        $edit_errors[] = $reg_errors[] = "password must be AT LEAST 8 characters long";
      }
    } else {
      $reg_errors[] = $edit_errors[] = "fill in password field please!";
    }
  }

  if(isset($_POST['confirm_password'])) {
    if(!empty($_POST['confirm_password'])) {
      $conf_pass = mysqli_real_escape_string($dbconnect, $_POST['confirm_password']);
      if($conf_pass != $pass) {
        $edit_errors[] = $reg_errors[] = "password confirmation DOESN'T match!";
      }
    } else {
      $edit_errors[] = $reg_errors[] = "password confirmation is empty!";
    }
  }

  if(isset($_POST['mobile'])) {
    $mobile = mysqli_real_escape_string($dbconnect, trim($_POST['mobile']));
    $reg_data[] = $mobile;
    if ($mobile != "" && !is_numeric($mobile)) {
      $edit_errors[] = $reg_errors[] = "mobile number must contain digits only (no spaces)!";
    }
    if (isset($_POST['orig_mobile'])) {
      if ($_POST['orig_mobile'] !== $mobile) {
        $edit_data['mobile'] = $mobile;
      }
    }
  }

  if(!isset($_POST['privacy'])) {
    $reg_errors[] = "You have to agree to the privacy policy!";
  }

  foreach(NEWSLETTER_COLUMNS as $value) {
    if ($value !== "email") {
      if (!isset($_POST["orig_email"])) {
        $reg_news[] = isset($_POST[$value])? "true": "false";
      }
      elseif (isset($_POST["orig_$value"])) {
        if ((isset($_POST[$value]) && $_POST["orig_$value"] === "0")
          || (!isset($_POST[$value]) && $_POST["orig_$value"] === "1")) {
        $edit_news[$value] = isset($_POST[$value])? "true": "false";
        }
      }
    }
  }

?>
