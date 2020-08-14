jQuery(function ($) {

    $('.column-enable .on_off').change(function () {

        var data = {
            action : 'yfwp_enable_switch',
            faq_id : $(this).attr('id').replace('enable_', ''),
            enabled: $(this).val()
        };

        $.post(yith_faq_post_type.ajax_url, data);

    });

    if (!yith_faq_post_type.is_order_by) {

        $('table.posts #the-list, table.pages #the-list').sortable({
            'items' : 'tr',
            'axis'  : 'y',
            'helper': function (e, ui) {
                ui.children().children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            },
            'update': function () {
                $.post(yith_faq_post_type.ajax_url, {
                    action: 'ywfp_order_faqs',
                    order : $('#the-list').sortable('serialize')
                });
            }
        });

    }

});