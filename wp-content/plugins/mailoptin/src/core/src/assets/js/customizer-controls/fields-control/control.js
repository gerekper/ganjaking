(function (api, $) {
    wp.customize.controlConstructor["mailoptin-fields"] = wp.customize.Control.extend({

        ready: function () {
            "use strict";

            var _this = this;

            var contextual_display_init = function () {
                _this.color_picker_init();
                _this.chosen_select_init();
                $('.mo-fields-widget.mo-custom-field').each(function (index) {
                    // re-order index
                    $(this).attr('data-field-index', index);

                    //Remove any previous click event handlers on the field type select field and reattach
                    var field = this;

                    var maybeHideOptionsField = function () {
                        var field_type = $(field).find('.mo-optin-fields-field').val();
                        var with_options = ["checkbox", "select", "radio"];
                        if (with_options.indexOf(field_type) === -1) {
                            $(field).find(".field_options.mo-fields-block").hide();
                        } else {
                            $(field).find(".field_options.mo-fields-block").show();
                        }
                    };

                    var maybeHideCountryOptionsField = function () {
                        var field_type = $(field).find('.mo-optin-fields-field').val();
                        if(field_type === 'country') {
                            $(field).find(".country_field_options.mo-fields-block").show();
                        } else {
                            $(field).find(".country_field_options.mo-fields-block").hide();
                        }
                    }

                    var maybeHideListSubscriptionFields = function () {
                        var field_type = $(field).find('.mo-optin-fields-field').val();
                        if (field_type === 'list_subscription') {
                            $(field).find(".list_subscription_integration.mo-fields-block").show();
                            $(field).find(".list_subscription_lists.mo-fields-block").show();
                            $(field).find(".list_subscription_display_type.mo-fields-block").show();
                            $(field).find(".list_subscription_alignment.mo-fields-block").show();
                        } else {
                            $(field).find(".list_subscription_integration.mo-fields-block").hide();
                            $(field).find(".list_subscription_lists.mo-fields-block").hide();
                            $(field).find(".list_subscription_display_type.mo-fields-block").hide();
                            $(field).find(".list_subscription_alignment.mo-fields-block").hide();
                        }
                    };

                    var maybeHideHiddenValueField = function () {
                        var field_type = $(field).find('.mo-optin-fields-field').val();
                        if (field_type === 'hidden') {
                            $(field).find(".hidden_value.mo-fields-block").show();
                            $(field).find(".color.mo-fields-block").hide();
                            $(field).find(".background.mo-fields-block").hide();
                            $(field).find(".font.mo-fields-block").hide();
                            $(field).find(".field_required.mo-fields-block").hide();
                        } else {
                            $(field).find(".hidden_value.mo-fields-block").hide();
                            $(field).find(".color.mo-fields-block").show();
                            $(field).find(".background.mo-fields-block").show();
                            $(field).find(".font.mo-fields-block").show();
                            $(field).find(".field_required.mo-fields-block").show();
                        }
                    };

                    var maybeHideRecaptchaField = function () {

                        var field_type = $(field).find('.mo-optin-fields-field').val();

                        $(field).find(".recaptcha_v2_size.mo-fields-block").show();
                        $(field).find(".recaptcha_v2_style.mo-fields-block").show();

                        $(field).find(".placeholder.mo-fields-block").show();
                        $(field).find(".color.mo-fields-block").show();
                        $(field).find(".background.mo-fields-block").show();
                        $(field).find(".font.mo-fields-block").show();
                        $(field).find(".field_required.mo-fields-block").show();

                        if (field_type !== 'recaptcha_v2') {
                            $(field).find(".recaptcha_v2_size.mo-fields-block").hide();
                            $(field).find(".recaptcha_v2_style.mo-fields-block").hide();
                        }

                        if (field_type === 'recaptcha_v2') {
                            $(field).find(".placeholder.mo-fields-block").hide();
                            $(field).find(".color.mo-fields-block").hide();
                            $(field).find(".background.mo-fields-block").hide();
                            $(field).find(".font.mo-fields-block").hide();
                            $(field).find(".field_required.mo-fields-block").hide();
                        }

                        if (field_type === 'recaptcha_v3') {

                            $(field).find(".recaptcha_v2_size.mo-fields-block").hide();
                            $(field).find(".recaptcha_v2_style.mo-fields-block").hide();

                            $(field).find(".placeholder.mo-fields-block").hide();
                            $(field).find(".color.mo-fields-block").hide();
                            $(field).find(".background.mo-fields-block").hide();
                            $(field).find(".font.mo-fields-block").hide();
                            $(field).find(".field_required.mo-fields-block").hide();
                        }
                    };

                    maybeHideOptionsField();
                    maybeHideCountryOptionsField();
                    maybeHideRecaptchaField();
                    maybeHideHiddenValueField();
                    maybeHideListSubscriptionFields();

                    $(this)
                        .find('.mo-optin-fields-field')
                        .off('change.mo_field')
                        .on('change.mo_field', function () {
                            maybeHideOptionsField();
                            maybeHideCountryOptionsField();
                            maybeHideRecaptchaField();
                            maybeHideHiddenValueField();
                            maybeHideListSubscriptionFields();
                        });

                    var widget_title_obj = $(this).find('.mo-fields-widget-title h3');
                    // only modify the widget headline if it has #ID
                    if (widget_title_obj.text().indexOf('#') !== -1) {
                        //index start at 0. Increment so it start from 1. Useful only for Field h3/title.
                        // I didnt do ++index because i dont want the new index copy to index variable.
                        widget_title_obj.text(mailoptin_globals.custom_field_label.replace('{ID}', index + 1));
                    }
                });
            };

            var fetch_list_subscription_integration_lists = function () {

                var update_options_select = function (newOptions, $el) {
                    if (_.isEmpty(newOptions)) newOptions = {}; // in case it's an empty array

                    $el.empty(); // remove old options
                    $.each(newOptions, function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", key).text(value));
                    });

                    $el.trigger('chosen:updated');
                };

                $(document).on('change', '[name=list_subscription_integration]', function () {

                    var list_options = {},
                        selected_integration = this.value,
                        obj = $(this).parents('.mo-fields-widget-form'),
                        $el = obj.find('select[name=list_subscription_lists]');

                    if (selected_integration !== '') {

                        $('.mo-list-subscription-spinner', obj).css('visibility', 'visible');

                        $.post(ajaxurl, {
                            action: 'mailoptin_list_subscription_integration_lists',
                            integration: selected_integration,
                            security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                        }, function (response) {
                            console.log(response);
                            if ('success' in response && response.success === true) {
                                list_options = response.data;
                            }


                            update_options_select(list_options, $el);

                            $('.mo-list-subscription-spinner', obj).css('visibility', 'hidden');

                        });

                        return;
                    }

                    update_options_select(list_options, $el);
                });
            };

            var unique_id = function () {
                return Math.random().toString(36).substring(2) + (new Date()).getTime().toString(36);
            };

            var add_new_field = function (e) {
                e.preventDefault();
                var index = 0;
                var preceding_index = $('.mo-fields-widget').eq(-1).data('field-index');
                if (typeof preceding_index === 'number' && isNaN(preceding_index) === false) {
                    index = preceding_index + 1;
                }

                var template = wp.template('mo-fields-js-template');
                // replace index placeholder with actual value.
                var template_structure = template().replace(/{mo-fields-index}/g, index);
                $(template_structure).appendTo('.mo-custom-fields-container.mo-fields-widgets').addClass('mo-fields-widget-expanded').attr('data-field-index', index);
                contextual_display_init();

                // search and replace ID of fields
                $(this).parents('.mo-fields-block').attr('data-field-index', index);

                // create a unique ID for the created field.
                var data_store = $('.mo-fields-save-field');
                var old_data = data_store.val();
                if (old_data === '' || typeof old_data === 'undefined') {
                    old_data = [];
                } else {
                    old_data = JSON.parse(old_data);
                }

                if (typeof old_data[index] === 'undefined') {
                    old_data[index] = {};
                }

                old_data[index]['cid'] = unique_id();
                old_data[index]['field_type'] = 'text';

                data_store.val(JSON.stringify(old_data)).trigger('change');
            };

            var toggleAllWidget = function (e) {
                e.preventDefault();
                var $button = $(this);
                $button.blur();

                $('.mo-fields-widget').each(function () {
                    var parent = $(this);
                    if ($button.hasClass('mo-expand')) {
                        $('.mo-fields-widget-content', parent).slideDown(function () {
                            parent.addClass('mo-fields-widget-expanded');
                        });

                    } else {
                        $('.mo-fields-widget-content', parent).slideUp(function () {
                            parent.removeClass('mo-fields-widget-expanded');
                        });
                    }
                });

                if ($button.hasClass('mo-expand')) {
                    $button.text($button.data('collapse-text')).removeClass('mo-expand').addClass('mo-collapse');
                } else {
                    $button.text($button.data('expand-text')).removeClass('mo-collapse').addClass('mo-expand');
                }
            };

            var save_change = function (_this) {
                var parent = $(_this).parents('.mo-fields-widget.mo-custom-field');

                var index = parent.attr('data-field-index'),
                    data_store = $('.mo-fields-save-field'),
                    old_data = data_store.val();

                if (old_data === '' || typeof old_data === 'undefined') {
                    old_data = [];
                } else {
                    old_data = JSON.parse(old_data);
                }

                if (typeof old_data[index] === 'undefined') {
                    old_data[index] = {};
                }

                var field_name = _this.name;
                var field_value = _this.value;

                // returning true continue/skip the iteration.
                if (field_name === '') return;

                if (field_name === 'placeholder') {
                    $('.mo-fields-widget-title h3', parent).text(field_value);
                }

                // shim for single checkbox
                if ($(_this).attr('type') === 'checkbox' && field_name.indexOf('[]') === -1) {
                    old_data[index][field_name] = _this.checked;
                } else if ($(_this).attr('type') === 'checkbox' && field_name.indexOf('[]') !== -1) {
                    var item_name = field_name.replace('[]', '');
                    if (_this.checked === true) {
                        old_data = _.without(old_data[index][item_name], field_value);
                    } else {

                        if (typeof old_data[index][item_name] === 'undefined') {
                            old_data[index][item_name] = [];
                            old_data[index][item_name].push(field_value);
                        } else {
                            old_data[index][item_name].push(field_value);
                        }

                        old_data[index][item_name] = _.uniq(old_data[index][item_name]);
                    }
                } else if (_this.tagName === 'SELECT' && $(_this).hasClass('mailoptin-field-chosen')) {
                    old_data[index][field_name] = $(_this).val();
                } else {
                    old_data[index][field_name] = field_value;
                }

                // we are doing this to ensure even when fields are sorted, their cid never changes.
                if (typeof parent.attr('data-field-old-cid') !== "undefined") {
                    old_data[index]['cid'] = parent.attr('data-field-old-cid');
                }

                // remove null and empty from array elements.
                old_data = _.without(old_data, null, '');

                data_store.val(JSON.stringify(old_data)).trigger('change');
            };

            var save_all_widget_changes = function () {
                var old_index, data_store,
                    cache = $('.mo-fields-widget.mo-custom-field');
                data_store = (data_store = $('.mo-fields-save-field').val()) !== "" ? JSON.parse(data_store) : [];

                // reorder data-field-index attributes
                cache.each(function (index) {

                    old_index = $(this).attr('data-field-index');

                    $(this).attr('data-field-index', index);

                    if (typeof data_store[old_index] !== 'undefined' && typeof data_store[old_index]['cid'] !== 'undefined') {
                        $(this).attr('data-field-old-cid', data_store[old_index]['cid']);
                    }
                });

                $('.mo-fields-widget.mo-custom-field select, .mo-fields-widget.mo-custom-field input, .mo-fields-widget.mo-custom-field textarea').each(function () {
                    save_change(this);
                });

                cache.removeAttr('data-field-old-cid');

            };

            var save_on_change = function () {
                save_change(this);
            };

            var sortable_init = function () {
                $(".mo-fields-widgets.mo-custom-field").sortable({
                    axis: "y",
                    containment: ".mo-custom-fields-container",
                    update: function (event, ui) {
                        save_all_widget_changes();
                    }
                });
            };

            fetch_list_subscription_integration_lists();

            contextual_display_init();
            sortable_init();
            $(document).on('click', '.mo-fields-expand-collapse-all', toggleAllWidget);
            $(document).on('click', '.mo-fields-widget-action', this.toggleWidget);
            $(document).on('click', '.mo-add-new-field', add_new_field);
            $(document).on('click', '.mo-fields-delete', this.remove_field);
            $(document).on('change keyup', '.mo-fields-widget.mo-custom-field select, .mo-fields-widget.mo-custom-field input, .mo-fields-widget.mo-custom-field textarea', save_on_change);
        },

        toggleWidget: function (e) {
            e.preventDefault();
            var parent = $(this).parents('.mo-fields-widget');
            $('.mo-fields-widget-content', parent).slideToggle(function () {
                parent.toggleClass('mo-fields-widget-expanded');
            });
        },

        remove_field: function (e) {
            e.preventDefault();
            var parent = $(this).parents('.mo-fields-widget.mo-custom-field');
            parent.slideUp(400, function () {
                $(this).remove();
                var index = parent.data('field-index');
                var data_store = $('.mo-fields-save-field');
                var old_data = JSON.parse(data_store.val());
                // remove field by index. see https://stackoverflow.com/a/1345122/2648410
                old_data.splice(index, 1);
                // remove null and empty from array elements.
                old_data = _.without(old_data, null, '');
                // store the data
                data_store.val(JSON.stringify(old_data)).trigger('change');
                // re-order index
                $('.mo-fields-widget.mo-custom-field').each(function (index) {
                    $(this).attr('data-field-index', index);
                });
            });
        },

        color_picker_init: function () {
            $('.mo-color-picker-hex').wpColorPicker({
                change: function () {
                    $(this).val($(this).wpColorPicker('color')).change();
                },
                clear: function () {
                    $(this).val('').change();
                }
            });
        },

        chosen_select_init: function () {
            $('.mailoptin-field-chosen').chosen({
                width: "100%"
            });
        }
    });

})(wp.customize, jQuery);