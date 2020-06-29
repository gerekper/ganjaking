/**
 * wacp-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    "use strict";

    var deps = $(document).find('[data-deps_id]');


    deps.each( function(){

        var d  = $(this),
            tr = d.parents('tr'),
            id = d.data('deps_id');

        $( '#' + id ).on( 'change', function(){

            if( $(this).is(':checked') ) {
                tr.show();
           }
            else {
                tr.hide();
           }
        }).trigger('change');
    });
});