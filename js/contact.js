(function() {

  $(document).ready( ()=> {

    var $subject = $("#subject");
    var $comments = $("#comments");

    $("form").on("submit", (e)=>{
    //  $.scrollTo('+=50px', 800, { axis:'y' });
      if ($subject.val() === "") {
        $subject.addClass("border border-danger");
        e.preventDefault();
      }
      if ($comments.val() === "") {
        $comments.addClass("border border-danger");
        e.preventDefault();
      }
    });

    $subject.on("focus", (e)=>{
      $subject.removeClass("border border-danger")
    });

    $comments.on("focus", (e)=>{
      $comments.removeClass("border border-danger")
    });

  });
}());
