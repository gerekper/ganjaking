jQuery(document).ready(function ($) {

	var display = ypop_frontend_var.when_display,
		exit_shown = false,
		set_cookie = true,
		content_popup = $(document).find('.ypop-container').html(),
		form = $('.ypop-container').find('form');

		show_popup = function () {

	    var hide_for = Cookies.get( 'yith_popup_hide_for_session' );

	    if( ! ( ypop_frontend_var.hide_option == 'session' &&  hide_for && display !='internal-link' )  ) {

            $('body').yit_popup({
                content: content_popup,
                delay: parseInt(ypop_frontend_var.delay * 1000),
                position: ypop_frontend_var.position,
                mobile: ypop_frontend_var.ismobile,
                destroy_on_close: true,
            });
        }


		};

	if ('load' == display) {
		show_popup();
	}
	else if ('leave-viewport' == display) {
		jQuery(document).mouseleave(function () {
			show_popup();
		})
	}
	else if ('leave-page' == display) {

        $(window).on('beforeunload', function (e) {

            if (exit_shown === false) {
                e = e || window.event;
                exit_shown = true;
                setTimeout(show_popup, 500);
                return false;

            }
        });

		$('a, input, button').on('click', function () {
			window.onbeforeunload = null;
		});
	}
	else if ('external-link' == display) {
		var external = false;
		$('a').on('click', function (e) {

			if (external == false && this.host !== location.host) {
				e.preventDefault();
				show_popup();
				external = true;
			}
		});
	}
	else if ('internal-link' == display) {
		$('a[href|="#yithpopup"]').on('click', function (e) {
			e.preventDefault();
			set_cookie = false;
			show_popup();
			return false;
		});
	}

	$('body').on('close.ypop', function () {

		if ($('input.no-view').is(':checked')) {
			Cookies.set(ypop_frontend_var.never_show_again_cookie_name, '1', {
				expires: parseInt(ypop_frontend_var.expired),
				path   : '/'
			});
		}
		else {

		    if( ypop_frontend_var.hide_option == 'session' ){

		        Cookies.set( 'yith_popup_hide_for_session', '1' );
            }else {
                Cookies.set(ypop_frontend_var.show_next_time_cookie_name, '1');
            }
		}
	});

	if (form.length) {
		form.on('submit', function (e) {

			Cookies.set(ypop_frontend_var.never_show_again_cookie_name, '1', {
				expires: parseInt(ypop_frontend_var.expired),
				path   : '/'
			});

		});
	}

	$(document).on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {

		if ($thisbutton.hasClass('btn-yit-popup')) {

			var redirect = $thisbutton.data('page_redirect');

			if (redirect !== '')
				window.location.replace(redirect);
		}
	});


});
