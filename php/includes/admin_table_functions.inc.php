<?php

  //function creating admin forms for editing or addition of records
  function create_table_form($table, $cols, $type, $orig_data=false) {

    echo '<table width="100%" align="center" class="form-table">
            <thead>
              <tr><th></th><th width="50%"></th></tr>
            </thead>
            <tbody>';
    foreach($cols as $desc => $column) {
      $orig_col = "orig_$column";
      $orig_val = $orig_data? get_value($column, $orig_data): get_value($orig_col, $orig_data);
      $value = get_value($column, $orig_data);
      if (($value !== "") && ($column === "date_from" || $column === "date_to")) {
        $orig_val = strtotime($orig_val);
        $orig_val = ($orig_val < 0)? "": date("d-m-Y", $orig_val);
        $value = strtotime($value);
        $value = ($value < 0)? "": date("d-m-Y", $value);
      }
      if ($column == "active") {
        if ($orig_data) {
          $orig_val = ($orig_val == NULL)? true: false;
        }
        $value = ($value == NULL)? true: false;
      }
      if ($orig_data && $desc === "password") {
        $value = $orig_val = "";
      }
      $inp_type = $type[$desc];

      echo '<tr>
              <td class="left">
                <label for="'.$column.'">'.$desc.'</label>
              </td>
              <td class="right">
                <input type="'.$inp_type.'" id="'.$column.'" name="'.$column.'" ';
                  if ($inp_type != "checkbox") echo ' value="'.$value.'"';
                  if ($inp_type == "checkbox" && $value == "1") echo 'checked="true"';
      echo        '><input type="hidden" name="'.$orig_col.'" value="'.$orig_val.'">
              </td></tr>';
            if ($inp_type === "password") {
        echo '<tr>
                <td class="left">
                  <label for="confirm-'.$column.'">confirm '.$desc.'</label>
                </td>
                <td class="right">
                  <input type="'.$inp_type.'" name="confirm_'.$column.'"
                    value="'.get_value("confirm_$column", $orig_data).'" id="confirm-'.$column.'">
                  </td>
              </tr>';
          }
    }
    if ($table === "users") {
      $value = get_value("newsletter", $orig_data);
      echo '<tr>
              <td class="left">
                <label for="newsletter">newsletter?</label>
              </td>
              <td class="right">
                <input type="checkbox" name="newsletter" id="newsletter" ';
                if ($value == "1") echo 'checked="true"';
                  echo  '></td>
            </tr>';
          $orig_news = $orig_data? $value: get_value("orig_newsletter", $orig_data);
      echo '<input type="hidden" name="orig_newsletter" value="'.$orig_news.'">';
    }
    if ($table === "holidays") {
      $value = $orig_data? get_value("image", $orig_data): get_value("orig_image", $orig_data);
      echo '<tr><td colspan="2" align="center">image:
              <input type="hidden" name="MAX_FILE_SIZE" VALUE="10000000">
              <input type="file" id="image" name="image">
              <input type="hidden" name="orig_image" value="'.$value.'">
            </td></tr>';
        }
    echo '<input type="hidden" id="table" name="table" value="'.$table.'">';
    echo '<input type="hidden" id="id" name="id" value="'.get_value("id", $orig_data).'">';
    echo '</tbody></table>';
  }

  //helper function obtaining values for above table form
  function get_value($key, $orig_data) {
    if($_SERVER['REQUEST_METHOD'] === 'GET' && $orig_data !== false && isset($orig_data[$key])) {
      return $orig_data[$key];
    }
    if (isset($_POST[$key]) && !empty($_POST[$key])) {
      return trim($_POST[$key]);
    }
    else return "";
  }

  //removes old images from the server
  function remove_image($dbc, $id) {
    $result = select_one_row($dbc, "holidays", $id);
    if (mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $path = $row['image'];
      @unlink($path);
    }
  }

  //prints out update report
  function report_query($dbc, $data_edited) {
    if (empty($data_edited)) {
      echo '<p class="pt-3">No changes have been requested</p>';
    }
    elseif (mysqli_affected_rows($dbc) === 1) {
      echo '<p class="pt-3">record have been updated/added successfully<br></p>';
    }
    elseif (mysqli_affected_rows($dbc) === 0) {
      echo '<p class="pt-3">No records have been updated/added</p>';
    }
    else {
      echo '<p class="pt-3">Error: '.mysqli_error($dbc).'</p>';
    }
  }


  //closing procedures
  function close_script($dbc) {
    echo '<br><button class="btn btn-info" id="ok" href="../admin.php">Ok</button>';
    mysqli_close($dbc);
    exit();
  }


 ?>
