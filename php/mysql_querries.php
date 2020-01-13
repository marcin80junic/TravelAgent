<?php

  //general use functions
  function count_num_of_rows($dbc, $table) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "SELECT COUNT($pk_name) FROM $table";
    return @mysqli_query($dbc, $query);
  }

  function select_one_row($dbc, $table, $id) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "SELECT * FROM $table WHERE $pk_name = '$id'";
    return @mysqli_query($dbc, $query);
  }

  function select_all_rows($dbc, $table) {
    $query = "SELECT * FROM $table";
    return @mysqli_query($dbc, $query);
  }

  function select_num_rows_sorted($dbc, $table, $sort, $start, $display) {
    $query = "SELECT * FROM $table ORDER BY $sort LIMIT $start, $display";
    return @mysqli_query($dbc, $query);
  }

  function update_one_row($dbc, $table, $id, $columns, $data) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "UPDATE $table SET ";
    for($i=0; $i<COUNT($columns); $i++) {
      if($i == (COUNT($data)-1)) {
        $query .= "$columns[$i]='$data[$i]' ";
        break;
      }
      $query .= "$columns[$i]='$data[$i]', ";
    }
     $query .= "WHERE $pk_name='$id'";
     return @mysqli_query($dbc, $query);
  }

  function remove_one_row($dbc, $table, $id) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = "DELETE FROM $table WHERE $pk_name = '$id' LIMIT 1";
    return @mysqli_query($dbc, $query);
  }

  //users table specific functions
  function users_insert($dbc, $data) {
    $query = "INSERT INTO users(f_name, l_name, email, password, mobile, date_registered)
              VALUES('$data[0]', '$data[1]', '$data[2]', SHA2('$data[3]', 512), '$data[4]', NOW())";
    return @mysqli_query($dbc, $query);
  }

  function users_is_newsletter($dbc, $email) {
    $query = "SELECT users.email FROM users INNER JOIN newsletter ON
    users.email = newsletter.email WHERE users.email = '$email'";
    return @mysqli_query($dbc, $query);
  }

/*
  function users_select($dbc, $data) {
    $query = 'SELECT ';
    $length = COUNT($data);
    for($i = 0; $i < $length; $i++) {
      if($i == $length - 1) {
        $query .= "$data[$i] ";
      } else {
        $query .= "$data[$i], ";
      }
    }
    $query .= "FROM users";
    return @mysqli_query($dbc, $query);
  }
*/

  function users_is_unique_email($dbc, $email) {
    $query = "SELECT email FROM users WHERE email='$email'";
    return _is_unique($dbc, $query);
  }

  //newsletter table specific functions
  function newsletter_insert($dbc, $email, $data) {
    $query = "INSERT INTO newsletter VALUES('$email', '$data[0]', '$data[1]', '$data[2]',
              '$data[3]', '$data[4]', '$data[5]', '$data[6]', '$data[7]', '$data[8]',
               '$data[9]', '$data[10]')";
    return @mysqli_query($dbc, $query);
  }

  function newsletter_is_unique_email ($dbc, $email) {
    $query = "SELECT email FROM newsletter WHERE email='$email'";
    return _is_unique($dbc, $query);
  }

  //helper functions for internal use
  function _is_unique($dbc, $query) {
    $response = mysqli_query($dbc, $query);
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


  //initiate variables used in register form

  define("USER_DATA", array(
    "user id" => "user_id",
    "first name" => "f_name",
    "last name" => "l_name",
    "email"  => "email",
    "password" => "password",
    "mobile number" => "mobile",
    "date registered" => "date_registered",
    "last login" => "last_login")
  );

  define("HOLIDAY_TYPES", array(
    "summer holidays" => "summer_hol",
    "city breaks" => "city_break",
    "mountains" => "mountains",
    "cruises" => "cruise",
    "tour holidays" => "tour_hol")
  );

  define("HOLIDAY_EXTRAS", array(
    "next to beach" => "beach",
    "swimming pool" => "swimming_pool",
    "aquapark" => "aquapark",
    "surfing" => "surfing",
    "skiing" => "skiing",
    "gym/fitness facilities" => "gym_fitness")
  );

  define("NEWSLETTER_DATA", array_merge(array("email" => "email"), HOLIDAY_TYPES, HOLIDAY_EXTRAS));

  define("EDIT_IGNORE", array("date registered", "last login"));

  $current_type;
  $current_data;

  function set_current_data($table_name) {
    switch ($table_name) {
      case "users":
        $GLOBALS['current_data'] = USER_DATA;
        $GLOBALS['current_type'] = "text";
        break;
      case "newsletter":
        $GLOBALS['current_data'] = NEWSLETTER_DATA;
        $GLOBALS['current_type'] = "checkbox";
        break;
    }
  }

?>
