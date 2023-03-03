(function($) {

$(".js-move").click(function(){

    const $that = $(this);
    const $parent = $that.closest(".service__item");

    var data = {
        'action': 'ct_ultimate_gdpr_wizard_ajax1',
        'postid': $parent.attr("data-id"),
        'serviceid' : $that.attr("data-serviceid")
    };

    $.post(ajaxurl, data, function(response) {

        if(response['success'] === true) {
            const $target = $(".js-group__item" + $that.attr("data-serviceid"));
            $($parent.detach()).appendTo($target);
       
        }

    });
});


$(".js-submit").click(function(){
    $(".js-form-wizard #submit5").click();
});

$(".js-save-and-go").click(function(){
    const $that = $(this);
    const backUrl = $that.attr("href");

    $('.js-form-wizard input[name=redirectToUrl]').val(backUrl);

    $(".js-form-wizard #submit5").click();
});

$(".js-select-service").change(function(){
   const val = $(this).val();
   const divID = "#"+ val;

   $(".service-elem").addClass("sr-only");

   if($(divID).length){
       $(divID).removeClass("sr-only");
   }
});


$('select').each(function () {
    $(this).niceSelect();
});

if ( $( '.form-check' ).length ) {
    var $this = $( '.form-check' );
    $this.find( 'input[type="checkbox"]' ).each( function() {
        var $this = $( this );
        $this.css( 'display', 'none' );
        // var $html = '<div class="ct-ultimate-gdpr-form-check"><div class="ct-ultimate-gdpr-checkbox-switch"><span class="on label">'+ct_ultimate_gdpr_admin_translations.enable+'</span><span class="off label">'+ct_ultimate_gdpr_admin_translations.disable+'</span><span class="switch">\'+ct_ultimate_gdpr_admin_translations.enable+\'</span></div></div>';
        // $( $html ).insertAfter( $this );

        if ( $this[0].hasAttribute( 'checked' )  ) {
            $this.next().find( '.switch' ).css( 'left', '0' ).text( ct_ultimate_gdpr_admin_translations.enabled );
        } else {
            $this.next().find( '.switch' ).css( 'left', '50%' ).text( ct_ultimate_gdpr_admin_translations.disabled );
        }
    } );

    $( '.ct-ultimate-gdpr-checkbox-switch .off' ).on( 'click', function () {
        var $this = $( this );
        $this.parent().parent().prev().removeAttr( 'checked' );
        $this.next().css( 'left', '50%' ).text( ct_ultimate_gdpr_admin_translations.disabled );
    });
    $( '.ct-ultimate-gdpr-checkbox-switch .on' ).on( 'click', function () {
        var $this = $( this );
        $this.parent().parent().prev().attr( 'checked', 'checked' );
        $this.next().next().css( 'left', '0' ).text( ct_ultimate_gdpr_admin_translations.enabled );
    });
}

})( jQuery );