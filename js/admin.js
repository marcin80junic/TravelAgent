
$(function() {

  var $dialog = $('#dialog-1').dialog({
                 autoOpen: false,
                 modal: true,
                 width: 500,
                 title: ""
                });

  function makeRequest(address, p_data) {
    var xhr = new XMLHttpRequest();
    if(p_data) {
      var data_query = address+"&"+p_data;
      xhr.open('POST', address, true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.send(data_query);
  //    alert("POST: "+data_query);
    } else {
      xhr.open('GET', address, true);
      xhr.send(null);
    }
    xhr.onload = function() {
      if(xhr.status === 200) {
        $dialog.dialog("open");
        $dialog.html(xhr.responseText);
        var table = $('[name="table"]').val();
        var $buttons = $('#decision [type="submit"]');
        if($buttons) {
          $buttons.on("click", (e)=> {
            e.preventDefault();
            var decision = $(e.target).val();
            var postData = decision+"="+decision;
            var $textInputs = $(e.target).parents("form").find("input").filter(":not(:checkbox)");
            var $checkboxes = $(e.target).parents("form").find("input:checkbox");
            $textInputs.each(function(i) {
              var $text = $(this);
              postData += "&"+$text.prop("name")+"="+$text.val();
            });
            $checkboxes.each(function(i) {
              var $box = $(this);
              postData += "&"+$box.prop("name")+"="+$box.is(":checked");
            });
            var xhr2 = new XMLHttpRequest();
            xhr2.open('POST', address, true);
            xhr2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr2.send(postData);
      //      alert("POST: "+postData);
            xhr2.onload = function() {
              if(xhr2.status === 200) {
                $dialog.dialog("option", "title", "action submitted");
                $dialog.dialog("option", "height", "250");
                $dialog.html(xhr2.responseText);
                $('#ok').on("click", ()=> {
                  $('.offset').val(window.pageYOffset);
                  $dialog.dialog("close");
                  $('#'+table+'-form').click();
                });
              }
              else {
          //      alert("making another request");
                makeRequest(address, postData);
              }
            };
          });
        }
      }
    };
  }

  function display_dialog(e, title, height) {
    e.preventDefault();
    var $this = $(e.target);
    var address = $this.attr("href");
    var table = address.substring(address.indexOf("=")+1, address.indexOf("&"));
    $dialog.dialog("option", "title", title);
    $dialog.dialog("option", "height", height);
    makeRequest(address);
  }

  $(".remove").on("click", (e)=> {
    display_dialog(e, "confirm remove", "250");
  });

  $(".edit").on("click", (e)=> {
    display_dialog(e, "edit record", "440");
  });

});

(function() {

  $(document).ready(()=>{
  //alert("refreshed");
    window.scrollTo(0, $('.offset').val());

    $('.select_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().find('input:checkbox').prop('checked', true);
    });

    $('.clear_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().find('input:checkbox').prop('checked', false);
    });

    $('[id$=form]').on('click', ()=>{
      var offsetY = $("#interface").offset().top + $("#interface").height() - $('.sticky-top').height();
      if($(".offset").val() < offsetY) {
        $(".offset").val(offsetY);
      };
    });

  });
}());
