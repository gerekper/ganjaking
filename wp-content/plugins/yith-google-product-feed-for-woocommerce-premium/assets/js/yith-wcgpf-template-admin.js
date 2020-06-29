/**
 * yith-wcgpf-template-admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

jQuery(document).ready( function($) {

    $(document).on('click', '#yith-wcgpf-add-new-row', function () {
        var tr = $("#yith-wcgpf-template-table tbody tr:first").clone();
        $(tr).appendTo('#yith-wcgpf-template-table tbody');
        $(tr).find("input:not('.button')").val('');
    });
    
    $(document).on('click', '.yith-wcgpf-delete', function(event) {
        if ($('.yith_wcgpf_template_table  >tbody >tr').length > 1  ) {
            var $target = $(event.target);
            var $tr = $target.closest('tr');
            $($tr).remove();
        }
    });
    
    $( ".yith_wcgpf_template_table_thead_tbody" ).sortable();
});