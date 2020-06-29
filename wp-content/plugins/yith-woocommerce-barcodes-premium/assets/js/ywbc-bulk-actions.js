(function ($) {
    $(document).ready(function ($) {
        var bulk_action_1 = $('#bulk-action-selector-top'),
            bulk_action_2 = $('#bulk-action-selector-bottom'),
            actions = ywbc_bk_data.action_options;

        //	Add action on bulk action dropdown to let the user set a custom action for barcode generation
        bulk_action_1.add(bulk_action_2).append(actions);
    });
})
(jQuery);