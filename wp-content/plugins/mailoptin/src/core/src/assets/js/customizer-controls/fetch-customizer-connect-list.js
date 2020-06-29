(function ($) {

    $(window).on('load', function () {

        toggle_connect_service_connected_fields();

        toggle_connect_service_email_list_field();

        $("select[data-customize-setting-link*='connection_service']").change(function (e) {

            var connect_service = $(this).val();

            // hide email list select dropdown field before fetching the list of the selected connect/email service.
            $("div#customize-theme-controls li[id*='connection_email_list']").hide();

            // hide all fields that depending on a connection service before showing that belonging to the selected one
            $('li[id*="Connect"]').hide();

            add_spinner(this);

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
                            $("select[data-customize-setting-link*='connection_email_list'] option").remove();

                            var connection_email_list = $("select[data-customize-setting-link*='connection_email_list']");

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

                            connection_email_list.trigger('change');

                            if ($.inArray(connect_service, ['RegisteredUsersConnect']) === -1) {
                                // show email list field.
                                $("div#customize-theme-controls li[id*='connection_email_list']").show();
                            }

                            toggle_connect_service_connected_fields();

                            $(document.body).trigger('mo_email_list_data_found', [connect_service]);
                        } else {
                            $("div#customize-theme-controls li[id*='connection_email_list']").hide();

                            // hide all dependent connection service fields if no connection email list was returned.
                            $('li[id*="Connect"]').hide();
                            $(document.body).trigger('mo_email_list_data_not_found', [connect_service]);
                        }
                    } else {
                        $("div#customize-theme-controls li[id*='connection_email_list']").hide();

                        // hide all dependent connection service fields if ajax response came badly or invalid.
                        $('li[id*="Connect"]').hide();

                        $(document.body).trigger('mo_email_list_invalid_response', [connect_service]);
                    }

                    remove_spinner();
                }
            );
        });

        function add_spinner(placement) {
            var spinner_html = $('<img class="mo-spinner fetch-email-list" src="' + mailoptin_globals.admin_url + 'images/spinner.gif">');
            $(placement).after(spinner_html);
        }


        function remove_spinner() {
            $('.mo-spinner.fetch-email-list').remove();
        }

        /**
         * contextually toggle custom fields connected to a connection service/ email provider
         */
        function toggle_connect_service_connected_fields(connection_service) {

            // for other selected connect dependent settings fields, hide them if their dependent connection isn't selected.
            // the code below apparently wont work for fields such as radio, checkbox
            var selected_connection_service = connection_service || $("select[data-customize-setting-link*='connection_service']").val();

            if (selected_connection_service !== '' &&
                selected_connection_service !== null &&
                selected_connection_service !== '..' &&
                selected_connection_service !== '...'
            ) {
                // hide any shown connection service fields before showing that of selected one.
                $('li[id*="Connect"]').hide();

                $('li[id*="' + selected_connection_service + '"]').show();
            } else {
                $('li[id*="Connect"]').hide();
            }

            $(document.body).trigger('toggle_connect_service_connected_fields', [selected_connection_service]);
        }

        /**
         * contextually toggle email list/option connected to a connection service/ email provider
         */
        function toggle_connect_service_email_list_field() {
            // Hide email list row if no option is found otherwise show it on admin page load.
            // '*=' selector check if the string after = is found in the element.
            // >= 2 is used because connection email list select-dropdown always have a default "Select..." option.
            if ($("select[data-customize-setting-link*='connection_email_list'] option").length >= 2) {
                $("div#customize-theme-controls li[id*='connection_email_list']").show();
            } else {
                $("div#customize-theme-controls li[id*='connection_email_list']").hide();
            }

            var selected_connection_service = $("select[data-customize-setting-link*='connection_service']").val();
            var selected_email_list = $("select[data-customize-setting-link*='connection_email_list']").val();

            $(document.body).trigger('toggle_connect_service_email_list_field', [selected_email_list, selected_connection_service]);
        }
    });

})(jQuery);
