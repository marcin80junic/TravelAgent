<?php

  //declare variables
  $reg_errors = $edit_errors = [];
  $reg_data = $edit_data = $reg_news = $edit_news = $reg_holid = $edit_holid = [];
  $username = $email = $pass = "";
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
      $image_path = "../../../../xxuploads/{$_FILES['image']['name']}"; //create destination path..
      if (!file_exists($image_path) && !is_file($image_path)) { //..check if it exists already
        $file_info = finfo_open(FILEINFO_MIME_TYPE);  //if not, create Fileinfo resource
        if ( stripos(finfo_file($file_info, $_FILES['image']['tmp_name']), "image") === 0) { //check if file is an image
          finfo_close($file_info);  //if yes, close the resource
          if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) { //copy the file to its destination
            $reg_holid[] = $edit_holid['image'] = mysqli_real_escape_string($dbconnect, $image_path); //store its path
            if (isset($_POST['orig_image']) && !empty($_POST['orig_image'])) {  //check if there was a previous image
              $orig_image_path = $_POST['orig_image']; //if yes, assign it to variable
              if ($orig_image_path !== $image_path) { //check if it got the same name as the new one
                if (file_exists($orig_image_path) && is_file($orig_image_path)) { //if not, check if the old one exists..
                  unlink($_POST['orig_image']); //..on disk and remove it
                }
              }
            }
          } else {
            $reg_errors[] = $edit_errors[] = "image upload failed! Error: {$_FILES['image']['error']}";
          }
        } else {
          $reg_errors[] = $edit_errors[] = "this file is not an image!";
        }
      } else {
        $reg_errors[] = $edit_errors[] = "this file is already in the folder!";
      }
      if (file_exists($_FILES['image']['tmp_name']) && is_file($_FILES['image']['tmp_name'])) {
        unlink($_FILES['image']['tmp_name']); //remove temporary file if it still exists
      }
    }
  }

  elseif($table_name === "users" || $table_name === "newsletter") {

    //create activation code
    $reg_data[] = md5(uniqid(rand(), true));

    if (isset($_POST['active'], $_POST['orig_active'])) {
      if ($_POST['orig_active'] == false) {
        if ($_POST['active'] == true) {
          $edit_data['active'] = NULL;
        }
      } else {
        if ($_POST['active'] == false) {
          $edit_errors[] = "cannot unactivate users!";
        }
      }
    }

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
        $email_unique = is_value_unique($dbconnect, $table_name, 'email', $email);
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

    if (isset($_POST['user_name'])) {
      if (!empty($_POST['user_name'])) {
        $username = mysqli_real_escape_string($dbconnect, trim($_POST['user_name']));
        $user_unique = is_value_unique($dbconnect, $table_name, 'user_name', $username);
        if (!$user_unique) {
          $reg_errors[] = "username is already in use";
        } else {
          $reg_data[] = $username;
        }
        if (isset($_POST['orig_user_name']) && $_POST['orig_user_name'] !== $username) {
          if (!$user_unique) {
            $edit_errors[] = "username is already in use";
          } else {
            $edit_data['user_name'] = $username;
          }
        }
      } else {
        $reg_errors[] = $edit_errors[] = "enter username please";
      }
    }

    if ((!isset($id) || !empty($_POST['password'])) && isset($_POST['password'])) {
      if(!empty($_POST['password'])) {
        $pass = $_POST['password'];
        $reg_data[] = password_hash($pass, PASSWORD_DEFAULT);
        if (isset($_POST['orig_password'])) {
          if ($_POST['orig_password'] !== $pass) {
            $edit_data['password'] = password_hash($pass, PASSWORD_DEFAULT);
          }
        }
        if (strlen($pass) < 8) {
          $edit_errors[] = $reg_errors[] = "password must be AT LEAST 8 characters long";
        }
      } else {
        $reg_errors[] = $edit_errors[] = "fill in password field please!";
      }
    }

    if ((!isset($id) || !empty($_POST['password']) || !empty($_POST['confirm_password']))
          && isset($_POST['confirm_password'])) {
      if (!empty($_POST['confirm_password'])) {
        $conf_pass = $_POST['confirm_password'];
        if ($conf_pass != $pass) {
          $edit_errors[] = $reg_errors[] = "password confirmation DOESN'T match!";
        }
      } else {
        $edit_errors[] = $reg_errors[] = "password confirmation is empty!";
      }
    }

    if (!isset($_POST['privacy'])) {
      $reg_errors[] = "You have to agree to the privacy policy!";
    }

    foreach(NEWSLETTER_COLUMNS as $value) {
      if ($value !== "email") {
        $reg_news[] = isset($_POST[$value])? true: false;
        if (isset($_POST["orig_$value"])) {
          if (($_POST[$value] == true && $_POST["orig_$value"] == false)
              || ($_POST[$value] == false && $_POST["orig_$value"] == true)) {
                $edit_news[$value] = $_POST[$value];
          }
        }
      }
    }
  }


?>
