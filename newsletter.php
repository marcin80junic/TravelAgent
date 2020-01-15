<?php

  //title the page, include site's menu
  $page_title = "Sign up for a newsletter";
  include("templates/header.php");

  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $news = [];

    //if the email field has been filled in open connection to the database,
    //check if the email is already in database, if not add it to newsletter table
    if(!empty($_POST['email'])) {
      require("../../../../xxsecure/dbconnect.php");
      require("php/mysql_querries.php");
      $email = mysqli_real_escape_string($dbconnect, trim($_POST['email']));
      if(newsletter_is_unique_email($dbconnect, $email)) {
        $length = count(NEWSLETTER_COLUMNS);
        for($i=0; $i<$length; $i++) {
          $news[] = 1;
        }
        $response = newsletter_insert($dbconnect, $email, $news);
        if($response) {
          echo '<div class="text-center"><h3 class="mb-3">Congratulations</h3>';
          echo '<p>You have succesfully signed up for our newsletter!</p>';
          echo "<p>You will receive special offers on following email address:
                <strong>$email</strong></p></div>";
        } else {
          echo '<h4>System error</h4><p class="error">You coud not be signed up for a newsletter due
                to a system error. We apologise for any inconvenience.<br>Please try again later..</p>';
          echo "<p>".mysqli_error($dbconnect)."</p>";
        }
      } else {
        echo '<p class="lead text-danger text-center font-weight-bold">
              You have already signed up for our newsletter!</p>';
      }
      mysqli_close($dbconnect);
      include("templates/footer.html");
      exit();
    }

    //else display error message
    else {
      echo '<p class="lead text-danger text-center font-weight-bold">
            Please anter your email address</p>';
    }
  }

?>

<div class="text-center">
  <form action="newsletter.php" method="post">
    <fieldset class="w-75 mx-auto my-5 p-4 border border-primary rounded-lg">
      <h3>Sign up for our newsletter</h3>
      <h6 class="mt-3 mb-5">Make sure that you will not miss on any special offers!</h6>
      <div class="input-group w-75 mx-auto">
        <div class="input-group-prepend">
          <span class="input-group-text">email</span>
        </div>
        <input type="email" name="email" class="form-control" maxlength="120">
        <div class="input-group-append">
          <button type="submit" class="btn btn-primary">Sign Up</button>
        </div>
      </div>
    </fieldset>
  </form>
</div>

<?php

  include("templates/footer.html");

?>
