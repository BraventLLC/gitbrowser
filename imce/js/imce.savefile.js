setTimeout(function()
    {
        var editor = CKEDITOR.instances['editor'];
        editor.on("change", function()
            {
                console.log("changed");
                if(editor.getData() !== window["current_data"]) {
                    jQuery("#save-content").removeClass("disabled");
                }

            }
        );
    }, 3000
);

jQuery("#save-content").click(function()
    {
        var btn = jQuery(this);
        if(!btn.hasClass("disabled")) {
            SwalConfirm.fire({text: "You want to save the new file data?"}).then(function(confirm)
                {
                    if(confirm.value) {
                        deleteBeforeSave();
                    }
                }
            );
        }
    }
);

function deleteBeforeSave() {
    var directory = imce.conf.dir;
    var data = {
    jsop: "delete",
    filenames: window["current_file"],
    form_id: jQuery("#imce-fileop-form [name='form_id']").val(),
    form_token: jQuery("#imce-fileop-form [name='form_token']").val(),
    form_build_id: jQuery("#imce-fileop-form [name='form_build_id']").val()
    };
    SwalLoading.fire({text:"File is now uploading..."});
    var jqxhr = jQuery.post(jQuery("#imce-fileop-form").attr("action")+"?jsop=delete&dir="+directory, data, function()
        {
            //alert( "success" );
        }
    ) .done(function() {
            //alert( "second success" );
        }
    ) .fail(function() {
            //alert( "error" );
        }
    ) .always(function() {
            savenewDataFile();
            //alert( "finished" );
        }
    );
}

function savenewDataFile() {

    var directory = imce.conf.dir;
    var filename = window["current_file"];
    var s = CKEDITOR.instances["editor"].getData();
    var formData = new FormData();
    formData.append('files[imce]', new File([new Blob([s])], filename));
    formData.append('op', 'upload');
    formData.append('html_response', '0');
    formData.append('form_build_id', jQuery("#imce-upload-form [name='form_build_id']").val());
    formData.append('form_token', jQuery("#imce-upload-form [name='form_token']").val());
    formData.append('form_id', jQuery("#imce-upload-form [name='form_id']").val());

    jQuery.ajax(
        {
        url: jQuery("#imce-upload-form").attr("action")+"?jsop=upload&dir="+directory,
        type: 'POST',
        data: formData,
        always:function(){
          Swal.close();
        },
        success: function(data) {
                //console.log(data);
                if(!data || !data.messages || !data.messages.status.length) {
                    SwalError.fire({text: "Critical error on system..."});
                    return;
                }
                if(data.messages.status[0].toLowerCase().indexOf("uploaded") !== -1) {
                    SwalSuccess.fire({text: "The new file data has been saved successfully!"});
                }else {
                    SwalError.fire({text: "An error occurred while trying to save the new data from the file ..."});
                }
            },
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {// Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                myXhr.onload = function () {
                    //alert("Enviado");
                };
                if (myXhr.upload) {// Avalia se tem suporte a propriedade upload
                    myXhr.upload.addEventListener('progress', function()
                        {
                            /* faz alguma coisa durante o progresso do upload */
                        }, false
                    );
                }
                return myXhr;
            }
        }
    );

}