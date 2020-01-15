<?php #admin_edit.php

  //closing procedures
  function close_script($dbc) {
    echo '<br><button class="btn btn-info" id="ok" href="../admin.php">Ok</button>';
    mysqli_close($dbc);
    exit();
  }

  //prints out update report
  function report_query($dbc) {
    if (mysqli_affected_rows($dbc) == 1) {
      echo '<p class="pt-3">record have been updated successfully<br></p>';
    }
    elseif (mysqli_affected_rows($dbc) == 0) {
      echo '<p class="pt-3">No records have been updated</p>';
    }
    else {
      echo '<p class="pt-3">Error: '.mysqli_error($dbc).'</p>';
    }
  }

  //
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

  //import db connection and constants
  require("../../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");

  //check if choice was to cancel and quit the script
  if(isset($_POST['no'])) {
    echo '<p>Edit has been cancelled</p>';
    close_script($dbconnect);
  }

  //first extract critical variables
  if(isset($_REQUEST['table']) && isset($_REQUEST['id'])) {
    $table = $_REQUEST['table'];
    $id = $_REQUEST['id'];
  } else {
    echo '<p>this page has been accessed in error</p>';
    close_script($dbconnect);
  }
  //and set up the script
  set_current_data($table);
  $pure_data = ignore_values($current_data);
  if($table === "newsletter") {
    \array_splice($pure_data, 0, 1);
  }
  $column_names = array_keys($pure_data);
  $db_columns = array_values($pure_data);

  //in case of first connection
  if($_SERVER['REQUEST_METHOD'] == 'GET') {

    //on first connection extract the record
    $result = select_one_row_selected($dbconnect, $table, $db_columns, $id);
    if(mysqli_num_rows($result) == 1){
      $pure_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $columns = array_keys($pure_row);
    } else {
      echo '<h2>'.mysqli_error($dbconnect).'</h2>';
      close_script($dbconnect);
    }
    //extract some original values to be kept in hidden inputs
    if($table == "users") {
      $orig_email = $pure_row['email'];
      $check_newsletter = users_is_newsletter($dbconnect, $orig_email);
      $orig_newsletter = (mysqli_num_rows($check_newsletter) == 1)? true: false;
      $newsletter = $orig_newsletter;
    }
  }

  //on submit
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //extracting critical variables
    if($table === "users") {
      $orig_email = $_POST['orig_email'];
      $email = $_POST['email'];
      $newsletter = isset($_POST['edit_newsletter'])? true: false;
      $orig_newsletter = $_POST['orig_newsletter'];
    }

    //using form validation to extract and check submitted data
    //if errors discovered print them out
    require("form_validation.php");
    if (!empty($edit_errors)) {
      foreach($edit_errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }

    //if no errors proceed with update
    else {
      if ($table == "users") {
        \array_splice($db_columns, 3, 1);
        $query_result = update_one_row($dbconnect, $table, $id, $db_columns, $reg_data);
        report_query($dbconnect);
        if (($orig_newsletter == "" && $newsletter == "on") ||
            ($orig_newsletter == "1" && $newsletter == "")) {
          if ($newsletter == "on") {
            if (newsletter_is_unique_email($dbconnect, $email)) {
              $length = count(NEWSLETTER_COLUMNS);
              for($i=0; $i<$length; $i++) {
                $news_data[] = 1;
              }
              $signed_in_result = newsletter_insert($dbconnect, $email, $news_data);
              if ($signed_in_result) {
                echo '<p>successfully signed up for a newsletter</p>';
              } else {
                echo '<p>MySql Error: '.mysqli_error($dbconnect).'</p>';
              }
            }
          }
          else {
            $signed_out_result = remove_one_row($dbconnect, "newsletter", $_POST['orig_email']);
            if ($signed_out_result) {
              echo '<p>successfully signed out of a newsletter</p>';
            } else {
              echo '<p>MySql Error: '.mysqli_error($dbconnect).'</p>';
            }
          }
        }
      }
      elseif($table == "newsletter") {
        $id = "'".$id."'";
        $query_result = update_one_row($dbconnect, $table, $id, $db_columns, $news);
        report_query($dbconnect);
      }
      close_script($dbconnect);
    }

  }

?>

<!-- Display the main form for updating records -->
<div>
  <form id="decision" action="admin_edit.php" method="post">
    <p>Edit current record id: <?php echo $id; ?></p>
    <table width="90%" class="admin-edit-table mx-auto">
      <thead>
        <tr><th></th><th width="50%"></th></tr>
      </thead>

    <?php

      $length = COUNT($column_names);
      for ($i=0; $i<$length; $i++) {

        $column_name = $column_names[$i];
        $db_column = $db_columns[$i];

        if($column_name == "password") {
          $value = "";
        } else {
          if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $value = $pure_row[$columns[$i]];
          } else {
              $value = $_POST[$db_columns[$i]];
          }
        }
        if($column_name == "email" || $column_name == "password") {
          $input_type = $column_name;
        } else {
          $input_type = $current_type;
        }

        echo '<tr>
                <td align="right">
                  <label class="my-auto" for="edit-'.$db_column.'">'.$column_name.'</label>
                </td>
                <td align="left">
                  <input type="'.$input_type.'"name="'.$db_column.'" id="edit-'.$db_column.'"';
                  if($input_type != "checkbox") echo ' value="'.$value.'"';
                  if($input_type == "checkbox" && $value == 1) {
                    echo ' checked="true"';
                  }
        echo '></td></tr>';
        if($column_name == "password") {
          echo '<tr><td align="right"><label class="my-auto" for="confirm_password">
               confirm password</label></td><td align="left"><input type="'.$input_type.'"
               name="confirm_password" id="confirm_password" value="'.$value.'"></td></tr>';
        }
      }
      if($table == "users") {
        echo '<input type="hidden" name="orig_email" value="'.$orig_email.'">';
        echo '<input type="hidden" name="orig_newsletter" value="'.$orig_newsletter.'">';
        echo '<tr><td></td></tr><tr>
                <td align="right">
                  <label class="my-auto" for="edit_newsletter">Newsletter?</label>
                </td>
                <td align="left">
                  <input type="checkbox" name="edit_newsletter" id="edit_newsletter" ';
        if ($newsletter == 1 || $newsletter == "on" || $newsletter == "true") {
                      echo 'checked="true"';
        echo '></td></tr>';
        }
      }
      echo "</tbody></table><br>";

  ?>

    <input type="hidden" name="table" value="<?php echo $table; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <?php
      if(!empty($edit_errors)) {
        echo '<input id="err" type="hidden" name="errors" value="error">';
      }
    ?>
    <button type="submit" name="yes" value="yes">Update</button>
    <a href="php/admin_edit.php?no=no">
      <button id="cancel" name="no" value="no" class="ml-2">Cancel</button>
    </a>
  </form>
</div>
