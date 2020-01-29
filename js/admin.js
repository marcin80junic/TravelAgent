
function displayImage(image, width, height) {
  width /= 6;
  height /= 6;
  var top = ($(window).height() - height) / 2;
  var left = ($(window).width() - width) / 2;
  var url = "php/show_image.php?image="+image;
  var specs = "location=no,menubar=no,toolbar=no,resizable=yes,left="+left+",top="+top+",width="+width+",height="+height;
  var popup = window.open(url, image, specs);
  popup.focus();
}


$(function() {
//  alert("jow");
  $(document).ready(()=>{

    //initialize dialog window object used for editing and removing records
    $dialog = $('#dialog-1').dialog({
      autoOpen: false,
      modal: true,
      width: 520
    });


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

    $("form").on("submit", (e)=>{
      e.preventDefault();
      var tableName = $(e.target).find('input[name="table_name"]').val();
      var postData = $(e.target).serialize();
      $.ajax({
        url: "php/admin_table.php",
        type: "post",
        data: postData,
        success: (data)=>{
          tablePostCallback(data, tableName);
        }
      });
    });

    //recursive function setting up admin table listeners
    function tablePostCallback(data, tableName) {
      $("#main-table").html(data);
      var offsetY = $("#main-table").offset().top - $(".sticky-top").height();
      if (offsetY > $(window).scrollTop()) {
          window.scrollTo(0, offsetY);
      }


      //listener for table headers sorting links
      $("#admin-table-head a, #table-navigation a").on('click', (e)=>{
        e.preventDefault();
        var getData = e.target.href? e.target.href: $(e.target).parent().prop("href");
        getData = getData.substring(getData.lastIndexOf("/")+1);
        var sort = getData.substring(getData.indexOf("sort=")+5);
        if (sort.indexOf("&") !== -1) {
          sort = sort.substring(0, sort.indexOf("&"));
        }
        var start = getData.substring(getData.indexOf("start=")+6);
        if (start.indexOf("&") !== -1) {
          start = start.substring(0, start.indexOf("&"));
        }
        start = decodeURI(start);
        sort = decodeURI(sort);
        $("#"+tableName+"_sort").val(sort);
        $("#"+tableName+"_start").val(start);
        $.ajax({
          url: "php/admin_table.php",
          data: getData,
          type: "get",
          success: (data)=>{
            tablePostCallback(data, tableName);
          }
        });
        return false;
      });
      //listener for select box changing number of rows per page
      $("#display").on("change", (e)=>{
        var display = $("#display").val();
        $("#"+tableName+"_display").val(display);
        var getData = $("#href").val();
        var temp = getData.substring(getData.indexOf("display=")+1);
        var end = temp.substring(temp.indexOf("&"));
        var start = getData.substring(0, getData.indexOf("display=")+8);
        getData = start+display+end;
  //      getData = getData.replace(/(?<=display\=)(.*?)(?=\&)/, display);
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
        display_dialog(e, "confirm remove", "500");
      });
      $(".edit").on("click", (e)=>{
        display_dialog(e, "edit record", "500");
      });
    }

    function display_dialog(e, title, height) {
      e.preventDefault();
      var href = e.target.href? e.target.href: e.target.parentElement.href;
      var address = href.substring(0, href.indexOf("?"));
      var data = href.substring(href.indexOf("?")+1);
      $dialog.dialog("option", {title: title, height: height});
      $.ajax({
        url: address,
        data: data,
        type: "get",
        success: function(data) {
          dialogCallback(data, address);
          addListeners();
        }
      });
    }

    function dialogCallback(data, address) {
      $dialog.html(data);
      $dialog.dialog("open");
      $("input[id^='date']").datepicker({dateFormat: "dd-mm-yy"});
      //add event listener on form
      $('#decision').on("submit", (e)=> {
        e.preventDefault();
        //send data in a FormData object in case there is a file upload field
        var postData = new FormData();
        var $fields = $('#decision input');
        $.each($fields, (i, f)=>{
          var $field = $(f);
          if ($field.is(":checkbox")) {
            if ($field.prop("checked")) {
              postData.append($field.prop("name"), true);
            }
          } else {
            postData.append($field.prop("name"), $field.val());
          }
        });
        if ($("#image").length > 0) {
          postData.append("image", $("#image")[0].files[0]);
        }
        $.ajax({
          url: address,
          type: "post",
          data: postData,
          processData: false,
          contentType: false,
          success: function(data) {
            dialogCallback(data, address);
            addListeners();
          }
        });
      });
    }

    function addListeners() {
      $("#cancel").on("click", (e)=>{
        e.preventDefault();
        $dialog.dialog("close");
      });
      var $okButton = $("#ok");
      if ($okButton.length > 0){
        $dialog.dialog("option", "height", "250");
        $okButton.on("click", ()=>{
          $dialog.dialog("close");
          var tableName = $("#main-table h3").text();
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
