<?php

  //set up a script
  require("../../../../xxsecure/dbconnect.php");
  require("mysql_querries.php");

  //declare and initialize critical variables
  $table_name = $display = $pages = $start = $sort = $get_query ="";

  if (isset($_REQUEST['table_name'])) {
    $table_name = $_REQUEST['table_name'];
    set_current_data($table_name);
  } else {
    echo "<h1>System Error</h1>";
    mysqli_close($dbconnect);
    exit();
  }

  $display = isset($_REQUEST['display'])? $_REQUEST['display']: 5;

  if (isset($_GET['pages'])) {
    $pages = $_GET['pages'];
  } else {
    $result = count_num_of_rows($dbconnect, $table_name);
    if($result) {
      $rows = mysqli_fetch_array($result)[0];
    }
    $pages = ($rows > $display)? ceil($rows/$display): 1;
  }

  $start = isset($_GET['start'])? $_GET['start']: 0;

  $sort = (isset($_REQUEST['sort']))? $_REQUEST['sort']: "";
  $found = false;
  foreach($current_data as $key => $value) {
    if("$value ASC" == $sort || "$value DESC" == $sort) {
      $found = true;
      break;
    }
  }
  if(!$found) {
    $sort = _get_pk_column_name($dbconnect, $table_name) . " ASC";
  }

  //check which columns should be displayed
  $db_columns = $table_headers = [];
  foreach($current_data as $key => $value) {
    if(isset($_REQUEST[$value])) {
      $get_query .= $value.'='.$_REQUEST[$value].'&';
      $db_columns[] = $value;
      $table_headers[] = $key;
    }
  }
  //if 1 or more checkboxes were checked retrieve number of records from the database
  if(COUNT($db_columns) > 0) {
    $result = select_num_rows_sorted($dbconnect, $table_name, $sort, $start, $display);
    if ($result) {
      $plain_href = "table_name=$table_name&display=$display&{$get_query}";
      $col_href = "{$plain_href}pages=$pages&sort=";

      //create and display a table
      echo '<h3 class="text-center py-3">'.$table_name.'</h3>';
      echo '<div class="overflow-auto"><table width="100%" class="table-bordered table-info mx-auto">
            <thead id="admin-table-head"><tr><th>#</th>';
      //display table headers..
      foreach($table_headers as $key => $header) {
        if($header != "password") {
          $column_sort = (strpos($sort, "{$db_columns[$key]} ASC") !== false)? "{$db_columns[$key]} DESC":
            "{$db_columns[$key]} ASC";
          if($header == "email") {
            echo '<th width="15%"><a href="'.$col_href.$column_sort.'">'.$header.'</a></th>';
          }
          else echo '<th><a href="'.$col_href.$column_sort.'">'.$header.'</a></th>';
        }
      }
      echo '<th width="10%"> actions </th></tr></thead>';
      //..and table body
      $index = $start+1;
      $length = COUNT($db_columns);
      $table = strchr($table_name, ' ', true);
      echo '<tbody>';
      while($row = mysqli_fetch_array($result)) {
        //insert # column
        echo '<tr><td align="right">'.$index++.'</td>';
        for($i = 0; $i < $length; $i++) {
          $value = $row[$db_columns[$i]];
          if ($db_columns[$i] === "date_from" || $db_columns[$i] === "date_to") {
            $value = strtotime($value);
            $value = ($value < 0)? "": date("d-m-Y", $value);
          }
          if ($db_columns[$i] != "password") {
            echo '<td align="right">'.$value.'</td>';
          }
        }
        $key = $row[0];
        //insert 'actions' column with admin actions
        echo '<td align="center"><form action="admin.php" method="get">
              <a href="php/admin_edit.php?table='.$table_name.'&id='.$key.'" class="edit">edit</a>
              <a href="php/admin_remove.php?table='.$table_name.'&id='.$key.'" class="remove ml-2">
              remove</a></form></td></tr>';
      }
      echo '</tbody></table></div>';

      //create table page navigation
      $current_page = ($start/$display) + 1;
      $prev_disabled = ($current_page == 1);
      $next_disabled = ($current_page == $pages);
      $button_href = $col_href.$sort;
      echo '<div class="d-flex justify-content-between pb-1 m-1"><div id="table-navigation" class="">';
      //previous button
      echo '<a href="'.$button_href.'&start='.($start-$display).'"><button';
      if($prev_disabled) echo ' disabled="disabled"';
      echo '>Prev</button></a>';
      //number buttons
      for($i=1; $i<=$pages; $i++) {
        $num = $i;
        if($current_page == $i) $num = "<b>$num</b>";
        echo '<a href="'.$button_href.'&start='.($display*($i-1)).'"><button>'.$num.'</button></a>';
      }
      //next button
      echo '<a href="'.$button_href.'&start='.($start+$display).'"><button';
      if($next_disabled) echo ' disabled="disabled"';
      echo '>Next</button></a>';

      //hidden input with "href" value to be retrieved by javascript
      $display_href = "{$plain_href}sort=$sort";
      echo '<input type="hidden" name="href" id="href" value="'.$display_href.'">';
      echo '</div>';
    }


    //if couldn't retrieve records from database display error message
    else {
      echo "<h6>MySQL Syntax Error</h6><br><p>".mysqli_error($dbconnect)."</p>";
      mysqli_close($dbconnect);
      exit();
    }
  }
  //if no columns were selected display empty table and quit the script
  else {
    echo '<h3 class="text-center mb-3">'.$table_name.'</h3>';
    echo '<p class="text-center text-danger">No data selected!</p>';
    mysqli_close($dbconnect);
  }

?>

  <!--add new records button -->
  <div>
    <a href="php/admin_add.php?table=<?php echo $table_name; ?>">
      <button id="add-record">Add Record</button>
    </a>
  </div>

  <!--creation of drop down options for display-->
  <div class="pt-1">
    <span>Display per page: </span>
    <select name="display" id="display">
      <option value="5" <?php if($display==="5") echo 'selected="selected"';?> >5</option>
      <option value="10" <?php if($display==="10") echo 'selected="selected"';?> >10</option>
      <option value="15" <?php if($display==="15") echo 'selected="selected"';?> >15</option>
      <option value="20" <?php if($display==="20") echo 'selected="selected"';?> >20</option>
      <option value="25" <?php if($display==="25") echo 'selected="selected"';?> >25</option>
      <option value="50" <?php if($display==="50") echo 'selected="selected"';?> >50</option>
    </select>
  </div>
</div>
