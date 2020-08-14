/**
 * yith-wtbe-frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WordPress Title Bar Effects
 * @version 1.0.1
 */
jQuery(document).ready(function ($) {
    var activeForChangeTab = yith_wtbe_options.change_tab;
    var is_admin = yith_wtbe_options.is_admin;
    var animation = null;
    var timer = null;
    var Title = {
        animation: yith_wtbe_options.animation,
        speed_animation: yith_wtbe_options.speed_animation,
        delay_start: yith_wtbe_options.delay_start,
        delay_stop: yith_wtbe_options.delay_stop,
        delay_cycle: yith_wtbe_options.delay_cycle,
        length: yith_wtbe_options.title_bar != '' ? yith_wtbe_options.title_bar.length : document.title.length,
        titleBar : '',
        count: 0,
        timer: 0,
        hided: 0,

        typing: function () {
            document.title = Title.titleBar.substring(0, (Title.count + 1));
            if (Title.count == Title.length) {
                Title.count = 0;
                clearInterval(animation);
                timeout = setTimeout(Title.init(), Title.delay_cycle);
            } else {
                Title.count++;
            }

        },

        scrolling: function () {
            document.title = Title.titleBar.substring(Title.count, Title.titleBar.length) + " " + Title.titleBar.substring(0, Title.count);
            Title.count++;
            if (Title.count > Title.length) {
                Title.count = 0;
                clearInterval(animation);

                timeout = setTimeout(Title.init(), Title.delay_cycle);
            }
        },

        intermittence: function () {
            if (Title.hided == 0) {
                document.title = '*************';
                Title.hided = 1;
            } else {
                document.title = Title.titleBar;
                Title.hided = 0;
            }

        },


        check_timer_animation: function () {
            if (Title.timer == (Title.delay_stop / 1000)) {
                Title.stop();
                return;
            }
            Title.timer++;

        },

        stop: function () {
            clearInterval(animation);
            clearInterval(timer);
            document.title = this.titleBar;
            this.count = 0;
        },


        init: function (TitleBar) {
            if (this.timer == 0) {
                timer = setInterval(this.check_timer_animation, 1000);
            }
            if( typeof TitleBar != 'undefined'){
                this.titleBar = TitleBar;
            }
            switch (this.animation) {
                case "typing":
                    animation = setInterval(this.typing, this.speed_animation);
                    break;

                case "scrolling":
                    animation = setInterval(this.scrolling, this.speed_animation);
                    break;

                case "intermittence":
                    animation = setInterval(this.intermittence, this.speed_animation);
                    break;

                case "stop":
                    this.stop();
                    break;
            }

        }

    };

    var originalTitleBar = yith_wtbe_options.title_bar != '' ? yith_wtbe_options.title_bar : document.title;

    if( is_admin!=1 ){
        if (activeForChangeTab == 'yes') {

            $(window).on ( 'visibilitychange', function(e) {
                if( document.hidden ){
                    setTimeout(Title.init( originalTitleBar ), Title.delay_start);
                }else{
                    Title.stop();
                }
            });

        } else {

            setTimeout(Title.init( originalTitleBar ), Title.delay_start);
        }
    }



    /* Integration with YITH WooCommerce Desktop Notifications */

    $(document).on('yith-wtbe-start-animation', function (event, titleBar) {

        Title.stop();
        setTimeout(function () {
            Title.init(titleBar)
        }, Title.delay_start);
    });

    $(document).on('yith-wtbe-stop-animation', function () {
        Title.stop();
    });


});