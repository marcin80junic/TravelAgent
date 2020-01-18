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

  function select_one_row_selected($dbc, $table, $data, $id) {
    $pk_name = _get_pk_column_name($dbc, $table);
    $query = 'SELECT ';
    $length = COUNT($data);
    for($i = 0; $i < $length; $i++) {
      if($i == $length - 1) {
        $query .= "$data[$i] ";
      } else {
        $query .= "$data[$i], ";
      }
    }
    $query .= "FROM $table WHERE $pk_name='$id'";
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
    $length = COUNT($columns);
    for($i=0; $i<$length; $i++) {
      if ($columns[$i] === "password") {
        $query .= "$columns[$i]=SHA2('$data[$i]', 512), ";
        continue;
      }
      if($i == ($length-1)) {
        $query .= "$columns[$i]=$data[$i] ";
        break;
      }
      $query .= "$columns[$i]=$data[$i], ";
    }
     $query .= "WHERE $pk_name=$id";
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

  //initialize constants
  define("USER_COLUMNS", array(
    "id" => "id",
    "first name" => "f_name",
    "last name" => "l_name",
    "email"  => "email",
    "password" => "password",
    "mobile number" => "mobile",
    "date registered" => "date_registered",
    "last login" => "last_login")
  );
  define("HOLIDAYS_COLUMNS", array(
    "id" => "id",
    "country" => "country",
    "image path" => "image",
    "hotel" => "hotel",
    "days" => "length",
    "price" => "price",
    "available from" => "date_from",
    "available until" => "date_to")
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
  define("NEWSLETTER_COLUMNS", array_merge(array("email" => "email"), HOLIDAY_TYPES, HOLIDAY_EXTRAS));

  //create associative array with table names and corresponding constant arrays
  define("TABLES", array(
    "users" => USER_COLUMNS,
    "holidays" => HOLIDAYS_COLUMNS,
    "newsletter" => NEWSLETTER_COLUMNS)
  );

  //define values which shouldn't be displayed by editing or registering forms
  define("EDIT_IGNORE", array("id", "date registered", "last login", "image path"));

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
        $GLOBALS['current_data'] = USER_COLUMNS;
        $GLOBALS['current_type'] = "text";
        break;
      case "newsletter":
        $GLOBALS['current_data'] = NEWSLETTER_COLUMNS;
        $GLOBALS['current_type'] = "checkbox";
        break;
      case "holidays":
        $GLOBALS['current_data'] = HOLIDAYS_COLUMNS;
        $GLOBALS['current_type'] = "text";
        break;
    }
  }

  function create_table_form($table, $cols, $type, $orig_data=false) {

    function get_value($key, $orig_data) {
      if($_SERVER['REQUEST_METHOD'] === 'GET' && $orig_data !== false && isset($orig_data[$key])) {
        return $orig_data[$key];
      }
      if (isset($_POST[$key]) && !empty($_POST[$key])) {
        return trim($_POST[$key]);
      }
      else return "";
    }

    echo '<table width="90%" class="form-table">
            <thead>
              <tr><th></th><th width="50%"></th></tr>
            </thead>
            <tbody>';

    foreach($cols as $desc => $column) {
      $inp_type = $type;
      if ($desc === "email" && $table === "newsletter") {
        $inp_type = "email";
      }
      if ($desc === "password") {
        $inp_type = "password";
      }
      echo '<tr>
              <td class="left">
                <label for="'.$column.'">'.$desc.'</label>
              </td>
              <td class="right">
                <input type="'.$inp_type.'" name="'.$column.'"
                  value="'.get_value($column, $orig_data).'" id="'.$column.'" ';
                  if ($inp_type != "checkbox") {
                    echo ' value="'.get_value($column, $orig_data).'"';
                  }
                  if ($inp_type == "checkbox" && get_value($column, $orig_data) == 1) {
                    echo ' checked="true"';
                  }
      echo        '></td></tr>';
      if ($inp_type === "password") {
        echo '<tr>
                <td class="left">
                  <label for="confirm-'.$column.'">confirm '.$desc.'</label>
                </td>
                <td class="right">
                  <input type="'.$inp_type.'" name="confirm_'.$column.'"
                    value="'.get_value("confirm_".$column, $orig_data).'" id="confirm-'.$column.'">
                </td>
              </tr>';
      }
    }
    if ($table === "users") {
      echo '<tr>
              <td class="left">
                <label for="newsletter">newsletter?</label>
              </td>
              <td class="right">
                <input type="checkbox" name="newsletter" id="newsletter" ';
                if (get_value("newsletter", $orig_data) == 1 || get_value("newsletter", $orig_data) == "on"
                    || get_value("newsletter", $orig_data) == "true") {
                  echo 'checked="true"';
                }
      echo  '></td>
            </tr>';
      echo '<input type="hidden" name="orig_email" value="'.get_value("orig_email", $orig_data).'">';
      echo '<input type="hidden" name="orig_newsletter" value="'.get_value("orig_newsletter", $orig_data).'">';
    }
    if ($table === "holidays") {
      echo '<tr>
              <td class="left">
                <input type="hidden" name="MAX_FILE_SIZE" VALUE="30000">
                <label for="upload">Choose image </label>
              </td>
              <td class="right">
                <input type="file" class="border border-danger" name="upload"
                  value="'.get_value($column, $orig_data).'" id="upload">
              </td>
            </tr>';
    }
    echo '<input type="hidden" name="table" value="'.$table.'">';
    echo '<input type="hidden" name="id" value="'.get_value("id", $orig_data).'">';
    echo '</tbody></table>';

  }


?>
