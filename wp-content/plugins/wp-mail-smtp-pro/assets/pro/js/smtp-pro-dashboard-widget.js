/* global wp_mail_smtp_dashboard_widget, ajaxurl, moment, Chart */
/**
 * WP Mail SMTP Dashboard Widget function.
 *
 * @since 2.7.0
 */

'use strict';

var WPMailSMTPDashboardWidget = window.WPMailSMTPDashboardWidget || ( function( document, window, $ ) {

	/**
	 * Elements reference.
	 *
	 * @since 2.7.0
	 *
	 * @type {object}
	 */
	var el = {
		$widget              : $( '#wp_mail_smtp_reports_widget_pro' ),
		$daysSelect          : $( '#wp-mail-smtp-dash-widget-timespan' ),
		$emailTypeSelect     : $( '#wp-mail-smtp-dash-widget-email-type' ),
		$canvas              : $( '#wp-mail-smtp-dash-widget-chart' ),
		$emailStatsBlock     : $( '#wp-mail-smtp-dash-widget-email-stats-block' ),
		$recomBlockDismissBtn: $( '#wp-mail-smtp-dash-widget-dismiss-recommended-plugin-block' ),
		$settingsBtn         : $( '#wp-mail-smtp-dash-widget-settings-button' ),
	};

	/**
	 * The default chart dataset config.
	 *
	 * Always use a copy of this array with datasets.slice().
	 *
	 * @since 2.7.0
	 *
	 * @type {Array}
	 */
	var datasets = wp_mail_smtp_dashboard_widget.no_send_confirmations ?
		[
			{
				label: wp_mail_smtp_dashboard_widget.texts.sent_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(106, 160, 139, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			},
			{
				label: wp_mail_smtp_dashboard_widget.texts.failed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(214, 54, 56, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			}
		] : [
			{
				label: wp_mail_smtp_dashboard_widget.texts.confirmed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(106, 160, 139, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			},
			{
				label: wp_mail_smtp_dashboard_widget.texts.unconfirmed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(167, 170, 173, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			},
			{
				label: wp_mail_smtp_dashboard_widget.texts.failed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(214, 54, 56, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			}
		];

	/**
	 * Chart.js functions and properties.
	 *
	 * @since 2.7.0
	 *
	 * @type {object}
	 */
	var chart = {

		/**
		 * Chart.js instance.
		 *
		 * @since 2.7.0
		 */
		instance: null,

		/**
		 * The cached chart data.
		 *
		 * @since 2.7.0
		 */
		cachedData: {
			confirmed: [],
			unconfirmed: [],
			sent: [],
			failed: [],
		},

		/**
		 * Chart.js settings.
		 *
		 * @since 2.7.0
		 */
		settings: {
			type: 'line',
			data: {
				labels: [],
				datasets: datasets.slice(),
			},
			options: {
				maintainAspectRatio: false,
				scales: {
					xAxes: [ {
						type: 'time',
						time: {
							unit: 'day',
							tooltipFormat: 'MMM D',
						},
						distribution: 'series',
						ticks: {
							beginAtZero: true,
							source: 'labels',
							padding: 10,
							minRotation: 25,
							maxRotation: 25,
							callback: function( value, index, values ) {

								// Distribute the ticks equally starting from a right side of xAxis.
								var gap = Math.floor( values.length / 7 );

								if ( gap < 1 ) {
									return value;
								}
								if ( ( values.length - index - 1 ) % gap === 0 ) {
									return value;
								}
							},
						},
						offset: false,
						gridLines: {
							offsetGridLines: false,
						},
					} ],
					yAxes: [ {
						ticks: {
							beginAtZero: true,
							maxTicksLimit: 6,
							padding: 20,
							callback: function( value ) {

								// Make sure the tick value has no decimals.
								if ( Math.floor( value ) === value ) {
									return value;
								}
							},
						},
					} ],
				},
				elements: {
					line: {
						tension: 0,
					},
				},
				animation: {
					duration: 0,
				},
				hover: {
					animationDuration: 0,
				},
				legend: {
					display: false,
				},
				tooltips: {
					displayColors: false,
				},
				responsiveAnimationDuration: 0,
			},
		},

		/**
		 * Init Chart.js.
		 *
		 * @since 2.7.0
		 */
		init: function() {

			var ctx;

			if ( ! el.$canvas.length ) {
				return;
			}

			ctx = el.$canvas[ 0 ].getContext( '2d' );

			chart.instance = new Chart( ctx, chart.settings );

			chart.updateUI( wp_mail_smtp_dashboard_widget.chart_data );
		},

		/**
		 * Update Chart.js with a new AJAX data.
		 *
		 * @since 2.7.0
		 *
		 * @param {number} days Timespan (in days) to fetch the data for.
		 */
		ajaxUpdate: function( days ) {

			var data = {
				_wpnonce: wp_mail_smtp_dashboard_widget.nonce,
				action  : 'wp_mail_smtp_' + wp_mail_smtp_dashboard_widget.slug + '_get_chart_data',
				days    : days,
			};

			el.$widget.trigger( 'WPMailSMTP.Admin.DashboardWidget.ajaxUpdate' );

			$.post( ajaxurl, data, function( response ) {
				chart.updateUI( response );
			} );
		},

		/**
		 * Update Chart.js canvas.
		 *
		 * @since 2.7.0
		 *
		 * @param {object} data Dataset for the chart.
		 */
		updateUI: function( data ) {

			el.$widget.trigger( 'WPMailSMTP.Admin.DashboardWidget.updateUI' );

			var graphStyle = el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=style]:checked' ).val();

			if ( $.isEmptyObject( data ) ) {
				graphStyle = 'line';
				chart.updateWithEmptyData();
				chart.showEmptyDataMessage();
			} else {
				chart.updateData( data );
				chart.removeEmptyDataMessage();
			}

			chart.updateColorScheme(
				el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=color]:checked' ).val()
			);

			chart.updateStyle( graphStyle );

			chart.updateDatasets();

			chart.instance.update();
		},

		/**
		 * Update Chart.js settings data.
		 *
		 * @since 2.7.0
		 *
		 * @param {object} data Dataset for the chart.
		 */
		updateData: function( data ) {

			chart.settings.data.labels = [];
			chart.cachedData.confirmed = [];
			chart.cachedData.unconfirmed = [];
			chart.cachedData.sent = [];
			chart.cachedData.failed = [];

			$.each( data, function( index, value ) {

				var date = moment( value.day );

				chart.settings.data.labels.push( date );

				chart.cachedData.confirmed.push( {
					t: date,
					y: value.delivered,
				} );
				chart.cachedData.unconfirmed.push( {
					t: date,
					y: value.sent,
				} );
				chart.cachedData.sent.push( {
					t: date,
					y: value.sent + value.delivered,
				} );
				chart.cachedData.failed.push( {
					t: date,
					y: value.unsent,
				} );

			} );

			chart.updateChartData();
		},

		/**
		 * Update the chart data from the cache.
		 *
		 * @since 2.7.0
		 */
		updateChartData: function() {

			chart.settings.data.datasets = datasets.slice();

			if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
				chart.settings.data.datasets[0].data = chart.cachedData.sent;
				chart.settings.data.datasets[1].data = chart.cachedData.failed;
			} else {
				chart.settings.data.datasets[0].data = chart.cachedData.confirmed;
				chart.settings.data.datasets[1].data = chart.cachedData.unconfirmed;
				chart.settings.data.datasets[2].data = chart.cachedData.failed;
			}
		},

		/**
		 * Update Chart.js settings with empty data (just x-axis dates).
		 *
		 * @since 2.7.0
		 */
		updateWithEmptyData: function() {

			chart.settings.data.labels = [];

			var end = moment().startOf( 'day' );
			var days = el.$daysSelect.val() || 7;
			var date;
			var i;

			for ( i = 1; i <= days; i++ ) {

				date = end.clone().subtract( i, 'days' );

				chart.settings.data.labels.push( date );
			}
		},

		/**
		 * Update the color scheme of the graph.
		 *
		 * @since 2.7.0
		 *
		 * @param {string} colorScheme The color scheme to update to.
		 */
		updateColorScheme: function( colorScheme ) {

			var style = el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=style]:checked' ).val();

			var colors = [ 'rgba(0, 0, 0, 0)', 'rgba(0, 0, 0, 0)', 'rgba(0, 0, 0, 0)' ];

			if ( colorScheme === 'smtp' ) {
				colors = chart.updateSmtpColorScheme();
			} else if ( colorScheme === 'wp' ) {
				colors = chart.updateWpColorScheme();
			}

			if ( style === 'bar' ) {
				if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
					colors[0] = chart.settings.data.datasets[ 0 ].borderColor;
					colors[2] = chart.settings.data.datasets[ 1 ].borderColor;
				} else {
					colors[0] = chart.settings.data.datasets[ 0 ].borderColor;
					colors[1] = chart.settings.data.datasets[ 1 ].borderColor;
					colors[2] = chart.settings.data.datasets[ 2 ].borderColor;
				}
			}

			if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
				chart.settings.data.datasets[ 0 ].backgroundColor = colors[0];
				chart.settings.data.datasets[ 1 ].backgroundColor = colors[2];
			} else {
				chart.settings.data.datasets[ 0 ].backgroundColor = colors[0];
				chart.settings.data.datasets[ 1 ].backgroundColor = colors[1];
				chart.settings.data.datasets[ 2 ].backgroundColor = colors[2];
			}
		},

		/**
		 * Update the colors to the SMTP color scheme.
		 *
		 * @returns {[string, string, string]} The confirmed, sent and unsent background color.
		 */
		updateSmtpColorScheme: function() {

			var emailType = el.$emailTypeSelect.val();

			var confirmedBg = 'rgba(0, 0, 0, 0)',
				sentBg = 'rgba(0, 0, 0, 0)',
				unsentBg = 'rgba(0, 0, 0, 0)';

			if ( emailType !== 'all' ) {
				confirmedBg = 'rgba(106, 160, 139, 0.16)';
				sentBg = 'rgba(167, 170, 173, 0.16)';
				unsentBg = 'rgba(214, 54, 56, 0.16)';
			}

			if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
				chart.settings.data.datasets[ 0 ].borderColor = 'rgba(106, 160, 139, 1)';
				chart.settings.data.datasets[ 1 ].borderColor = 'rgba(214, 54, 56, 1)';
			} else {
				chart.settings.data.datasets[ 0 ].borderColor = 'rgba(106, 160, 139, 1)';
				chart.settings.data.datasets[ 1 ].borderColor = 'rgba(167, 170, 173, 1)';
				chart.settings.data.datasets[ 2 ].borderColor = 'rgba(214, 54, 56, 1)';
			}

			return [ confirmedBg, sentBg, unsentBg ];
		},

		/**
		 * Update the colors to the WordPress color scheme.
		 *
		 * @returns {[string, string, string]} The confirmed, sent and unsent background color.
		 */
		updateWpColorScheme: function() {

			var emailType = el.$emailTypeSelect.val();

			var confirmedBg = 'rgba(0, 0, 0, 0)',
				sentBg = 'rgba(0, 0, 0, 0)',
				unsentBg = 'rgba(0, 0, 0, 0)';

			if ( emailType !== 'all' ) {
				confirmedBg = 'rgba(34, 113, 177, 0.16)';
				sentBg = 'rgba(167, 170, 173, 0.16)';
				unsentBg = 'rgba(214, 54, 56, 0.16)';
			}

			if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
				chart.settings.data.datasets[ 0 ].borderColor = 'rgba(34, 113, 177, 1)';
				chart.settings.data.datasets[ 1 ].borderColor = 'rgba(214, 54, 56, 1)';
			} else {
				chart.settings.data.datasets[ 0 ].borderColor = 'rgba(34, 113, 177, 1)';
				chart.settings.data.datasets[ 1 ].borderColor = 'rgba(167, 170, 173, 1)';
				chart.settings.data.datasets[ 2 ].borderColor = 'rgba(214, 54, 56, 1)';
			}

			return [ confirmedBg, sentBg, unsentBg ];
		},

		/**
		 * Remove/hide any datasets that are not needed.
		 * This enables the bar chart with single dataset (full width bar display).
		 * Removing datasets for line graph breaks the Graph library, so we are setting their data to [].
		 *
		 * @since 2.7.0
		 */
		updateDatasets: function() {

			var style = el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=style]:checked' ).val();

			if ( style === 'line' ) {
				chart.updateLineDatasets();
			} else if ( style === 'bar' ) {
				chart.updateBarDatasets();
			}
		},

		/**
		 * Update the datasets for the line graph type.
		 */
		updateLineDatasets: function() { // eslint-disable-line complexity

			switch ( el.$emailTypeSelect.val() ) {
				case 'delivered':
					chart.settings.data.datasets[1].data = [];

					if ( ! wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
						chart.settings.data.datasets[2].data = [];
					}
					break;
				case 'sent':
					if ( wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
						chart.settings.data.datasets[1].data = [];
					} else {
						chart.settings.data.datasets[0].data = [];
						chart.settings.data.datasets[2].data = [];
					}
					break;
				case 'unsent':
					chart.settings.data.datasets[0].data = [];

					if ( ! wp_mail_smtp_dashboard_widget.no_send_confirmations ) {
						chart.settings.data.datasets[1].data = [];
					}
					break;
			}
		},

		/**
		 * Update the datasets for the bar graph type.
		 */
		updateBarDatasets: function() {

			switch ( el.$emailTypeSelect.val() ) {
				case 'delivered':
					chart.settings.data.datasets = chart.settings.data.datasets.slice( 0, 1 );
					break;
				case 'sent':
					chart.settings.data.datasets = wp_mail_smtp_dashboard_widget.no_send_confirmations ?
						chart.settings.data.datasets.slice( 0, 1 ) :
						chart.settings.data.datasets.slice( 1, 2 );
					break;
				case 'unsent':
					chart.settings.data.datasets = wp_mail_smtp_dashboard_widget.no_send_confirmations ?
						chart.settings.data.datasets.slice( 1 ) :
						chart.settings.data.datasets.slice( 2 );
					break;
			}
		},

		/**
		 * Update the style of the chart.
		 *
		 * @param {string} style The style of the chart.
		 */
		updateStyle: function( style ) {

			chart.settings.type = style;

			if ( style === 'line' ) {
				chart.settings.options.scales.xAxes[0].offset = false;
				chart.settings.options.scales.xAxes[0].gridLines.offsetGridLines = false;
			} else if ( style === 'bar' ) {
				chart.settings.options.scales.xAxes[0].offset = true;
				chart.settings.options.scales.xAxes[0].gridLines.offsetGridLines = true;
			}
		},

		/**
		 * Display an error message if the chart data is empty.
		 *
		 * @since 2.7.0
		 */
		showEmptyDataMessage: function() {

			chart.removeEmptyDataMessage();
			el.$canvas.after( wp_mail_smtp_dashboard_widget.empty_chart_html );
		},

		/**
		 * Remove all empty data error messages.
		 *
		 * @since 2.7.0
		 */
		removeEmptyDataMessage: function() {

			el.$canvas.siblings( '.wp-mail-smtp-error' ).remove();
		},

		/**
		 * Chart related event callbacks.
		 *
		 * @since 2.7.0
		 */
		events: {

			/**
			 * Update a chart on a timespan change.
			 *
			 * @since 2.7.0
			 */
			daysChanged: function() {

				var days = el.$daysSelect.val();

				chart.ajaxUpdate( days );

				el.$widget.trigger( 'WPMailSMTP.Admin.DashboardWidget.daysChanged', days );
			},

			/**
			 * Update the chart on a email type select change.
			 *
			 * @since 2.7.0
			 */
			emailTypeChanged: function() {

				var emailType = el.$emailTypeSelect.val();

				chart.updateChartData();
				chart.updateColorScheme(
					el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=color]:checked' ).val()
				);
				chart.updateDatasets();
				chart.instance.update();

				el.$widget.trigger( 'WPMailSMTP.Admin.DashboardWidget.emailTypeChanged', emailType );
			},
		},
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 2.7.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Publicly accessible Chart.js functions and properties.
		 *
		 * @since 2.7.0
		 */
		chart: chart,

		/**
		 * Start the engine.
		 *
		 * @since 2.7.0
		 */
		init: function() {
			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 2.7.0
		 */
		ready: function() {

			// The app events need to be registered before the chart initializes.
			app.events();

			chart.init();

			// Update email stats display.
			app.refreshEmailStats( el.$emailTypeSelect.val() );
		},

		/**
		 * Register JS events.
		 *
		 * @since 2.7.0
		 */
		events: function() {

			app.chartEvents();
			app.miscEvents();
		},

		/**
		 * Register chart area JS events.
		 *
		 * @since 2.7.0
		 */
		chartEvents: function() {

			el.$daysSelect.on( 'change', function() {
				chart.events.daysChanged();
				app.updateEmailStats(
					$( this ).val(),
					el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=color]:checked' ).val()
				);
			} );

			el.$emailTypeSelect.on( 'change', function() {
				chart.events.emailTypeChanged();
				app.refreshEmailStats( $( this ).val() );
			} );
		},

		/**
		 * Register other JS events.
		 *
		 * @since 2.7.0
		 */
		miscEvents: function() {

			el.$recomBlockDismissBtn.on( 'click', function() {
				app.dismissRecommendedBlock();
			} );

			el.$settingsBtn.on( 'click', function() {
				$( this ).siblings( '.wp-mail-smtp-dash-widget-settings-menu' ).toggle();
			} );

			el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu-save' ).on( 'click', function() {
				app.saveSettings();
			} );

			el.$widget.on( 'WPMailSMTP.Admin.DashboardWidget.ajaxUpdate', function() {
				app.addOverlay( el.$canvas );
			} );

			el.$widget.on( 'WPMailSMTP.Admin.DashboardWidget.updateUI', function() {
				app.removeOverlay( el.$canvas );
			} );

			el.$widget.on( 'WPMailSMTP.Admin.DashboardWidget.daysChanged', function( event, days ) {
				app.saveWidgetMeta( 'timespan', days );
			} );

			el.$widget.on( 'WPMailSMTP.Admin.DashboardWidget.emailTypeChanged', function( event, emailType ) {
				app.saveWidgetMeta( 'email_type', emailType );
			} );
		},

		/**
		 * Update email stats with a new AJAX data.
		 *
		 * @since 2.7.0
		 *
		 * @param {number} days Timespan (in days) to fetch the data for.
		 * @param {string} colorScheme color scheme to fetch the data for.
		 */
		updateEmailStats: function( days, colorScheme ) {

			var data = {
				_wpnonce: wp_mail_smtp_dashboard_widget.nonce,
				action  : 'wp_mail_smtp_' + wp_mail_smtp_dashboard_widget.slug + '_get_email_stats',
				days    : days,
				color   : colorScheme,
			};

			app.addOverlay( el.$emailStatsBlock.children().first() );

			$.post( ajaxurl, data, function( response ) {
				el.$emailStatsBlock.html( response );
				app.refreshEmailStats( el.$emailTypeSelect.val() );
				app.saveWidgetMeta( 'timespan', days );
			} );
		},

		/**
		 * Show/hide email stats for selected email type.
		 *
		 * @since 2.7.0
		 *
		 * @param {string} emailType The email type: 'all', 'delivered', 'sent', 'unsent'.
		 */
		refreshEmailStats: function( emailType ) {

			var allStats = el.$emailStatsBlock.find( '.wp-mail-smtp-dash-widget-email-stats-table-cell' );

			if ( emailType !== 'all' ) {
				allStats.hide();
				el.$emailStatsBlock.find( '.wp-mail-smtp-dash-widget-email-stats-table-cell--' + emailType ).show();
			} else {
				allStats.show();
			}
		},

		/**
		 * Save the widgets settings.
		 *
		 * @since 2.7.0
		 */
		saveSettings: function() {

			var style = el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=style]:checked' ).val();
			var color = el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu input[name=color]:checked' ).val();

			if ( style ) {
				app.saveWidgetMeta( 'graph_style', style );
			}

			if ( color ) {
				app.saveWidgetMeta( 'color_scheme', color );
			}

			app.updateEmailStats( el.$daysSelect.val(), color );
			chart.updateChartData();
			chart.updateColorScheme( color );
			chart.updateStyle( style );
			chart.updateDatasets();
			chart.instance.update();

			el.$widget.find( '.wp-mail-smtp-dash-widget-settings-menu' ).hide();
		},

		/**
		 * Save dashboard widget meta in backend.
		 *
		 * @since 2.7.0
		 *
		 * @param {string} meta Meta name to save.
		 * @param {number} value Value to save.
		 */
		saveWidgetMeta: function( meta, value ) {

			var data = {
				_wpnonce: wp_mail_smtp_dashboard_widget.nonce,
				action  : 'wp_mail_smtp_' + wp_mail_smtp_dashboard_widget.slug + '_save_widget_meta',
				meta    : meta,
				value   : value,
			};

			$.post( ajaxurl, data );
		},

		/**
		 * Add an overlay to a widget block containing $el.
		 *
		 * @since 2.7.0
		 *
		 * @param {object} $el jQuery element inside a widget block.
		 */
		addOverlay: function( $el ) {

			if ( ! $el.parent().closest( '.wp-mail-smtp-dash-widget-block' ).length ) {
				return;
			}

			app.removeOverlay( $el );
			$el.after( '<div class="wp-mail-smtp-dash-widget-overlay"></div>' );
		},

		/**
		 * Remove an overlay from a widget block containing $el.
		 *
		 * @since 2.7.0
		 *
		 * @param {object} $el jQuery element inside a widget block.
		 */
		removeOverlay: function( $el ) {
			$el.siblings( '.wp-mail-smtp-dash-widget-overlay' ).remove();
		},

		/**
		 * Dismiss recommended plugin block.
		 *
		 * @since 2.7.0
		 */
		dismissRecommendedBlock: function() {

			$( '.wp-mail-smtp-dash-widget-recommended-plugin-block' ).remove();
			app.saveWidgetMeta( 'hide_recommended_block', 1 );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPMailSMTPDashboardWidget.init();
