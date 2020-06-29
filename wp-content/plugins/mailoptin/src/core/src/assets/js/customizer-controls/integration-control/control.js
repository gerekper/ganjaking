(function (api, $) {
    wp.customize.controlConstructor["mailoptin-integration"] = wp.customize.Control.extend({

        ready: function () {
            "use strict";

            var _this = this;

            this.fetch_email_list();

            var contextual_display_init = function () {

                $('.mo-integration-widget').each(function (index) {
                    var parent = $(this);
                    // re-order index
                    $(this).attr('data-integration-index', index);
                    _this.color_picker_init();
                    _this.chosen_select_init();
                    _this.toggle_connect_service_connected_fields(parent);
                    _this.toggle_connect_service_email_list_field(parent);
                });
            };

            var add_new_integration = function (e) {
                e.preventDefault();
                var index = 0;
                var preceding_index = $('.mo-integration-widget').eq(-1).data('integration-index');
                if (typeof preceding_index === 'number' && isNaN(preceding_index) === false) {
                    index = preceding_index + 1;
                }

                var template = wp.template('mo-integration-js-template');
                // replace index placeholder with actual value.
                var template_structure = template().replace(/{mo-integration-index}/g, index);
                $(template_structure).insertBefore('.mo-integration__add_new').addClass('mo-integration-widget-expanded').attr('data-integration-index', index);
                contextual_display_init();

                // search and replace ID of fields
                $(this).parents('.mo-integration-block').attr('data-integration-index', index);

                $(document.body).trigger('mo_optin_add_new_integration');
            };

            var toggleAllWidget = function (e) {
                e.preventDefault();
                var $button = $(this);
                $button.blur();

                $('.mo-integration-widget').each(function () {
                    var parent = $(this);
                    if ($button.hasClass('mo-expand')) {
                        $('.mo-integration-widget-content', parent).slideDown(function () {
                            parent.addClass('mo-integration-widget-expanded');
                        });

                    } else {
                        $('.mo-integration-widget-content', parent).slideUp(function () {
                            parent.removeClass('mo-integration-widget-expanded');
                        });
                    }
                });

                if ($button.hasClass('mo-expand')) {
                    $button.text($button.data('collapse-text')).removeClass('mo-expand').addClass('mo-collapse');
                } else {
                    $button.text($button.data('expand-text')).removeClass('mo-collapse').addClass('mo-expand');
                }
            };


            contextual_display_init();
            this.conditionally_display();
            $(document).on('click', '.mo-expand-collapse-all', toggleAllWidget);
            $(document).on('click', '.mo-integration-widget-action', this.toggleWidget);
            $(document).on('click', '.mo-add-new-integration', add_new_integration);
            $(document).on('click', '.mo-integration-delete', this.remove_integration);
            $(document).on('click', '.mo-integration-advanced-settings', this.toggle_advance_settings);
            $(document).on('change keyup', '.mo-integration-widget select, .mo-integration-widget input, .mo-integration-widget textarea', this.save_changes);
        },

        conditionally_display: function () {
            var callback = function () {
                $('.mo-integration-widget').each(function () {
                    var parent = $(this);
                    var connection_service = $("select[name='connection_service']", parent).val();

                    $('.mo-optin-map-custom-field', parent).toggle(
                        _.isArray(mo_connections_with_custom_field_support) &&
                        _.indexOf(mo_connections_with_custom_field_support, connection_service) !== -1
                    );

                    $('.mo-integration-advanced-settings', parent).toggle(
                        _.isArray(mo_connections_with_advance_settings_support) &&
                        _.indexOf(mo_connections_with_advance_settings_support, connection_service) !== -1
                    );
                });
            };

            callback();
            $(document).on('change', '.mo-integration-widget select[name="connection_service"]', callback);
            $(document).on('mo_optin_add_new_integration', callback);
        },

        save_changes: function () {
            var data_store = $('.mo-integrations-save-field');

            var old_data = data_store.val();
            if (old_data === '' || typeof old_data === 'undefined') {
                old_data = [];
            } else {
                old_data = JSON.parse(old_data);
            }

            var parent = $(this).parents('.mo-integration-widget');
            var index = parent.attr('data-integration-index');
            if (typeof old_data[index] === 'undefined') {
                old_data[index] = {};
            }

            var field_name = this.name;
            var field_value = this.value;

            // if the integration or connection is changed, clear.
            if (field_name === 'connection_service') {
                old_data[index] = {};
            }

            // returning true continue/skip the iteration.
            if (field_name === '') return;

            // shim for single checkbox
            if ($(this).attr('type') === 'checkbox' && field_name.indexOf('[]') === -1) {
                old_data[index][field_name] = this.checked;
            } else if ($(this).hasClass('mo_mc_interest') && $(this).attr('type') === 'checkbox' && field_name.indexOf('[]') !== -1) {
                var item_name = field_name.replace('[]', '');
                if (this.checked === false) {
                    delete old_data[index][item_name][field_value];
                } else {
                    if (typeof old_data[index][item_name] === 'undefined') {
                        old_data[index][item_name] = {};
                        old_data[index][item_name][field_value] = $(this).next('.mo_mc_interest_label').text();
                    } else {
                        // ideally, we should check if it's === 0 but because checked event ha fired before
                        // we get to this point, so we check if this is the first check.
                        // all this is so we are sure we are not adding new checked interests to previous obsolete checked ones
                        if ($('.mo_mc_interest:checked', parent).length === 1) {
                            old_data[index][item_name] = {}
                        }
                        old_data[index][item_name][field_value] = $(this).next('.mo_mc_interest_label').text();
                    }
                }
            } else if ($(this).attr('type') === 'checkbox' && field_name.indexOf('[]') !== -1) {
                var item_name = field_name.replace('[]', '');
                if (this.checked === true) {
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
            } else if (this.tagName === 'SELECT' && $(this).hasClass('mailoptin-integration-chosen')) {
                old_data[index][field_name] = $(this).val();
            } else {
                old_data[index][field_name] = field_value;
            }

            // remove null and empty from array elements.
            old_data = _.without(old_data, null, '');

            data_store.val(JSON.stringify(old_data)).trigger('change');
        },

        toggleWidget: function (e) {
            e.preventDefault();
            var parent = $(this).parents('.mo-integration-widget');
            $('.mo-integration-widget-content', parent).slideToggle(function () {
                parent.toggleClass('mo-integration-widget-expanded');
            });
        },

        remove_integration: function (e) {
            e.preventDefault();
            var cache = $('.mo-integration-widget');
            var integrations_count = cache.length;
            if (integrations_count <= 1) return;

            var parent = $(this).parents('.mo-integration-widget');
            parent.slideUp(400, function () {
                $(this).remove();
                var index = parent.data('integration-index');
                var data_store = $('.mo-integrations-save-field');
                var old_data = JSON.parse(data_store.val());
                // remove integration by index. see https://stackoverflow.com/a/1345122/2648410
                old_data.splice(index, 1);
                // remove null and empty from array elements.
                old_data = _.without(old_data, null, '');
                // store the data
                data_store.val(JSON.stringify(old_data)).trigger('change');
                // re-order index
                $('.mo-integration-widget').each(function (index) {
                    $(this).attr('data-integration-index', index);
                });
            });
        },

        toggle_advance_settings: function (e) {
            e.preventDefault();

            var parent = $(this).parents('.mo-integration-widget');

            $('.mo-integration-widget-advanced-settings-wrap', parent).slideToggle(300, function () {
                $(e.target).toggleClass('opened').blur();
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
            $('.mailoptin-integration-chosen').chosen({
                width: "100%"
            });
        },

        fetch_email_list: function () {

            var _this = this;

            $(document).on('change', "select[name='connection_service']", function () {

                var parent = $(this).parents('.mo-integration-widget');

                var connect_service = $(this).val();
                var connect_service_label = $('option:selected', this).text();

                $('.mo-integration-widget-title h3', parent).html(connect_service_label);

                // hide email list select dropdown field before fetching the list of the selected connect/email service.
                $(".connection_email_list", parent).hide();

                // hide all fields that depending on a connection service before showing that belonging to the selected one
                $('div[class*="Connect"]', parent).hide();

                _this.add_spinner(this);

                $.post(ajaxurl, {
                        action: 'mailoptin_customizer_fetch_email_list',
                        connect_service: connect_service,
                        security: $("input[data-customize-setting-link*='[ajax_nonce]']").val()
                    },
                    function (response) {

                        if (_.isObject(response) && 'success' in response && 'data' in response) {
                            var data = response.data;

                            if (_.size(data) >= 1 || $.inArray(connect_service, ['RegisteredUsersConnect']) !== -1) {

                                // clear out the select options before appending.
                                $("select[name='connection_email_list'] option", parent).remove();

                                var connection_email_list = $("select[name='connection_email_list']", parent);

                                // append default "Select..." option to select dropdown.
                                connection_email_list.append($('<option>', {
                                    value: '',
                                    text: 'Select...'
                                }));

                                $.each(data, function (key, value) {
                                    connection_email_list.append($('<option>', {
                                        value: key,
                                        text: value
                                    }));
                                });

                                if ($.inArray(connect_service, ['RegisteredUsersConnect']) === -1) {
                                    // show email list field.
                                    $(".connection_email_list", parent).show();
                                }

                                _this.toggle_connect_service_connected_fields(parent);
                                _this.toggle_connect_service_email_list_field(parent);

                                $(document.body).trigger('mo_new_email_list_data_found', [connect_service, parent]);
                            } else {

                                $(".connection_email_list", parent).hide();

                                // hide all dependent connection service fields if no connection email list was returned.
                                $('div[class*="Connect"]', parent).hide();
                                $(document.body).trigger('mo_email_list_data_not_found', [connect_service, parent]);
                            }
                        } else {
                            $(".connection_email_list", parent).hide();

                            // hide all dependent connection service fields if ajax response came badly or invalid.
                            $('div[class*="Connect"]', parent).hide();
                            $(document.body).trigger('mo_email_list_invalid_response', [connect_service, parent]);
                        }

                        _this.remove_spinner(parent);
                    }
                );
            });
        },

        /**
         * contextually toggle custom fields connected to a connection service/ email provider
         */
        toggle_connect_service_connected_fields: function (parent) {

            // for other selected connect dependent settings fields, hide them if their dependent connection isn't selected.
            // the code below apparently wont work for fields such as radio, checkbox
            var selected_connection_service = $("select[name='connection_service']", parent).val();

            if (selected_connection_service !== '' &&
                selected_connection_service !== null &&
                selected_connection_service !== '..' &&
                selected_connection_service !== '...'
            ) {
                // hide any shown connection service fields before showing that of selected one.
                $('div[class*="Connect"]', parent).hide();

                // if interest selection is available, do not make group related fields hidden.
                if ($('[name="MailChimpConnect_interests[]"]', parent).length === 0) {
                    $('div[class*="' + selected_connection_service + '"]', parent).not('.mc-group-block').show();
                } else {
                    $('div[class*="' + selected_connection_service + '"]', parent).show();
                }
            } else {
                $('div[class*="Connect"]', parent).hide();
            }

            $(document.body).trigger('toggle_connect_service_connected_fields', [parent, selected_connection_service]);
        },

        /**
         * contextually toggle email list/option connected to a connection service/ email provider
         */
        toggle_connect_service_email_list_field: function (parent) {
            // Hide email list row if no option is found otherwise show it on admin page load.
            // '*=' selector check if the string after = is found in the element.
            // >= 2 is used because connection email list select-dropdown always have a default "Select..." option.
            if ($("select[name='connection_email_list'] option", parent).length >= 2) {
                $('.connection_email_list', parent).show();
            } else {
                $('.connection_email_list', parent).hide();
            }

            var selected_connection_service = $("select[name='connection_service']", parent).val();
            var selected_email_list = $("select[name='connection_email_list']", parent).val();

            $(document.body).trigger('toggle_connect_service_email_list_field', [parent, selected_email_list, selected_connection_service]);
        },

        add_spinner: function (placement) {
            var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + 'images/spinner.gif">');
            $(placement).after(spinner_html);
        },

        remove_spinner: function (parent) {
            $('.mo-spinner.fetch-email-list', parent).remove();
        }
    });

})(wp.customize, jQuery);