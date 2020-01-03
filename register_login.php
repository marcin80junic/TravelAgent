<?php #register_login.php

  //title the page, include a header and javascript
  $page_title = "Register/Login";
  include("templates/header.php");

?>

<form id="login_form" action="register_login.php" method="post">
  <div id="login_div" class="text-center w-50 p-4 mx-auto border border-primary">
    <h3>Login</h3>
    <div class="input-group my-4">
      <div class="input-group-prepend">
        <span class="input-group-text">email</span>
      </div>
      <input type="email" class="form-control" autocomplete="username">
    </div>
    <div class="input-group my-4">
      <div class="input-group-prepend">
        <span class="input-group-text">password</span>
      </div>
      <input type="password" class="form-control" autocomplete="current-password">
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
  </div>
</form>

<div class="text-center mt-5">
  <h5 class="mb-4">OR..</h5>
  <a id="register" href="register.php" class="lead font-weight-bold">Register an account</a>
</div>

<?php

//include footer navigation
include("templates/footer.html");

?>
