jQuery(function ($) {

    /**
     * Main Template
     */
    function ywpc_text_color(color) {

        $('.ywpc-countdown > .ywpc-header, .ywpc-sale-bar > .ywpc-header').css('color', color);

    }

    function ywpc_border_color(color) {

        $('.ywpc-countdown, .ywpc-sale-bar').css('border-color', color);

    }

    function ywpc_back_color(color) {

        $('.ywpc-countdown, .ywpc-sale-bar').css('background-color', color);

    }

    function ywpc_timer_fore_color(color) {

        $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span').css('color', color);

    }

    function ywpc_timer_back_color(color) {

        if (check_active_template()) {

            $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span').css('background-color', color);
            $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount').css('background-color', '');

        } else {

            $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount').css('background-color', color);
            $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span').css('background-color', '');

        }

    }

    function ywpc_bar_fore_color(color) {

        $('.ywpc-sale-bar > .ywpc-bar > .ywpc-back > .ywpc-fore').css('background-color', color);

    }

    function ywpc_bar_back_color(color) {

        $('.ywpc-sale-bar > .ywpc-bar > .ywpc-back').css('background-color', color);

    }

    function ywpc_text_font_size(size) {

        $('.ywpc-countdown > .ywpc-header, .ywpc-sale-bar > .ywpc-header').css('font-size', size);

    }

    function ywpc_timer_font_size(size) {

        $('.ywpc-countdown > .ywpc-timer > div > .ywpc-amount > span').css('font-size', size);

    }

    function enable_customization() {

        var active = 'def';

        $('.ywpc-appearance').each(function () {

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

        });

        return (active !== 'def');

    }

    function check_active_template() {

        var active = 0;

        $('.ywpc-template').each(function () {

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

        });

        return (active === 1);

    }

    function set_color(id, color) {

        switch (id) {

            case 'ywpc_text_color':
                ywpc_text_color(color);
                break;

            case 'ywpc_border_color':
                ywpc_border_color(color);
                break;

            case 'ywpc_back_color':
                ywpc_back_color(color);
                break;

            case 'ywpc_timer_fore_color':
                ywpc_timer_fore_color(color);
                break;

            case 'ywpc_timer_back_color':
                ywpc_timer_back_color(color);
                break;

            case 'ywpc_bar_fore_color':
                ywpc_bar_fore_color(color);
                break;

            case 'ywpc_bar_back_color':
                ywpc_bar_back_color(color);
                break;

            default:
        }

    }

    function set_font_size(id, size) {

        switch (id) {

            case 'ywpc_text_font_size':
                ywpc_text_font_size(size);
                break;

            case 'ywpc_timer_font_size':
                ywpc_timer_font_size(size);
                break;

            default:
        }

    }

    function initialize_customization() {

        $('.colorpick').each(function () {

            if (customization) {

                var id = $(this).attr('id'),
                    color = $(this).val();

                set_color(id, color)

            }

        });

        $('.ywpc-font-size').each(function () {

            if (customization) {

                var id = $(this).attr('id'),
                    size = $(this).val() + 'px';

                set_font_size(id, size)

            }
        }).change(function () {

            if (customization) {

                var id = $(this).attr('id'),
                    size = $(this).val() + 'px';

                set_font_size(id, size)

            }

        });

    }

    /**
     * Topbar template
     */
    function ywpc_topbar_text_color(color) {
        $('.ywpc-countdown-topbar > .ywpc-header').css('color', color);
    }

    function ywpc_topbar_text_label_color(color) {
        $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-label').css('color', color);

    }

    function ywpc_topbar_back_color(color) {
        $('.ywpc-countdown-topbar').css('background-color', color);

    }

    function ywpc_topbar_timer_text_color(color) {
        $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span').css('color', color);

    }

    function ywpc_topbar_timer_back_color(color) {

        if (check_active_template_topbar()) {

            $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span').css('background-color', color);

        } else {

            $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount').css('background-color', color);
            $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span').css('background-color', '');

        }

    }

    function ywpc_topbar_timer_border_color(color) {

        if (check_active_template_topbar()) {

            $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount').css('background-color', color).css('border-color', '');

        } else {

            $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount').css('border-color', color);

        }

    }

    function ywpc_topbar_text_font_size(size) {

        $('.ywpc-countdown-topbar > .ywpc-header').css('font-size', size);

    }

    function ywpc_topbar_timer_font_size(size) {

        $('.ywpc-countdown-topbar > .ywpc-timer > div > .ywpc-amount > span').css('font-size', size);

    }

    function enable_customization_topbar() {

        var active = 'def';

        $('.ywpc-appearance-topbar').each(function () {

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

        });

        return (active !== 'def');

    }

    function check_active_template_topbar() {

        var active = 0;

        $('.ywpc-template-topbar').each(function () {

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

        });

        return (active === 2);

    }

    function set_color_topbar(id, color) {

        switch (id) {
            case 'ywpc_topbar_text_color':
                ywpc_topbar_text_color(color);
                break;

            case 'ywpc_topbar_text_label_color':
                ywpc_topbar_text_label_color(color);
                break;

            case 'ywpc_topbar_back_color':
                ywpc_topbar_back_color(color);
                break;

            case 'ywpc_topbar_timer_text_color':
                ywpc_topbar_timer_text_color(color);
                break;

            case 'ywpc_topbar_timer_back_color':
                ywpc_topbar_timer_back_color(color);
                break;

            case 'ywpc_topbar_timer_border_color':
                ywpc_topbar_timer_border_color(color);
                break;

            default:
        }

    }

    function set_font_size_topbar(id, size) {

        switch (id) {

            case 'ywpc_topbar_text_font_size':
                ywpc_topbar_text_font_size(size);
                break;

            case 'ywpc_topbar_timer_font_size':
                ywpc_topbar_timer_font_size(size);
                break;

            default:
        }

    }

    function initialize_customization_topbar() {

        $('.colorpick').each(function () {

            if (customization_topbar) {

                var id = $(this).attr('id'),
                    color = $(this).val();

                set_color_topbar(id, color)

            }

        });

        $('.ywpc-font-size-topbar').each(function () {

            if (customization_topbar) {

                var id = $(this).attr('id'),
                    size = $(this).val() + 'px';

                set_font_size_topbar(id, size)

            }
        }).change(function () {

            if (customization_topbar) {

                var id = $(this).attr('id'),
                    size = $(this).val() + 'px';

                set_font_size_topbar(id, size)

            }

        });

    }

    var customization = enable_customization(),
        customization_topbar = enable_customization_topbar();

    $(window).load(function () {

        //Initialization of custom style
        if (customization) {

            initialize_customization();

        }

        //Initialization of custom style for topbar
        if (customization_topbar) {

            initialize_customization_topbar();

        }

        $('.colorpick').iris({
            change: function (event, ui) {

                var id = $(this).attr('id'),
                    color = ui.color.toString();

                if (customization) {

                    set_color(id, color)

                }

                if (customization_topbar) {

                    set_color_topbar(id, color)

                }

                $(this).css({backgroundColor: ui.color.toString()});
            },
            hide  : true,
            border: true
        }).each(function () {
            $(this).css({backgroundColor: $(this).val()});
        }).click(function () {
            $('.iris-picker').hide();
            $(this).closest('.color_box, td').find('.iris-picker').show();
        });

        //enable or disable customization view
        $('.ywpc-appearance').click(function () {

            var active;

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

            if (active !== 'def') {

                customization = true;
                initialize_customization()

            } else {

                customization = false;
                ywpc_text_color('');
                ywpc_border_color('');
                ywpc_back_color('');
                ywpc_timer_fore_color('');
                ywpc_timer_back_color('');
                ywpc_bar_fore_color('');
                ywpc_bar_back_color('');
                ywpc_text_font_size('');
                ywpc_timer_font_size('');

            }

        });

        $('.ywpc-template').click(function () {

            initialize_customization();

        });

        //enable or disable customization view for topbar
        $('.ywpc-appearance-topbar').click(function () {

            var active;

            if ($(this).is(':checked')) {

                active = $(this).val();

            }

            if (active !== 'def') {

                customization_topbar = true;
                initialize_customization_topbar()

            } else {

                customization_topbar = false;
                ywpc_topbar_text_color('');
                ywpc_topbar_text_label_color('');
                ywpc_topbar_back_color('');
                ywpc_topbar_timer_text_color('');
                ywpc_topbar_timer_back_color('');
                ywpc_topbar_timer_border_color('');
                ywpc_topbar_text_font_size('');
                ywpc_topbar_timer_font_size('');

            }

        });

        $('.ywpc-template-topbar').click(function () {

            initialize_customization_topbar();

        });

    });

});