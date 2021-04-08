(function ($) {
    $(window).on('load', function () {

        setTimeout(function () {
            var chosen_selector = $('.mailoptin-chosen'), data_link;

            $(document).on('change', '.mailoptin-chosen', function () {
                // multi select return null when no option is return whereas, customizer requires
                // an empty array when a form field (such as select) that returns array of option inorder to
                // trigger a change state/event.
                if (!$(this).val()) {
                    data_link = $(this).attr('data-customize-setting-link');
                    if (typeof wp.customize.value(data_link) !== 'undefined') {
                        wp.customize.value(data_link).set([])
                    }
                }
            });

            chosen_selector.chosen({
                inherit_select_classes: true,
                width: "100%"
            });

            function search_trigger(e) {
                var val = $(this).val();
                if (val.length < 3) return;

                return chosen_search.call(this, e, val);
            }

            // Variables for setting up the typing timer
            var typingTimer;               // Timer identifier
            var doneTypingInterval = 342;  // Time in ms, Slow - 521ms, Moderate - 342ms, Fast - 300ms
            function chosen_search(e, val) {
                var container = $(this).closest('.mailoptin-chosen');
                var select = container.prev();
                var lastKey = e.which;
                var search_type = container.prev().data('search-type');

                // Don't fire if short or is a modifier key (shift, ctrl, apple command key, or arrow keys)
                if (
                    lastKey == 16 ||
                    lastKey == 13 ||
                    lastKey == 91 ||
                    lastKey == 17 ||
                    lastKey == 37 ||
                    lastKey == 38 ||
                    lastKey == 39 ||
                    lastKey == 40
                ) {
                    return;
                }

                clearTimeout(typingTimer);
                typingTimer = setTimeout(
                    function () {
                        $.ajax({
                            type: 'GET',
                            url: ajaxurl,
                            data: {
                                action: 'mailoptin_page_targeting_search',
                                q: val,
                                search_type: search_type
                            },
                            dataType: "json",
                            beforeSend: function () {
                                select.closest('ul.chosen-results').empty();
                            },
                            success: function (data) {
                                if (jQuery.isEmptyObject(data)) return;

                                // Remove all options but those that are selected
                                $('option:not(:selected)', select).remove();
                                $.each(data, function (key, item) {
                                    if (typeof item === 'object') {
                                        var html = '<optgroup label="' + key + '">';
                                        $.each(item, function (key2, item2) {
                                            // Add only options that doesn't already hasn't been selected.
                                            if (!$('option[value="' + key2 + '"]', select).length) {
                                                html += '<option value="' + key2 + '">' + item2 + '</option>';
                                            }
                                        });
                                        html += '</optgroup>';
                                        select.prepend(html);
                                    } else {

                                        // Add any option that doesn't already exist
                                        if (!$('option[value="' + key + '"]', select).length) {
                                            select.prepend('<option value="' + key + '">' + item + '</option>');
                                        }
                                    }
                                });
                                // Update the options
                                $('.mailoptin-chosen').trigger('chosen:updated');
                                select.next().find('input').val(val);
                            }
                        }).fail(function (response) {
                            if (window.console && window.console.log) {
                                console.log(response);
                            }
                        });
                    },
                    doneTypingInterval
                );
            }

            // add type to select inside chosen search.
            $('.mailoptin-chosen .search-field input').each(function () {
                $(this).attr('placeholder', mailoptin_globals.chosen_search_placeholder);
            });

            // Replace options with search results
            $(document.body).on('keyup', '.mailoptin-chosen.chosen-container .chosen-search input, .mailoptin-chosen.chosen-container .search-field input', search_trigger);

        }, 1000);
    });
})(jQuery);