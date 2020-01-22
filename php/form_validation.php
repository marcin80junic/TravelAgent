<?php

  //declare variables
  $reg_errors = $edit_errors = [];
  $reg_data = $edit_data = $reg_news = $edit_news = $reg_holid = $edit_holid = [];
  $email = $pass = "";
  $email_unique = $privacy = false;

  //validate all form fields and record all errors
  if($table_name === "holidays") {

    if (isset($_POST['country'])) {
      if (!empty($_POST['country'])) {
        $country = mysqli_real_escape_string($dbconnect, trim($_POST['country']));
        $reg_holid[] = $country;
        if (isset($_POST['orig_country'])) {
          if ($_POST['orig_country'] !== $country) {
            $edit_holid['country'] = $country;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in country please";
      }
    }

    if (isset($_POST['hotel'])) {
      if (!empty($_POST['hotel'])) {
        $hotel = mysqli_real_escape_string($dbconnect, trim($_POST['hotel']));
        $reg_holid[] = $hotel;
        if (isset($_POST['orig_hotel'])) {
          if ($_POST['orig_hotel'] !== $hotel) {
            $edit_holid['hotel'] = $hotel;
          }
        }
      }
    }

    if (isset($_POST['length'])) {
      if (!empty($_POST['length'])) {
        $length = mysqli_real_escape_string($dbconnect, trim($_POST['length']));
        $reg_holid[] = $length;
        if (isset($_POST['orig_length'])) {
          if ($_POST['orig_length'] !== $length) {
            $edit_holid['length'] = $length;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in length please";
      }
    }

    if (isset($_POST['price'])) {
      if (!empty($_POST['price'])) {
        $price = mysqli_real_escape_string($dbconnect, trim($_POST['price']));
        $reg_holid[] = $price;
        if (isset($_POST['orig_price'])) {
          if ($_POST['orig_price'] !== $price) {
            $edit_holid['price'] = $price;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in price please";
      }
    }

    if (isset($_POST['date_from'])) {
      if (!empty($_POST['date_from'])) {
        $date_from = mysqli_real_escape_string($dbconnect, trim($_POST['date_from']));
        $reg_holid[] = $date_from;
        if (isset($_POST['orig_date_from'])) {
          if ($_POST['orig_date_from'] !== $date_from) {
            $edit_holid['date_from'] = $date_from;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in date_from please";
      }
    }

    if (isset($_POST['date_to'])) {
      if (!empty($_POST['date_to'])) {
        $date_to = mysqli_real_escape_string($dbconnect, trim($_POST['date_to']));
        $reg_holid[] = $date_to;
        if (isset($_POST['orig_date_to'])) {
          if ($_POST['orig_date_to'] !== $date_to) {
            $edit_holid['date_to'] = $date_to;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in date_to please";
      }
    }

    if (isset($_FILES['image']) && empty($reg_errors) && empty($edit_errors)) {
      $image_path = "../../../../xxuploads/{$_FILES['image']['name']}";
      if (!file_exists($image_path) && !is_file($image_path)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
          $reg_holid[] = $edit_holid['image'] = mysqli_real_escape_string($dbconnect, $image_path);
          if (isset($_POST['orig_image']) && !empty($_POST['orig_image'])) {
            $orig_image_path = $_POST['orig_image'];
            if ($orig_image_path !== $image_path) {
              if (file_exists($orig_image_path) && is_file($orig_image_path)) {
                unlink($_POST['orig_image']);
              }
            }
          }
        } else {
          $reg_errors[] = $edit_errors[] = "image upload failed! Error: {$_FILES['image']['error']}";
        }
      } else {
        $reg_errors[] = $edit_errors[] = "this file is already in the folder!";
      }
      if (file_exists($_FILES['image']['tmp_name']) && is_file($_FILES['image']['tmp_name'])) {
        unlink($_FILES['image']['tmp_name']);
      }
    }
  }

  elseif($table_name === "users" || $table_name === "newsletter") {

    if (isset($_POST['f_name'])) {
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

    if (isset($_POST['l_name'])) {
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

    if (isset($_POST['password'])) {
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

    if (isset($_POST['confirm_password'])) {
      if (!empty($_POST['confirm_password'])) {
        $conf_pass = mysqli_real_escape_string($dbconnect, $_POST['confirm_password']);
        if ($conf_pass != $pass) {
          $edit_errors[] = $reg_errors[] = "password confirmation DOESN'T match!";
        }
      } else {
        $edit_errors[] = $reg_errors[] = "password confirmation is empty!";
      }
    }

    if (isset($_POST['mobile'])) {
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

    if (!isset($_POST['privacy'])) {
      $reg_errors[] = "You have to agree to the privacy policy!";
    }

    foreach(NEWSLETTER_COLUMNS as $value) {
      if ($value !== "email") {
        $reg_news[] = isset($_POST[$value])? true: false;
        if (isset($_POST["orig_$value"])) {
          if ((isset($_POST[$value]) && $_POST["orig_$value"] === "0")
              || (!isset($_POST[$value]) && $_POST["orig_$value"] === "1")) {
                $edit_news[$value] = isset($_POST[$value])? true: false;
          }
        }
      }
    }
  }


?>
