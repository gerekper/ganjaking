(function ($) {

    var open_split_test_form = function (e, parent_optin_id) {
        e.preventDefault();
        // remove active popover
        $('.mo-ellipsis-tooltipster').tooltipster('close');

        $.fancybox.open({
            src: '#mo-optin-add-split',
            type: 'inline',
        });

        $('#mo-split-parent-id').val(parent_optin_id);
    };

    $(document.body).on('click', '.mo-split-test', function (e) {
        var parent_optin_id = $(this).data('optin-id');
        open_split_test_form(e, parent_optin_id);
    });

    $(document.body).on('click', '.mo-split-test-add-variant', function (e) {
        var parent_optin_id = $(this).data('parent-optin-id');
        open_split_test_form(e, parent_optin_id);
    });

    $(document.body).on('click', '#mo-split-submit', function (e) {
        e.preventDefault();
        var _this = this;

        var variant_name_obj = $('#mo-variant-name');
        var split_note_obj = $('#mo-split-notes');

        var variant_name = variant_name_obj.val();
        var split_note = split_note_obj.val();

        var isEmpty = function (str) {
            return (str.length === 0 || !str.trim());
        };

        if (isEmpty(variant_name)) {
            variant_name_obj.addClass('mailoptin-input-error');
        } else {
            variant_name_obj.removeClass('mailoptin-input-error');
        }

        if (isEmpty(split_note)) {
            split_note_obj.addClass('mailoptin-input-error');
        } else {
            split_note_obj.removeClass('mailoptin-input-error');
        }

        if (isEmpty(variant_name) || isEmpty(split_note)) return;

        $(_this).prop("disabled", true);
        $('#mo-split-submit-error').hide();
        $('#mo-split-submit-spinner').show();

        $.post(ajaxurl, {
            action: 'mailoptin_create_optin_split_test',
            variant_name: variant_name,
            split_note: split_note,
            parent_optin_id: $('#mo-split-parent-id').val(),
            nonce: mailoptin_globals.nonce
        }, function (response) {
            if ('success' in response && response.success === true && typeof response.data.redirect !== 'undefined') {
                window.location.assign(response.data.redirect);
            } else {
                $(_this).prop("disabled", false);
                $('#mo-split-submit-error').show().html(response.data);
                $('#mo-split-submit-spinner').hide();
            }
        });

    });

    // handle click of A/B test pause button
    $('.mo-split-test-pause-start').click(function (e) {
        e.preventDefault();
        var label,
            _this = this,
            split_test_action = $(this).data('split-test-action'),
            parent_optin_id = $(this).data('parent-id');

        $(_this).next('#mo-split-pause-spinner').show();

        $.post(ajaxurl, {
            action: 'mailoptin_pause_optin_split_test',
            split_test_action: split_test_action,
            parent_optin_id: parent_optin_id,
            nonce: mailoptin_globals.nonce
        }, function (response) {
            if ('success' in response && response.success === true) {
                if (split_test_action === 'pause') {
                    label = 'start';
                    $(_this).addClass('mo-split-test-action-paused');
                } else {
                    label = 'pause';
                    $(_this).removeClass('mo-split-test-action-paused');
                }
                $(_this).text(mailoptin_globals['split_test_' + label + '_label']);
            }

            $(_this).next('#mo-split-pause-spinner').hide();
            $(_this).attr('data-split-test-action', label);
            $(_this).data('split-test-action', label);
        });
    });

    // handle click of A/B test end_select_winner button
    $('.mo-split-test-end-select-winner').click(function (e) {
        e.preventDefault();
        var spinner_obj = $(this).next('#mo-split-end-winner-spinner'),
            parent_optin_id = $(this).data('parent-id');

        spinner_obj.show();

        $.post(ajaxurl, {
            action: 'mailoptin_end_optin_split_modal',
            parent_optin_id: parent_optin_id,
            nonce: mailoptin_globals.nonce
        }, function (response) {
            if ('success' in response && response.success === true && typeof response.data !== 'undefined') {
                $.fancybox.open(response.data);
            }

            spinner_obj.hide();
        });
    });

    // handle click of A/B test ultimate winner selection
    $(document.body).on('click', '.mo-end-test-tbody', function (e) {
        e.preventDefault();
        if (confirm(mailoptin_globals.js_confirm_text)) {
            var parent_optin_id = $(this).data('parent-id'),
                winner_optin_id = $(this).data('optin-id'),
                preloader_obj = $('.mo-end-test-preloader');

            preloader_obj.show();

            $.post(ajaxurl, {
                action: 'mailoptin_split_test_select_winner',
                parent_optin_id: parent_optin_id,
                winner_optin_id: winner_optin_id,
                nonce: mailoptin_globals.nonce
            }, function (response) {
                if ('success' in response && response.success === true && typeof response.data.redirect !== 'undefined') {
                    window.location.assign(response.data.redirect);
                } else {
                    preloader_obj.hide();
                    $('#mo-select-winner-error').show();
                }
            });
        }
    });

}(jQuery));
