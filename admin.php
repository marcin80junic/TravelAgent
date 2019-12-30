<?php

  //setting up page
  $page_title = "Admin Utility";
  include("templates/header.php");
  require("php/mysql_querries.php");

  //generic function creating and displaying table
  function create_table($table_name, $headers=false, $data=false, $result=false) {
    echo '<h3 class="text-center mb-3">'.$table_name.'</h3>';
    if(!$headers) {
      echo '<p class="text-center text-danger">No data selected!</p>';
      return;
    }
    echo '<table class="table table-bordered table-info"><thead class="thead-dark"><tr><th>#</th>';
    foreach($headers as $header) {
      echo "<th>$header</th>";
    }
    echo '<th>actions</th></tr></thead>';
    if($data) {
      $index = 1;
      $length = COUNT($data);
      echo '<tbody>';
      while($row = mysqli_fetch_array($result)) {
        echo '<tr><td>'.$index++.'</td>';
        for($i = 0; $i < $length; $i++) {
          echo '<td>'.$row["$data[$i]"].'</td>';
        }
        $key = $row[0];
        echo '<td><a href="php/edit_user.php?id='.$key.'">edit</a>
              <a href="php/remove_user.php?id='.$key.'" class="ml-2">remove</a></td></tr>';
      }
      echo '</tbody></table>';
    } else {
      echo '<h2>Internal Error</h2>';
    }
  }

  function create_newsletter_form($data) {
    foreach($data as $array) {
      $value = $array[0];
      $name = $array[1];
      echo '<div class="custom-control custom-checkbox d-inline ml-3">
              <input type="checkbox" class="custom-control-input" id="'.$value.'" name="'.$value.'" ';
      if(isset($_POST["$value"])) echo 'checked="true"';
      echo '><label class="custom-control-label mt-2" for="'.$value.'">'.$name.'</label></div>';
    }
  }

  ?>


<div class="text-center mb-4">
  <h3>Database Admin Utility</h3>

  <form action="admin.php" method="post" class="mt-5">
    <fieldset class="border border-primary px-3 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Users table</h6>
      </legend>
      <div class="custom-control custom-checkbox d-inline">
        <input type="checkbox" class="custom-control-input" id="user_id" name="user_id"
        <?php if(isset($_POST['user_id'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="user_id">user id</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="first_name" name="first_name"
        <?php if(isset($_POST['first_name'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="first_name">first name</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="last_name" name="last_name"
        <?php if(isset($_POST['last_name'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="last_name">last name</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="email" name="email"
        <?php if(isset($_POST['email'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="email">email</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="mobile" name="mobile"
        <?php if(isset($_POST['mobile'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="mobile">mobile</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="date_registered" name="date_registered"
        <?php if(isset($_POST['date_registered'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="date_registered">date registered</label>
      </div>
      <div class="custom-control custom-checkbox d-inline ml-3">
        <input type="checkbox" class="custom-control-input" id="last_login" name="last_login"
        <?php if(isset($_POST['last_login'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="last_login">last login</label>
      </div>
      <button type="submit" name="users_table" class="btn btn-info float-right">show</button>
    </fieldset>
  </form>
  <form action="admin.php" method="post" class="mt-3">
    <fieldset class="border border-primary px-3 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Newsletter table</h6>
      </legend>
      <div class="custom-control custom-checkbox d-inline">
        <input type="checkbox" class="custom-control-input" id="n_email" name="n_email"
        <?php if(isset($_POST['n_email'])) echo 'checked="true"'; ?>>
        <label class="custom-control-label mt-2" for="n_email">email</label>
      </div>
      <?php create_newsletter_form(array_merge($types, $extras)); ?>
      <button type="submit" name="users_table" class="btn btn-info float-right mt-3">show</button>
    </fieldset>
  </form>
</div>


<?php

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == "POST") {

    require("../../../../xxsecure/dbconnect.php");
    $table_name = "";
    $data = $headers = [];
    $result = "";

    if(isset($_POST['users_table'])) {

      $table_name = "users table";

      if(isset($_POST['user_id'])) {
        $data[] = 'user_id';
        $headers[] = "user id";
      }
      if(isset($_POST['first_name'])) {
        $data[] = 'f_name';
        $headers[] = "first name";
      }
      if(isset($_POST['last_name'])) {
        $data[] = 'l_name';
        $headers[] = "last name";
      }
      if(isset($_POST['email'])) {
        $data[] = 'email';
        $headers[] = "email";
      }
      if(isset($_POST['mobile'])) {
        $data[] = 'mobile';
        $headers[] = "mobile";
      }
      if(isset($_POST['date_registered'])) {
        $data[] = 'date_registered';
        $headers[] = "registration date";
      }
      if(isset($_POST['last_login'])) {
        $data[] = 'last_login';
        $headers[] = "last login";
      }
      if(COUNT($data) > 0) {
        $result = users_select_all($dbconnect);
      } else {
        create_table($table_name);
        include("templates/footer.html");
        exit();
      }
    }

    if($result) {
      create_table($table_name, $headers, $data, $result);
    } else {
      echo "<h3>System Error</h3><p>".mysqli_error($dbconnect)."</p>";
    }

  }

  include("templates/footer.html");

?>
