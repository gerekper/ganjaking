( function( $ ) {

	/**
	 * AJAX Request Queue
	 *
	 * - add()
	 * - remove()
	 * - run()
	 * - stop()
	 *
	 * @since 1.2.0.8
	 */
	var UaelAjaxQueue = (function() {

		var requests = [];

		return {

			/**
			 * Add AJAX request
			 *
			 * @since 1.2.0.8
			 */
			add:  function(opt) {
			    requests.push(opt);
			},

			/**
			 * Remove AJAX request
			 *
			 * @since 1.2.0.8
			 */
			remove:  function(opt) {
			    if( jQuery.inArray(opt, requests) > -1 )
			        requests.splice($.inArray(opt, requests), 1);
			},

			/**
			 * Run / Process AJAX request
			 *
			 * @since 1.2.0.8
			 */
			run: function() {
			    var self = this,
			        oriSuc;

			    if( requests.length ) {
			        oriSuc = requests[0].complete;

			        requests[0].complete = function() {
			             if( typeof(oriSuc) === 'function' ) oriSuc();
			             requests.shift();
			             self.run.apply(self, []);
			        };

			        jQuery.ajax(requests[0]);

			    } else {

			      self.tid = setTimeout(function() {
			         self.run.apply(self, []);
			      }, 1000);
			    }
			},

			/**
			 * Stop AJAX request
			 *
			 * @since 1.2.0.8
			 */
			stop:  function() {

			    requests = [];
			    clearTimeout(this.tid);
			}
		};

	}());

	UaelAdmin = {

		init: function () {
			/**
			 * Run / Process AJAX request
			 */
			UaelAjaxQueue.run();
			this._knowledgebase();
			this._support();


			$(document).on("click", ".uael-activate-widget", UaelAdmin._activate_widget);
			$(document).on("click", ".uael-deactivate-widget", UaelAdmin._deactivate_widget);

			$(document).on("click", ".uael-activate-all", UaelAdmin._bulk_activate_widgets);
			$(document).on("click", ".uael-deactivate-all", UaelAdmin._bulk_deactivate_widgets);

			$(document).on("click", ".uael-activate-skins-all", UaelAdmin._bulk_activate_skins);
			$(document).on("click", ".uael-deactivate-skins-all", UaelAdmin._bulk_deactivate_skins);

			$(document).on("click", "#uael-gen-enable-beta-update", UaelAdmin._allow_beta_updates);

			/* White Label */
			$(document).on("change", "#uael-wl-enable-knowledgebase", UaelAdmin._knowledgebase);
			$(document).on("change", "#uael-wl-enable-support", UaelAdmin._support);

			/* Instagram token */
			$(document).on("click", ".uael-instagram-access-token-generator", this._generateInstagramAccessToken);
		},

		/**
		 * Activate All Widgets.
		 */
		_bulk_activate_widgets: function (e) {
			var button      = $(this);
			var widget_item = $('.uael-option-type-widget').children("li");

			var data = {
				action: 'uael_bulk_activate_widgets',
				nonce: uael.ajax_nonce,
			};

			if (button.hasClass('updating-message')) {
				return;
			}

			$(button).addClass('updating-message');

			UaelAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function (data) {

					// Bulk add or remove classes to all modules.
					widget_item.addClass('activate').removeClass('deactivate');
					widget_item.find('.uael-activate-widget')
						.addClass('uael-deactivate-widget')
						.prop('checked', true)
						.removeClass('uael-activate-widget');
					$(button).removeClass('updating-message');
				}
			});
			e.preventDefault();
		},

		/**
		 * Deactivate All Widgets.
		 */
		_bulk_deactivate_widgets: function (e) {
			var button      = $(this);
			var widget_item = $('.uael-option-type-widget').children("li");

			var data = {
				action: 'uael_bulk_deactivate_widgets',
				nonce: uael.ajax_nonce,
			};

			if (button.hasClass('updating-message')) {
				return;
			}
			$(button).addClass('updating-message');

			UaelAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function (data) {

					// Bulk add or remove classes to all modules.
					widget_item.addClass('deactivate').removeClass('activate');
					widget_item.find('.uael-deactivate-widget')
						.addClass('uael-activate-widget')
						.removeClass('uael-deactivate-widget')
						.prop('checked', false);
					$(button).removeClass('updating-message');
				}
			});
			e.preventDefault();
		},

		/**
		 * Activate All Widgets.
		 */
		_bulk_activate_skins: function (e) {
			var button = $(this);

			var data = {
				action: 'uael_bulk_activate_skins',
				nonce: uael.ajax_nonce,
			};

			if (button.hasClass('updating-message')) {
				return;
			}

			$(button).addClass('updating-message');

			UaelAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function (data) {

					// Bulk add or remove classes to all modules.
					$('.uael-option-type-skin').children("li").addClass('activate').removeClass('deactivate');
					$('.uael-option-type-skin').children("li").find('.uael-activate-widget')
						.addClass('uael-deactivate-widget')
						.prop('checked', true)
						.removeClass('uael-activate-widget');
					$(button).removeClass('updating-message');
				}
			});
			e.preventDefault();
		},

		/**
		 * Deactivate All Widgets.
		 */
		_bulk_deactivate_skins: function (e) {
			var button = $(this);

			var data = {
				action: 'uael_bulk_deactivate_skins',
				nonce: uael.ajax_nonce,
			};

			if (button.hasClass('updating-message')) {
				return;
			}
			$(button).addClass('updating-message');

			UaelAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function (data) {

					// Bulk add or remove classes to all modules.
					$('.uael-option-type-skin').children("li").addClass('deactivate').removeClass('activate');
					$('.uael-option-type-skin').children("li").find('.uael-deactivate-widget')
						.addClass('uael-activate-widget')
						.removeClass('uael-deactivate-widget')
						.prop('checked', false);
					$(button).removeClass('updating-message');
				}
			});
			e.preventDefault();
		},

		/**
		 * Activate Module.
		 */
		_activate_widget: function (e) {
			var button = $(this),
				id = button.parents('li').attr('id');

			if (button[0].checked) {
				var data = {
					module_id: id,
					action: 'uael_activate_widget',
					nonce: uael.ajax_nonce,
				};

				if (button.hasClass('updating-message')) {
					return;
				}

				$(button).addClass('updating-message');

				UaelAjaxQueue.add({
					url: ajaxurl,
					type: 'POST',
					data: data,
					success: function (data) {

						// Add active class.
						$('#' + id).addClass('activate').removeClass('deactivate');
						// Change button classes & text.
						$('#' + id).find('.uael-activate-widget')
							.addClass('uael-deactivate-widget')
							.prop('checked', true)
							.removeClass('uael-activate-widget')
							.removeClass('updating-message');
					}
				});

				e.preventDefault();
			}
		},

		/**
		 * Deactivate Module.
		 */
		_deactivate_widget: function (e) {
			e.preventDefault();
			var button = $(this),
				id = button.parents('li').attr('id');

			if (!button[0].checked) {
				var data = {
					module_id: id,
					action: 'uael_deactivate_widget',
					nonce: uael.ajax_nonce,
				};

				if (button.hasClass('updating-message')) {
					return;
				}

				$(button).addClass('updating-message');

				UaelAjaxQueue.add({
					url: ajaxurl,
					type: 'POST',
					data: data,
					success: function (data) {

						// Remove active class.
						$('#' + id).addClass('deactivate').removeClass('activate');

						// Change button classes & text.
						$('#' + id).find('.uael-deactivate-widget')
							.addClass('uael-activate-widget')
							.removeClass('uael-deactivate-widget')
							.prop('checked', false)
							.removeClass('updating-message');
					}
				});
			}

		},

		/**
		 * Allow Beta Updates.
		 */
		_allow_beta_updates: function (e) {

			var $this = $(this);
			var allow_beta = $this.attr('data-value');

			if ('disable' === allow_beta) {
				allow_beta = 'enable';
			} else {
				allow_beta = 'disable';
			}

			$this.addClass('loading');

			var data = {
				allow_beta: allow_beta,
				action: 'uael_allow_beta_updates',
				nonce: uael.ajax_nonce,
			};

			UaelAjaxQueue.add({
				url: ajaxurl,
				type: 'POST',
				data: data,
				success: function (data) {

					window.location.href += '&message=saved';
				}
			})
		},

		/**
		 * Knowledge Base.
		 */
		_knowledgebase: function () {
			if ($('#uael-wl-enable-knowledgebase').is(':checked')) {
				$('p.uael-knowledgebase-url').show();
			} else {
				$('p.uael-knowledgebase-url').hide();
			}
		},

		/**
		 * Support.
		 */
		_support: function () {
			if ($('#uael-wl-enable-support').is(':checked')) {
				$('p.uael-support-url').show();
			} else {
				$('p.uael-support-url').hide();
			}
		},

		/**
		 * Generate Instagram Access Token
		 */
		_generateInstagramAccessToken: function () {
			var appId = $('#uael-instagram-app-id');
			var appSecret = $('#uael-instagram-secret-key');
			var accessToken = $('#uael-instagram-access-token');
			var refreshTokenURL = 'https://graph.instagram.com/refresh_access_token';

			var successMessageSpan = $('.uael-insta-response-msg.uael-response-success');
			var warningMessageSpan = $('.uael-insta-response-msg.uael-response-warning');

			if (accessToken.length) {
				$.ajax({
					url: refreshTokenURL,
					type: "GET",
					data: {
						grant_type: 'ig_refresh_token',
						access_token: accessToken.val(),
					}
				})
					.done(function (data, textStatus, jqXHR) {
						warningMessageSpan.hide();
						successMessageSpan.show();
					})
					.fail(function (jqXHR, textStatus, errorThrown) {
						successMessageSpan.hide();
						warningMessageSpan.show();
					})
					.always(function (jqXHROrData, textStatus, jqXHROrErrorThrown) {
					});
			}
		},
	}

	$( document ).ready(
		function() {
			UaelAdmin.init();

			var filter_inputs = $( '.uael-container.uael-general .uael-widget-filters [type="radio"]' );
			var filter_tabs   = $( '.uael-container.uael-general .uael-widget-filters li' );
			var widget_list   = $( '.uael-container.uael-general .uael-list-section .uael-widget-list li' );
			var filterUlList  = $( '.uael-widget-filters' );

			// Sort the widget list according to selected/clicked filter.
			function mayBeSort(widgets, cat) {
				for (var widget in widgets) {
					if ( widgets.hasOwnProperty( widget ) ) {
						var _widget = $( widgets[widget] );

						if ('all' === cat) {
							widgets.removeClass( 'filter-item-disable' );
							widgets.addClass( 'filter-item-active' );
						}

						if (_widget.data( 'category' ) !== cat) {
							_widget.removeClass( 'filter-item-active' );
							_widget.addClass( 'filter-item-disable' );
						} else {
							_widget.addClass( 'filter-item-active' );
							_widget.removeClass( 'filter-item-disable' );
						}
					}
				}
			}

			// Show filtered view when clicked on filter tab.
			function showFiltered() {
				var tab      = $( this );
				var category = tab.data( 'category' );
				var header_bar = $( '.uael-settings-widgets-heading' );

				filter_tabs.removeClass( 'filter-active' );
				tab.parent().addClass( 'filter-active' );

				if( 'all' !== category && ! header_bar.hasClass( 'uael-hide-activation-options' ) ) {
					header_bar.addClass( 'uael-hide-activation-options' );
				} else if( 'all' === category ) {
					header_bar.removeClass( 'uael-hide-activation-options' );
				}

				mayBeSort( widget_list, category );
			}

			filter_inputs.on( 'click', showFiltered );

			// Build the filters dropdown ui.
			filterUlList.each(
				function(){
					var list   = $( this ),
						select = $( document.createElement( 'select' ) ).insertBefore( $( this ) ).addClass( 'uael-widget-filters-dropdown uael-widget-filters-dropdown-hide' );
					$( '>li', this ).each(
						function(){
							var items    = $( this ),
								label    = items.text(),
								category = items.find( '[type="radio"]' ).data( 'category' );
							var option   = $( document.createElement( 'option' ) )
								.appendTo( select )
								.prop(
									{
										value: category,
										text: label
									}
								);

						}
					);
				}
			);

			var filterDropdownList = $( '.uael-widget-filters-dropdown' );

			// Check the window size for show filter dropdown instead of filter tabs.
			const mediaQuery = window.matchMedia( '(max-width: 1120px)' );
			// Show/Hide filter dropdown based on media query(window size).
			function handleFilterDropdown(e) {
				if (e.matches) {
					filterDropdownList.removeClass( 'uael-widget-filters-dropdown-hide' );
					filterUlList.addClass( 'uael-widget-filter-list-hide' );
				} else {
					filterDropdownList.addClass( 'uael-widget-filters-dropdown-hide' );
					filterUlList.removeClass( 'uael-widget-filter-list-hide' );
				}
			}
			// Add listener to detect window size change.
			if (mediaQuery.addEventListener !== undefined) {
				mediaQuery.addEventListener( "change", handleFilterDropdown );
			} else if (mediaQuery.addListener !== undefined) {
				mediaQuery.addListener( handleFilterDropdown );
			}

			// Invoke Show/Hide filter dropdown function for first time on page load.
			handleFilterDropdown( mediaQuery );

			filterDropdownList.on( 'change', showFilteredByDropdown );

			// Show filtered view when selected filter from dropdown.
			function showFilteredByDropdown(){
				var selected_option   = this,
					selected_category = selected_option.value;

				mayBeSort( widget_list, selected_category );

			}
		}
	);

} )( jQuery );
