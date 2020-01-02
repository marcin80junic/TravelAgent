$(function() {
  var $dialog = $('#dialog-1').dialog({
                 autoOpen: false,
                 modal: true,
                 height: 300,
                 width: 500,
                 title: "Some title"
             });
  $( ".remove" ).click(function(e) {
    e.preventDefault();
    $this = $(e.target);
    var page = $this.attr('href');
    $('#external-frame').attr('src', page);
    $dialog.dialog('option', 'title', 'confirm remove');
    $dialog.dialog('open');
    $( "#dialog-1" ).dialog( "open" );
  });
});
