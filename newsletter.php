<?php #newsletter.php

  session_start();

  //title the page, include site's menu
  $page_title = "Sign up for a newsletter";
  include("templates/header.php");

  function send_confirmation_email($dest) {
    $subject = "Europe Travel Experts Newsletter";
    $body = "You have successfully signed up for our newsletter!\nYou will
            receive chosen offers to $dest every now and then :)";
    $body = wordwrap($body, 70);
    $from = "From: postmaster@localhost.com";
    $dest = "dest@localhost.com";  //line to be removed before publishing
    if (@mail($dest, $subject, $body, $from)) {
      echo "<p>email has been sent to $dest, $from</p>";
    } else {
      echo "<p>mail have not been sent, the mail server is off</p>";
    }
  }


  //check if the form has been submitted
  if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $news = [];

    //if the email field has been filled in open connection to the database,
    //check if the email is already in database, if not add it to newsletter table
    if(!empty($_POST['email'])) {
      require("../../../xxsecure/dbconnect.php");
      require("php/mysql_querries.php");
      $email = mysqli_real_escape_string($dbconnect, trim($_POST['email']));
      if (is_email_unique($dbconnect, "newsletter", $email)) {
        $length = count(NEWSLETTER_COLUMNS);
        for($i=0; $i<$length; $i++) {
          $news[] = 1;
        }
        $response = newsletter_insert($dbconnect, $email, $news);
        if($response) {
          echo '<div class="text-center"><h3 class="mb-3">Congratulations</h3>';
          echo '<p>You have succesfully signed up for our newsletter!</p>';
          echo "<p>You will receive special offers on following email address:
                <strong>$email</strong></p>";
          send_confirmation_email($email);
          echo "</div>";
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

<div class="text-center w-50 m-auto">
  <div>
    <h6 class="text-justify mb-4">Feel like you want to go somewhere but just can't see the offer which
      hits the spot? You don't have to register an account, just sign up for our newsletter and you will be first
      one to know about any last minute deals and exclusive offers!</h6>
  </div>
  <form action="newsletter.php" method="post">
    <fieldset class="p-5 border border-primary rounded-lg">
      <h3>Sign up for our newsletter!</h3>
      <h6 class="mt-3 mb-5">Make sure that you will not miss on any special offers</h6>
      <div class="input-group">
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
