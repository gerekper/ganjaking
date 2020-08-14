var array_unique_noempty, element_box;

jQuery(function ($) {

    $('.ywaf-start-check, .ywaf-repeat-check').click(function () {

        var container = $('.ywaf-risk-container'),
            repeat = ($(this).is('.ywaf-repeat-check')) ? 'true' : 'false';

        if (container.is('.processing')) {
            return false;
        }

        container.addClass('processing');

        container.block({
            message   : null,
            overlayCSS: {
                background: '#fff',
                opacity   : 0.6
            }
        });

        $.ajax({
            type    : 'POST',
            url     : ywaf.ajax_url,
            data    : {
                repeat: repeat
            },
            success : function (response) {

                if (response.status === 'success') {

                    if (response.redirect.indexOf("https://") !== -1 || response.redirect.indexOf("http://") !== -1) {
                        window.location = response.redirect;
                    } else {
                        window.location = decodeURI(response.redirect);
                    }

                } else {

                    container.removeClass('processing').unblock();
                    window.alert(response.error);

                }

            },
            dataType: 'json'
        });

        return false;

    });

    $(document).ready(function ($) {

        $('#ywaf_risk').knob({
            'draw': function () {
                $('.ywaf-risk-container-knob').show();
            }
        });

        $('.ywaf-thresholds').change(function () {

            var value = parseInt($(this).val());

            if ($(this).is('.ywaf-high')) {
                $('.ywaf-medium').prop('max', value - 1);

            } else if ($(this).is('.ywaf-medium')) {
                $('.ywaf-high').prop('min', value + 1);

            }

        }).change();

        element_box.init();

    });

    array_unique_noempty = function (array) {
        var out = [];

        $.each(array, function (key, val) {
            val = $.trim(val);

            if (val && $.inArray(val, out) === -1) {
                out.push(val);
            }
        });

        return out;
    };

    element_box = {
        clean: function (tags) {

            tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');

            return tags;
        },

        parseTags: function (el) {
            var id = el.id,
                num = id.split('-check-num-')[1],
                element_box = $(el).closest('.ywcc-checklist-div'),
                values = element_box.find('.ywcc-values'),
                current_values = values.val().split(','),
                new_elements = [];

            delete current_values[num];

            $.each(current_values, function (key, val) {
                val = $.trim(val);
                if (val) {
                    new_elements.push(val);
                }
            });

            values.val(this.clean(new_elements.join(',')));

            this.quickClicks(element_box);
            return false;
        },

        quickClicks: function (el) {

            var values = $('.ywcc-values', el),
                values_list = $('.ywcc-value-list ul', el),

                id = $(el).attr('id'),
                current_values;

            if (!values.length)
                return;

            current_values = values.val().split(',');
            values_list.empty();

            $.each(current_values, function (key, val) {

                var item, xbutton;

                val = $.trim(val);

                if (!val)
                    return;

                item = $('<li class="select2-selection__choice" />');
                xbutton = $('<span id="' + id + '-check-num-' + key + '" class="select2-selection__choice__remove" tabindex="0"></span>');

                xbutton.on('click keypress', function (e) {

                    if (e.type === 'click' || e.keyCode === 13) {

                        if (e.keyCode === 13) {
                            $(this).closest('.ywcc-checklist-div').find('input.ywcc-insert').focus();
                        }

                        element_box.parseTags(this);
                    }

                });

                item.prepend( val).prepend(xbutton);

                values_list.append(item);

            });
        },

        flushTags: function (el, a, f) {
            var current_values,
                new_values,
                text,
                values = $('.ywcc-values', el),
                add_new = $('input.ywcc-insert', el);

            a = a || false;

            text = a ? $(a).text() : add_new.val();

            if ('undefined' === typeof( text )) {
                return false;
            }

            current_values = values.val();
            new_values = current_values ? current_values + ',' + text : text;
            new_values = this.clean(new_values);
            new_values = array_unique_noempty(new_values.split(',')).join(',');
            values.val(new_values);

            this.quickClicks(el);

            if (!a)
                add_new.val('');
            if ('undefined' === typeof( f ))
                add_new.focus();

            return false;

        },

        init: function () {
            var ajax_div = $('.ywcc-checklist-ajax');

            $('.ywcc-checklist-div').each(function () {
                element_box.quickClicks(this);
            });

            $('input.ywcc-insert', ajax_div).keyup(function (e) {
                if (13 === e.which) {
                    element_box.flushTags($(this).closest('.ywcc-checklist-div'));
                    return false;
                }
            }).keypress(function (e) {
                if (13 === e.which) {
                    e.preventDefault();
                    return false;
                }
            });


        }
    };

});