<?php #php/admin_edit.php

  //import db connection and functions
  require("includes/config.inc.php");
  require(MYSQL);
  require("includes/admin_table_functions.inc.php");

  //check if choice was to cancel and quit the script
  if(isset($_POST['cancel'])) {
    echo '<p>Edit has been cancelled</p>';
    exit();
  }

  //first extract critical variables
  if(isset($_REQUEST['table']) && isset($_REQUEST['id'])) {
    $table_name = $_REQUEST['table'];
    $id = $_REQUEST['id'];
  } else {
    echo '<p>this page has been accessed in error</p>';
    close_script($dbconnect);
  }
  set_current_data($table_name);


  //first connection
  if($_SERVER['REQUEST_METHOD'] == 'GET') {

    //on first connection extract the record
    $result = select_one_row($dbconnect, $table_name, $id);
    if (mysqli_num_rows($result) == 1) {
      $record_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
      echo '<h2>'.mysqli_error($dbconnect).'</h2>';
      close_script($dbconnect);
    }
    //check if user is signed up for newsletter
    if ($table_name === "users") {
      $email = $record_data['email'];
      $check_newsletter = users_is_signed_up_for_newsletter($dbconnect, $email);
      $orig_newsletter = (mysqli_num_rows($check_newsletter) == 1)? true: false;
      $record_data["newsletter"] = $orig_newsletter;
    }
    //newsletter table doesn't have an id column so one needs to be inserted
    if ($table_name === "newsletter") {
      $record_data["id"] = $record_data["email"];
    }
  }


  //ignoring columns which shouldn't be edited
  $pure_data = ignore_values($current_data);
  $chosen_db_columns = array_values($pure_data);


  //on submit
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //extracting critical variables
    if($table_name === "users") {
      if (isset($_POST['newsletter'])) {
        $newsletter = $_POST['newsletter'];
      }
      $orig_newsletter = $_POST['orig_newsletter'];
    }

    //using form validation to extract and check submitted data
    //if errors discovered print them out
    require("includes/form_validation.inc.php");
    if (!empty($edit_errors)) {
      foreach($edit_errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }

    //if no errors proceed with update
    else {
      if ($table_name == "users") {
        update_one_row($dbconnect, $table_name, $id, $edit_data);
        report_query($dbconnect, $edit_data);
        if (($orig_newsletter == "" && $newsletter == "1") ||
            ($orig_newsletter == "1" && $newsletter == "")) {
          if ($newsletter) {
            $sign_up_result = newsletter_sign_up($dbconnect, $email);
            if ($sign_up_result) {
              echo '<p>successfully signed up for a newsletter</p>';
            } elseif (mysqli_error($dbconnect)) {
              echo '<p>MySql Error: '.mysqli_error($dbconnect).'</p>';
            } else {
              echo '<p>Your email address is already receiving a newsletter</p>';
            }
          } else {
            $signed_out_result = remove_one_row($dbconnect, "newsletter", $_POST['orig_email']);
            if ($signed_out_result) {
              echo '<p>successfully signed out of a newsletter</p>';
            } else {
              echo '<p>MySql Error: '.mysqli_error($dbconnect).'</p>';
            }
          }
        }
      }
      elseif ($table_name == "newsletter") {
        $id = "'".$id."'";
        $query_result = update_one_row($dbconnect, $table_name, $id, $edit_news);
        report_query($dbconnect, $edit_news);
      }
      elseif ($table_name === "holidays") {
        $result_insert = update_one_row($dbconnect, $table_name, $id, $edit_holid);
        report_query($dbconnect, $edit_holid);
      }
      close_script($dbconnect);
    }
  }

?>

<!-- Display the main form for updating records -->
<div>
  <form id="decision" action="admin_edit.php" method="post">
    <p>Edit current record id: <?php echo $id; ?></p>

    <?php

      //use function to display a table with form fields
      if($_SERVER['REQUEST_METHOD'] == 'GET') {
        create_table_form($table_name, $pure_data, $current_type, $record_data);
      } else {
        create_table_form($table_name, $pure_data, $current_type);
      }

     ?>

     <br>
    <button type="submit" name="yes" value="yes">Update</button>
    <button id="cancel" name="cancel" value="cancel" class="ml-2">Cancel</button>
  </form>
</div>
