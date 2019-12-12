<?php

  $types = array(
    array("summer", "summer deals"),
    array("fall", "after season deals"),
    array("winter", "winter deals"),
    array("cruises", "cruises")
  );

  $extras = array(
    array("all_inclusive", "all inclusive")
  );

  function createCheckboxGroup($data) {
    $length = count($data);
    for($i = 0; $i < $length; $i++){
      $name = $data[$i][0];
      $description = $data[$i][1];
      echo '<div class="form-check">';
      echo "<input type=\"checkbox\" class=\"form-check-input\" name=\"$name\" value=\"$name\"";
      if(isset($_POST[$name]) && $_POST[$name]){
        echo " checked=\"checked\"";
      }
      echo '>';
      echo "<label for=\"$name\" class=\"form-check-label font-weight-bold\"> $description </label>";
      echo '</div>';
    }
  }

  $page_title = "Sign Up for Newsletter";
  include("templates/header.html");
  echo '<script src="js/newsletter.js"></script>';

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if( ($_POST['first-name'] != "") && ($_POST['email'] != "") ){
      $first_name = $_POST['first-name'];
      $last_name = $_POST['last-name'];
      $email = $_POST['email'];
      $mobile = $_POST['mobile-number'];
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
      echo '<div class="text-center">';
      echo "<h4 class=\"mb-3\">Thank You $first_name !</h4>";
      echo "<p>You have succesfully signed up for our newsletter!</p>";
      echo "<p>We will inform you about any new offers regarding: </p>";
      foreach($options as $value){
        echo "<p> - $value </p>";
      }
      echo "<p>All promotions will be sent to you to the following email address: $email </p>";
      if($mobile != "") echo "<p>We will also text you on your mobile: $mobile </p>";
      echo "</div>";
      echo '<script>
              window.addEventListener("load", function(){
                $("form").hide();
              }, false);
            </script>';
    } else {
      echo '<p class="lead text-danger font-weight-bold">Fill in required fields please!</p>';
    }
  }

?>

<form action="newsletter.php" method="post">
  <fieldset class="border border-primary rounded-lg px-5 pb-3">
    <legend class="w-auto">Sign up for Newsletter</legend>
    <div class="row my-3">
      <div class="col"><h5>Please enter your details:</h5></div>
      <div class="col"><h5>What are you interested in:</h5></div>
    </div>
    <div class="row">
      <div class="col">
        <div class="form-group">
          <label for="first-name">*first name:</label>
          <input type="text" class="form-control" name="first-name" placeholder="first name"
          value="<?php if( isset($_POST['first-name']) ) echo $_POST['first-name']; ?>" />
        </div>
        <div class="form-group">
          <label for="last-name">last name:</label>
          <input type="text" class="form-control" name="last-name" placeholder="last name"
          value="<?php if( isset($_POST['last-name']) ) echo $_POST['last-name'] ?>" />
        </div>
        <div class="form-group">
          <label for="email">*email:</label>
          <input type="email" class="form-control needs-validation" name="email" placeholder="email"
          value="<?php if( isset($_POST['email']) ) echo $_POST['email'] ?>" />
        </div>
        <div class="form-group">
          <label for="mobile-number">mobile number:</label>
          <input type="text" class="form-control" name="mobile-number" placeholder="mobile number"
          value="<?php if( isset($_POST['mobile-number']) ) echo $_POST['mobile-number'] ?>" />
        </div>
      </div>
      <div class="col pt-4">
        <div class="row">
          <div class="col-sm-6 pl-5"> <?php createCheckboxGroup($types); ?> </div>
          <div class="col-sm-1"></div>
          <div class="col-sm-5"> <?php createCheckboxGroup($extras); ?> </div>
        </div>
      </div>
    </div>
    <div class="row float-md-right mt-4">
      <input type="submit" value="submit" class="btn btn-primary" />
    </div>
  </fieldset>
</form>

<?php  include("templates/footer.html"); ?>
