jQuery(document).ready(function ($) {
    if (jQuery().tooltipster) {

        $('.track-button').tooltipster();

        if ( ywot.tooltip == 'yes' )
            $(document).on('mouseover', 'a.track-button', (function (e) {
                $(this).tooltipster('content', $(this).attr('data-title'));
            }));

        if (1 != ywot.p) {
            $(document).on('click', "a.track-button", (function (e) {
                e.preventDefault();

                $(this).tooltipster('content', $(this).attr('data-title'));
            }));
        }
    }


    var originalDefaultLabel = $("label[for='ywot_tracking_code']").text();
    var originalDefaultPlaceholder = $("#ywot_tracking_code").attr('placeholder');

    /*
    Change label and placeholder for BRT_WITH_PACKAGE_NUMBER carrier
     */
    jQuery( '#ywot_carrier_id' ).on('change',function(){
        var label = '';
        var placeholder = '';
        var currentCarrier = $(this).val();
        if( currentCarrier == 'BRT_WITH_PACKAGE_NUMBER' ){
            label = 'Package Number';
            placeholder = 'Enter package number';
        }else{
            label = originalDefaultLabel;
            placeholder = originalDefaultPlaceholder
        }
        $("label[for='ywot_tracking_code']").text(label);
        $("#ywot_tracking_code").attr('placeholder',placeholder);
    });


});