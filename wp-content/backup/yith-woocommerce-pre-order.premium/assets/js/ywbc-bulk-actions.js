/**
 * Created by Carlos Mora on 25/07/2016.
 */
(function ($) {
    $(document).ready(function ($) {
        var bulk_action_1 = $('#bulk-action-selector-top'),
            bulk_action_2 = $('#bulk-action-selector-bottom'),
            set_pre_order_status = ywbc_bk_data.set_pre_order_status,
            remove_pre_order_status = ywbc_bk_data.remove_pre_order_status;
        //  Add action on bulk action dropdown to let the user set a custom action for barcode generation
        bulk_action_1.add(bulk_action_2).append(set_pre_order_status, remove_pre_order_status);
    });
})
(jQuery);