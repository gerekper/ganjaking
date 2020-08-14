/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Cart Messages Premium
 * @version 1.2.0
 */

jQuery(document).ready(function ($) {
    "use strict";

    $('#_ywcm_message_start, #_ywcm_message_expire').each(function () {
        $(this).prop('placeholder', 'YYYY-MM-DD HH:mm')
    }).datetimepicker({
        timeFormat: 'HH:mm',
        defaultDate    : '',
        dateFormat     : 'yy-mm-dd',
        numberOfMonths : 1,
        showButtonPanel: true
    });

    $(document).find('.ui-datepicker').addClass('yith-plugin-fw-datepicker-div');

});

