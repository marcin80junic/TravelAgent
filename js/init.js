(function() {
  window.addEventListener('load', ()=>{
    $menu = $("#navigation li");
    var path = window.location.pathname;
    var fileName = path.substring(path.lastIndexOf("/")+1, path.indexOf("."));
    $menu.filter("#"+fileName).addClass("bg-success");
  }, false);
}())
