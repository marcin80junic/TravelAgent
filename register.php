<?php

  //define function creating register form checkboxes
  function createCheckboxGroup($data) {
    foreach($data as $key => $value) {
      echo '<div class="form-check mb-2">';
      echo "<label class=\"form-check-label font-weight-bold\">";
      echo "<input type=\"checkbox\" class=\"form-check-input\" name=\"$value\" value=\"$value\"";
      if(isset($_POST[$value]) && $_POST[$value]){
        echo " checked=\"checked\"";
      }
      echo "> $key</label></div>";
    }
  }

  //function creating bootstrap forms
  function create_user_registration_form($data) {
    foreach($data as $key => $val) {
      echo '<div class="form-group">
              <label for="'.$val.'" class="pl-1">'.$key.':';
              if ($val != "mobile") echo '*'; echo '</label>
              <input type="'.USERS_FORM_TYPES[$key].'" class="form-control"
              name="'.$val.'" placeholder="'.$key.'" maxlength="40"';
              if (isset($_POST[$val])) echo ' value="'.$_POST[$val].'"';
      echo    '></div>';
    }
  }

  //import constants, title the page, include site's menu and js script
  require("php/includes/config.inc.php");
  set_current_data("users");
  $page_title = "Register an Account";
  include("templates/header.php");
  echo '<script src="js/register.js"></script>';


  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //open connection to the database and validate the form
    $table_name = "users";
    require(MYSQL);
    require("php/includes/form_validation.inc.php");

    //if any errors encountered display them and continue back to the form
    if(!empty($reg_errors)) {
      foreach($reg_errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }

    //otherwise insert a record into a database
    else {
      $register_response = users_insert($dbconnect, $reg_data);

      //display confirmation message..
      if($register_response) {
        echo '<div class="text-center">';
        echo "<h4 class=\"mb-3\">Thank You '$f_name' !</h4>";
        echo "<p>You have succesfully registered an account!</p>";
        echo '<p>To activate an account please click a link in confirmation email</p>';
        $body = "Thank you for registering an account!\n\r";

        //check if signed up for a newsletter
        if(isset($_POST['newsletter']) && ($_POST['newsletter'] === "yes")) {
          if (is_value_unique($dbconnect, "newsletter", 'email', $email)) {
            $newsletter_response = newsletter_insert($dbconnect, $email, $reg_news);
            if($newsletter_response) {
              echo "<p>You have also signed up for our newsletter!</p>
                    <p>We will send you offers you are interested in to the following
                    email address: <strong>$email</strong></p>";
              $body .= "You have also signed up for our newsletter service, well done!\n\r";
              if($mobile != "") {
                echo "<p>We will also text you on your mobile: <strong>$mobile</strong>
                 about very special and exclusive offers</p>";
              }
            }
            else {
              echo '<h4>System error</h4><p class="error">You coud not be signed up for a newsletter due
                    to a system error. We apologise for any inconvenience.<br>Please try again later..</p>';
            }
          } else {
            echo "<p>You are already receiving our newsletter</p>";
          }
        }
        echo "</div>";
        //send confirmation email containing activation link
        $body .= "To activate your account please click the following link:\n\r\n\r";
        $body .= BASE_URL . "activation.php?x=" . urlencode($email) . "&y={$reg_data[0]}";
        $body .= "\n\r\n\rEurope Travel Experts";
        if (!LIVE) $email = "dest@localhost.com";
        mail($email, 'Activate Your Account', $body, 'From: ' . EMAIL);
      }

      //..or error message
      else {
        echo '<h4>System error</h4><p class="error">You coud not be registered due to a system error.
              We apologise for any inconvenience.<br>Please try again later..</p>';
      }

      //include footer navigation, close the database connection and terminate script
      include("templates/footer.html");
      exit();
    }
    mysqli_close($dbconnect);
  }

?>

<!-- display register form -->
<form id="register_form" action="register.php" method="post">
  <fieldset class="border border-primary rounded-lg p-4">
    <legend class="w-auto">Register an account</legend>

    <div class="row">

      <div class="col d-flex flex-column border border-primary border-top-0 border-bottom-0 border-left-0">
        <h5>Please enter your details:</h5>
        <?php
          $data = array_diff_key(USERS_COLUMNS, array_flip(REGISTRATION_IGNORE));
          create_user_registration_form($data);
        ?>
        <div class="form-group">
          <label for="confirm_password" class="pl-1">confirm password:*</label>
          <input type="password" class="form-control" name="confirm_password"
           maxlength="40" placeholder="confirm password"
           <?php if(isset($_POST['confirm_password'])) echo ' value="'.$_POST['confirm_password'].'"'; ?>>
        </div>
        <div class="form-check pl-2 pt-2 mt-auto d-flex">
          <p class="d-inline mr-auto my-0">*Required fields</p>
          <label class="form-check-label" for="privacy">
            <input type="checkbox"  name="privacy" class="form-check-input" value="privacy" id="privacy">
              I agree to <a href="#">Privacy policy*</a>
          </label>
        </div>
      </div>

      <div class="col d-flex flex-column border border-primary border-top-0 border-bottom-0 border-right-0">
        <div class="mb-3">
          <h5 class="text-center">Do you want to sign up for the newsletter?</h5>
          <div class="text-center mt-3">
            <div class="form-check-inline">
              <label class="form-check-label mx-3">
                <input type="radio" name="newsletter" class="form-check-input" name="newsletter"
                value="yes" checked="true">Yes
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" name="newsletter" class="form-check-input" name="newsletter"
                value="no" <?php if( isset($_POST['newsletter']) && ($_POST['newsletter'] == "no") )
                echo 'checked="true"'; ?> >No
              </label>
            </div>
          </div>
        </div>
        <div class="text-center mb-2">
          <h6>Choose points of interest:</h6>
        </div>
        <div class="row">
          <div class="col"><?php createCheckboxGroup(HOLIDAY_TYPES); ?></div>
          <div class="col"><?php createCheckboxGroup(HOLIDAY_EXTRAS); ?></div>
        </div>
        <div class="ml-auto mt-auto">
          <input type="submit" value="submit" class="btn btn-primary">
        </div>
      </div>

    </div>

  </fieldset>
</form>

<?php

  //include footer navigation
  include("templates/footer.html");

?>
