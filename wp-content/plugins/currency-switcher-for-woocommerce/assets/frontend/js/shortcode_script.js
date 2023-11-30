jQuery(document).ready(function ($) {
    /*$('.wcc_switcher').select2({
        //placeholder: variables.flags_placeholder,
        templateResult: format,
    });*/
    
    var dd = $('.wcc_switcher').prettyDropdown({
	classic: true,
        customClass: 'wccs_arrow'
    });
    
    $(document).on("change", ".wcc_switcher", function(){
        $(this).closest('form').submit();
    }); 
});

/*function format (option) {
    if (!option.id) { return option.text; }
    var flag = option.element.getAttribute('data-flag');
    var ob = '';
    if(flag){
        ob = jQuery('<span><img width="60" height="40" src="'+flag+'"> ('+option.text+')</span>');
    }else{
        ob = jQuery('<span>'+option.text+'</span>');
    }
    return ob;
};*/