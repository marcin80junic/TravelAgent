<?php

  //initialize constants

  define("LIVE", false);
  define("EMAIL", "postmaster@localhost.com");
  define("BASE_URL", "http://localhost/MyProjects/TravelAgent/");
  define("MYSQL", "c:\\xampp\\xxsecure\\dbconnect.inc.php");

  //MySQL tables and related data

  define("USERS_COLUMNS", array(
    "id" => "id",
    "active" => "active",
    "user level" => "user_level",
    "first name" => "f_name",
    "last name" => "l_name",
    "email" => "email",
    "mobile number" => "mobile",
    "username" => "user_name",
    "password" => "password",
    "date registered" => "date_registered",
    "last login" => "last_login"
  ));

  define("USERS_FORM_TYPES", array(
    "id" => "text",
    "active" => "checkbox",
    "user level" => "number",
    "username" => "text",
    "password" => "password",
    "first name" => "text",
    "last name" => "text",
    "email" => "email",
    "mobile number" => "text"
  ));

  define("HOLIDAYS_COLUMNS", array(
    "id" => "id",
    "country" => "country",
    "hotel" => "hotel",
    "days" => "length",
    "price" => "price",
    "available from" => "date_from",
    "available until" => "date_to",
    "image" => "image"
  ));

  define("HOLIDAYS_FORM_TYPES", array(
    "id" => "text",
    "country" => "text",
    "hotel" => "text",
    "days" => "number",
    "price" => "text",
    "available from" => "text",
    "available until" => "text",
    "image" => "file"
  ));

  define("HOLIDAY_TYPES", array(
    "summer holidays" => "summer_hol",
    "city breaks" => "city_break",
    "mountains" => "mountains",
    "cruises" => "cruise",
    "tour holidays" => "tour_hol"
  ));

  define("HOLIDAY_EXTRAS", array(
    "next to beach" => "beach",
    "swimming pool" => "swimming_pool",
    "aquapark" => "aquapark",
    "surfing" => "surfing",
    "skiing" => "skiing",
    "gym/fitness facilities" => "gym_fitness"
  ));

  define("NEWSLETTER_COLUMNS", array_merge(array("email" => "email"), HOLIDAY_TYPES, HOLIDAY_EXTRAS));

  define("NEWSLETTER_FORM_TYPES", array(
    "email" => "email",
    "summer holidays" => "checkbox",
    "city breaks" => "checkbox",
    "mountains" => "checkbox",
    "cruises" => "checkbox",
    "tour holidays" => "checkbox",
    "next to beach" => "checkbox",
    "swimming pool" => "checkbox",
    "aquapark" => "checkbox",
    "surfing" => "checkbox",
    "skiing" => "checkbox",
    "gym/fitness facilities" => "checkbox"
  ));

  //create associative array with table names and corresponding constant arrays
  define("TABLES", array(
    "users" => USERS_COLUMNS,
    "holidays" => HOLIDAYS_COLUMNS,
    "newsletter" => NEWSLETTER_COLUMNS)
  );

  //define values which shouldn't be displayed by editing or registering forms
  define("EDIT_IGNORE", array("id", "date registered", "last login", "image"));
  define("REGISTRATION_IGNORE", array("id", "active", "user level", "date registered", "last login"));

  //function returning array cleared from ignored values
  function ignore_values($array) {
    $pure = [];
    foreach($array as $key => $val) {
      foreach(EDIT_IGNORE as $ign) {
        if($key === $ign) {
          continue 2;
        }
      }
      $pure[$key] = $val;
    }
    return $pure;
  }

  //set up current_data variable for use by other scripts,
  // also choose type of html inputs according to the current_data
  $current_data;
  $current_type;

  function set_current_data($table_name) {
    switch ($table_name) {
      case "users":
        $GLOBALS['current_data'] = USERS_COLUMNS;
        $GLOBALS['current_type'] = USERS_FORM_TYPES;
        break;
      case "newsletter":
        $GLOBALS['current_data'] = NEWSLETTER_COLUMNS;
        $GLOBALS['current_type'] = NEWSLETTER_FORM_TYPES;
        break;
      case "holidays":
        $GLOBALS['current_data'] = HOLIDAYS_COLUMNS;
        $GLOBALS['current_type'] = HOLIDAYS_FORM_TYPES;
        break;
    }
  }

  //error management

  function error_handler($e_number, $e_message, $e_file, $e_line, $e_vars) {

    $message = "An error occurred in a script: '$e_file' on line: ";
    $message .= "'<b>$e_line</b>' \n message: <b>$e_message</b> \n";
    $message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n";

    if (!LIVE) {
      echo '<div class="danger">' . nl2br($message);
      echo '<pre>' . print_r($e_vars, 1) . "\n";
      debug_print_backtrace();
      echo '</pre></div>';
    }
    else {
      $body = $message . "\n" . print_r($e_vars, 1);
      mail(EMAIL, 'Travel Agent Error', $body, 'From: ' . EMAIL);

      if ($_number != E_NOTICE) {
        echo '<div class="danger">A system error ocurred.';
        echo ' We apologize for the inconvenience.</div><br>';
      }
    }
  }
  set_error_handler('error_handler');

  //function determines the absolute URL and redirects the user
  function redirect_user($page='index.php') {
    $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    $url = rtrim($url, '/\\');
    $url .= '/' . $page;
    header("Location: $url");
    exit();
  }

?>
