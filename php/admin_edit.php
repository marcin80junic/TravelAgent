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

  //import db connection and constants
  require("../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");

  //check if choice was to cancel and quit the script
  if(isset($_POST['cancel'])) {
    echo '<p>Edit has been cancelled</p>';
    close_script($dbconnect);
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
    if(mysqli_num_rows($result) == 1){
      $record_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
      echo '<h2>'.mysqli_error($dbconnect).'</h2>';
      close_script($dbconnect);
    }
    //check if user is signed up for newsletter
    if ($table_name === "users") {
      $email = $record_data['email'];
      $check_newsletter = users_is_newsletter($dbconnect, $email);
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
      $newsletter = isset($_POST['newsletter'])? true: false;
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
      if ($table_name == "users") {
        $query_result = update_one_row($dbconnect, $table_name, $id, $edit_data);
        report_query($dbconnect);
        if (($orig_newsletter == "" && $newsletter == "on") ||
            ($orig_newsletter == "1" && $newsletter == "")) {
          if ($newsletter == "on") {
            if (is_email_unique($dbconnect, "newsletter", $email)) {
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
      elseif($table_name == "newsletter") {
        $id = "'".$id."'";
        $query_result = update_one_row($dbconnect, $table_name, $id, $edit_news);
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

    <?php
      //display a table with form fields
      if($_SERVER['REQUEST_METHOD'] == 'GET') {
        create_table_form($table_name, $pure_data, $current_type, $record_data);
      } else {
        create_table_form($table_name, $pure_data, $current_type);
      }

     ?>
    <button type="submit" name="yes" value="yes">Update</button>
    <button id="cancel" name="cancel" value="cancel" class="ml-2">Cancel</button>
  </form>
</div>
