(function ($) {
    $(document).on('change', '.mo-ea-cpt-setting select', function () {
        var storage = {};
        $('.mo-ea-cpt-setting select').each(function (index) {
            var field_key = $(this).attr('name');
            storage[field_key] = $(this).val();
        });

        storage = JSON.stringify(storage);

        $(this).parents('li[id*="custom_post_type_settings"]').find('.mo-ea-cpt-control').val(storage).change();
    });
})(jQuery);