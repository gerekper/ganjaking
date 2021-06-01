jQuery(document).on( 'click', '.pdf-invoices-missing-logo-notice .notice-dismiss', function() {

    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: 'dismiss_pdf_invoices_missing_logo_notice'
        }
    });

})