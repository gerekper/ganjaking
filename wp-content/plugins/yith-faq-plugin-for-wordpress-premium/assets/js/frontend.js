/**
 * Frontend scripts
 *
 * @package YITH\FAQPluginForWordPress\Assets\JS
 */

jQuery(
	function ( $ ) {

		var $ajax_call = false;

		$(
			function () {

				set_actions();

				if ( window.location.hash ) {
					find_faq( window.location.hash );
				} else {
					$( '#yith-faqs-container' ).removeClass( 'yith-faqs-loading' );
				}

				$( document ).trigger( 'yith_faq_loaded' );

			}
		);

		$.fn.yith_faq_filtering = function ( e = false, obj ) {
			if ( e ) {
				e.preventDefault();
			}
			var $href       = obj.href,
				$hash       = window.location.hash,
				$url        = window.location.href,
				$this       = $( obj ),
				$search_box = $( '.yith-faqs-search input' ),
				$terms      = ( $search_box !== undefined ? $search_box.val() : '' ),
				$container  = '#yith-faqs-container',
				$categories = '.yith-faqs-categories',
				$navigation = '.yith-faqs-pagination',
				$new_page   = '',
				$old_page   = '';

			if ( $url.indexOf( '?term_id=' ) !== -1 ) {

				if ( $href && $href.indexOf( '?pg=' ) !== -1 ) {

					$new_page = $href.substr( $href.indexOf( '=' ) + 1 );

					if ( $url.indexOf( '&pg=' ) !== -1 ) {
						$old_page = $url.substr( $url.indexOf( '&pg=' ) + 4 );
						$href     = $url.replace( 'pg=' + $old_page, 'pg=' + $new_page )
					} else {
						$href = $url + '&pg=' + $new_page;
					}

				}

			}

			if ( $this.is( 'button' ) || $this.is( 'input' ) ) {

				if ( $terms !== '' ) {
					$terms = $terms.replace( ' ', '+' );
					$href  = '?faq-s=' + $terms;
				}
			}

			$( $container ).addClass( 'yith-faqs-loading' );
			$( $navigation ).hide();

			if ( $ajax_call !== false ) {
				$ajax_call.abort();
				$ajax_call = false;
			}

			if ( undefined === $href ) {
				if ( $url.indexOf( '?' ) > 0 ) {
					$href = $url.substring( 0, $url.indexOf( '?' ) );
					window.history.replaceState( {}, document.title, $href );
				} else {
					$href = $url;
				}
			}

			$ajax_call = $.ajax(
				{
					url    : $href,
					success: function ( response ) {

						$ajax_call = false;
						$( $container ).removeClass( 'yith-faqs-loading' );

						if ( '' !== $( '.yith-faqs input' ).val() ) {
							$( '.yith-faqs-reset' ).show( 500 );
						} else {
							$( '.yith-faqs-reset' ).hide();
						}

						if ( $( response ).find( $container ).length > 0 ) {
							$( $container ).html( '' ).html( $( response ).find( $container ).html() );
							if ( yith_faq.enable_scroll ) {
								var $scroll_top = $( '.yith-faqs' ).offset().top - yith_faq.scroll_offset;
								$( window ).scrollTop( $scroll_top );
							}
							set_actions();
						} else {
							$( $container ).html( '' ).html( $( response ).find( '.woocommerce-info' ) );
						}

						if ( $( response ).find( $navigation ).length > 0 ) {
							if ( $( $navigation ).length === 0 ) {
								$.jseldom( $navigation ).insertAfter( $( $navigation ) );
							}
							$( $navigation ).html( $( response ).find( $navigation ).html() ).show();
						} else {
							$( $navigation ).empty();
						}

						if ( $( response ).find( $categories ).length > 0 ) {
							if ( $( $categories ).length === 0 ) {
								$.jseldom( $categories ).insertAfter( $( $categories ) );
							}
							$( $categories ).html( $( response ).find( $categories ).html() ).show();
						} else {
							$( $categories ).empty();
						}

						// update browser history (IE doesn't support it).
						if ( ! navigator.userAgent.match( /msie/i ) ) {
							window.history.pushState( { "pageTitle": response.pageTitle }, "", $href + $hash );
						}

						if ( $hash ) {
							scroll_to_faq( $hash );
						}

						// trigger ready event.
						$( document ).trigger( 'ready' );
						$( window ).trigger( 'scroll' );
						$( document ).trigger( 'yith_faq_loaded' );

					}
				}
			);

		};

		$( document ).on(
			'click',
			'.yith-faqs-categories a, .yith-faqs-page a, .yith-faqs button, .yith-faqs-reset',
			function ( e ) {

				if ( $( this ).hasClass( 'yith-faqs-reset' ) ) {
					$( '.yith-faqs input' ).val( '' );
				}

				$( this ).yith_faq_filtering( e, this );
			}
		);

		$( document ).on(
			'keydown',
			'.yith-faqs input',
			function ( e ) {
				var container = $( '.yith-faqs-search-container' );
				container.addClass( 'active' );
				if ( e.keyCode === 13 && ! e.shiftKey ) {
					$( this ).yith_faq_filtering( e, this );
					container.removeClass( 'active' );
				}
			}
		);

		$( document ).on(
			'focusin',
			'.yith-faqs-search-input input',
			function () {
				$( '.yith-faqs-search-container' ).addClass( 'active' );
			}
		);

		$( document ).on(
			'focusout',
			'.yith-faqs-search-input input',
			function () {
				$( '.yith-faqs-search-container' ).removeClass( 'active' );
			}
		);

		$( document ).on(
			'click',
			'.yith-faqs-summary-link',
			function ( e ) {
				e.preventDefault();
				var page_id = $( this ).closest( '.yith-faqs-summary' ).data( 'page_id' ),
					faq_id  = $( this ).data( 'faq_id' );

				if ( String( page_id ) === yith_faq.page_id ) {
					find_faq( faq_id );
				} else {
					window.location.href = $( this ).data( 'href' );
				}
			}
		);

		function find_faq( hash ) {
			var container = $( '#yith-faqs-container' );

			container.addClass( 'yith-faqs-loading' );
			if ( container.hasClass( 'yith-faqs-paged' ) ) {
				$.post(
					yith_faq.ajax_url,
					{
						action : 'yfwp_find_faq',
						page_id: yith_faq.page_id,
						faq_id : hash.replace( '#faq-', '' )
					},
					function ( response ) {
						if ( response.success ) {
							window.location.hash = hash;

							var button = $( '.page-' + response.page + ' a' );
							if ( response.page >= 1 && button.length > 0 ) {
								button.trigger( 'click' );
							} else {
								scroll_to_faq( hash );
								container.removeClass( 'yith-faqs-loading' );
							}
						}
					}
				);
			} else {
				scroll_to_faq( hash );
				container.removeClass( 'yith-faqs-loading' );
			}
		}

		function scroll_to_faq( hash ) {
			if ( $( hash ).length > 0 ) {
				if ( ! $( hash ).hasClass( 'opened' ) ) {
					$( hash ).find( '.yith-faqs-title' ).trigger( 'click' );
				}
				$( 'html, body' ).animate(
					{
						scrollTop: $( hash ).offset().top - yith_faq.scroll_offset
					},
					500
				);
			}
		}

		function set_actions() {

			$( '.yith-faq-type-toggle .yith-faqs-title' ).each(
				function () {
					$( this ).on(
						'click',
						function () {

							var faq        = $( this ).parent(),
								icon       = $( this ).find( '.icon' ),
								icon_class = icon.attr( 'class' ),
								new_icon_class;

							faq.find( '.yith-faqs-content-wrapper' ).slideToggle();

							if ( faq.hasClass( 'opened' ) ) {
								faq.removeClass( 'opened' );
								faq.removeClass( 'active' );

							} else {
								faq.addClass( 'opened' );
								faq.addClass( 'active' );
							}

							if ( faq.hasClass( 'active' ) && faq.hasClass( 'opened' ) ) {
								new_icon_class = icon_class.replace( 'plus', 'minus' ).replace( 'down', 'up' );
							} else {
								new_icon_class = icon_class.replace( 'minus', 'plus' ).replace( 'up', 'down' );
							}

							icon.removeClass( icon_class ).addClass( new_icon_class );

						}
					);
				}
			);

			$( '.yith-faq-type-accordion .yith-faqs-title' ).each(
				function () {
					$( this ).on(
						'click',
						function () {

							var faq               = $( this ).parent(),
								icon              = $( this ).find( '.icon' ),
								icon_class        = icon.attr( 'class' ),
								new_icon_class,
								active_faq        = $( '.yith-faqs-item.active.opened' ),
								active_icon       = active_faq.find( '.icon' ),
								active_icon_class = active_icon.attr( 'class' );

							if ( active_icon_class !== undefined && faq.attr( 'id' ) !== active_faq.attr( 'id' ) ) {
								var active_new_icon_class = active_icon_class.replace( 'minus', 'plus' ).replace( 'up', 'down' );
								active_faq.find( '.yith-faqs-content-wrapper' ).slideUp();
								active_faq.removeClass( 'active' );
								active_faq.removeClass( 'opened' );
								active_icon.removeClass( active_icon_class ).addClass( active_new_icon_class );
							}

							faq.find( '.yith-faqs-content-wrapper' ).slideToggle();
							if ( faq.hasClass( 'opened' ) ) {
								faq.removeClass( 'opened' );
								faq.removeClass( 'active' );

							} else {
								faq.addClass( 'opened' );
								faq.addClass( 'active' );
							}

							if ( faq.hasClass( 'active' ) && faq.hasClass( 'opened' ) ) {
								new_icon_class = icon_class.replace( 'plus', 'minus' ).replace( 'down', 'up' );
							} else {
								new_icon_class = icon_class.replace( 'minus', 'plus' ).replace( 'up', 'down' );
							}

							icon.removeClass( icon_class ).addClass( new_icon_class );

						}
					);
				}
			);

			$( '.yith-faqs-link a' ).each(
				function () {

					$( this ).on(
						'click',
						function ( e ) {
							e.preventDefault();
							var $this         = $( this ),
								$hover_text   = $this.find( '.hover-text' ),
								$success_text = $this.find( '.success-text' ),
								$temp         = $( '<input>' ),
								$href         = $this.data( 'faq' );

							$hover_text.hide();
							$success_text.show();
							$( 'body' ).append( $temp );
							$temp.val( $href ).select();
							document.execCommand( "copy" );
							$temp.remove();

							setTimeout(
								function () {
									$this.removeClass( 'hover' );
									$hover_text.show();
									$success_text.hide();
								},
								1000
							);

						}
					);
				}
			);

		}

	}
);
