<?php

  //general use functions
  function count_num_of_rows($dbc, $table) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "SELECT COUNT($pk_name) FROM $table";
    return @mysqli_query($dbc, $query);
  }

  function remove_one_row($dbc, $table, $id) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "DELETE FROM $table WHERE $pk_name = '$id' LIMIT 1";
    return @mysqli_query($dbc, $query);
  }

  function select_all_rows($dbc, $table) {
    $query = "SELECT * FROM $table";
    return @mysqli_query($dbc, $query);
  }

  function select_one_row($dbc, $table, $id) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "SELECT * FROM $table WHERE $pk_name = '$id'";
    return @mysqli_query($dbc, $query);
  }

  function select_num_rows_sorted($dbc, $table, $sort, $start, $display) {
    $query = "SELECT * FROM $table ORDER BY $sort LIMIT $start, $display";
    return @mysqli_query($dbc, $query);
  }

  function update_one_row($dbc, $table, $id, $data) {
    if (empty($data)) return;
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "UPDATE $table SET ";
    foreach($data as $col => $val) {
      if ($col === "date_from" || $col === "date_to") {
        $query .= "$col=STR_TO_DATE('$val', '%d-%m-%Y'), ";
        continue;
      }
      $query .= "$col='$val', ";
    }
    $query = substr($query, 0, strlen($query)-2);
    $query .= " WHERE $pk_name=$id";
    return @mysqli_query($dbc, $query);
  }

  function is_email_unique($dbc, $table, $email) {
    $query = "SELECT email FROM $table WHERE email='$email'";
    return _is_unique($dbc, $query);
  }

  //users table specific functions
  function users_insert($dbc, $data) {
    $query = "INSERT INTO users(f_name, l_name, email, password, mobile, date_registered)
              VALUES('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', NOW())";
    return @mysqli_query($dbc, $query);
  }

  function users_is_newsletter($dbc, $email) {
    $query = "SELECT users.email FROM users INNER JOIN newsletter ON
    users.email = newsletter.email WHERE users.email = '$email'";
    return @mysqli_query($dbc, $query);
  }

  //holidays table functions
  function holidays_insert($dbc, $data) {
    $query = "INSERT INTO holidays(country, hotel, length, price, date_from, date_to";
    if (count($data) > 6) {
      $query .= ", image";
    }
    $query .= ") VALUES('$data[0]', '$data[1]', '$data[2]', '$data[3]',
              STR_TO_DATE('$data[4]', '%d-%m-%Y'), STR_TO_DATE('$data[5]', '%d-%m-%Y')";
    if (count($data) > 6) {
      $query .= ", '$data[6]'";
    }
    $query .= ")";
    return @mysqli_query($dbc, $query);
  }

  //newsletter table specific functions

  function newsletter_sign_up($dbc, $email) {
    if (is_email_unique($dbc, "newsletter", $email)) {
      $news_data = [];
      $length = count(NEWSLETTER_COLUMNS);
      for($i=0; $i<$length; $i++) {
        $news_data[] = 1;
      }
      return newsletter_insert($dbc, $email, $news_data);
    } else {
      return false;
    }
  }

  function newsletter_insert($dbc, $email, $data) {
    $query = "INSERT INTO newsletter VALUES('$email', '$data[0]', '$data[1]', '$data[2]',
              '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]',
               '$data[9]', '$data[10]')";
    return @mysqli_query($dbc, $query);
  }

  //helper functions for internal use
  function _is_unique($dbc, $query) {
    $response = @mysqli_query($dbc, $query);
    $num = mysqli_num_rows($response);
    if($num == 0) {
      return true;
    }
    return false;
  }

  function _get_pk_column_name($dbc, $table) {
    $pk_query = "SELECT column_name FROM information_schema.key_column_usage WHERE TABLE_NAME = '$table'";
    $pk_result = mysqli_query($dbc, $pk_query);
    if($pk_result) {
      $pk_name = mysqli_fetch_array($pk_result)[0];
      return $pk_name;
    }
  }


  //initialize constants
  define("USERS_COLUMNS", array(
    "id" => "id",
    "first name" => "f_name",
    "last name" => "l_name",
    "email"  => "email",
    "password" => "password",
    "mobile number" => "mobile",
    "date registered" => "date_registered",
    "last login" => "last_login"
  ));
  define("USERS_FORM_TYPES", array(
    "id" => "text",
    "first name" => "text",
    "last name" => "text",
    "email" => "email",
    "password" => "password",
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


?>
