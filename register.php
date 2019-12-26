<?php

  //initiate variables used in register form
  $types = array(
    array("summer_hol", "summer holidays"),
    array("city_break", "city breaks"),
    array("mountains", "mountains"),
    array("cruise", "cruises"),
    array("tour_hol", "tour holidays")
  );
  $extras = array(
    array("beach", "next to beach"),
    array("swimming_pool", "swimming pool"),
    array("aquapark", "aquapark"),
    array("surfing", "surfing"),
    array("skiing", "skiing"),
    array("gym_fitness", "gym/fitness facilities")
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

  //function checking is email address already in a database
  function isUnique($dbc, $email) {
    $query = "SELECT email FROM users WHERE email='$email'";
    $result = mysqli_query($dbc, $query);
    if($result) {
      $row = mysqli_fetch_array($result);
      if($row[0]) return false;
      else return true;
    }
    return false;
  }

  //title the page, include site's menu and js script
  $page_title = "Register an Account";
  include("templates/header.html");
  echo '<script src="js/register.js"></script>';

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //declare variables and open connection to the database
    $errors = $options = $news = [];
    $first_name = $last_name = $email = $pass = $conf_pass = $mobile = "";
    $newsletter = false;
    require("../../../../xxsecure/dbconnect.php");

    //validate all form fields and record all errors
    if(!empty($_POST['first_name'])) {
      $first_name = trim($_POST['first_name']);
    } else {
      $errors[] = "Fill in first name please!";
    }
    if(!empty($_POST['last_name'])) {
      $last_name = trim($_POST['last_name']);
    } else {
      $errors[] = "Fill in last name please!";
    }
    if(!empty($_POST['email'])) {
      $email = trim($_POST['email']);
      if(!isUnique($dbconnect, $email)){
        $errors[] = "this email address is already in our database!";
      }
    } else {
      $errors[] = "fill in an email address please!";
    }
    if(!empty($_POST['password'])) {
      $pass = trim($_POST['password']);
      if(strlen($pass) < 8) {
        $errors[] = "password must be AT LEAST 8 characters long";
      }
    } else {
      $errors[] = "fill in password field please!";
    }
    if(!empty($_POST['confirm_password'])) {
      $conf_pass = trim($_POST['confirm_password']);
      if($conf_pass != $pass) {
        $errors[] = "password confirmation DOESN'T match!";
      }
    }
    if(!empty($_POST['mobile_number'])) {
      $mobile = trim($_POST['mobile_number']);
    }
    if(!isset($_POST['privacy'])) {
      $errors[] = "You have to agree to our Privacy Policy!";
    }

    //if any errors encountered display them and continue back to the form
    if(count($errors) > 0) {
      foreach($errors as $message) {
        echo '<p class="lead text-danger font-weight-bold">' . $message . '</p>';
      }
    }

    //otherwise check if signed up for newsletter...
    else {
      if(isset($_POST['newsletter'])) {
        $newsletter = ($_POST['newsletter'] == "Yes")? true: false;
      }
      if($newsletter) {
        foreach($types as $value){
          $news[] = isset($_POST[$value[0]])? 1: 0;
          if(isset($_POST[$value[0]])){
            array_push($options, $value[1]);
          }
        }
        foreach($extras as $value){
          $news[] = isset($_POST[$value[0]])? 1: 0;
          if(isset($_POST[$value[0]])){
            array_push($options, $value[1]);
          }
        }
      }

      //..and insert a record into a database
      $reg_query = "INSERT INTO users(f_name, l_name, email, password, mobile, date_registered)";
      $reg_query .= "VALUES('$first_name', '$last_name', '$email', SHA2('$pass', 512), '$mobile', NOW())";
      $register_response = @mysqli_query($dbconnect, $reg_query);
      $newsletter_response = null;
      if($newsletter) {
        if($register_response) {
          $user_id_query = "SELECT user_id FROM users WHERE email='$email'";
          $r = mysqli_query($dbconnect, $user_id_query);
          $row = mysqli_fetch_array($r);
          $user_id = $row[0];
          $newsletter_query = "INSERT INTO newsletter VALUES('$user_id', '{$news[0]}', '{$news[1]}',
           '{$news[2]}', '{$news[3]}', '{$news[4]}', '{$news[5]}', '{$news[6]}', '{$news[7]}', '{$news[8]}',
          '{$news[9]}', '{$news[10]}')";
          $newsletter_response = @mysqli_query($dbconnect, $newsletter_query);
        }
      }

      //display confirmation message..
      if($register_response){
        echo '<div class="text-center">';
        echo "<h4 class=\"mb-3\">Thank You $first_name !</h4>";
        echo "<p>You have succesfully registered an account!</p>";
        if($newsletter_response) {
          echo "<p>You have also signed up for our newsletter which means that we will
                send you info regarding any new offers regarding: </p>";
          foreach($options as $value){
            echo "<p class=\"text-left w-25 m-auto pl-5\"> - $value </p>";
          }
          echo "<p class=\"mt-3\">to the following email address: $email </p>";
          if($mobile != "") {
            echo "<p>We will also text you on your mobile: $mobile about very special and exclusive offers</p>";
          }
        }
        echo "</div>";
      }

      //..or error message
      else {
        echo '<h4>System error</h4><p class="error">You coud not be registered due to a system error.
              We apologise for any inconvenience.<br>Please try again later..</p>';
        echo "<p>{mysqli_error($dbconnect)}<br><br>Query: $query</p>";
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
           maxlength="128" placeholder="email"
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
        </div>
      </div>

      <div class="col">
        <div class="text-center">
          <h6>Choose points of interest:</h6>
        </div>
        <div class="row">
          <div class="col-sm-6 pl-5 mt-4"> <?php createCheckboxGroup($types); ?> </div>
          <div class="col-sm-6 pr-5 mt-4"> <?php createCheckboxGroup($extras); ?> </div>
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
