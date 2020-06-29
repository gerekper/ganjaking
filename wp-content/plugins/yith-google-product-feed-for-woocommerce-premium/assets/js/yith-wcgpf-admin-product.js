/**
 * yith-wcgpf-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */
jQuery(document).ready( function($) {

    $('.yith-wcgpf-general-tab-options-google-select').select2();
    google_category();

    /*Datetime in Product Feed Table*/
    $('.yith-wcgpf-datetime').each(function( ) {
        var utcSeconds =  parseInt($(this).html());
        var d = new Date(0); // The 0 there is the key, which sets the date to the epoch
        d.setUTCSeconds(utcSeconds);
        $(this).text(d.toLocaleString());
    });

    $( document.body ).on( 'woocommerce_variations_loaded', function() {
        google_category();
    });


    function google_category() {
        $('.yith-wcgpf-google-category').select2();
    }
});