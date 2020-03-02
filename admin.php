<?php #admin.php

  //setting up page
  $page_title = "Admin Utility";
  include("templates/header.php");
  echo '<script src="js/admin.js"></script>';
  require("php/includes/config.inc.php");

  //make sure that current user has been granted administrative privilege
  if (!isset($_SESSION['agent'], $_SESSION['user_level']) || ($_SESSION['user_level'] !== "3")) {
    echo 'access forbidden';
    exit();
  }

  //generic helper function creating checkboxes for a table column choice form
  function create_form($data, $name) {
    foreach($data as $key => $value) {
      if($key != "password") {
        $id = $name."_".$value;
        echo '<div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="'.$id.'" name="'.$value.'" ';
                if(isset($_POST[$value])) {
                  echo 'checked="true"';
                }
        echo '><label class="custom-control-label mt-1" for="'.$id.'">'.$key.'</label></div>';
      }
    }
    echo '<input type="hidden" name="table_name" value="'.$name.'">';
    echo '<input type="hidden" name="display" value="10" id="'.$name.'_display">';
    echo '<input type="hidden" name="start" value="0" id="'.$name.'_start">';
    echo '<input type="hidden" name="sort" value="" id="'.$name.'_sort">';
  }

  //generic function creating layout for table forms with column choice
  function create_table_forms($tables_data) {
    foreach($tables_data as $table_name => $table_columns) {
      echo '<div class="flex-column m-1">
              <form action="php/admin_table.php" method="post">
                <fieldset class="border border-primary px-1 pb-1">
                  <legend class="border border-primary w-auto ml-3">
                    <h6 class="m-0 p-1">'.$table_name.' table</h6>
                  </legend>
                  <div class="d-flex flex-column">
                    <div class="d-flex flex-column align-items-start">';
                      create_form($table_columns, $table_name);
      echo         '</div>
                    <div class="d-flex flex-column mt-3">
                      <div class="flex-row">
                        <button class="select_all btn btn-info">select all</button>
                        <button class="clear_all btn btn-info">clear all</button>
                      </div>
                      <button type="submit" name="'.$table_name.'" class="btn btn-info mt-1">show</button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>';
    }
  }

?>

<!--admin interface-->
<div id="interface" class="text-center mb-4">
  <h3>Database Admin Utility</h3>
  <div class="d-flex mt-3">

    <?php create_table_forms(TABLES); ?>

  </div>
</div>

<div id="main-table" class="bg-info"></div>

<div id="dialog-1" class="text-center pt-3"></div>
<div id="dialog-2" class="text-center pt-3"></div>

<?php

  include("templates/footer.html");

?>
