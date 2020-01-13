<?php #admin.php

  //setting up page
  $page_title = "Admin Utility";
  include("templates/header.php");
  echo '<script src="js/admin.js"></script>';
  require("php/mysql_querries.php");

  //generic function creating table form
  function create_form($data, $name) {
    foreach($data as $key => $value) {
      if($key != "password") {
        $id = $name."_".$value;
        echo '<div class="custom-control custom-checkbox d-inline mr-3">
                <input type="checkbox" class="custom-control-input" id="'.$id.'" name="'.$value.'" ';
                if(isset($_POST[$value])) {
                  echo 'checked="true"';
                }
        echo '><label class="custom-control-label mt-2" for="'.$id.'">'.$key.'</label></div>';
      }
    }
    echo '<input type="hidden" name="table_name" value="'.$name.'">';
    echo '<input type="hidden" name="display" value="5" id="'.$name.'_display">';
  }

?>

<!--admin interface-->
<div id="interface" class="text-center mb-4">
  <h3>Database Admin Utility</h3>

  <form action="php/admin_table.php" method="post" class="mt-4">
    <fieldset class="border border-primary px-4 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Users table</h6>
      </legend>
      <div class="d-flex flex-row">
        <div class="d-flex flex-wrap align-self-center">
          <?php create_form(USER_DATA, "users"); ?>
        </div>
        <div class="align-self-end ml-auto d-flex flex-column bg-light p-2">
          <a href="#" class="select_all">select all</a>
          <a href="#" class="clear_all">clear all</a>
          <button type="submit" name="users" class="btn btn-info mt-1">show</button>
        </div>
      </div>
    </fieldset>
  </form>

  <form action="php/admin_table.php" method="post" class="mt-3">
    <fieldset class="border border-primary px-4 pb-3">
      <legend class="text-left border border-primary w-auto ml-3">
        <h6 class="m-0 p-1">Newsletter table</h6>
      </legend>
      <div class="d-flex flex-row">
        <div class="d-flex flex-wrap align-self-center">
          <?php create_form(NEWSLETTER_DATA, "newsletter"); ?>
        </div>
        <div class="align-self-end ml-auto d-flex flex-column bg-light p-2">
          <a href="#" class="select_all">select all</a>
          <a href="#" class="clear_all">clear all</a>
          <button type="submit" name="newsletter" class="btn btn-info mt-1">show</button>
        </div>
      </div>
    </fieldset>
  </form>

</div>

<div id="table"></div>

<div id="dialog-1" class="text-center pt-3"></div>

<?php

  include("templates/footer.html");

?>
