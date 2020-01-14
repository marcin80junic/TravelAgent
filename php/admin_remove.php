<?php #admin_remove.php

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

  //if deletion has been confirmed remove the record from db
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(!isset($_POST['no'])) {
      $del = remove_one_row($dbconnect, $table, $id);
      if(mysqli_affected_rows($dbconnect) == 1) {
        echo '<p class="pt-3">record have been removed from the database<br></p>';
      } else {
        echo '<p class="pt-3">'.mysqli_error($dbconnect).'</p>';
      }
    }
    else {
      echo '<p>Record has NOT been removed from the database</p>';
    }
    echo '<br><button class="btn btn-info" id="ok" href="../admin.php">Ok</button>';
    mysqli_close($dbconnect);
    exit();
  }

?>

<p>Are you sure you want to remove the following record:<br><br>

  <?php
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
      $result = select_one_row($dbconnect, $table, $id);
      if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $columns = array_keys($row);
        for($i=0; $i<3; $i++) {
          $col = $columns[$i];
          echo $col.': '.$row[$col].' | ';
        }
      } else {
        echo '<h2>'.mysqli_error($dbconnect).'</h2>';
      }
    }

  ?>
  <br><br>from the database?
</p>
<form id="decision" action="admin_remove.php" method="post">
  <input type="hidden" name="table" value="<?php echo $table; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <?php echo '<a href="php/admin_remove.php?yes=yes&table='.$table.'&id='.$id.'">'; ?>
    <button id="confirm-remove" type="submit" name="yes" value="yes">Yes</button>
  </a>
  <?php echo '<a href="php/admin_remove.php?no=no&table='.$table.'&id='.$id.'">'; ?>
    <button type="submit" id="cancel" name="no" value="no" class="ml-2">No</button>
  </a>

</form>
