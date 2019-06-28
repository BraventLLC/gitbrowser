jQuery(".edit-item-action").unbind("dblclick").on("dblclick",function(){
    var btn = jQuery(this);
    var file = decodeURIComponent(btn.attr("data-name"));
    var current_dir = decodeURIComponent(imce.conf.dir);
    var current_url =  decodeURIComponent(imce.conf.furl);
    var c_url = encodeURIComponent(current_url+current_dir+"/"+file);
    console.log(decodeURIComponent(c_url));

     SwalConfirm.fire({text:"You want to load this file data?"}).then(function(confirm){
         if(confirm.value){
             var jqxhr = $.post(decodeURIComponent(c_url), function(data) {
                 console.log(data);
  //alert( "success" );
},"text")
  .done(function(data) {
      window["current_file"] = file;
      jQuery("#file-list .active-item").removeClass("active-item");
      jQuery(btn).addClass("active-item");
      CKEDITOR.instances["editor"].setData(data);
      window["current_data"] = CKEDITOR.instances["editor"].getData();
      //console.log(data);
    //alert( "second success" );
  })
  .fail(function() {
    SwalError.fire({text:"Failed to fetch file data"});
  })
  .always(function() {
    //alert( "finished" );
  });
         }
     });

});