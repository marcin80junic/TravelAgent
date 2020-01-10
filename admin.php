<?php #admin.php

  //setting up page
  $page_title = "Admin Utility";
  include("templates/header.php");
  echo '<script src="js/admin.js"></script>';
  require("php/mysql_querries.php");

  //function checking was the page scrolled when reloading
  function check_Offset() {
    if(isset($_POST['offset'])) {
      echo $_POST['offset'];
    }
    else echo '0';
  }

  //generic function creating table form
  function create_form($data) {
    foreach($data as $key => $value) {
      if($key != "password") {
        echo '<div class="custom-control custom-checkbox d-inline mr-3">
                <input type="checkbox" class="custom-control-input" id="'.$value.'" name="'.$value.'" ';
        if(isset($_POST["$value"])) {
          echo 'checked="true"';
        }
        echo '><label class="custom-control-label mt-2" for="'.$value.'">'.$key.'</label></div>';
      }
    }
  }

  //generic function collecting admin choices
  function collect_data($data) {
    $which = $headers = [];
    foreach($data as $key => $value) {
      if(isset($_POST[$value])) {
        $which[] = $value;
        $headers[] = $key;
      }
    }
    return array($which, $headers);
  }

  //generic function creating and displaying a table
  function create_table($table_name, $headers=false, $data=false, $result=false) {
    echo '<h3 class="text-center mb-3">'.$table_name.'</h3>';
    if(!$headers) {
      echo '<p class="text-center text-danger">No data selected!</p>';
      return;
    }
    echo '<div class="overflow-auto"><table class="table-bordered table-info mx-auto">
          <thead class="thead-dark"><tr><th>#</th>';
    foreach($headers as $header) {
      if($header != "password") {
        if($header == "email")   echo '<th width="15%">'.$header.'</th>';
        else echo '<th>'.$header.'</th>';
      }
    }
    echo '<th width="10%">actions</th></tr></thead>';
    if($data) {
      $index = 1;
      $length = COUNT($data);
      $table = strchr($table_name, ' ', true);
      echo '<tbody>';
      while($row = mysqli_fetch_array($result)) {
        echo '<tr><td align="right">'.$index++.'</td>';
        for($i = 0; $i < $length; $i++) {
          if($data[$i] != "password") {
            echo '<td align="right">'.$row[$data[$i]].'</td>';
          }
        }
        $key = $row[0];
        echo '<td align="center"><form action="admin.php" method="get">
              <a href="php/admin_edit.php?table='.$table.'&id='.$key.'" class="edit">edit</a>
              <a href="php/admin_remove.php?table='.$table.'&id='.$key.'" class="remove ml-2">
              remove</a></form></td></tr>';
      }
      echo '</tbody></table></div>';
    } else {
      echo '<h2>Internal Error</h2>';
    }
  }

?>

<!--admin interface-->
<div id="interface" class="text-center mb-4">
  <h3>Database Admin Utility</h3>

  <form action="admin.php" method="post" class="mt-4">
    <fieldset class="border border-primary px-4 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Users table</h6>
      </legend>
      <div class="d-flex flex-row">
        <div class="d-flex flex-wrap align-self-center">
          <div class="custom-control custom-checkbox d-inline mr-3">
            <input type="checkbox" class="custom-control-input" id="user_id" name="user_id"
            <?php if(isset($_POST['user_id'])) echo 'checked="true"'; ?>>
            <label class="custom-control-label mt-2" for="user_id">user id</label>
          </div>
          <?php create_form(USER_DATA); ?>
        </div>
        <div class="align-self-end ml-auto d-flex flex-column bg-light p-2">
          <a href="#" class="select_all">select all</a>
          <a href="#" class="clear_all">clear all</a>
          <button type="submit" id="users-form" name="users_table" class="btn btn-info mt-1">show</button>
          <input class="offset" name="offset" type="hidden" value="<?php check_Offset(); ?>">
        </div>
      </div>
    </fieldset>
  </form>

  <form action="admin.php" method="post" class="mt-3">
    <fieldset class="border border-primary px-4 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Newsletter table</h6>
      </legend>
      <div class="d-flex flex-row">
        <div class="d-flex flex-wrap align-self-center">
          <div class="custom-control custom-checkbox d-inline mr-3">
            <input type="checkbox" class="custom-control-input" id="newsletter_email" name="newsletter_email"
            <?php if(isset($_POST['newsletter_email'])) echo 'checked="true"'; ?>>
            <label class="custom-control-label mt-2" for="newsletter_email">email</label>
          </div>
          <?php create_form(NEWSLETTER_DATA); ?>
        </div>
        <div class="align-self-end ml-auto d-flex flex-column bg-light p-2">
          <a href="#" class="select_all">select all</a>
          <a href="#" class="clear_all">clear all</a>
          <button type="submit" id="newsletter-form" name="newsletter_table" class="btn btn-info mt-1">show</button>
          <input class="offset" name="offset" type="hidden" value="<?php check_Offset(); ?>">
        </div>
      </div>
    </fieldset>
  </form>

</div>

<div id="dialog-1" class="text-center pt-3"></div>

<?php

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == "POST") {

    //connect to the database and initiate variables
    require("../../../../xxsecure/dbconnect.php");
    $table_name = "";
    $collected_data = $which_data = $headers = [];
    $result = "";

    //check which table have been requested
    if(isset($_POST['users_table'])) {
      $table_name = "users table";
      if(isset($_POST['user_id'])) {
        $which_data[] = "user_id";
        $headers[] = "user id";
      }
      $collected_data = collect_data(USER_DATA);
    }
    elseif(isset($_POST['newsletter_table'])) {
      $table_name = "newsletter table";
      if(isset($_POST['newsletter_email'])) {
        $which_data[] = "email";
        $headers[] = "email";
      }
      $collected_data = collect_data(NEWSLETTER_DATA);
    }

    $which_data = array_merge($which_data, $collected_data[0]);
    $headers = array_merge($headers, $collected_data[1]);
    if(COUNT($which_data) > 0) {
      switch($table_name) {
        case 'users table':
          $result = users_select_all($dbconnect);
          break;
        case 'newsletter table':
          $result = newsletter_select_all($dbconnect);
          break;
      }
    } else {
      create_table($table_name);
      include("templates/footer.html");
      mysqli_close($dbconnect);
      exit();
    }
    if($result) {
      create_table($table_name, $headers, $which_data, $result);
    } else {
      echo "<h3>System Error</h3><p>".mysqli_error($dbconnect)."</p>";
    }

  }

  include("templates/footer.html");

?>
