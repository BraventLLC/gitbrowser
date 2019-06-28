 /* Fits CkEditor to the Window as well */

      function ckEditorFitWindow(){
          var contents = jQuery("#cke_1_contents");
          if(contents.length){
          var top = jQuery("#cke_1_top");
          var bottom =jQuery("#cke_1_bottom");
            var th = top.height();
            var th_pt = parseInt(top.css("padding-top").replace("px",""));
            var th_pb = parseInt(top.css("padding-bottom").replace("px",""));
            var bh = bottom.height();
            var bh_pt = parseInt(bottom.css("padding-top").replace("px",""));
            var bh_pb = parseInt(bottom.css("padding-bottom").replace("px",""));
            var topsize = th+th_pt+th_pb;
            var botsize = bh+bh_pt+bh_pb;
            //console.log(topsize+"/"+botsize);
            contents.css("height","calc(100vh - "+((botsize+topsize)+6)+"px)");
            document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
           }
      }

      jQuery(window).on("resize scroll",function(){
           ckEditorFitWindow();
      });

      var fitsOnInterval = setInterval(function(){
          if(jQuery("#cke_1_contents").length){
              ckEditorFitWindow();
          }
      }, 1500);


