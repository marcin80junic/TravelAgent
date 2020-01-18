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

  //title the page, include site's menu and js script
  $page_title = "Register an Account";
  include("templates/header.php");
  require("php/mysql_querries.php");
  echo '<script src="js/register.js"></script>';

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //open connection to the database
    require("../../../xxsecure/dbconnect.php");
    require("php/form_validation.php");

    //check if agreed to the privacy policy
    if(!$privacy) {
      $reg_errors[] = "You have to agree to the privacy policy!";
    }

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
        echo "<h4 class=\"mb-3\">Thank You '$reg_data[0]' !</h4>";
        echo "<p>You have succesfully registered an account!</p>";

        //check if signed up for a newsletter
        if($newsletter) {
          if(newsletter_is_unique_email($dbconnect, $email)) {
            $newsletter_response = newsletter_insert($dbconnect, $email, $news);
            if($newsletter_response) {
              echo "<p>You have also signed up for our newsletter!</p>
                    <p>We will send you offers you are interested in to the following
                    email address: <strong>$email</strong></p>";
              if($mobile != "") {
                echo "<p>We will also text you on your mobile: <strong>$mobile</strong>
                 about very special and exclusive offers</p>";
              }
            }
            else {
              echo '<h4>System error</h4><p class="error">You coud not be signed up for a newsletter due
                    to a system error. We apologise for any inconvenience.<br>Please try again later..</p>';
              echo "<p>".mysqli_error($dbconnect)."</p>";
            }
          }
          else {
        //    $newsletter_response = newsletter_update($dbconnect, $email, $options);
          }
        }
        echo "</div>";
      }

      //..or error message
      else {
        echo '<h4>System error</h4><p class="error">You coud not be registered due to a system error.
              We apologise for any inconvenience.<br>Please try again later..</p>';
        echo "<p>".mysqli_error($dbconnect)."</p>";
      }

      //include footer navigation, close the database connection and terminate script
      include("templates/footer.html");
      mysqli_close($dbconnect);
      exit();
    }
  }

?>

<!-- display register form -->
<form id="register_form" action="register.php" method="post">
  <fieldset class="border border-primary rounded-lg p-4">
    <legend class="w-auto">Register an account</legend>
    <div class="row">
      <div class="col mb-3"><h5>Please enter your details:</h5></div>
      <div class="col mb-3"><h5 class="text-center">Sign up for the newsletter</h5></div>
    </div>
    <div class="row">
      <div class="col pr-3 border border-primary border-top-0 border-left-0 border-bottom-0">
        <div class="form-group">
          <label for="f_name" class="pl-1">first name:*</label>
          <input type="text" class="form-control" name="f_name" placeholder="first name"
           maxlength="40"
           value="<?php if( isset($_POST['f_name']) ) echo $_POST['f_name']; ?>" />
        </div>
        <div class="form-group">
          <label for="l_name" class="pl-1">last name:*</label>
          <input type="text" class="form-control" name="l_name"
           maxlength="60" placeholder="last name"
           value="<?php if( isset($_POST['l_name']) ) echo $_POST['l_name'] ?>"/>
        </div>
        <div class="form-group">
          <label for="email" class="pl-1">email:*</label>
          <input type="email" class="form-control" name="email"
           maxlength="128" placeholder="email" autocomplete="username"
           value="<?php if( isset($_POST['email']) ) echo $_POST['email'] ?>" />
        </div>
        <div class="form-group">
          <label for="password" class="pl-1">password:*
            <span id="password_validation">(at least 8 characters)<span></label>
          <input type="password" class="form-control" name="password"
           maxlength="20" placeholder="password" autocomplete="new-password"
           value="<?php if( isset($_POST['password']) ) echo $_POST['password'] ?>" />
        </div>
        <div class="form-group">
          <label for="confirm_password" class="pl-1">confirm password:*
            <span id="confirm_password_validation">(must match the password)<span></label>
          <input type="password" class="form-control" name="confirm_password"
           maxlength="20" placeholder="confirm password" autocomplete="new-password"
           value="<?php if( isset($_POST['confirm_password']) ) echo $_POST['confirm_password'] ?>" />
        </div>
        <div class="form-group">
          <label for="mobile" class="pl-1">mobile number:</label>
          <input type="text" class="form-control" name="mobile" placeholder="mobile number"
          value="<?php if( isset($_POST['mobile']) ) echo $_POST['mobile']; ?>" />
        </div>
        <div class="form-check mt-4">
          <label class="form-check-label" for="privacy">
            <input type="checkbox"  name="privacy" class="form-check-input" value="privacy" id="privacy">
              I agree to <a href="#">Privacy policy*</a>
          </label>
        </div>
      </div>

      <div class="col">
        <div class="text-center">
          <h6>Choose points of interest:</h6>
        </div>
        <div class="row">
          <div class="col-sm-6 pl-5 mt-4"> <?php createCheckboxGroup(HOLIDAY_TYPES); ?> </div>
          <div class="col-sm-6 pr-5 mt-4"> <?php createCheckboxGroup(HOLIDAY_EXTRAS); ?> </div>
        </div>
        <div class="row mt-3">
          <h6 class="mx-auto my-3">Do you want to sign up for the newsletter?</h6>
        </div>
        <div class="row">
          <div class="mx-auto">
            <div class="form-check-inline">
              <label class="form-check-label mx-3">
                <input type="radio" name="newsletter" class="form-check-input" name="newsletter"
                value="Yes" checked="true">Yes
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" name="newsletter" class="form-check-input" name="newsletter"
                value="No" <?php if( isset($_POST['newsletter']) && ($_POST['newsletter'] == "No") )
                echo 'checked="true"'; ?> >No
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col pt-3">*Required fields</div>
      <div class="col">
        <input type="submit" value="submit" class="btn btn-primary float-md-right">
      </div>
    </div>
  </fieldset>
</form>

<?php

  //include footer navigation
  include("templates/footer.html");

?>
