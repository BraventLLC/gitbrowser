var SwalCustomClass = {
        confirmButton: 'btn btn-primary mr-2',
        cancelButton: 'btn btn-secondary'
};

var SwalError = Swal.mixin({
    type:"error",
    title:"Something went wrong...",
    confirmButtonText: "Close",
    showCancelButton:false,
    customClass: SwalCustomClass,
    buttonsStyling: false,
});

var SwalInfo = Swal.mixin({
    type:"info",
    title:"Application says...",
    confirmButtonText: "I understood",
    showCancelButton:false,
    customClass: SwalCustomClass,
    buttonsStyling: false,
});

var SwalConfirm = Swal.mixin({
    type:"question",
    title:"Application requests...",
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
    showCancelButton:true,
    customClass: SwalCustomClass,
    buttonsStyling: false,
});

var SwalSuccess = Swal.mixin({
    type:"success",
    title:"Application says...",
    confirmButtonText: "Confirm",
    showCancelButton:false,
    customClass: SwalCustomClass,
    buttonsStyling: false,
});

var SwalLoading =  Swal.mixin({
    type:"info",
    title:"Application is running...",
    showCancelButton:false,
    showConfirmButton:false,
    allowOutsideClick:false,
    allowEscapeKey:false,
    allowEnterKey:false,
    
});