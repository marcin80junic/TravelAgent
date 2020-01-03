
$(function() {

  var $dialog = $('#dialog-1').dialog({
                 autoOpen: false,
                 modal: true,
                 height: 280,
                 width: 500,
                 title: ""
                });
  $( ".remove" ).click(function(e) {
    e.preventDefault();
    var $this = $(e.target);
    var address = $this.attr('href');
    var table = address.substring(address.indexOf("=")+1, address.indexOf("&"));
    $('.offset').val(window.pageYOffset);
    $dialog.dialog('option', 'title', 'confirm remove');
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if(xhr.readyState === 4 && xhr.status === 200){
        $('#dialog-1').html(xhr.responseText);
        var table = $('[name="table"]').val();
        var id = $('[name="id"]').val();
        var $buttons = $('#decision [type="submit"]');
        if($buttons) {
          $buttons.on('click', (e)=>{
            e.preventDefault();
            var decision = $(e.target).val();
            var xhr2 = new XMLHttpRequest();
            xhr2.onload = function() {
              if(xhr2.status === 200) {
                $('#dialog-1').html(xhr2.responseText);
                $('#ok').on('click', ()=>{
                  $dialog.dialog("close");
                  $('#'+table+'-form').click();
                });
              }
            };
            xhr2.open('POST', address, true);
            xhr2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr2.send(decision+'='+decision+'&table='+table+'&id='+id);
          });
        }
        $dialog.dialog("open");
      }
    };
    xhr.open('GET', address, true);
    xhr.send(null);
  });

});

(function() {

  window.addEventListener("load", ()=>{

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
