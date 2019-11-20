function toast(type, message, title) {
    toastr.options = {
        "closeButton": true,
        "debug": true,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    toastr[type](message, title);
}

var validateRequiredFields=function(f){
    var stat=true;
    $('input[required],textarea[required],select[required]',f).removeClass('invalid');
    $('label',f).removeClass('data-error');
    $('input[required],textarea[required],select[required]',f).each(function(){
            if($(this).val()==""){
                $(this).next().next().html($(this).data('error') + ' is required!');
                $(f).find('input:first').focus();
                stat = false;
            }
    });
    return stat;
};

var clearFields=function(f){
    $('input,textarea',f).val('');
    $(f).find('input:first').focus();
};

var showSpinningProgress = function(e,type,text='Save'){
  if(type) {
    e.html('<div class="custom-spinner spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>');
  }else {
    e.html(text);
  }
    
};