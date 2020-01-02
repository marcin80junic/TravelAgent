(function(){
  window.addEventListener("load", ()=>{

    $('.select_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().find('[type="checkbox"]').attr('checked', 'true');
    });
    $('.clear_all').on('click', (e)=>{
      e.preventDefault();
      $this = $(e.target);
      $this.parent().parent().find('[type="checkbox"]').removeAttr('checked');
    })

  });

}())
