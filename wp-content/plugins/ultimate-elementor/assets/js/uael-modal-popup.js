( function( $ ) {

	UAELModalPopup = {

		/**
		 * Place the Modal Popup on centre of screen
		 *
		 */
		_center: function() {
			setTimeout( function() {
				$( '.uael-modal-parent-wrapper' ).each( function() {
					var $this = $( this );
					var tmp_id = $this.attr( 'id' );
					var popup_id = tmp_id.replace( '-overlay', '' );
					UAELModalPopup._centerModal( popup_id );
				} );
			}, 300 );
		},

		/**
		 * Place the Modal Popup on centre of screen
		 *
		 */
		_centerModal: function ( popup_id ) {

			var popup_wrap = $( '.uamodal-' + popup_id ),
				modal_popup  = $( '#modal-' + popup_id ),
				extra_value = 0,
				close_handle = modal_popup.find( '.uael-modal-close' ),
				top_pos = ( ( $( window ).height() - modal_popup.outerHeight() ) / 2 );

			if ( modal_popup.hasClass('uael-center-modal') ) {
	        	modal_popup.removeClass('uael-center-modal');
			}

			if( close_handle.hasClass( 'uael-close-custom-popup-top-right' ) || close_handle.hasClass( 'uael-close-custom-popup-top-left' ) ) {
				extra_value = parseInt( close_handle.outerHeight() );
			}

			if ( popup_wrap.find( '.uael-content' ).outerHeight() > $( window ).height() ) {
				top_pos = ( 20 + extra_value );
				if( modal_popup.hasClass( 'uael-show' ) ) {
					$( 'html' ).addClass( 'uael-html-modal' );
					modal_popup.addClass( 'uael-modal-scroll' );

					var $admin_bar = $( '#wpadminbar' );

					if( $admin_bar.length > 0 ) {
						top_pos = ( top_pos + parseInt( $admin_bar.outerHeight() ) );
					}

					var modal_popup_content = modal_popup.find( '.uael-content' );
					modal_popup_content.css( 'margin-top', + top_pos +'px' );
					modal_popup_content.css( 'margin-bottom', '20px' );
				}
			} else {
				top_pos = ( parseInt( top_pos ) + 20 );
			}

			modal_popup.css( 'top', + top_pos +'px' );
			modal_popup.css( 'margin-bottom', '20px' );
		},

		/**
		 * Invoke show modal popup
		 *
		 */
		_show: function( popup_id ) {

			$( window ).trigger( 'uael_before_modal_popup_open', [ popup_id ] );

			UAELModalPopup._autoPlay( popup_id );

			var modal_popup  = $( '#modal-' + popup_id );

			if( modal_popup.hasClass( 'uael-modal-vimeo' ) || modal_popup.hasClass( 'uael-modal-youtube' ) ) {
				setTimeout( function() { modal_popup.addClass( 'uael-show' ); }, 300 );
			} else {
				modal_popup.addClass( 'uael-show' );
			}
			setTimeout(
				function() {
					modal_popup.removeClass( 'uael-effect-13' );
				},
				1000
			);
			UAELModalPopup._centerModal( popup_id );
			UAELModalPopup._afterOpen( popup_id );
		},

		/**
		 * Invoke close modal popup
		 *
		 */
		_close: function( popup_id ) {
			var modal_popup  = $( '#modal-' + popup_id );
			modal_popup.removeClass( 'uael-show' );
			$( 'html' ).removeClass( 'uael-html-modal' );
			modal_popup.removeClass( 'uael-modal-scroll' );
			UAELModalPopup._stopVideo( popup_id );
			
			var cookie_type  = $( '.uamodal-' + popup_id ).data( 'cookies-type' );

			if ( 'closed' === cookie_type ){
				UAELModalPopup._setPopupCookie( popup_id );
			}

		},

		/**
		 * Check all the end conditions to show modal popup
		 *
		 */
		_canShow: function( popup_id ) {0

			var is_cookie = $( '.uamodal-' + popup_id ).data( 'cookies' );
			var current_cookie = Cookies.get( 'uael-modal-popup-' + popup_id );
			var display = true;

			// Check if cookies settings are set
			if ( 'undefined' !== typeof is_cookie && 'yes' === is_cookie ) {
				if( 'undefined' !== typeof current_cookie && 'true' == current_cookie ) {
					display = false;
				} else {
					Cookies.remove( 'uael-modal-popup-' + popup_id );
				}
			} else {
				Cookies.remove( 'uael-modal-popup-' + popup_id );
			}

			// Check if any other modal is opened on screen.
			if( $( '.uael-show' ).length > 0 ) {
				display = false;
			}

			// Check if this is preview or actuall load.
			if( $( '#modal-' + popup_id ).hasClass( 'uael-modal-editor' ) ) {
				display = false;
			}

			return display;
		},

		/**
		 * Auto Play video
		 *
		 */
		_autoPlay: function( popup_id ) {

			var active_popup = $( '.uamodal-' + popup_id ),
				video_autoplay = active_popup.data( 'autoplay' ),
				modal_content = active_popup.data( 'content' ),
				modal_popup  = $( '#modal-' + popup_id );

			if ( video_autoplay == 'yes' && ( modal_content == 'youtube' || modal_content == 'vimeo' ) ) {

				var vid_id = modal_popup.find( '.uael-video-player' ).data( 'id' );

				if( 0 == modal_popup.find( '.uael-video-player iframe' ).length ) {

					modal_popup.find( '.uael-video-player div[data-id=' + vid_id + ']' ).trigger( 'click' );

				} else {

					var modal_iframe 		= active_popup.find( 'iframe' ),
						modal_src 			= modal_iframe.attr( "src" ) + '&autoplay=1';

					modal_iframe.attr( "src",  modal_src );
				}
			}

			if ( 'iframe' == modal_content ) {

				if( active_popup.find( '.uael-modal-content-data iframe' ).length == 0 ) {

					var src = active_popup.find( '.uael-modal-content-type-iframe' ).data( 'src' );

					var iframe = document.createElement( "iframe" );
					iframe.setAttribute( "src", src );
					iframe.setAttribute( "style", "display:none;" );
					iframe.setAttribute( "frameborder", "0" );
					iframe.setAttribute( "allowfullscreen", "1" );
					iframe.setAttribute( "width", "100%" );
					iframe.setAttribute( "height", "100%" );
					iframe.setAttribute( "class", "uael-content-iframe" );

					var active_popup_data = active_popup.find( '.uael-modal-content-data' );

					active_popup_data.html( iframe );
					active_popup_data.append( '<div class="uael-loader"><div class="uael-loader-1"></div><div class="uael-loader-2"></div><div class="uael-loader-3"></div></div>' );

					iframe.onload = function() {
						window.parent.jQuery( document ).find('#modal-' + popup_id + ' .uael-loader' ).fadeOut();
						this.style.display='block';
					};
				}
			}
		},

		/**
		 * Stop playing video
		 *
		 */
		_stopVideo: function( popup_id ) {

			var active_popup = $( '.uamodal-' + popup_id ),
				modal_content = active_popup.data( 'content' );

			if ( modal_content != 'photo' ) {

				var modal_iframe 		= active_popup.find( 'iframe' ),
					modal_video_tag 	= active_popup.find( 'video' );

				if ( modal_iframe.length ) {
					var modal_src = modal_iframe.attr( "src" ).replace( "&autoplay=1", "" );
					modal_iframe.attr( "src", '' );
				    modal_iframe.attr( "src", modal_src );
				} else if ( modal_video_tag.length ) {
		        	modal_video_tag[0].pause();
					modal_video_tag[0].currentTime = 0;
				}
			}
		},

		/**
		 * Process after modal popup open event
		 *
		 */
		_afterOpen: function( popup_id ) {

			var cookie_type  = $( '.uamodal-' + popup_id ).data( 'cookies-type' );

			if ( 'default' == cookie_type ){
				UAELModalPopup._setPopupCookie( popup_id );
			}

			$( window ).trigger( 'uael_after_modal_popup_open', [ popup_id ] );
		},

		/**
		 * Process to set cookie
		 *
		 */
		 _setPopupCookie: function( popup_id ) {

			var current_cookie = Cookies.get( 'uael-modal-popup-' + popup_id );
			var cookies_days  = parseInt( $( '.uamodal-' + popup_id ).data( 'cookies-days' ) );
			var url_condition = window.location.protocol === 'https:' ? true : '';

			if( 'undefined' === typeof current_cookie && 'undefined' !== typeof cookies_days ) {
				Cookies.set( 'uael-modal-popup-' + popup_id, true, { expires: cookies_days, secure: url_condition } );
			}
		},
	}

	/**
	 * ESC keypress event
	 *
	 */
	$( document ).on( 'keyup', function( e ) {

		if ( 27 == e.keyCode ) {

			$( '.uael-modal-parent-wrapper' ).each( function() {
				var $this = $( this );
				var tmp_id = $this.attr( 'id' );
				var popup_id = tmp_id.replace( '-overlay', '' );
				var close_on_esc = $this.data( 'close-on-esc' );

				if( 'yes' == close_on_esc ) {
					UAELModalPopup._close( popup_id );
				}
			} );
		}
	});

	/**
	 * Overlay click event
	 *
	 */
	$( document ).on( 'click touchstart', '.uael-overlay, .uael-modal-scroll', function( e ) {

		if( $( e.target ).hasClass( 'uael-content' ) || $( e.target ).closest( '.uael-content' ).length > 0 ) {
			return;
		}

		var $this = $( this ).closest( '.uael-modal-parent-wrapper' );
		var tmp_id = $this.attr( 'id' );
		var popup_id = tmp_id.replace( '-overlay', '' );
		var close_on_overlay = $this.data( 'close-on-overlay' );

		if( 'yes' == close_on_overlay ) {
			UAELModalPopup._close( popup_id );
		}
	});

	/**
	 * Close img/icon clicked
	 *
	 */
	$( document ).on( 'click', '.uael-modal-close, .uael-close-modal', function() {

		var $this = $( this ).closest( '.uael-modal-parent-wrapper' );
		var tmp_id = $this.attr( 'id' );
		var popup_id = tmp_id.replace( '-overlay', '' );
		UAELModalPopup._close( popup_id );
	} );

	/**
	 * Trigger open modal popup on click img/icon/button/text
	 *
	 */
	$( document ).on( 'click', '.uael-trigger', function() {

		var popup_id = $( this ).closest( '.elementor-element' ).data( 'id' );
		var selector = $( '.uamodal-' + popup_id );
		var trigger_on = selector.data( 'trigger-on' );
		
		if(
			'text' == trigger_on
			|| 'icon' == trigger_on
			|| 'photo' == trigger_on
			|| 'button' == trigger_on
		) {
			UAELModalPopup._show( popup_id );
		}
	} );

	/**
	 * Center the modal popup event
	 *
	 */
	$( document ).on( 'uael_modal_popup_init', function( e, node_id ) {

		if( $( '#modal-' + node_id ).hasClass( 'uael-show-preview' ) ) {
			setTimeout( function() {
				UAELModalPopup._show( node_id );
			}, 400 );
		}

		var overlay_node = $( '#' + node_id + '-overlay' );
		var content_type = overlay_node.data( 'content' );
		var device = overlay_node.data( 'device' );

		if ( 'youtube' == content_type || 'vimeo' == content_type ) {

			if( 0 == $( '.uael-video-player iframe' ).length ) {

				$( '.uael-video-player' ).each( function( index, value ) {

					var div = $( "<div/>" ),
						$this = $( this );
						div.attr( 'data-id', $this.data( 'id' ) );
						div.attr( 'data-src', $this.data( 'src' ) );
						div.attr( 'data-sourcelink', $this.data( 'sourcelink' ) );
						div.html( '<img src="' + $this.data( 'thumb' ) + '"><div class="play ' + $this.data( 'play-icon' ) + '"></div>' );

					div.on( "click", videoIframe );

					$this.html( div );

					if( true == device ) {

						$( div[0] ).trigger( 'click' );
					}

				} );
			}

		}

		UAELModalPopup._centerModal( node_id );
	} );

	/**
	 * Resize event
	 *
	 */
	$( window ).on( 'resize', function() {
		UAELModalPopup._center();
	} );

	/**
	 * Exit intent event
	 *
	 */
	$( document ).on( 'mouseleave', function( e ) {

		if ( e.clientY > 20 ) {
            return;
        }

		$( '.uael-modal-parent-wrapper' ).each( function() {

			var $this = $( this );
			var tmp_id = $this.attr( 'id' );
			var popup_id = tmp_id.replace( '-overlay', '' );
			var trigger_on = $this.data( 'trigger-on' );
			var exit_intent = $this.data( 'exit-intent' );

			if( 'automatic' == trigger_on ) {
				if(
					'yes' == exit_intent
					&& UAELModalPopup._canShow( popup_id )
				) {
					UAELModalPopup._show( popup_id );
				}
			}
		} );
    } );

    function videoIframe() {

        var iframe = document.createElement( "iframe" );
        var src = this.dataset.src;

        var url = '';

        if ( 'youtube' == src ) {
        	url = this.dataset.sourcelink;
        } else {
        	url = this.dataset.sourcelink;
        }

        iframe.setAttribute( "src", url );
        iframe.setAttribute( "frameborder", "0" );
        iframe.setAttribute( "allowfullscreen", "1" );
        this.parentNode.replaceChild( iframe, this );
    }

	/**
	 * Load page event
	 *
	 */
	$( document ).ready( function( e ) {

		var current_url = window.location.href;
		if( current_url.indexOf( '&action=elementor' ) <= 0 ) {
			$( '.uael-modal-parent-wrapper' ).each( function() {
				$( this ).appendTo( document.body );
			});
		}

		UAELModalPopup._center();

		$( '.uael-modal-content-data' ).resize( function() {
	        UAELModalPopup._center();
	    } );

		$( '.uael-modal-parent-wrapper' ).each( function() {

			var $this = $( this );
			var tmp_id = $this.attr( 'id' );
			var popup_id = tmp_id.replace( '-overlay', '' );
			var trigger_on = $this.data( 'trigger-on' );
			var after_sec = $this.data( 'after-sec' );
			var after_sec_val = $this.data( 'after-sec-val' );
			var custom = $this.data( 'custom' );
			var custom_id = $this.data( 'custom-id' );

			// Trigger automatically.
			if( 'automatic' == trigger_on ) {
				if(
					'yes' == after_sec
					&& 'undefined' != typeof after_sec_val
				) {
					var id = popup_id;
					setTimeout( function() {
						if( UAELModalPopup._canShow( id ) ) {
							UAELModalPopup._show( id );
						}
					}, ( parseInt( after_sec_val ) * 1000 ) );
				}
			}

			// Custom Class click event
			if( 'custom' == trigger_on ) {
				if( 'undefined' != typeof custom && '' != custom ) {
					var custom_selectors = custom.split( ',' );
					if( custom_selectors.length > 0 ) {
						for( var i = 0; i < custom_selectors.length; i++ ) {
							if( 'undefined' != typeof custom_selectors[i] && '' != custom_selectors[i] ) {
								$('.' + custom_selectors[i]).css("cursor", "pointer");
								$( document ).on( 'click', '.' + custom_selectors[i], function() {
									UAELModalPopup._show( popup_id );
								} );
							}
						}
					}
				}
			}

			// Custom ID click event
			if( 'custom_id' == trigger_on ) {
				if( 'undefined' != typeof custom_id && '' != custom_id ) {
					var custom_selectors = custom_id.split( ',' );
					if( custom_selectors.length > 0 ) {
						for( var i = 0; i < custom_selectors.length; i++ ) {
							if( 'undefined' != typeof custom_selectors[i] && '' != custom_selectors[i] ) {
								$('#' + custom_selectors[i]).css("cursor", "pointer");
								$( document ).on( 'click', '#' + custom_selectors[i], function() {
									UAELModalPopup._show( popup_id );
								} );
							}
						}
					}
				}
			}

			if( 'via_url' == trigger_on ) {

				var path = window.location.href;
				var page_url = new URL( path );
				var param_modal_id = page_url.searchParams.get( "uael-modal-action" );

				if( param_modal_id === popup_id ) {
					UAELModalPopup._show( param_modal_id );
				}
			}
		} );
	} );

	/**
	 * Modal popup handler Function.
	 *
	 */
	var WidgetUAELModalPopupHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;
		
		var scope_id = $scope.data( 'id' ),
			modal_scope = $( '.uamodal-' + scope_id );

		if ( $scope.hasClass('elementor-hidden-desktop') ) {
        	modal_scope.addClass( 'uael-modal-hide-desktop' );
		}

		if ( $scope.hasClass('elementor-hidden-tablet') ) {
        	modal_scope.addClass( 'uael-modal-hide-tablet' );
		}

		if ( $scope.hasClass('elementor-hidden-phone') ) {
        	modal_scope.addClass( 'uael-modal-hide-phone' );
		}

		$( document ).trigger( 'uael_modal_popup_init', scope_id );
	};

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/uael-modal-popup.default', WidgetUAELModalPopupHandler );

	});

} )( jQuery );
