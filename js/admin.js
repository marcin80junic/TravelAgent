

$(function() {
//  alert("jow");
  $(document).ready(()=>{

    $('.select_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().parent().find('input:checkbox').prop('checked', true);
    });

    $('.clear_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().parent().find("input:checkbox").prop("checked", false);
    });

    //initialize dialog window object used for editing and removing records
    var $dialog = $('#dialog-1').dialog({
      autoOpen: false,
      modal: true,
      width: 500,
    });

    $("form").on("submit", (e)=>{
      e.preventDefault();
      var tableName = $(e.target).find('input[name="table_name"]').val();
      var postData = $(e.target).serialize();
      $.ajax({
        url: "php/admin_table.php",
        data: postData,
        type: "post",
        success: (data)=>{
          tablePostCallback(data, tableName);
        }
      });
    });

    //recursive function setting up admin table listeners
    function tablePostCallback(data, tableName) {
      $("#table").html(data);
      window.scrollTo(0, $("#table").offset().top - $(".sticky-top").height());

      //listener for table headers sorting links
      $("#admin-table-head a, #table-navigation a").on('click', (e)=>{
        e.preventDefault();
        var getData = e.target.href? e.target.href: $(e.target).parent().prop("href");
        getData = getData.substring(getData.lastIndexOf("/")+1);
        $.ajax({
          url: "php/admin_table.php",
          data: getData,
          type: "get",
          success: (data)=>{
            tablePostCallback(data, tableName);
          }
        });
      });
      //listener for select box changing number of rows per page
      $("#display").on("change", (e)=>{
        var display = $("#display").val();
        $("#"+tableName+"_display").val(display);
        var getData = $("#href").val();
        getData = getData.replace(/(?<=display\=)(.*?)(?=\&)/, display);
        $.get("php/admin_table.php", getData, function(data) {
          tablePostCallback(data, tableName);
        });
      });
      //listener for add record button
      $("#add-record").on("click", (e)=>{
        display_dialog(e, "add new record", "500");
      });
      //listeners for edit and remove record links
      $(".remove").on("click", (e)=>{
        display_dialog(e, "confirm remove", "250");
      });
      $(".edit").on("click", (e)=>{
        display_dialog(e, "edit record", "440");
      });
    }

    function display_dialog(e, title, height) {
      e.preventDefault();
      var href = e.target.href? e.target.href: e.target.parentElement.href;
      var address = href.substring(0, href.indexOf("?"));
      var data = href.substring(href.indexOf("?")+1);
      var type = (data.indexOf("no=no") == -1)? "get": "post";
      $dialog.dialog("option", "title", title);
      $dialog.dialog("option", "height", height);
      $.ajax({
        url: address,
        data: data,
        type: type,
        success: function(data) {
          dialogCallback(data, address);
          addListeners();
        }
      });
    }

    function dialogCallback(data, address) {
      $dialog.html(data);
      $dialog.dialog("open");
      //add event listener on form
      $('#decision').on("submit", (e)=> {
        e.preventDefault();
        var postData = $(e.target).serialize();
        $.ajax({
          url: address,
          type: "post",
          data: postData,
          success: function(data) {
            dialogCallback(data, address);
            addListeners();
          }
        });
      });
    }

    function addListeners() {
      $("#cancel").on("click", (e)=>{
        display_dialog(e, "action cancelled", "200");
      });
      var $okButton = $("#ok");
      if ($okButton.length > 0){
        $dialog.dialog("option", "height", "200");
        $okButton.on("click", ()=>{
          $dialog.dialog("close");
          var tableName = $("#table h3").text();
          $('[name="'+tableName+'"]').click();
        });
      } else if ($("#confirm-remove").length > 0) {
        $dialog.dialog("option", "height", "250");
      } else {
        $dialog.dialog("option", "height", "500");
      }
    }
  });
}());
