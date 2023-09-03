/* global wp_mail_smtp, ajaxurl, wp_mail_smtp_alerts */
'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};

/**
 * WP Mail SMTP Admin area Alerts module.
 *
 * @since 3.5.0
 */
WPMailSMTP.Admin.Alerts = WPMailSMTP.Admin.Alerts || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 3.5.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine. DOM is not ready yet, use only to init something.
		 *
		 * @since 3.5.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 3.5.0
		 */
		ready: function() {

			app.bindActions();

			app.insertRemoveConnectionButtons();

			app.toggleTestAlertsButton();
		},

		/**
		 * Process all generic actions/events, mostly custom that were fired by our API.
		 *
		 * @since 3.5.0
		 */
		bindActions: function() {

			$( '.wp-mail-smtp-tab-alerts' )
				.on( 'click', '.js-wp-mail-smtp-setting-alert-add-connection', app.addConnection )
				.on( 'click', '.js-wp-mail-smtp-setting-alert-remove-connection', app.removeConnection )
				.on( 'change', '.js-wp-mail-smtp-setting-alert-enabled', app.toggleAlertSettings )
				.on( 'change', '.js-wp-mail-smtp-setting-alert-enabled', app.toggleTestAlertsButton )
				.on( 'click', '#js-wp-mail-smtp-btn-test-alerts', app.submitTestAlerts );
		},

		/**
		 * Show/hide alert settings on alert enable/disable.
		 *
		 * @since 3.5.0
		 */
		toggleAlertSettings: function() {

			var $options = $( this ).closest( '.wp-mail-smtp-setting-row-alert' ).find( '.wp-mail-smtp-setting-row-alert-options' ),
				$inputs = $options.find( '.wp-mail-smtp-setting-field input' );

			if ( $( this ).is( ':checked' ) ) {
				$options.show();
				$inputs.prop( 'required', true );
			} else {
				$options.hide();
				$inputs.prop( 'required', false );
			}
		},

		/**
		 * Add new connection.
		 *
		 * @since 3.5.0
		 *
		 * @param {object} event jQuery event.
		 */
		addConnection: function( event ) {

			event.preventDefault();

			var $optionsHolder = $( this ).closest( '.wp-mail-smtp-setting-row-alert-options' ),
				$btnRow = $( this ).closest( '.wp-mail-smtp-setting-row' ),
				provider = $( this ).data( 'provider' ),
				connectionsCount = $optionsHolder.find( '.wp-mail-smtp-setting-row-alert-connection-options' ).length,
				connectionOptionsTmpl = wp_mail_smtp_alerts.providers[ provider ].connection_options_tmpl,
				maxConnectionsCount = wp_mail_smtp_alerts.providers[ provider ].max_connections_count;

			var $connectionOptionsTmpl = $( connectionOptionsTmpl.replaceAll( '%%index%%', connectionsCount ) );

			if ( connectionsCount === 1 ) {
				app.insertRemoveConnectionButton( $optionsHolder.find( '.wp-mail-smtp-setting-row-alert-connection-options' ) );
			}

			app.insertRemoveConnectionButton( $connectionOptionsTmpl );

			$btnRow.before( $connectionOptionsTmpl );

			if ( connectionsCount + 1 === maxConnectionsCount ) {
				$( this ).prop( 'disabled', true );
			}
		},

		/**
		 * Remove connection.
		 *
		 * @since 3.5.0
		 */
		removeConnection: function() {

			var $optionsHolder = $( this ).closest( '.wp-mail-smtp-setting-row-alert-options' ),
				$addConnectionBtn = $optionsHolder.find( '.js-wp-mail-smtp-setting-alert-add-connection' ),
				provider = $addConnectionBtn.data( 'provider' ),
				maxConnectionsCount = wp_mail_smtp_alerts.providers[ provider ].max_connections_count;

			$( this ).closest( '.wp-mail-smtp-setting-row-alert-connection-options' ).remove();

			var connectionsCount = $optionsHolder.find( '.wp-mail-smtp-setting-row-alert-connection-options' ).length;

			if ( connectionsCount === 1 ) {
				$optionsHolder.find( '.js-wp-mail-smtp-setting-alert-remove-connection' ).remove();
			}

			if ( connectionsCount < maxConnectionsCount ) {
				$addConnectionBtn.prop( 'disabled', false );
			}
		},

		/**
		 * Insert remove connection button to connection options row.
		 *
		 * @since 3.5.0
		 *
		 * @param {object} $connectionOptions Connection options row jQuery object.
		 */
		insertRemoveConnectionButton: function( $connectionOptions ) {

			$connectionOptions.find( '.wp-mail-smtp-setting-field' ).first()
				.append( '<i class="js-wp-mail-smtp-setting-alert-remove-connection dashicons dashicons-trash"></i>' );
		},

		/**
		 * Insert remove connection buttons to all providers if connections count more than one per provider.
		 *
		 * @since 3.5.0
		 */
		insertRemoveConnectionButtons: function() {

			$( '.wp-mail-smtp-setting-row-alert-options' ).each( function() {
				var $connectionsOptions = $( this ).find( '.wp-mail-smtp-setting-row-alert-connection-options' );

				if ( $connectionsOptions.length > 1 ) {
					$connectionsOptions.each( function() {
						app.insertRemoveConnectionButton( $( this ) );
					} );
				}
			} );
		},

		/**
		 * Enable the test alerts button when at least one
		 * alert channel is enabled, or disable it otherwise.
		 *
		 * @since 3.9.0
		 */
		toggleTestAlertsButton: function() {

			var enabledChannelsCount = $( '.wp-mail-smtp-tab-alerts .js-wp-mail-smtp-setting-alert-enabled:checked' ).length;
			var $button = $( '#js-wp-mail-smtp-btn-test-alerts' );

			if ( enabledChannelsCount > 0 ) {
				$button.prop( 'disabled', false );
			} else {
				$button.prop( 'disabled', true );
			}
		},

		/**
		 * Submit test alerts action.
		 *
		 * @since 3.9.0
		 */
		submitTestAlerts: function() {

			// If settings have been changed, display an alert
			// and bail early.
			if ( WPMailSMTP.Admin.Settings.pluginSettingsChanged ) {
				$.confirm( {
					backgroundDismiss: true,
					escapeKey: true,
					animationBounce: 1,
					closeIcon: true,
					type: 'blue',
					icon: app.getModalIcon( 'info-circle-blue' ),
					title: wp_mail_smtp_alerts.texts.alert_title,
					content: wp_mail_smtp_alerts.texts.alert_content,
					buttons: {
						confirm: {
							text: wp_mail_smtp_alerts.texts.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						},
					}
				} );

				return;
			}

			$.post( ajaxurl, {
				action: 'wp_mail_smtp_ajax',
				nonce: wp_mail_smtp.nonce,
				task: 'test_alerts',
			} ).then( function() {
				window.location.replace( window.location.href );
			} );
		},

		/**
		 * Returns prepared modal icon.
		 *
		 * @since 3.9.0
		 *
		 * @param {string} icon The icon name from /assets/images/font-awesome/ to be used in modal.
		 *
		 * @returns {string} Modal icon HTML.
		 */
		getModalIcon: function( icon ) {

			return '"></i><img src="' + wp_mail_smtp_alerts.plugin_url + '/assets/images/font-awesome/' + icon + '.svg" style="width: 40px; height: 40px;" alt=""><i class="';
		},
	};

	// Provide access to public functions/properties.
	return app;
}( document, window, jQuery ) );

// Initialize.
WPMailSMTP.Admin.Alerts.init();
