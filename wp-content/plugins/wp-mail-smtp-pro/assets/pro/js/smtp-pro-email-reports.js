/* global wp_mail_smtp, wp_mail_smtp_email_reports, moment, WPMailSMTPChart, flatpickr */
/**
 * WP Mail SMTP Email Reports function.
 *
 * @since 3.0.0
 */

'use strict';

var WPMailSMTPEmailReports = window.WPMailSMTPEmailReports || ( function( document, window, $ ) {

	/**
	 * Elements reference.
	 *
	 * @since 3.0.0
	 *
	 * @type {object}
	 */
	var el = {
		$canvas: $( '#wp-mail-smtp-email-reports-chart' ),
		$timespanSelect: $( '.wp-mail-smtp-filter-date select[name="timespan"]' ),
		$dateInput: $( '.wp-mail-smtp-filter-date input[name="date"]' ),
		$reportsTitle: $( '.wp-mail-smtp-email-reports__title' ),
		$spinner: $( '.wp-mail-smtp-email-reports .spinner' ),
	};

	/**
	 * The default chart dataset config.
	 *
	 * Always use a copy of this array with datasets.slice().
	 *
	 * @since 3.0.0
	 *
	 * @type {Array}
	 */
	var datasets = wp_mail_smtp_email_reports.no_send_confirmations ?
		[
			{
				label: wp_mail_smtp_email_reports.texts.sent_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(106, 160, 139, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				key: 'sent',
			},
			{
				label: wp_mail_smtp_email_reports.texts.failed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(214, 54, 56, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				key: 'failed',
			}
		] : [
			{
				label: wp_mail_smtp_email_reports.texts.confirmed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(106, 160, 139, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				key: 'confirmed',
			},
			{
				label: wp_mail_smtp_email_reports.texts.unconfirmed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(167, 170, 173, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				key: 'unconfirmed',
			},
			{
				label: wp_mail_smtp_email_reports.texts.failed_emails,
				data: [],
				backgroundColor: 'rgba(0, 0, 0, 0)',
				borderColor: 'rgba(214, 54, 56, 1)',
				borderWidth: 2,
				pointRadius: 4,
				pointBorderWidth: 1,
				pointBackgroundColor: 'rgba(255, 255, 255, 1)',
				key: 'failed',
			}
		];

	if ( wp_mail_smtp_email_reports.open_email_tracking ) {
		datasets.push( {
			label: wp_mail_smtp_email_reports.texts.opened_emails,
			data: [],
			backgroundColor: 'rgba(0, 0, 0, 0)',
			borderColor: 'rgba(220, 127, 60, 1)',
			borderWidth: 2,
			pointRadius: 4,
			pointBorderWidth: 1,
			pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			key: 'openCount',
		} );
	}

	if ( wp_mail_smtp_email_reports.click_link_tracking ) {
		datasets.push( {
			label: wp_mail_smtp_email_reports.texts.clicked_links,
			data: [],
			backgroundColor: 'rgba(0, 0, 0, 0)',
			borderColor: 'rgba(251, 170, 111, 1)',
			borderWidth: 2,
			pointRadius: 4,
			pointBorderWidth: 1,
			pointBackgroundColor: 'rgba(255, 255, 255, 1)',
			key: 'clickCount',
		} );
	}

	/**
	 * Chart.js functions and properties.
	 *
	 * @since 3.0.0
	 *
	 * @type {object}
	 */
	var chart = {

		/**
		 * Chart.js instance.
		 *
		 * @since 3.0.0
		 */
		instance: null,

		/**
		 * Chart.js settings.
		 *
		 * @since 3.0.0
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
		 * @since 3.0.0
		 */
		init: function() {

			var ctx;

			if ( ! el.$canvas.length ) {
				return;
			}

			ctx = el.$canvas[ 0 ].getContext( '2d' );

			chart.instance = new WPMailSMTPChart( ctx, chart.settings );

			chart.updateUI( wp_mail_smtp_email_reports.stats_by_date_chart_data );
		},

		/**
		 * Update Chart.js canvas.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} data Dataset for the chart.
		 */
		updateUI: function( data ) {

			chart.updateChartData( data );

			chart.instance.update();
		},

		/**
		 * Update Chart.js settings data.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} data Dataset for the chart.
		 */
		updateChartData: function( data ) {

			chart.settings.data.labels = [];

			var chartData = {
				confirmed: [],
				unconfirmed: [],
				sent: [],
				failed: [],
				openCount: [],
				clickCount: []
			};

			$.each( data, function( index, value ) {

				var date = moment( value.day );

				chart.settings.data.labels.push( date );

				chartData.confirmed.push( {
					t: date,
					y: value.delivered,
				} );
				chartData.unconfirmed.push( {
					t: date,
					y: value.sent,
				} );
				chartData.sent.push( {
					t: date,
					y: Number( value.sent ) + Number( value.delivered ),
				} );
				chartData.failed.push( {
					t: date,
					y: value.unsent,
				} );
				chartData.openCount.push( {
					t: date,
					y: value.open_count,
				} );
				chartData.clickCount.push( {
					t: date,
					y: value.click_count,
				} );
			} );

			chart.settings.data.datasets = datasets.slice();

			for ( var i = 0; i < chart.settings.data.datasets.length; i++ ) {
				chart.settings.data.datasets[ i ].data = chartData[ chart.settings.data.datasets[ i ].key ];
			}
		}
	};

	/**
	 * Public functions and properties.
	 *
	 * @since 3.0.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Publicly accessible Chart.js functions and properties.
		 *
		 * @since 3.0.0
		 */
		chart: chart,

		/**
		 * Start the engine.
		 *
		 * @since 3.0.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 3.0.0
		 */
		ready: function() {

			app.events();

			chart.init();

			app.updateTotalsUI( wp_mail_smtp_email_reports.stats_totals );

			app.initFlatpickr();
		},

		/**
		 * Register JS events.
		 *
		 * @since 3.0.0
		 */
		events: function() {

			// Show/hide date input based on timespan value.
			el.$timespanSelect.on( 'change', function() {
				app.showHideDateInput();
			} ).trigger( 'change' );

			// Reset stats to default state.
			$( document ).on( 'click', '.js-wp-mail-smtp-reset-stats', function() {
				app.resetStatsUI();
			} );

			// Enable/disable single stat view.
			$( '.js-wp-mail-smtp-toggle-single-stats' ).on( 'click', app.toggleSingleStats );
			$( '.subject-toggle-single-stats' ).on( 'click', app.subjectToggleSingleStats );
		},

		/**
		 * Show/hide date input based on timespan value.
		 *
		 * @since 3.0.0
		 */
		showHideDateInput: function() {

			var $dateHolder = $( '.wp-mail-smtp-filter-date' ),
				visibilityClass = 'wp-mail-smtp-filter-date-custom';

			if ( el.$timespanSelect.val() === 'custom' ) {
				$dateHolder.addClass( visibilityClass );
			} else {
				$dateHolder.removeClass( visibilityClass );
			}
		},

		/**
		 * Show single stats of a clicked subject.
		 *
		 * @since 3.7.0
		 *
		 * @param {Event} e Event object.
		 */
		subjectToggleSingleStats: function( e ) {

			e.preventDefault();

			const $self = $( this ),
				$tableRow = $self.closest( 'tr' );

			if ( $tableRow.hasClass( 'wp-mail-smtp-active-row' ) ) {
				return;
			}

			app.performToggleSingleStats(
				$tableRow,
				$self.data( 'subject' ),
				$tableRow.find( '.js-wp-mail-smtp-toggle-single-stats' ),
				false
			);
		},

		/**
		 * Enable/disable single stats view.
		 *
		 * @since 3.0.0
		 * @since 3.7.0 Move the logic to load the stats to `app.performToggleSingleStats()`.
		 */
		toggleSingleStats: function() {

			const $self = $( this );

			app.performToggleSingleStats(
				$self.closest( 'tr' ),
				$self.data( 'subject' ),
				$self,
				$self.find( '.dashicons' ).hasClass( 'dashicons-dismiss' )
			);
		},

		/**
		 * Toggle single stats of a table row.
		 *
		 * @since 3.7.0
		 *
		 * @param {object}  $tableRow     Table row of the subject toggled.
		 * @param {string}  subject       Email subject.
		 * @param {object}  $toggleButton Graph button.
		 * @param {boolean} dismiss       Whether the action is to dismiss the single stats.
		 */
		performToggleSingleStats: function( $tableRow, subject, $toggleButton, dismiss ) {

			if ( dismiss ) {
				app.resetStatsUI();
				return;
			}

			// Reset table state.
			app.resetTableState();

			const $icon = $toggleButton.find( '.dashicons' ),
				timespan = el.$timespanSelect.val(),
				date = el.$dateInput.val();

			// Display spinnner in graph.
			el.$spinner.removeClass( 'wp-mail-smtp-hide' );

			// Update table state.
			$tableRow.addClass( 'wp-mail-smtp-active-row' );

			$toggleButton.addClass( 'dismiss-single-stats' );
			$icon.removeClass( 'dashicons-chart-line' ).addClass( 'dashicons-dismiss' );

			app.loadSingleStats( subject, timespan, date ).done( function( data ) {

				// Update heading.
				const $dismissIcon = '<i class="dashicons dashicons-dismiss js-wp-mail-smtp-reset-stats"></i>';
				el.$reportsTitle.html( subject + $dismissIcon );

				// Update totals and chart.
				app.updateTotalsUI( data.totals );
				chart.updateUI( data.by_date_chart_data );

				// Hide the spinner.
				el.$spinner.addClass( 'wp-mail-smtp-hide' );
			} );
		},

		/**
		 * Load single stats data via AJAX.
		 *
		 * @since 3.0.0
		 *
		 * @param {string} subject Email subject.
		 * @param {string} timespan Filter timespan.
		 * @param {string} date Filter date.
		 *
		 * @returns {jqXHR} xhr object.
		 */
		loadSingleStats: function( subject, timespan, date ) {

			var data = {
				_wpnonce: wp_mail_smtp_email_reports.nonce,
				action: 'wp_mail_smtp_email_reports_get_single_stats',
				s: subject,
				timespan: timespan,
				date: date
			};

			return $.post( wp_mail_smtp.ajax_url, data );
		},

		/**
		 * Reset stats UI to default state.
		 *
		 * @since 3.0.0
		 */
		resetStatsUI: function() {

			// Reset heading.
			if ( wp_mail_smtp_email_reports.is_search ) {
				el.$reportsTitle.html( wp_mail_smtp_email_reports.texts.search_results );
			} else {
				el.$reportsTitle.html( wp_mail_smtp_email_reports.texts.all_emails );
			}

			// Reset totals and chart.
			app.updateTotalsUI( wp_mail_smtp_email_reports.stats_totals );
			chart.updateUI( wp_mail_smtp_email_reports.stats_by_date_chart_data );
			app.resetTableState();
		},

		/**
		 * Reset table state.
		 *
		 * @since 3.7.0
		 */
		resetTableState: function() {

			// Reset table state.
			const $wpListTable = $( '.wp-list-table' );

			$wpListTable.find( '.wp-mail-smtp-active-row' ).removeClass( 'wp-mail-smtp-active-row' );
			$wpListTable.find( '.dashicons-dismiss' ).removeClass( 'dashicons-dismiss' ).addClass( 'dashicons-chart-line' );
			$wpListTable.find( '.dismiss-single-stats' ).removeClass( 'dismiss-single-stats' );
		},

		/**
		 * Update totals counts.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} totals Totals counts.
		 */
		updateTotalsUI: function( totals ) {

			// Skip UI update if the totals object is empty.
			if ( totals === null ) {
				return;
			}

			var sentCount = Number( totals.sent ) + Number( totals.delivered ),
				totalCount = sentCount + Number( totals.unsent );

			$( '.wp-mail-smtp-email-reports__stats-item--total span' ).text( totalCount );
			$( '.wp-mail-smtp-email-reports__stats-item--unsent span' ).text( totals.unsent );

			if ( wp_mail_smtp_email_reports.no_send_confirmations ) {
				$( '.wp-mail-smtp-email-reports__stats-item--sent span' ).text( sentCount );
			} else {
				$( '.wp-mail-smtp-email-reports__stats-item--confirmed span' ).text( totals.delivered );
				$( '.wp-mail-smtp-email-reports__stats-item--unconfirmed span' ).text( totals.sent );
			}

			if ( wp_mail_smtp_email_reports.open_email_tracking ) {
				$( '.wp-mail-smtp-email-reports__stats-item--open-count span' ).text( totals.open_count );
			}

			if ( wp_mail_smtp_email_reports.click_link_tracking ) {
				$( '.wp-mail-smtp-email-reports__stats-item--click-count span' ).text( totals.click_count );
			}
		},

		/**
		 * Init date picker.
		 *
		 * @since 3.0.0
		 */
		initFlatpickr: function() {

			var flatpickrLocale = {
					rangeSeparator: ' - ',
				},
				args = {
					altInput: true,
					altFormat: 'M j, Y',
					dateFormat: 'Y-m-d',
					mode: 'range',
					maxDate: moment().subtract( 1, 'days' ).toDate()
				};

			if (
				flatpickr !== 'undefined' &&
				Object.prototype.hasOwnProperty.call( flatpickr, 'l10ns' ) &&
				Object.prototype.hasOwnProperty.call( flatpickr.l10ns, wp_mail_smtp.lang_code )
			) {
				flatpickrLocale = flatpickr.l10ns[ wp_mail_smtp.lang_code ];

				// Rewrite separator for all locales to make filtering work.
				flatpickrLocale.rangeSeparator = ' - ';
			}

			args.locale = flatpickrLocale;

			$( '.wp-mail-smtp-filter-date-selector' ).flatpickr( args );
		}
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

// Initialize.
WPMailSMTPEmailReports.init();
