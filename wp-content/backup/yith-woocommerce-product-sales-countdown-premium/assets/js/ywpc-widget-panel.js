jQuery(function ($) {

    $(document).on('widget-updated', function (e, widget) {

        $(document.body).trigger('wc-enhanced-select-init');

    });

    $(document).on('widget-added', function (e, widget) {

        var input_hidden = widget.find('.wc-product-search');
        input_hidden.removeClass('enhanced');

        var select2container = widget.find('.select2-container');
        select2container.remove();

        if (input_hidden.length > 0) {

            input_hidden.each(function () {

                $(document.body).trigger('wc-enhanced-select-init');

            });

        }

    });
    
});
