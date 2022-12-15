'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};

/**
 * WP Mail SMTP Admin area Smart Routing module.
 *
 * @since 3.7.0
 */
WPMailSMTP.Admin.SmartRouting = WPMailSMTP.Admin.SmartRouting || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 3.7.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine. DOM is not ready yet, use only to init something.
		 *
		 * @since 3.7.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 3.7.0
		 */
		ready: function() {

			app.bindActions();
		},

		/**
		 * Element bindings.
		 *
		 * @since 3.7.0
		 */
		bindActions: function() {

			var $holder = $( '.wp-mail-smtp-tab-routing' );

			$holder
				.on( 'change', '.wp-mail-smtp-smart-routing-route__connection', app.processConnectionUpdate )
				.on( 'click', '.wp-mail-smtp-smart-routing-route-add', app.processRouteAdd )
				.on( 'click', '.wp-mail-smtp-smart-routing-route__delete', app.processRouteDelete )
				.on( 'click', '.wp-mail-smtp-smart-routing-route__order-btn', app.processRouteOrder );
		},

		/**
		 * Process connection select update.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processConnectionUpdate: function( e ) {

			// Remove invalid connection elements.
			if ( $( this ).hasClass( 'wp-mail-smtp-smart-routing-route__connection--invalid' ) ) {
				$( this ).removeClass( 'wp-mail-smtp-smart-routing-route__connection--invalid' );
				$( this ).closest( '.wp-mail-smtp-smart-routing-route' )
					.find( '.wp-mail-smtp-smart-routing-route__notice--invalid' ).slideUp();
			}
		},

		/**
		 * Add new route.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processRouteAdd: function( e ) {

			e.preventDefault();

			var indexes = $( '.wp-mail-smtp-smart-routing-route__connection' ).map( function() {
				return $( this ).attr( 'name' ).match( /\[route-(\d)]/ )[ 1 ];
			} ).get();

			var maxIndex = indexes.reduce( function( a, b ) {
				return Math.max( a, b );
			}, 0 );

			var $routes = $( '.wp-mail-smtp-smart-routing-route' ),
				routeBlock = wp.template( 'wp-mail-smtp-smart-route' ),
				data = {
					routeIndex: 'route-' + ( maxIndex + 1 )
				};

			$routes.last().after( routeBlock( data ) );

			$( '.wp-mail-smtp-smart-routing-route__delete' ).prop( 'disabled', false );

			$( 'html' ).animate( {
				scrollTop: $routes.last().offset().top
			}, 500 );
		},

		/**
		 * Delete route.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processRouteDelete: function( e ) {

			e.preventDefault();

			var $routes = $( '.wp-mail-smtp-smart-routing-route' ),
				$route = $( this ).closest( '.wp-mail-smtp-smart-routing-route' );

			if ( $routes.length > 1 ) {
				$route.remove();
			} else {
				var routeBlock = wp.template( 'wp-mail-smtp-smart-route' );
				$route.replaceWith( routeBlock( { routeIndex: 'route-0' } ) );
			}
		},

		/**
		 * Re-order routes.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processRouteOrder: function( e ) {

			e.preventDefault();

			var $currentRoute = $( this ).closest( '.wp-mail-smtp-smart-routing-route' );

			if ( $( this ).hasClass( 'wp-mail-smtp-smart-routing-route__order-btn--down' ) ) {
				var $nextRoute = $currentRoute.next( '.wp-mail-smtp-smart-routing-route' );

				if ( $nextRoute.length !== 0 ) {
					$nextRoute.after( $currentRoute );
				}
			} else {
				var $prevRoute = $currentRoute.prev( '.wp-mail-smtp-smart-routing-route' );

				if ( $prevRoute.length !== 0 ) {
					$prevRoute.before( $currentRoute );
				}
			}
		},
	};

	return app;

}( document, window, jQuery ) );

WPMailSMTP.Admin.SmartRouting.init();
