jQuery(function ($) {

    $(document).ready(function () {
        "use strict";

        var vendor_id = (ywctm.vendor_id != '0') ? '_' + ywctm.vendor_id : '';

        function custom_button_enable(caller, disable) {

            if (caller.attr('id') == 'ywctm_hide_price' + vendor_id) {

                if (disable === false) {

                    $('#ywctm_custom_button' + vendor_id).attr('disabled', disable);
                    $('#ywctm_custom_button_loop' + vendor_id).attr('disabled', disable);

                } else {

                    if (!$('#ywctm_hide_add_to_cart_single' + vendor_id).is(':checked')) {

                        $('#ywctm_custom_button' + vendor_id).attr('disabled', disable);

                    }

                    if (!$('#ywctm_hide_add_to_cart_loop' + vendor_id).is(':checked')) {

                        $('#ywctm_custom_button_loop' + vendor_id).attr('disabled', disable);

                    }

                }

            } else {

                if (!$('#ywctm_hide_price' + vendor_id).is(':checked')) {

                    var custom_button = (caller.attr('id') == 'ywctm_hide_add_to_cart_single' + vendor_id) ? '#ywctm_custom_button' + vendor_id : '#ywctm_custom_button_loop' + vendor_id;
                    $(custom_button).attr('disabled', disable);

                }

            }

        }

        $('#ywctm_hide_price' + vendor_id).change(function () {

            var rows = $(this).parent().parent().parent().parent().nextAll('*:lt(2)'),
                add_to_cart = $('#ywctm_hide_add_to_cart_single' + vendor_id).parent().parent().parent().parent(),
                checked = $(this).is(':checked');

            custom_button_enable($(this), !checked);

            if (checked) {

                rows.show();
                add_to_cart.hide();
                $('#ywctm_exclude_hide_add_to_cart' + vendor_id + ', #ywctm_exclude_hide_price' + vendor_id).change();

            } else {

                rows.hide();
                add_to_cart.show();
                $('#ywctm_hide_add_to_cart_single' + vendor_id + ', #ywctm_hide_add_to_cart_loop' + vendor_id).change();

            }

        }).change();

        $('#ywctm_hide_add_to_cart_single' + vendor_id + ', #ywctm_hide_add_to_cart_loop' + vendor_id).change(function () {

            var other = ($(this).attr('id') == 'ywctm_hide_add_to_cart_single' + vendor_id) ? '#ywctm_hide_add_to_cart_loop' + vendor_id : '#ywctm_hide_add_to_cart_single' + vendor_id,
                checked = $(this).is(':checked');

            custom_button_enable($(this), !checked);

            if (checked) {

                $('#ywctm_exclude_hide_add_to_cart' + vendor_id).parent().parent().show();
                $('.ywctm-variations').parent().parent().show();

            } else if (!checked && !$(other).is(':checked')) {

                $('#ywctm_exclude_hide_add_to_cart' + vendor_id).parent().parent().hide();
                $('.ywctm-variations').parent().parent().hide();

            }

            $('#ywctm_exclude_hide_add_to_cart' + vendor_id + ', #ywctm_exclude_hide_price' + vendor_id).change();

        }).change();

        $('.ywctm-variations-price, .ywctm-variations-atc').change(function () {

            var other = ($(this).hasClass('ywctm-variations-price')) ? '.ywctm-variations-atc' : '.ywctm-variations-price',
                checked = $(this).is(':checked');

            if (checked) {
                $(other).prop('checked', true);
            } else {
                $(other).prop('checked', false);
            }

        });

        $('#ywctm_exclude_hide_add_to_cart' + vendor_id + ', #ywctm_exclude_hide_price' + vendor_id).change(function () {

            var other = ($(this).attr('id') == 'ywctm_exclude_hide_add_to_cart' + vendor_id) ? '#ywctm_exclude_hide_price' + vendor_id : '#ywctm_exclude_hide_add_to_cart' + vendor_id,
                other_class = ($(this).attr('id') == 'ywctm_exclude_hide_add_to_cart' + vendor_id) ? '.ywctm-atc' : '.ywctm-full',
                checked = $(this).is(':checked'),
                visible = $(this).is(':visible');

            $(other).prop('checked', checked);

            if (checked && visible) {

                $(other_class).parent().parent().show();

            } else {

                $(other_class).parent().parent().hide();

            }

        }).change();

        $('.ywctm-reverse-full, .ywctm-reverse-atc').change(function () {

            var other = ($(this).is('.ywctm-reverse-full')) ? '.ywctm-reverse-atc' : '.ywctm-reverse-full';

            $(other).prop('checked', $(this).is(':checked'));

        }).change();

        $('#ywctm_custom_button' + vendor_id + ', #ywctm_custom_button_loop' + vendor_id).change(function () {

            var other = ($(this).attr('id') == 'ywctm_custom_button' + vendor_id) ? '#ywctm_custom_button_loop' + vendor_id : '#ywctm_custom_button' + vendor_id,
                rows = $(this).parent().parent().parent().parent().nextAll('*:lt(9)'),
                checked = $(this).is(':checked');

            if (checked) {

                rows.show();

            } else if (!checked && !$(other).is(':checked')) {

                rows.hide();

            }

        }).change();

        $('#ywctm_admin_override').change(function () {

            var checked = $(this).is(':checked'),
                override = $('#ywctm_admin_override_exclusion');

            if (checked) {

                override.parent().parent().show();

            } else {

                override.parent().parent().hide();

            }

            override.change();

        }).change();

        $('#ywctm_admin_override_exclusion').change(function () {

            var checked = $(this).is(':checked'),
                visible = $(this).is(':visible');

            if (checked && visible) {

                $('#ywctm_admin_override_reverse').parent().parent().show();

            } else {

                $('#ywctm_admin_override_reverse').parent().parent().hide();

            }

        }).change();

        $('#ywctm_hide_price_users' + vendor_id).change(function () {

            var option = $('option:selected', this).val(),
                countries = $('#ywctm_hide_countries' + vendor_id).parent().parent(),
                countries_rev = $('#ywctm_hide_countries_reverse' + vendor_id).parent().parent().parent().parent();

            if (option == 'country') {
                countries.show();
                countries_rev.show();
            } else {
                countries.hide();
                countries_rev.hide();
            }

        }).change();

        //Contact form selection
        var yit_contact_form = $('.yit-contact-form').parent().parent(),
            contact_form_7 = $('.contact-form-7').parent().parent(),
            gravity_forms = $('.gravity-forms').parent().parent(),
            permalink = $('#ywctm_inquiry_product_permalink' + vendor_id).parent().parent().parent().parent(),
            tab_title = $('#ywctm_inquiry_form_tab_title' + vendor_id).parent().parent(),
            form_position = $('#ywctm_inquiry_form_where_show' + vendor_id).parent().parent();

        $('select#ywctm_inquiry_form_type' + vendor_id).change(function () {

            var option = $('option:selected', this).val();

            switch (option) {
                case "yit-contact-form":
                    yit_contact_form.show();
                    contact_form_7.hide();
                    gravity_forms.hide();
                    permalink.hide();
                    tab_title.show();
                    form_position.show();
                    break;

                case "contact-form-7":
                    yit_contact_form.hide();
                    gravity_forms.hide();
                    contact_form_7.show();
                    permalink.show();
                    tab_title.show();
                    form_position.show();
                    break;

                case "gravity-forms":
                    yit_contact_form.hide();
                    gravity_forms.show();
                    contact_form_7.hide();
                    permalink.show();
                    tab_title.show();
                    form_position.show();
                    break;

                default:
                    yit_contact_form.hide();
                    contact_form_7.hide();
                    gravity_forms.hide();
                    permalink.hide();
                    tab_title.hide();
                    form_position.hide();
            }

        }).change();

        //Custom button activation
        $('input#ywctm_custom_button' + vendor_id).change(function () {

            if ($(this).is(':checked')) {
                $('input#ywctm_button_text' + vendor_id).prop('required', true)
            } else {
                $('input#ywctm_button_text' + vendor_id).prop('required', false)
            }

        }).change();

        //Custom button icon selection
        var icon_list = $('.ywctm-icon-option.ywctm-icon-list'),
            custom_icon = $('.ywctm-icon-option.custom-icon');

        $('select.icon_list_type').change(function () {

            var option = $('option:selected', this).val();

            switch (option) {
                case "icon":
                    icon_list.show();
                    custom_icon.hide();
                    break;
                case "custom":
                    icon_list.hide();
                    custom_icon.show();
                    break;
                default:
                    icon_list.hide();
                    custom_icon.hide();
            }
        }).change();

        var element_list = $('ul.ywctm-icon-list-wrapper > li'),
            icon_preview = $('.ywctm-icon-preview'),
            icon_text = $('.ywctm-icon-text');

        element_list.on('click', function () {
            var current = $(this);
            element_list.removeClass('active');
            current.addClass('active');
            icon_preview.attr('data-font', current.data('font'));
            icon_preview.attr('data-icon', current.data('icon'));
            icon_preview.attr('data-name', current.data('name'));
            icon_preview.attr('data-key', current.data('key'));

            icon_text.val(current.data('font') + ':' + current.data('name'));

        });

        //upload icon
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment,
            upload_button = $('.forminp-icon .upload_button'),
            upload_img_url = $('.forminp-icon .upload_img_url'),
            upload_preview = $('.forminp-icon .upload_img_preview img');

        upload_img_url.change(function () {
            var url = upload_img_url.val(),
                re = new RegExp('(http|ftp|https)://[a-zA-Z0-9@?^=%&amp;:/~+#-_.]*.(gif|jpg|jpeg|png|ico)');

            if (re.test(url)) {
                upload_preview.attr('src', url);
            } else {
                upload_preview.attr('');
            }
        }).change();

        upload_button.on('click', function () {

            var send_attachment_bkp = wp.media.editor.send.attachment;
            _custom_media = true;

            wp.media.editor.send.attachment = function (props, attachment) {
                if (_custom_media) {
                    upload_img_url.val(attachment.url).change()
                } else {
                    return _orig_send_attachment.apply(this, [props, attachment]);
                }
            };

            wp.media.editor.open(upload_button);
            return false;
        });

        $('.add_media').on('click', function () {
            _custom_media = false;
        });

        //exclusion list
        $('#_ywctm_exclude_button').change(function () {

            var rows = $(this).parent().parent().nextAll('*:lt(4)');

            if ($(this).is(':checked')) {

                rows.hide();

            } else {

                rows.show();

            }

        }).change();

    });

});