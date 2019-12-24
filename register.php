<?php

  //initiate variables used in register form
  $types = array(
    array("summer", "summer holidays"),
    array("city", "city breaks"),
    array("mountains", "mountains/skiing"),
    array("cruises", "cruises"),
    array("tour", "tour holidays")
  );
  $extras = array(
    array("beach", "next to beach"),
    array("swimming_pool", "swimming pool"),
    array("aquapark", "aquapark"),
    array("surfing", "surfing"),
    array("gym", "gym/fitness facilities")
  );

  //define function creating register form checkboxes
  function createCheckboxGroup($data) {
    $length = count($data);
    for($i = 0; $i < $length; $i++){
      $name = $data[$i][0];
      $description = $data[$i][1];
      echo '<div class="form-check mb-2">';
      echo "<label class=\"form-check-label font-weight-bold\">";
      echo "<input type=\"checkbox\" class=\"form-check-input\" name=\"$name\" value=\"$name\"";
      if(isset($_POST[$name]) && $_POST[$name]){
        echo " checked=\"checked\"";
      }
      echo "> $description</label></div>";
    }
  }

  //title the page, include site's menu and js script
  $page_title = "Register an Account";
  include("templates/header.html");
  echo '<script src="js/register.js"></script>';

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //check if required fields has been filled in..
    if( (isset($_POST['privacy'])) && (!empty($_POST['first_name'])) && (!empty($_POST['last_name'])) &&                         (!empty($_POST['email'])) && (!empty($_POST['password'])) && (!empty($_POST['confirm_password'])) &&
    ($_POST['password'] == $_POST['confirm_password'])) {

      require("../../../../xxsecure/dbconnect.php");
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $email = $_POST['email'];
      $pass = $_POST['password'];
      $mobile = null;
      if(!empty($_POST['mobile_number'])) $mobile = $_POST['mobile_number'];
      $newsletter = isset($_POST['newsletter']);
      if($newsletter) {
        $options = [];
        foreach($types as $value){
          if(isset($_POST[$value[0]])){
            array_push($options, $value[1]);
          }
        }
        foreach($extras as $value){
          if(isset($_POST[$value[0]])){
            array_push($options, $value[1]);
          }
        }
      }

      //display confirmation message
      echo '<div class="text-center">';
      echo "<h4 class=\"mb-3\">Thank You $first_name !</h4>";
      echo "<p>You have succesfully registered an account!</p>";
      if($newsletter) {
        echo "<p>You have also signed up for our newsletter which means that we will
              send you info regarding any new offers regarding: </p>";
        foreach($options as $value){
          echo "<p class=\"text-left w-25 m-auto pl-5\"> - $value </p>";
        }
        echo "<p class=\"mt-3\">to the following email address: $email </p>";
        if($mobile != "") {
          echo "<p>We will also text you on your mobile: $mobile about very special and exclusive offers</p>";
        }
        echo "</div>";
      }

      //include footer navigation, close the database connection and terminate script
      include("templates/footer.html");
      mysqli_close($dbconnect);
      exit();
    }

    //..if required fields has not been filled in,  display warning message(s)
    else {
      $errors = [];
      if (empty($_POST['first_name']) OR empty($_POST['last_name']) OR empty($_POST['email'])
          OR (empty($_POST['password'])) OR empty($_POST['confirm_password'])) {
        $errors[] = "Fill in required fields please!";
      }
      if ($_POST['password'] != $_POST['confirm_password']) {
        $errors[] = "Password confirmation doesn't match the password!";
      }
      if(!isset($_POST['privacy'])){
        $errors[] = "You have to agree to our Privacy Policy!";
      }
      foreach($errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }
  }

?>

<!-- display register form -->
<form id="register_form" action="register.php" method="post">
  <fieldset class="border border-primary rounded-lg p-4">
    <legend class="w-auto">Register an account</legend>
    <div class="row">
      <div class="col mb-3"><h5>Please enter your details:</h5></div>
      <div class="col mb-3"><h5 class="text-center">What are you interested in:</h5></div>
    </div>
    <div class="row">
      <div class="col pr-3 border border-primary border-top-0 border-left-0 border-bottom-0">
        <div class="form-group">
          <label for="first_name" class="pl-1">first name:*</label>
          <input type="text" class="form-control" name="first_name" placeholder="first name"
           maxlength="40"
           value="<?php if( isset($_POST['first_name']) ) echo $_POST['first_name']; ?>" />
        </div>
        <div class="form-group">
          <label for="last_name" class="pl-1">last name:*</label>
          <input type="text" class="form-control" name="last_name"
           maxlength="60" placeholder="last name"
           value="<?php if( isset($_POST['last_name']) ) echo $_POST['last_name'] ?>"/>
        </div>
        <div class="form-group">
          <label for="email" class="pl-1">email:*</label>
          <input type="email" class="form-control needs-validation" name="email"
           maxlength="60" placeholder="email"
           value="<?php if( isset($_POST['email']) ) echo $_POST['email'] ?>" />
        </div>
        <div class="form-group">
          <label for="password" class="pl-1">password:*
            <span id="password_validation">(at least 8 characters)<span></label>
          <input type="password" class="form-control" name="password"
           maxlength="20" placeholder="password"
           value="<?php if( isset($_POST['password']) ) echo $_POST['password'] ?>" />
        </div>
        <div class="form-group">
          <label for="confirm_password" class="pl-1">confirm password:*
            <span id="confirm_password_validation">(must match the password)<span></label>
          <input type="password" class="form-control" name="confirm_password"
           maxlength="20" placeholder="confirm password"
           value="<?php if( isset($_POST['confirm_password']) ) echo $_POST['confirm_password'] ?>" />
        </div>
        <div class="form-group">
          <label for="mobile_number" class="pl-1">mobile number:</label>
          <input type="text" class="form-control" name="mobile_number" placeholder="mobile number"
          value="<?php if( isset($_POST['mobile_number']) ) echo $_POST['mobile_number']; ?>" />
        </div>
        <div class="form-check mt-4">
          <label class="form-check-label">
            <input type="checkbox"  name="privacy" class="form-check-input" value="">
              I agree to <a href="#">Privacy policy*</a>
          </label>
          <label class="form-check-label float-right">
            <input type="checkbox" name="newsletter" class="form-check-input" value=""
              <?php if( isset($_POST['newsletter']) ) echo 'checked="true"'; ?> >
              Sign up for our newsletter
          </label>
        </div>
      </div>
      <div class="col">
        <div class="row">
          <div class="col-sm-6 pl-5 mt-4"> <?php createCheckboxGroup($types); ?> </div>
          <div class="col-sm-6 pr-5 mt-4"> <?php createCheckboxGroup($extras); ?> </div>
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
