
(function() {

  $(document).ready(()=>{

    //organize navbar highlights
    $menu = $("#navigation li");
    var path = window.location.pathname;
    var fileName = path.substring(path.lastIndexOf("/")+1, path.indexOf("."));
    if(fileName.indexOf("_") > 0) {
      fileName = fileName.substring(0, fileName.indexOf("_"));
    }
    $menu.filter("#"+fileName).addClass("bg-success");

  });

}())
