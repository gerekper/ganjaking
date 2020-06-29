/**
 * Javascript support for the WordPress dashboard analytics widget.
 *
 * @since  4.6
 */

( function( $ ) {

	$( function initWidget() {

		function widgetTabs() {

			var $wrapper = $( '.wpmudui-tabs' ),
				$tabs    = $wrapper.find( '> [data-tabs]' ),
				$tab     = $wrapper.find( '> [data-tabs] > [data-tab]' )
				;

			if ( 0 !== $tabs.length ) {

				$tab.on( 'click', function( e ) {

					var $this   = $( this ),
						$parent = $(this).closest( '.wpmudui-tabs' ),
						$panes  = $parent.find( '> [data-panes]' ),
						$pane   = $parent.find( '> [data-panes] > [data-pane]' )
						;

					var $datatab  = $( this ).data( 'tab' ),
						$datapane = $parent.find( '> [data-panes] > [data-pane="' + $datatab + '"]' )
						;

					// Remove "current" class from all tabs
					$this.parent().find( '> [data-tab]' ).removeClass( 'wpmudui-current' );

					// Add "current" class to target tab
					$this.addClass( 'wpmudui-current' );

					if ( 0 !== $panes.length ) {

						// Hide all tabs content
						$pane.hide();

						// Show "current" tab content
						$datapane.show();

					}

					e.stopPropagation();

				} );
				// on pageload change pane to current tab as set in html
				$('.wpmudui-analytics-tabs .wpmudui-current').click();
			}

			// hook up date range changes.
			$('.wpmudui-analytics-range').on('change', function( e ) {
				window.location.assign( document.location.origin + document.location.pathname + '?tab=' + $('.wpmudui-analytics-tabs .wpmudui-current').data('tab') + '&analytics_range=' + $(this).val() );
			});

		}

		function tableFilter() {
			$('.wpmudui-analytics-column-filter').on('change', function( e ) {

				var parent = $(this).parents(".wpmudui-tab-content"),
				    table = $(parent).find('.wpmudui-table');

				// change which column to show
				$(parent).find('.wpmudui-table-views').removeClass( 'wpmudui-current' );
				$(parent).find('.wpmudui-table-views.data-' + this.value).addClass( 'wpmudui-current' );

				// sort the table in descending order according to sort data attribute of selected column
				var items = $(parent).find('.wpmudui-table-item');
				var filter = '.data-' + $(this).val();
				items.sort(function(a, b){
					return +$(b).find(filter).data('sort') - +$(a).find(filter).data('sort');
				});
				$(parent).find('.wpmudui-table-sortable').append(items);

				table.trigger('reset');

				e.stopPropagation();
			});
			$('.wpmudui-analytics-column-filter').change(); //init

			// handle table row clicks
			$('.wpmudui-table-item').on('click', function( e ) {
				var data = {};
				data.action = 'wdp-analytics';
				data.hash = wdp_analytics_ajax.nonce;
				data.type = $(this).data('filter-type');
				data.filter = $(this).data('filter');
				data.range = $('#wpmudui-analytics-range').val();

				var parent = $(this).parents(".wpmudui-tab-content");
				var label_el = $(this).find('td:first-child');
				var label = $(this).data('label') + ' ' + label_el.text();

				label_el.find('.wpmudui-icon').remove();
				label_el.prepend('<span class="wpmudui-icon wpmudui-icon-loader wpmudui-loading"></span>');

				$.post(
					window.ajaxurl,
					data,
					function(response) {
						if ( response.success ) {
							wdp_analytics_ajax.current_data = response.data;
							updateTotals($(parent).find('.wpmudui-analytics-column-filter').val());
							$('#wpmudui-analytics-search').val(label);
							$('.wpmudui-analytics-tabs a[data-tab="overview"]').click();
							label_el.find('.wpmudui-icon').remove();
						} else {
							label_el.find('.wpmudui-icon').remove();
							label_el.prepend('<span class="wpmudui-icon dashicons dashicons-warning"></span>').find('.wpmudui-icon').fadeOut(2000);
						}
					},
					'json'
				)
				.always(function() {
				}).fail(function(xhr) {
					label_el.find('.wpmudui-icon').remove();
					label_el.prepend('<span class="wpmudui-icon dashicons dashicons-warning"></span>').find('.wpmudui-icon').fadeOut(2000);
				});

				e.stopPropagation();
			});
		}

		// updates and shows the appropriate totals boxes with current data
		function updateTotals(type) {
			var data = [];
			if (typeof wdp_analytics_ajax.current_data !== 'undefined' && typeof wdp_analytics_ajax.current_data.totals !== 'undefined') {
				data = wdp_analytics_ajax.current_data.totals;
			}

			$('.wpmudui-chart-options button').hide();
			$.each(data, function( key, item ) {
				var button = $('.wpmudui-chart-options button[data-type="'+key+'"]');
				if ( button.length ) {
					button.find('.wpmudui-chart-option-value').html( item.value );
					button.find('.wpmudui-chart-option-trend').html( item.change );
					button.removeClass('wpmudui-up wpmudui-down wpmudui-none').addClass('wpmudui-'+item.direction);
					button.show();
				}
			});

			if ( type ) {
				if ( 'unique_pageviews' === type ) {
					type = 'pageviews';
				}
				$('.wpmudui-chart-options button[data-type="'+type+'"]').click();
			} else {
				// for some reason :visible filter doesn't work here
				$('.wpmudui-chart-options button').filter(function () {
					return $(this).css('display') != 'none';
				}).filter(':first').click();
			}
		}

		function chartOptions() {

			var $options = $( '.wpmudui-chart-options' ),
				$option  = $options.find( 'button' )
				;

			$option.on( 'click', function( e ) {

				$option.removeClass( 'wpmudui-current' );
				$( this ).addClass( 'wpmudui-current' );

				initChart( $( this ).data('type') );

				e.stopPropagation();

			} );
		}

		function initAutocomplete() {

			var filters = wdp_analytics_ajax.autocomplete;

			$( '.wpmudui-autocomplete' ).autocomplete({
				minLength: 2,
				source: filters,
				select: function (e, ui) {
					console.log(ui.item);
					$( '.wpmudui-autocomplete' ).val(ui.item.label);

					var data = {};
					data.action = 'wdp-analytics';
					data.hash = wdp_analytics_ajax.nonce;
					data.type = ui.item.value.type;
					data.filter = ui.item.value.filter;
					data.range = $('#wpmudui-analytics-range').val();

					$('.wpmudui-search-form .wpmudui-icon').remove();
					$('.wpmudui-autocomplete').before('<span class="wpmudui-icon wpmudui-icon-loader wpmudui-loading"></span>').css('text-indent', '10px');

					$.post(
						window.ajaxurl,
						data,
						function(response) {
							if ( response.success ) {
								wdp_analytics_ajax.current_data = response.data;
								updateTotals();
								$('.wpmudui-search-form .wpmudui-icon').fadeOut(400, function() {
									$('.wpmudui-autocomplete').css('text-indent', '0px');
								});
							} else {
								$('.wpmudui-search-form .wpmudui-icon').remove();
								$('.wpmudui-autocomplete').before('<span class="wpmudui-icon dashicons dashicons-warning"></span>').css('text-indent', '10px');
								$('.wpmudui-search-form .wpmudui-icon').fadeOut(2000, function() {
									$('.wpmudui-autocomplete').css('text-indent', '0px');
								});
							}
						},
						'json'
					)
					.always(function() {
					}).fail(function(xhr) {
						$('.wpmudui-search-form .wpmudui-icon').remove();
						$('.wpmudui-autocomplete').before('<span class="wpmudui-icon dashicons dashicons-warning"></span>').css('text-indent', '10px');
						$('.wpmudui-search-form .wpmudui-icon').fadeOut(2000, function() {
							$('.wpmudui-autocomplete').css('text-indent', '0px');
						});
					});

					e.preventDefault();
				}
			}).autocomplete( 'widget' ).addClass( 'wpmudui-autocomplete-list' );

			// when clearing search revert to overall data
			$( '.wpmudui-autocomplete' ).on("input", function() {
				if ( '' === $(this).val() ) {
					wdp_analytics_ajax.current_data = wdp_analytics_ajax.overall_data;
					updateTotals();
				}
			});
		}

		function initTablePagination(numPerPage) {

			// prev page handler
			$('.wpmudui-table ~ .wpmudui-pagination-wrapper .wpmudui-page-prev').on('click', function (e) {
				var $pagination_wrapper = $(this).closest('.wpmudui-pagination-wrapper'),
				    $table              = $pagination_wrapper.prev('.wpmudui-table'),
				    currentPage         = +$pagination_wrapper.data('current-page');

				if (!$table.length) {
					return true;
				}
				currentPage--;
				if (currentPage < 1) {
					currentPage = 1;
				}

				$pagination_wrapper.data('current-page', currentPage);
				$table.trigger('paginate');

			});

			// next page handler
			$('.wpmudui-table ~ .wpmudui-pagination-wrapper .wpmudui-page-next').on('click', function (e) {
				var $pagination_wrapper = $(this).closest('.wpmudui-pagination-wrapper'),
				    $table              = $pagination_wrapper.prev('.wpmudui-table'),
				    numRows             = $table.data('rows'),
				    numPages            = Math.ceil(numRows / numPerPage),
				    currentPage         = +$pagination_wrapper.data('current-page');

				if (!$table.length) {
					return true;
				}
				currentPage++;
				if (currentPage > numPages) {
					currentPage = numPages;
				}

				$pagination_wrapper.data('current-page', currentPage);
				$table.trigger('paginate');
			});

			// go to page
			$('.wpmudui-table ~ .wpmudui-pagination-wrapper .wpmudui-goto-page').on('change', function (e) {
				var $pagination_wrapper = $(this).closest('.wpmudui-pagination-wrapper'),
				    $table              = $pagination_wrapper.prev('.wpmudui-table'),
				    numRows             = $table.data('rows'),
				    numPages            = Math.ceil(numRows / numPerPage),
				    currentPage         = +$(this).val();

				if (!$table.length) {
					return true;
				}

				if (currentPage < 1) {
					currentPage = 1;
				}

				if (currentPage > numPages) {
					currentPage = numPages;
				}

				$pagination_wrapper.data('current-page', currentPage);
				$table.trigger('paginate');

			});

			// reset page to 1
			$('.wpmudui-table').on('reset', function (e) {
				var $table              = $(this),
				    $pagination_wrapper = $table.next('.wpmudui-pagination-wrapper');

				$pagination_wrapper.data('current-page', 1);
				$table.trigger('paginate');
			});

			// main paginate function
			$('.wpmudui-table').on('paginate', function (e) {
				var $table              = $(this),
				    $pagination_wrapper = $table.next('.wpmudui-pagination-wrapper'),
				    numRows             = $table.data('rows'),
				    numPages            = Math.ceil(numRows / numPerPage)
				;

				// no pagination markup
				if (!$pagination_wrapper.length) {
					return true;
				}

				if (numRows <= numPerPage) {
					$pagination_wrapper.hide();
					return true;
				}

				$pagination_wrapper.find('.wpmudui-page-next').removeAttr('disabled');
				$pagination_wrapper.find('.wpmudui-page-prev').removeAttr('disabled');

				// current page reference
				var currentPage = +$pagination_wrapper.data('current-page');
				if (currentPage < 1) {
					currentPage = 1;
				}
				if (currentPage > numPages) {
					currentPage = numPages;
				}

				$pagination_wrapper.data('current-page', +currentPage);

				// num rows reference
				$pagination_wrapper.data('rows', +numRows);

				// max and min GO TO
				$pagination_wrapper.find('.wpmudui-goto-page').attr('min', 1);
				$pagination_wrapper.find('.wpmudui-goto-page').attr('max', numPages);
				// set value GO TO
				$pagination_wrapper.find('.wpmudui-goto-page').val(currentPage);

				var indexRowStart = (currentPage - 1) * numPerPage + 1;
				var indexRowEnd   = currentPage * numPerPage;

				$pagination_wrapper.find('.wpmudui-start-row').html(indexRowStart);
				$pagination_wrapper.find('.wpmudui-end-row').html(indexRowEnd);

				$table.find('tbody tr').hide().slice(indexRowStart - 1, indexRowEnd).show();


				if (currentPage === 1) {
					$pagination_wrapper.find('.wpmudui-page-prev').attr('disabled', 'disabled');
				}
				if (currentPage === numPages) {
					$pagination_wrapper.find('.wpmudui-page-next').attr('disabled', 'disabled');
				}

			});

			$('.wpmudui-table').trigger('paginate');

		}

		function openForm() {

			var $form   = $( '.wpmudui-search-form' ),
				$handle = $form.find( '.wpmudui-handle' )
				;

			$handle.on( 'click', function() {
				$( this ).closest( '.wpmudui-search-form' ).toggleClass( 'wpmudui-open' );
			} );
		}

		if ( 0 !== $( '.wpmudui-analytics' ).length ) {
			widgetTabs();
			tableFilter();
			initMomentLocale();
			chartOptions();
			initAutocomplete();
			initTablePagination(10);
			openForm();
			updateTotals();
		}

		function initMomentLocale() {
			var currentLocale = moment.locale();
			moment.updateLocale(wdp_analytics_ajax.locale_settings.locale, {
				// Inherit anything missing from the default locale.
				parentLocale: currentLocale,
				monthsShort: wdp_analytics_ajax.locale_settings.monthsShort,
				weekdays: wdp_analytics_ajax.locale_settings.weekdays
			});

			// set the locale!
			moment.locale(wdp_analytics_ajax.locale_settings.locale);
		}

		function initChart($type) {
			var canvas_element = $("#wpmudui-analytics-graph");
			var all_data = wdp_analytics_ajax.current_data.chart;

			// this creates the nifty new chart type with vertical gray line while hovering over tooltips
			Chart.defaults.LineWithLine = Chart.defaults.line;
			Chart.controllers.LineWithLine = Chart.controllers.line.extend({
				draw: function(ease) {
					Chart.controllers.line.prototype.draw.call(this, ease);

					if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
						var activePoint = this.chart.tooltip._active[0],
							ctx = this.chart.ctx,
							x = activePoint.tooltipPosition().x,
							topY = this.chart.scales['y-axis-0'].top,
							bottomY = this.chart.scales['y-axis-0'].bottom;

						// draw line
						ctx.save();
						ctx.beginPath();
						ctx.moveTo(x, topY);
						ctx.lineTo(x, bottomY);
						ctx.lineWidth = 1;
						ctx.strokeStyle = 'rgba(0, 0, 0, 0.1)';
						ctx.stroke();
						ctx.restore();
					}
				}
			});

			function timeFormat(seconds) {
				var minutes = Math.floor(seconds / 60) % 60,
					seconds = (seconds - minutes * 60);

				var lable = '';
				if ( minutes ) {
					lable = lable + minutes + 'm';
				}
				if ( seconds ) {
					if ( lable.length ) {
						lable = lable + ' ';
					}
					lable = lable + seconds + 's';
				}
				return lable;
			}

			var configNum = {
				type: 'LineWithLine',
				data: {
					datasets: [{
						label: 'NA',
						backgroundColor: "rgba(0,133,186,0.98)",
						borderColor: "rgba(0,133,186,0.98)",
						borderWidth: 3,
						pointRadius: 3,
						pointBorderWidth: 1,
						pointBorderColor: "white",
						pointBackgroundColor: "rgba(0,133,186,0.98)",
						fill: false,
						data: []
					}]
				},
				options: {
					legend: {
						display: true
					},
					tooltips: {
						mode: 'x',
						intersect: false,
						displayColors: false,
						titleFontSize: 12,
						callbacks: {
							label: function (tooltipItem, data) {
								var label = data.datasets[tooltipItem.datasetIndex].label || '';
								if (label) {
									label = tooltipItem.yLabel + ' ' + label;
								}
								return label;
							}
						}
					},
					scales: {
						xAxes: [{
							type: 'time',
							time: {
								round: 'day',
								minUnit: 'day',
								tooltipFormat: 'dddd, MMM. D'
							},
							gridLines: {
								display: false
							},
							scaleLabel: {
								display: false
							}
						}],
						yAxes: [{
							scaleLabel: {
								display: false
							},
							ticks: {
								min: 0,
								suggestedMax:''
							}
						}]
					}
				}
			};

			var configPcnt = {
				type: 'LineWithLine',
				data: {
					datasets: [{
						label: 'NA',
						backgroundColor: "rgba(0,133,186,0.98)",
						borderColor: "rgba(0,133,186,0.98)",
						borderWidth: 3,
						pointRadius: 3,
						pointBorderWidth: 1,
						pointBorderColor: "white",
						pointBackgroundColor: "rgba(0,133,186,0.98)",
						fill: false,
						data: []
					}]
				},
				options: {
					legend: {
						display: true
					},
					tooltips: {
						mode: 'x',
						intersect: false,
						displayColors: false,
						titleFontSize: 12,
						callbacks: {
							label: function (tooltipItem, data) {
								var label = data.datasets[tooltipItem.datasetIndex].label || '';
								if (label) {
									label = ( tooltipItem.yLabel * 100 ) + '% ' + label;
								}
								return label;
							}
						}
					},
					scales: {
						xAxes: [{
							type: 'time',
							time: {
								round: 'day',
								minUnit: 'day',
								tooltipFormat: 'dddd, MMM. D'
							},
							gridLines: {
								display: false
							},
							scaleLabel: {
								display: false
							}
						}],
						yAxes: [{
							scaleLabel: {
								display: false
							},
							ticks: {
								min: 0,
								callback: function(value) {
									return Math.round( value * 100 ) + "%"
								}
							}
						}]
					}
				}
			};

			var configTime = {
				type: 'LineWithLine',
				data: {
					datasets: [{
						label: 'NA',
						backgroundColor: "rgba(0,133,186,0.98)",
						borderColor: "rgba(0,133,186,0.98)",
						borderWidth: 3,
						pointRadius: 3,
						pointBorderWidth: 1,
						pointBorderColor: "white",
						pointBackgroundColor: "rgba(0,133,186,0.98)",
						fill: false,
						data: []
					}]
				},
				options: {
					legend: {
						display: true
					},
					tooltips: {
						mode: 'x',
						intersect: false,
						displayColors: false,
						titleFontSize: 12,
						callbacks: {
							label: function (tooltipItem, data) {
								var label = data.datasets[tooltipItem.datasetIndex].label || '';
								if (label) {
									label = timeFormat( tooltipItem.yLabel ) + ' ' + label;
								}
								return label;
							}
						}
					},
					scales: {
						xAxes: [{
							type: 'time',
							time: {
								round: 'day',
								minUnit: 'day',
								tooltipFormat: 'dddd, MMM. D'
							},
							gridLines: {
								display: false
							},
							scaleLabel: {
								display: false
							}
						}],
						yAxes: [{
							scaleLabel: {
								display: false
							},
							ticks: {
								min: 0,
								callback: timeFormat
							}
						}]
					}
				}
			};

			if ( typeof all_data[$type] === 'undefined' ) {
				console.log('Missing chart data for '+$type);
				return false;
			}

			var chartConfig = null;
			if ( $type.includes("_rate") ) {
				chartConfig = $.extend(true,{},configPcnt); //copy not by reference;
			} else if ( $type.includes("_time") ) {
				chartConfig = $.extend(true,{},configTime); //copy not by reference;
			} else {
				chartConfig = $.extend(true,{},configNum); //copy not by reference;

				//Set YAxis max value for proper curve
				var yAxisData 	 = all_data[$type].data.map( ( data ) => data.y ),
				yAxisDataMax = Math.max.apply(null, yAxisData);
				chartConfig.options.scales.yAxes[0].ticks.suggestedMax = yAxisDataMax + Math.round( 0.1 * yAxisDataMax );

			}

			chartConfig.data.datasets[0].label = all_data[$type].label;
			chartConfig.data.datasets[0].data = all_data[$type].data;

			if ( 'pageviews' === $type ) {
				if (typeof all_data['unique_pageviews'] !== 'undefined') {
					chartConfig.data.datasets.push({
						label: all_data['unique_pageviews'].label,
						backgroundColor: "purple",
						borderColor: "purple",
						borderWidth: 3,
						pointRadius: 3,
						pointBorderWidth: 1,
						pointBorderColor: "white",
						pointBackgroundColor: "purple",
						fill: false,
						data: all_data['unique_pageviews'].data
					});
				}
			}

			if ( 'visits' === $type ) {
				chartConfig.data.datasets.push({
					label: all_data['unique_visits'].label,
					backgroundColor: "purple",
					borderColor: "purple",
					borderWidth: 3,
					pointRadius: 3,
					pointBorderWidth: 1,
					pointBorderColor: "white",
					pointBackgroundColor: "purple",
					fill: false,
					data: all_data['unique_visits'].data
				});
			}

			// if all values for this chart are null, show empty message
			var chart_empty = true;
			$.each(all_data[$type].data, function( key, item ) {
				if ( item.y !== null ) {
					chart_empty = false;
				}
			});
			if ( chart_empty ) {
				canvas_element.closest( '.wpmudui-analytics-chart' ).find( '.wpmudui-analytics-chart-empty' ).show();
			} else {
				canvas_element.closest( '.wpmudui-analytics-chart' ).find( '.wpmudui-analytics-chart-empty' ).hide();
			}

			// this stops an weird overlapping charts issues
			if ( typeof window.$type !== 'undefined' ) {
				window.$type.destroy();
			}
			window.$type = new Chart($(canvas_element)[0].getContext('2d'), chartConfig);
		}

	} );

}( jQuery ) );
