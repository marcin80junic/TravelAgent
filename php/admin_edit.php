<?php #admin_edit.php

  //closing procedures
  function close_script($dbc) {
    echo '<br><button class="btn btn-info" id="ok" href="../admin.php">Ok</button>';
    mysqli_close($dbc);
    exit();
  }

  //prints out reports of updates
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

  //extracting critical variables connecting to the database
  if(isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
  }
  elseif(isset($_POST['table']) && isset($_POST['id'])) {
    $table = $_POST['table'];
    $id = $_POST['id'];
  }
  else {
    echo '<p>this page has been accessed in error</p>';
    exit();
  }
  require("../../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");
  set_current_data($table);
  $column_names = array_keys($current_data);
  $db_columns = array_values($current_data);

  //on first connection extracting original values
  if($_SERVER['REQUEST_METHOD'] == 'GET') {

    $result = select_one_row($dbconnect, $table, $id);
    if(mysqli_num_rows($result) == 1){
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $columns = array_keys($row);
    } else {
      echo '<h2>'.mysqli_error($dbconnect).'</h2>';
      close_script($dbconnect);
    }
  }

  //on submit
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //using form validation to extract and check submitted data
    //if errors discovered print them and exit the script
    require("form_validation.php");
    if (!empty($edit_errors)) {
      foreach($edit_errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
  //    http_response_code(205);
      close_script($dbconnect);
    }

    //if no errors and choice was "update" proceed with update
    if (isset($_POST['yes'])) {

      if ($table == "users") {

        \array_splice($db_columns, 3, 1);
        $query_result = update_one_row($dbconnect, $table, $id, $db_columns, $reg_data);
        report_query($dbconnect);
        if (($_POST['orig_newsletter'] == "" && $newsletter == "true") ||
            ($_POST['orig_newsletter'] == "1" && $newsletter == "false")) {
          if ($newsletter == "true") {
            if (newsletter_is_unique_email($dbconnect, $email)) {
              $length = count(NEWSLETTER_DATA);
              for($i=0; $i<$length; $i++) {
                $news_data[] = 1;
              }
              $signed_in_result = newsletter_insert($dbconnect, $email, $news_data);
              if ($signed_in_result) {
                echo '<p>successfully signed up for a newsletter</p>';
              }
            }
          }
          else {
            $signed_out_result = remove_one_row($dbconnect, "newsletter", $_POST['orig_email']);
            if ($signed_out_result) {
              echo '<p>successfully signed out of a newsletter</p>';
            }
          }
        }
      }

      elseif($table == "newsletter") {
        $query_result = update_one_row($dbconnect, $table, $id, $db_columns, $news);
        report_query($dbconnect);
      }

      close_script($dbconnect);
    }

    //if choice was "cancel" display message and quit the script
    elseif(isset($_POST['no'])) {
      echo '<p>Edit has been cancelled</p>';
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

        foreach(EDIT_IGNORE as $ignore) {
          if($column_name == $ignore) continue 2;
        }

        if($column_name == "password") {
          $value = "";
        } else {
          if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $value = $row[$columns[$i+1]];
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
        $newsletter_signed = false;
        if(isset($_POST['edit_newsletter'])) {
            $newsletter_signed = $_POST['edit_newsletter'];
        } else {
          $check_newsletter = users_is_newsletter($dbconnect, $row['email']);
          if(mysqli_num_rows($check_newsletter) == 1) {
            $newsletter_signed = true;
          }
        }
        echo '<tr><td></td></tr><tr>
                <td align="right">
                  <label class="my-auto" for="edit_newsletter">Newsletter?</label>
                </td>
                <td align="left">
                  <input type="checkbox" name="edit_newsletter" id="edit_newsletter" ';
                    if ($newsletter_signed == 1 || $newsletter_signed == "true") {
                      echo 'checked="true"';
                    }
        echo '></td></tr>';
        echo '<input type="hidden" name="orig_email" value="'.$row['email'].'">';
        if(!isset($_POST['orig_newsletter'])) {
          $org_nws = $newsletter_signed;
        } else {
          $org_nws = $_POST['orig_newsletter'];
        }
        echo '<input type="hidden" name="orig_newsletter" value="'.$org_nws.'">';
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
    <button type="submit" name="no" value="no" class="ml-2">Cancel</button>
  </form>
</div>
