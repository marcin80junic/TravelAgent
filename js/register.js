(function(){
  window.addEventListener("load", ()=>{
    $firstName = $("[name='f_name']");
    $lastName = $("[name='l_name']");
    $email = $("[name='email']");
    $password = $("[name='password']");
    $passFeedback = $("#password_validation");
    $confirmPassword = $("[name='confirm_password']");
    $confirmPassFeedback = $("#confirm_password_validation");
    $privacy = $("[name='privacy']");

    $password.on('keyup', ()=>{
      var len = $password.val().length;
      if(len == 0) {
        $passFeedback.removeClass("text-danger text-success");
        $passFeedback.text("(at least 8 characters)");
      }
      else if(len < 8) {
        $passFeedback.removeClass("text-success");
        $passFeedback.addClass("text-danger");
        $passFeedback.text("(another "+(8-len)+" characters left)");
      }
      else {
        $passFeedback.removeClass("text-danger");
        $passFeedback.addClass("text-success");
        $passFeedback.text("password valid");
      }
      $confirmPassword.keyup();
    });

    $confirmPassword.on('keyup', ()=>{
      var password = $password.val();
      var passwordConfirmation = $confirmPassword.val();
      if(passwordConfirmation === "") {
        $confirmPassFeedback.removeClass("text-danger text-success");
        $confirmPassFeedback.text("(must match the password)");
      }
      else if (passwordConfirmation !== password) {
        $confirmPassFeedback.removeClass("text-success");
        $confirmPassFeedback.addClass("text-danger");
        $confirmPassFeedback.text("(doesn't match the password!)");
      }
      else {
        $confirmPassFeedback.removeClass("text-danger");
        $confirmPassFeedback.addClass("text-success");
        $confirmPassFeedback.text("match!");
      }
    });

    $("#register_form").on("submit", (e)=>{
      validateField(e, $firstName);
      validateField(e, $lastName);
      validateField(e, $email);
      validateField(e, $password);
      validateField(e, $confirmPassword);
    });

    $password.keyup();
    $confirmPassword.keyup();
  });

  function validateField(e, $element) {
    if($element === $password) {
      if($passFeedback.is(".text-danger")) {
        $element.addClass("border border-danger");
        e.preventDefault();
        return;
      }
    }
    var value = $element.val();
    if(value != "") {
      $element.removeClass("border border-danger");
    } else {
      e.preventDefault();
      $element.addClass("border border-danger");
    }
  }
}())
