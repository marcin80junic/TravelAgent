<?php

  require("../../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");

  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    if(isset($_POST['yes'])) {

    }
    mysqli_close($dbconnect);
    exit();
  }

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../css/jquery-ui.css">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/my.css">
  <script src="../js/jquery-3.4.1.js"></script>
  <script src="../js/jquery-ui.js"></script>
  <script src="../js/bootstrap.js"></script>
  <title>remove</title>
</head>
<body>
  <div class="container-fluid text-center">
    <p>Are you sure you want to remove the following record<br><br>
      <?php
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
          $table = $_GET['table'];
          $id = $_GET['id'];
          $result = select_one_row($dbconnect, $table, $id);
          if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_array($result);
            for($i=0; $i<4; $i++) {
              echo $row[$i].' ';
            }
          } else {
            echo '<h2>'.mysqli_error($dbconnect).'</h2>';
          }
        }
      ?>
      <br><br>from the database?
    </p>
    <form action="admin_remove.php" method="post">
      <button type="submit" name="yes">Yes</button>
      <button type="submit" name="no">No</button>
    </form>
  </div>
</body>
</html>
