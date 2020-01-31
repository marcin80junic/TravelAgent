<?php

  session_start();

  $page_title = "Contact us";
  include("templates/header.php");
  echo '<script src=js/contact.js></script>';


  if ($_SERVER['REQUEST_METHOD'] === "POST") {

    function spam_cleaner($value) {
      $spam = ['to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:',
              'multipart-mixed:', 'content-transfer-encoding:'];
      foreach($spam as $s) {
        if (stripos($value, $s) !== false) {
          return '';
        }
      }
      $value = str_replace(["\r", "\n", "%0a", "%0d"], ' ', $value);
      return trim($value);
    }

    $clean = array_map('spam_cleaner', $_POST);
    $errors = [];

    if (empty($clean['subject'])) {
      $errors[] = "Fill in the subject please!";
    }
    if (empty($clean['comments'])) {
      $errors[] = "Fill in the comments please!";
    }

    if (empty($errors)) {
      $body = "Comments: \n\r\n\r\t{$clean['comments']}";
      if (!empty($clean['email'])) {
        $body .= "\n\r\n\r\tRespond to: '{$clean['email']}'";
      }
      $from = "From: postmaster@localhost.com\r\nContent-Description: Website Comments Form";
      $body = wordwrap($body, 70);
      mail('junic@localhost.com', $clean['subject'], $body, $from);
      $clean = [];
    }

  }

 ?>

 <div class="w-50 mx-auto mb-3">
   <div class="mb-5">
     <h2 class="text-center">Contact us</h2>
     <h6 class="text-justify m-3">If you feel that we missed something or you're not entirely happy with
       our services... or you simply thing that we are doing a good job, please
       don't hesitate to contact us. Just fill in the contact form and press
       send! If you will provide an email address we will get back to you as
       soon as possible!</h6>
   </div>
   <form action="contact.php" method="post">
     <fieldset class="border border-info rounded-lg p-3">
       <legend class="border border-info text-center w-auto px-2">Contact form</legend>
       <div class="form-group">
         <label for="subject">Subject:</label>
         <input class="form-control" type="text" name="subject" id="subject"
            value="<?php if (isset($_POST['subject'])) echo $_POST['subject']; ?>">
       </div>
       <div class="form-group">
         <label for="comments">Comments:</label>
         <textarea class="form-control" name="comments" id="comments" rows="5"
          placeholder="we value your feedback"><?php if (isset($_POST['comments']))
          echo trim($_POST['comments']); ?></textarea>
        </div>
        <div class="form-group">
          <label for="email">Your email:</label>
          <input class="form-control" type="email" name="email" id="email"
            value="<?php if (isset($_POST['email'])) {
                          echo $_POST['email'];
                        }
                        elseif (isset($_SESSION['agent'])) {
                          echo $_SESSION['email'];
                        } ?>">
        </div>
        <div class="text-center mt-4">
          <input type="submit" class="btn btn-info" value="Send">
        </div>
      </fieldset>
    </form>
  </div>

<?php

  echo '<div class="text-center font-weight-bold">';

  if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (!empty($errors)) {
      foreach($errors as $err) {
        echo '<p class="text-danger">'.$err.'</p>';
      }
    } else {
      echo '<p>Your comments have been submitted</p>';
      if (isset($_POST['email'])) {
        echo "<p>We will get in touch with you on '{$_POST['email']}' as soon as we can</p>";
      }
    }
  }

  echo '</div>';

  include("templates/footer.html");

?>
