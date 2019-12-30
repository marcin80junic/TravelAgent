<?php

  function users_insert($dbc, $data) {
    $query = "INSERT INTO users(f_name, l_name, email, password, mobile, date_registered)
              VALUES('$data[0]', '$data[1]', '$data[2]', SHA2('$data[3]', 512), '$data[4]', NOW())";
    return @mysqli_query($dbc, $query);
  }

  function users_select_all($dbc) {
    $query = "SELECT * FROM users";
    return @mysqli_query($dbc, $query);
  }

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

  function users_is_unique_email($dbc, $email) {
    $query = "SELECT email FROM users WHERE email='$email'";
    return _is_unique($dbc, $query);
  }

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

  function _is_unique($dbc, $query) {
    $response = mysqli_query($dbc, $query);
    $num = mysqli_num_rows($response);
    if($num == 0) {
      return true;
    }
    return false;
  }

  //initiate variables used in register form
  $types = array(
    array("summer_hol", "summer holidays"),
    array("city_break", "city breaks"),
    array("mountains", "mountains"),
    array("cruise", "cruises"),
    array("tour_hol", "tour holidays")
  );
  $extras = array(
    array("beach", "next to beach"),
    array("swimming_pool", "swimming pool"),
    array("aquapark", "aquapark"),
    array("surfing", "surfing"),
    array("skiing", "skiing"),
    array("gym_fitness", "gym/fitness facilities")
  );


?>
