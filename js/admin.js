
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

  //initialize dialog window object used for editing and removing records
  var $dialog = $("#dialog-1").dialog({
    autoOpen: false,
    modal: false,
    width: 520
  });
  var $prog = $("#dialog-2").dialog({
    autoOpen: false,
    modal: false,
    width: 180,
    height: 110,
    resizable: false,
    draggable: false
  });
  $("#dialog-2").prev(".ui-dialog-titlebar").hide();

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
      type: "POST",
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
      //extract sort and start variables to store in hidden inputs
      var sort = getData.match(/sort=(.*?)(?=&|$)/)[1];
      var start = getData.match(/start=(\d*?)(?=&|$)/);
      start = start? start[1]: 0;
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
      var display = "display=" + $("#display").val();
      $("#"+tableName+"_display").val(display);
      var getData = $("#href").val();
      getData = getData.replace(/(display=)(\d*?)(?=&)/, display);
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
      type: "GET",
      success: function(data) {
        dialogCallback(data, address);
        addListeners();
      }
    });
  }

  function dialogCallback(data, address) {
    $dialog.html(data);
    $dialog.dialog("open");
    if ($("input[id^='date']").length > 0) initDatepickers();
    //add event listener on form
    $('#decision').on("submit", (e)=> {
      e.preventDefault();
      //send data in a FormData object in case there is a file upload field
      var postData = new FormData();
      var $fields = $('#decision input');
      $.each($fields, (i, f)=>{
        var $field = $(f);
        if ($field.is(":checkbox")) {
          var isChecked = $field.prop("checked")? "1": "";
          postData.append($field.prop("name"), isChecked);
        } else {
          postData.append($field.prop("name"), $field.val());
        }
      });
      if ($("#image").length > 0) {
        postData.append("image", $("#image")[0].files[0]);
      }
      $.ajax({
        url: address,
        type: "POST",
        data: postData,
        processData: false,
        contentType: false,
        beforeSend: (xhr, settings)=>{
          $prog.dialog("open");
        },
        success: function(data) {
          setTimeout(()=>{
            dialogCallback(data, address);
            addListeners();
          }, 250);
        },
        xhr: ()=>{
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = (e)=>{
            var perc = Math.round(e.loaded/e.total * 100) +"%";
            $prog.html("<p>Loading..</p><p>"+perc+"</p>");
          }
          xhr.upload.onload = (e)=>{
            setTimeout(()=>{$prog.dialog("close");}, 250);
          }
          return xhr;
        }
      });
    });
  }

  function addListeners() {
    var $dateFrom = $("#date_from");
    var $dateTo = $("#date_to");
    $dateFrom.on("change", (e)=>{
      dateInputHandler($dateFrom, $dateTo, true);
    });
    $dateTo.on("change", (e)=>{
      dateInputHandler($dateTo, $dateFrom, false);
    });

    $("#cancel").on("click", (e)=>{
      e.preventDefault();
      $dialog.dialog("close");
    });
    if ($("#ok").length > 0){
      $dialog.dialog("option", "height", "250");
      $("#ok").on("click", ()=>{
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

  function initDatepickers() {
    var $dateFrom = $("#date_from").datepicker({
      dateFormat: "dd-mm-yy",
      minDate: new Date(),
      beforeShowDay: (date)=>{
        var dateTo = $dateTo.datepicker("getDate");
        if (date >= dateTo) return [false, ""];
        return [true, ""];
      }
    });
    var $dateTo = $("#date_to").datepicker({
      dateFormat: "dd-mm-yy",
      minDate: new Date(),
      beforeShowDay: (date)=>{
        var dateFrom = $dateFrom.datepicker("getDate");
        if (date <= dateFrom) return [false, ""];
        return [true, ""];
      }
    });
  }

  function dateInputHandler($input1, $input2, earlier) {
    if (validateDateInput($input1)) {
      var date1 = $input1.datepicker("getDate");
      if (validateDateInput($input2)) {
        var date2 = $input2.datepicker("getDate");
        if ((earlier && (date1 > date2)) || (!earlier && (date1 < date2))) {
          $input2.datepicker("setDate", date1);
        }
      }
    }
  }

  function validateDateInput($dateInput, earlier) {
    var value = $dateInput.val();
    var date = $dateInput.datepicker("getDate");
    if ((!value.match(/^\d{2}-\d{2}-\d{4}$/)) || (date < new Date())) {
      $dateInput.val("");
      return false;
    }
    return true;
  }

});
