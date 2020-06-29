(function ($) {
    // get field row to clone via data-repeatable-field attribute
    var mo_repeatable_cache = $('a.mo_add_repeatable');
    var field_row_to_clone_id = mo_repeatable_cache.attr('data-repeatable-field');

    // add repeatable field group.
    mo_repeatable_cache.click(function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        // eq(-1) is used to get the last repeatable field.
        var clone = $('tr.' + field_row_to_clone_id + '_fields_row').eq(-1).clone()
            // convert cloned copy to string. [0] is used because result could be an array (albeit unlikely here)
            [0].outerHTML
        // increment by 1, the index number in name attribute.
            .replace(/(.+\[.+\])\[(.+)\](\[.+\])/g, function (fullMatch, $1, $2, $3) {
                return $1 + '[' + (Number($2) + 1) + ']' + $3;
            })
            // increment by 1, the index number in 'data-index' attribute.
            .replace(/(data-index=")(.+)(")/g, function (fullMatch, $1, $2, $3) {
                return $1 + (Number($2) + 1) + $3;
            })
            // empty out the value
            .replace(/(value=")(.+)("\s)/g, '$1' + '' + '$3');

        var position = $(this).parents('tr').prev('.' + field_row_to_clone_id + '_fields_row');

        $(position).after(clone);
    });

    // remove repeatable field group
    $(document.body).on('click', '.mo_remove_repeatable', function (e) {
        e.preventDefault();

        if ($('tr.' + field_row_to_clone_id + '_fields_row').length === 1) return false;

        // get parent tr row and remove it.
        $(this).parent().parent().remove();
    });

    $('form#mo-clear-stat').submit(function (e) {
        e.stopImmediatePropagation();

        var response = confirm(mailoptin_globals.js_clear_stat_text);

        if (response === true) {
            HTMLFormElement.prototype.submit.call($(this).get(0));
            return false;
        }
        return false;
    });

    $('#mo-metabox-collapse').click(function (e) {
        e.preventDefault();
        $('#post-body-content').find('div.postbox').addClass('closed');
    });

    $('#mo-metabox-expand').click(function (e) {
        e.preventDefault();
        $('#post-body-content').find('div.postbox').removeClass('closed');
    });

    var optin_automation_activation_locked = false;
    var optin_automation_activation_queue = [];
    var optin_automation_activation_queue_checker = function () {
        var job;
        if (optin_automation_activation_locked === true || optin_automation_activation_queue.length === 0) return;
        job = optin_automation_activation_queue.shift();
        optin_automation_activation_ajax(job.id, job.status, job.activation_type);
    };

    var optin_automation_activation_ajax = function (id, status, activation_type) {
        if (optin_automation_activation_locked === true) {
            return optin_automation_activation_queue.push({
                id: id,
                status: status,
                activation_type: activation_type
            });
        }

        if (typeof activation_type === 'undefined') return;

        optin_automation_activation_locked = true;

        $.post(ajaxurl, {
            action: 'mailoptin_toggle_' + activation_type + '_activated',
            id: id,
            status: status
        }, function () {
            optin_automation_activation_locked = false;
            optin_automation_activation_queue_checker();
        });
    };

    $(window).on('beforeunload', function () {
        if (optin_automation_activation_locked === true) {
            return 'stop';
        }
    });

    // handles activation and deactivation of optin
    $('.mo-optin-activate-switch').on('change', function () {
        var _this = this,
            id = $(_this).data('mo-optin-id'),
            status = _this.checked;

        optin_automation_activation_ajax(id, status, 'optin');
    });

    // handles activation and deactivation of email campaigns
    $('.mo-automation-activate-switch').on('change', function () {
        var _this = this,
            id = $(_this).data('mo-automation-id'),
            status = _this.checked;

        optin_automation_activation_ajax(id, status, 'automation');
    });

    // handle sidebar nav tag menu.
    $(function () {
        var open_tab = function (tab_selector, control_view) {
            if ($(tab_selector).length === 0) return;

            $('.mailoptin-settings-wrap .nav-tab-wrapper a').removeClass('nav-tab-active');
            $(tab_selector).addClass('nav-tab-active').blur();
            var clicked_group = $(tab_selector).attr('href');
            if (typeof(localStorage) !== 'undefined') {
                localStorage.setItem(option_name + "_active-tab", $(tab_selector).attr('href'));
            }
            $('.mailoptin-group-wrapper').hide();
            $(clicked_group).fadeIn();

            if (typeof control_view !== 'undefined') {
                $('html, body').animate({
                    // we are removing 20 to accomodate admin bar which cut into view.
                    scrollTop: $("#" + control_view).offset().top - 20
                }, 2000);
            }

            // reset/remove hash from url
            window.location.hash = '';
        };

        var open_active_or_first_tab = function () {
            var active_tab = '';
            if (typeof(localStorage) !== 'undefined') {
                active_tab = localStorage.getItem(option_name + "_active-tab");
            }

            if (active_tab !== '' && $(active_tab).length) {
                active_tab += '-tab';
            }
            else {
                active_tab = $('.mailoptin-settings-wrap .nav-tab-wrapper a:first')
            }

            open_tab(active_tab);
        };

        $('.mailoptin-group-wrapper').hide();
        var option_name = $('div.mailoptin-settings-wrap').data('option-name');

        $('.mailoptin-settings-wrap .nav-tab-wrapper a').click(function (e) {
            open_tab(this);
            e.preventDefault();
        });

        var hash_event_triggered = false;

        $(window).on('hashchange', function () {
            if (hash_event_triggered === true) return;

            // in #registration_page?login_page, registration_page is the tab id and \
            // login_page the control/settings tr id.
            var hash = this.location.hash, tab_id_len, tab_id, cache;
            if (hash.length === 0) open_active_or_first_tab();

            if ((tab_id_len = hash.indexOf('?')) !== -1) {
                tab_id = hash.slice(0, tab_id_len);
                control_tr_id = hash.slice(tab_id_len + 1);

                if ((cache = $('a' + tab_id + '-tab')).length !== 0) {
                    open_tab(cache, control_tr_id);
                }
            }
            else {
                open_tab(hash + '-tab')
            }

            hash_event_triggered = true;

        });

        $(window).trigger('hashchange');
    });

    $(function () {
        $('.mo-delete-prompt').on('click', function (e) {
            e.preventDefault();
            if (confirm(mailoptin_globals.js_confirm_text)) {
                window.location.href = $(this).attr('href');
            }
        });
    });

}(jQuery));
