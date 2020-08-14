/**
 * General subscription form handling
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

jQuery(document).ready(function ($) {

    $('.yith_wcac_listbox').select2(
        {
            allowClear             : true,
            minimumResultsForSearch: Infinity,
            tokenSeparators        : [',', ' ']
        }
    );

    $('._field_datepicker').datepicker({dateFormat: 'mm/dd/yy', changeMonth: true, changeYear: true});


});