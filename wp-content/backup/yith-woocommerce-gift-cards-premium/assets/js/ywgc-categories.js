jQuery(document).ready(function ($) {
    var bulk_action_1 = $('#bulk-action-selector-top'),
        bulk_action_2 = $('#bulk-action-selector-bottom'),
        set_category_action = '<option value="ywgc-set-category">' + ywgc_data.set_category_action + '</option>',
        unset_category_action = '<option value="ywgc-unset-category">' + ywgc_data.unset_category_action + '</option>';

    //	Add action on dropdown to let the user set a gift card category for media
    bulk_action_1.add(bulk_action_2).append(set_category_action);
    bulk_action_1.add(bulk_action_2).append(unset_category_action);

    bulk_action_1.add(bulk_action_2).on('change', function (e) {

        if ($(this).get(0).id.indexOf('top')) {
            $('#categories1_id').remove();
            if ($(this).val().match('^ywgc')) {
                $(this).after(ywgc_data.categories1);
            }
        }
        else if ($(this).get(0).id.indexOf('bottom')) {
            $('#categories2_id').remove();
            if ($(this).val().match('^ywgc')) {
                $(this).after(ywgc_data.categories2);
            }
        }
    });
});