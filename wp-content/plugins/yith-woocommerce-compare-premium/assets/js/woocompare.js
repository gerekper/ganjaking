jQuery(document).ready(function ($) {
	"use strict";

	// Exit if is elementor editor.
	if ( $(document.body).hasClass('elementor-editor-active') ) {
		return false;
	}

	function getCookie(cname) {
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1);
			if ( c.indexOf(name) == 0 ) return decodeURIComponent(c.substring(name.length, c.length));
		}
		return "";
	}
	function addQueryArg(key, value) {
		key = escape(key);
		value = escape(value);

		var s = document.location.search;
		var kvp = key + "=" + value;

		var r = new RegExp("(&|\\?)" + key + "=[^\&]*");

		s = s.replace(r, "$1" + kvp);

		if ( !RegExp.$1 ) {
			s += (s.length > 0 ? '&' : '?') + kvp;
		}

		//again, do what you will here
		return s;
	}
	function hideShowWidget(widget) {

		var hide = widget.data('hide');

		if ( typeof hide == 'undefined' ) {
			return;
		}

		var cookie = getCookie(yith_woocompare.cookie_name),
			cookie_val = cookie ? JSON.parse(cookie) : '';

		if ( !cookie_val.length ) {
			widget.closest('.yith-woocompare-widget').hide();
		} else {
			widget.closest('.yith-woocompare-widget').show();
		}
	}
	function blockItem( item ) {
		if ( typeof $.fn.block !== 'undefined' ) {
			if ( Array.isArray( item ) ) {
				for ( var i of item ) {
					blockItem( i );
				}
			} else {
				item.addClass( 'js-blocked' )
					.block({
						message: null,
						overlayCSS: {
							background: '#fff url(' + yith_woocompare.loader + ') no-repeat center',
							backgroundSize: '20px 20px',
							opacity: 0.6
						}
					});
			}

		}
	}
	function unblockItem(item) {

		if ( Array.isArray( item ) ) {
			for ( var i of item ) {
				unblockItem( i );
			}
		} else {
			if ( item.hasClass( 'js-blocked' ) ) {
				item.removeClass( 'js-blocked' )
					.unblock();

			}
		}
	}
	// Complete data with default values.
	function buildAjaxRequestData( data ) {

		var compare_widget = $( '.yith-woocompare-widget-content' ),
			compare_table  = $( '#yith-woocompare:not(.fixed-compare-table)' );

		return $.extend( data, {
			context: 'frontend',
			lang: compare_table.data('lang') || compare_widget.data('lang'),
		});
	}

	function updateCompare() {

		var compare_widget = $( '.yith-woocompare-widget-content' ),
			compare_table  = $( '#yith-woocompare:not(.fixed-compare-table)' );

		if ( ! compare_widget.length && ! compare_table.length ) {
			return;
		}

		blockItem( [ compare_widget, compare_table ] );

		$.ajax({
			type: 'post',
			url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionreload),
			data: buildAjaxRequestData( {action: yith_woocompare.actionreload} ),
			dataType: 'json',
			cache: false,
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR, textStatus, errorThrown );
				unblockItem( [ compare_widget, compare_table ] );
			},
			success: replaceCompare
		});
	}
	function replaceCompare( html ) {
		if ( html?.table_html ) {
			$('#yith-woocompare:not(.fixed-compare-table)').replaceWith( html.table_html );
			// Trigger.
			$(document).trigger('yith_woocompare_table_updated');
		}
		if ( html?.widget_html ) {
			$('.yith-woocompare-widget-content').replaceWith( html.widget_html );
			// Trigger.
			$(document).trigger('yith_woocompare_widget_updated');
		}
	}
	function updateCounter() {
		var counter = $('.yith-woocompare-counter');
		if ( counter.length ) {
			var type = counter.data('type'),
				text = counter.data('text_o'),
				cookie = getCookie(yith_woocompare.cookie_name),
				c = cookie ? JSON.parse(cookie).length : 0;

			text = text.replace('{{count}}', c);
			counter.find('.yith-woocompare-count').html((type === 'text') ? text : c);
		}
		$(document).trigger('yith_woocompare_counter_updated', c);
	}

	updateCompare();
	updateCounter();

	// ##### ADD TO COMPARE TABLE #####
	$(document).on('click', 'a.compare:not(.added)', function (event) {
		event.preventDefault();

		var button 			= $(this),
			is_related 		= button.closest('.yith-woocompare-related').length,
			product_id 		= button.data('product_id');

		blockItem( button );

		$.ajax({
			type: 'post',
			url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionadd),
			data: buildAjaxRequestData( {
				action: yith_woocompare.actionadd,
				id: product_id,
			} ),
			cache: false,
			dataType: 'json',
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR, textStatus, errorThrown );
				unblockItem( button );
			},
			success: function (response) {

				if ( ! is_related ) {
					// Reload button.
					unblockItem( button );
					button.addClass('added')
						.attr('href', response.table_url )
						.text(yith_woocompare.added_label);

					if ( yith_woocompare.auto_open === 'yes' && ! response.only_one && ! yith_woocompare.is_page ) {
						$('body').trigger('yith_woocompare_open_popup', {response: response.table_url, button: button});
					}
				}

				replaceCompare( response );
				updateCounter();
			}
		});
	});

	// ##### OPEN COMPARE POPUP #####
	if ( ! yith_woocompare.is_page ) {

		$(document).on('click', 'a.compare.added', function (ev) {
			ev.preventDefault();

			var table_url = this.href;

			if ( typeof table_url == 'undefined' )
				return;

			$('body').trigger('yith_woocompare_open_popup', {response: table_url, button: $(this)});
		});
	}

	// ##### OPEN POPUP COMPARE HANDLER #####
	$('body').on('yith_woocompare_open_popup', function (e, data) {

		var response = data.response,
			button = data.button;

		if ( yith_woocompare.force_showing_popup || $(window).width() >= 768 ) {
			$.colorbox({
				href: response,
				iframe: true,
				width: yith_woocompare.settings.width,
				height: yith_woocompare.settings.height,
				fixed: true,
				className: 'yith_woocompare_colorbox',
				close: yith_woocompare.close_label,
				onClosed: function () {
					if ( yith_woocompare.im_in_page ) {
						location.reload();
					} else {
						update_widget(false);
						update_counter();
					}
				},
				onComplete: function () {
					// related slider
					relatedSlider();
					// data Tables
					$.dataTableFunction();
				}
			});

			$(window).resize(function () {
				$.colorbox.resize({
					width: yith_woocompare.settings.width,
					height: yith_woocompare.settings.height
				});
			});

		} else {
			window.location = yith_woocompare.page_url;
		}
	});

	// ##### REMOVE FROM COMPARE ######
	$(document).on('click', '.compare-list .remove a, a.yith_woocompare_clear', function (e) {
		e.preventDefault();

		var button 		= $(this),
			product_id 	= button.data('product_id');

		blockItem( button );

		$.ajax({
			type: 'post',
			url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionremove),
			data: buildAjaxRequestData( {
                action: yith_woocompare.actionremove,
                id: product_id
            } ),
			cache: false,
			dataType: 'json',
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR, textStatus, errorThrown );
				unblockItem( button );
			},
			success: function (response) {

				var to_remove_selector = (product_id === 'all') ? '.compare.added' : '.compare[data-product_id="' + product_id + '"]',
					button_text = yith_woocompare.custom_label_for_compare_button ? button.closest('tbody').find('tr' + yith_woocompare.selector_for_custom_label_compare_button).find('td.product_' + product_id).text() : yith_woocompare.button_text;

				$(to_remove_selector, window.parent.document).removeClass('added').html(button_text);

				replaceCompare( response );
				updateCounter();

				// removed trigger
				$(document).trigger('yith_woocompare_product_removed');
			}
		});
	});

	// ##### LINK OPEN COMPARE POPUP #####
	$('.yith-woocompare-open a, a.yith-woocompare-open').on('click', function (e) {
		if ( yith_woocompare.is_page ) {
			return;
		}
		e.preventDefault();
		$('body').trigger('yith_woocompare_open_popup', {response: addQueryArg('action', yith_woocompare.actionview) + '&iframe=1'});
	});

	// ##### WIDGET ######
	$('.yith-woocompare-widget')
		.on('click', 'a.compare-widget', function (e) { // view table (click on compare)
			if ( yith_woocompare.is_page ) {
				return;
			}
			e.preventDefault();
			$('body').trigger('yith_woocompare_open_popup', {response: $(this).attr('href')});
		})
		.on('click', 'li a.remove, a.clear-all', function (e) { // remove product & clear all
			e.preventDefault();

			var button = $(this),
				product_id = button.data('product_id'),
				compare_widget = button.closest( '.yith-woocompare-widget-content' );

			if ( typeof product_id === 'undefined' ) {
				var href = button.attr('href'),
					args = href.split('id=');
				product_id = args[1];
			}

			blockItem( compare_widget );

			$.ajax({
				type: 'post',
				url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionremove),
				data: buildAjaxRequestData({
					action: yith_woocompare.actionremove,
					id: product_id,
				}),
				cache: false,
				dataType: 'json',
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( jqXHR, textStatus, errorThrown );
					unblockItem( compare_widget );
				},
				success: function (response) {
					replaceCompare(response);
					updateCounter();

					if ( product_id === 'all' ) {
						$('.compare.added').removeClass('added').html(yith_woocompare.button_text);
					} else {
						$('.compare[data-product_id="' + product_id + '"]').removeClass('added').html(yith_woocompare.button_text);
					}
				}
			});
		});


	// ##### NAV CATEGORIES ######
	$(document).on('click', '#yith-woocompare-cat-nav li > a', function (ev) {
		ev.preventDefault();

		var t = $(this),
			container = t.closest('#yith-woocompare'),
			cat = t.data('cat_id'),
			nav = t.closest('#yith-woocompare-cat-nav > ul'),
			products = nav.data('product_ids');

		blockItem( container );

		$.ajax({
			type: 'post',
			url: yith_woocompare.ajaxurl.toString().replace('%%endpoint%%', yith_woocompare.actionfilter),
			data: buildAjaxRequestData({
				action: yith_woocompare.actionfilter,
				yith_compare_cat: cat,
				yith_compare_prod: products
			}),
			cache: false,
			dataType: 'html',
			error: function( jqXHR, textStatus, errorThrown ) {
				console.log( jqXHR, textStatus, errorThrown );
				unblockItem( container );
			},
			success: function (response) {
				container.replaceWith( response );
				// Trigger.
				$(document).trigger('yith_woocompare_table_updated');
			}
		})

	});

	// ####### RELATED PRODUCTS SLIDER #######
	var relatedSlider = function () {
		if ( typeof $.fn.owlCarousel != 'undefined' ) {

			var related = $('#yith-woocompare-related'),
				slider = related.find('.related-products'),
				nav = related.find('.related-slider-nav');

			if ( !related.length )
				return;

			slider.owlCarousel({
				autoplay: yith_woocompare.autoplay_related,
				autoplayHoverPause: true,
				loop: true,
				margin: 15,
				responsiveClass: true,
				responsive: {
					0: {
						items: 2
					},
					// breakpoint from 480 up
					480: {
						items: 3
					},
					// breakpoint from 768 up
					768: {
						items: yith_woocompare.num_related
					}
				}
			});

			if ( nav.length ) {
				nav.find('.related-slider-nav-prev').click(function () {
					slider.trigger('prev.owl.carousel');
				});

				nav.find('.related-slider-nav-next').click(function () {
					slider.trigger('next.owl.carousel');
				})
			}
		}
	};
	relatedSlider();
	$(document).on('yith_woocompare_table_updated', relatedSlider);

	// ########## DATA TABLES ############

	$.dataTableFunction = function (table) {

		var Tables = (table && table.length) ? table : $(document).find('#yith-woocompare table.compare-list'),
			referenceWidth = $(window).outerWidth(),
			dTable;

		if ( Tables.length && typeof $.fn.DataTable != 'undefined' && typeof $.fn.imagesLoaded != 'undefined' ) {
			Tables.each(function () {
				var t = $(this);

				// TODO check fixedcolumns number it must be lower or equal to number of columns

				t.imagesLoaded(function () {
					dTable = t.DataTable({
						'info': false,
						'scrollX': true,
						'scrollCollapse': true,
						'paging': false,
						'ordering': false,
						'searching': false,
						'autoWidth': false,
						'destroy': true,
						'fixedColumns': {
							leftColumns: yith_woocompare.fixedcolumns
						}
					});
				});
			});

			$(window)
				.off('yith_woocompare_refresh_table')
				.on('yith_woocompare_refresh_table', function () {
					if ( typeof dTable !== 'undefined' && referenceWidth !== $(window).outerWidth() ) {
						dTable.destroy();
						$.dataTableFunction(false);
					}
				});
		}
	};
	$.dataTableFunction(false);
	$(window).on( 'resize orientationchange', function() {
		$(window).trigger( 'yith_woocompare_refresh_table' );
	});

	$(document).on('yith_woocompare_table_updated', function (ev, content) {
		var table = content ? $(content).find('table.compare-list') : false;
		$.dataTableFunction(table);
	});

	// remove add to cart button after added
	$('body').on('added_to_cart', function (ev, fragments, cart_hash, $thisbutton) {
		if ( $($thisbutton).closest('table.compare-list').length )
			$thisbutton.hide();
	});
});