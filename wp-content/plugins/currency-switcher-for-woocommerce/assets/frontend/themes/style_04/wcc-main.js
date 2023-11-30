jQuery(document).ready(function($){
    /* Do slide function when click main button-------------- */
    $("#wcc-currency-modal .wcc-list li").on("click", function () {
        var selectedItemHTML = $(this).html();
        $("#wcc-currency-modal .wcc-list").find(".crnt").removeClass("crnt");
        $(this).addClass("crnt");        
        $("#wcc-switcher-style-04 .wcc-crnt-currency").html(selectedItemHTML);
    });

});

/* Theme 4 Popup modal function ------------------------- */
var modal = jQuery("#wcc-currency-modal");
var btn = jQuery("#wcc-switcher-style-04 .wcc-crnt-currency");

// Get the <span> element that closes the modal
var span = jQuery(".wcc-currency-modal-close");

// When the user clicks the button, open the modal
btn.on('click', function (e) {
    modal.show();
    e.preventDefault();
    e.stopPropagation();
});

span.on('click', function () {
    modal.hide();
})

jQuery('.wcc-currency-modal-content').on('click', function (e) {
    /*e.preventDefault();
    e.stopPropagation();*/
});


jQuery(window).on('click', function () {
    modal.hide();
});

jQuery(document).ready(function($){
    $(document).on('click', '#wcc-currency-modal ul li', function(){
        var code = $(this).data('code');
        $('.wcc_switcher_form_04 .wcc_switcher').val(code);
        $('form.wcc_switcher_form_04').submit();
        //setTimeout(function(){ $('form.wcc_switcher_form_04').submit(); }, 500);
    });
});