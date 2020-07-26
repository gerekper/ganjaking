/**
 * WC OD calendar
 *
 * @package WC_OD
 * @since   1.0.0
 */

/**
 * Calendar function.
 *
 * @param {jQuery} $ The jQuery instance.
 */
/* global ajaxurl */
jQuery(function( $ ) {

	'use strict';

	$.fn.serializeObject = function() {
		var o = {},
		    a = this.serializeArray();

		$.each( a, function() {
			if ( o[this.name] ) {
				if ( ! o[this.name].push ) {
					o[this.name] = [ o[this.name] ];
				}
				o[this.name].push( this.value || '' );
			} else {
				o[this.name] = this.value || '';
			}
		});

		return o;
	};

	$.WC_OD_Calendar = function( options, element ) {

		var defaults = {
			language: 'en',
			weekStart: 0,
			eventsType: '',
			loadingText: '',
			modalContent: 'No content',
			eventTooltipContent: '',
			modalTexts: {
				add: 'Add event',
				edit: 'Edit event',
				'delete': 'Are you sure you want to delete this event?',
				saving: '',
				deleting: ''
			},
			ajaxActions: {
				fetch: 'wc_od_calendar_fetch_events',
				add: 'wc_od_calendar_add_event',
				update: 'wc_od_calendar_update_event',
				'delete': 'wc_od_calendar_delete_event'
			}
		};

		this.options = $.extend( true, {}, defaults, options );
		this.$calendar = $( element );
		this.$modal = $( this.options.modalContent );
		this.$modalForm = this.$modal.find( 'form' ).prepend( '<input type="hidden" name="type" value="' + this.options.eventsType + '" />');
		this.modalAction = 'add';
		this.currentEvent = null;
	};

	$.WC_OD_Calendar.prototype = {

		_init : function() {
			var self = this;

			this._bindEvents();

			// Init calendar
			this.$calendar.fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: ''
				},
				allDayDefault: true,
				events: function( start, end, timezone, callback ) {
					var $calendarContent = self.$calendar.find( '.fc-view-container' );

					$calendarContent.block( self.getBlockUIOptions( self.options.loadingText, '#fff' ) );

					$.ajax({
						url : ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: self.options.ajaxActions.fetch,
							filters: {
								timezone: timezone,
								start: start.format(),
								end: end.format(),
								type: self.options.eventsType
							}
						},
						success: function( eventsData ) {
							var events = [];

							for ( var index in eventsData ) {
								var event = self.getEventObject( eventsData[ index ] );
								events.push( event );
							}
							$calendarContent.unblock();
							callback( events );
						},
						error: function() {
							$calendarContent.find( '.blockMsg' ).text( 'there was an error while fetching events!' );
						}
					});
				},
				selectable: true,
				firstDay: self.options.weekStart,
				select: function( start, end ) {
					var eventObject = {
						start: start,
						end: end
					};

					self.setModalAction( 'add' );
					self.loadEventForm( eventObject );
					self.$modal.dialog( 'open' );
				},
				eventClick: function( eventObject ) {
					self.currentEvent = eventObject;
					self.setModalAction( 'edit' );
					self.loadEventForm( eventObject );
					self.$modal.dialog( 'open' );
				},
				eventRender: function( event, $element ) {
					var content = self.getTooltipContent( event );

					$element.tooltipster({
						contentAsHTML: true,
						content: content,
						minWidth: 250
					});
				}
			});

			// Fix select2 interaction inside the jQuery UI Dialog.
			if ( $.ui && $.ui.dialog && $.ui.dialog.prototype._allowInteraction ) {
				var uiDialogInteraction = $.ui.dialog.prototype._allowInteraction;

				$.ui.dialog.prototype._allowInteraction = function( event ) {
					return ( $( event.target ).closest( '.select2-dropdown' ).length || uiDialogInteraction( event ) );
				};
			}

			// Init modal dialog
			this.$modal.dialog({
				dialogClass   : 'wp-dialog wc-od-dialog',
				title         : this.options.modalTexts.add,
				modal         : true,
				autoOpen      : false,
				closeOnEscape : true,
				minWidth      : 450,
				create: function() {
					var $start = self.$modalForm.find( 'input[name="start"]' ),
						$end = self.$modalForm.find( 'input[name="end"]' );

					// Add datepickers
					self.$modalForm.find( '.date-field' ).wc_od_datepicker({
						autoclose: true,
						format: 'yyyy-mm-dd',
						language: self.options.language,
						weekStart: self.options.weekStart
					}).on( 'changeDate', function() {
						var field = $(this).attr( 'name' ), value = $(this).val();

						if ( 'start' === field ) {
							$end.wc_od_datepicker( 'option', 'startDate', ( value ? new Date( value ) : false ) );
						} else if ( 'end' === field ) {
							$start.wc_od_datepicker( 'option', 'endDate', ( value ? new Date( value ) : false ) );
						}
					});

					self.$modalForm.find( '.actions .cancel' ).click(function( event ) {
						event.preventDefault();
						self.$modal.dialog( 'close' );

						return false;
					});

					self.$modalForm.find( '.actions .delete' ).click(function( event ) {
						event.preventDefault();
						if ( window.confirm( self.options.modalTexts['delete'] ) ) {
							if ( self.currentEvent ) {
								self.eventAction( 'delete', self.getEventData( self.currentEvent ) );
							}
						}

						return false;
					});
				}
			});
		},
		_bindEvents : function() {
			var self = this;

			this.$modalForm.submit(function( event ) {
				var eventData;

				event.preventDefault();
				eventData = $(this).serializeObject();
				self.eventAction( self.modalAction, eventData );

				return false;
			});

			$( this ).on( 'eventAdded eventUpdated eventDeleted', function() {
				// Close de modal
				self.$modal.dialog( 'close' );
				// Reset the form fields
				self.$modalForm[0].reset();
				self.$modalForm.unblock();
			});
		},
		eventAction: function( action, eventData ) {
			this.$modalForm.block( this.getBlockUIOptions(
				( 'delete' === action ? this.options.modalTexts.deleting : this.options.modalTexts.saving ),
				this.$modalForm.closest( '.ui-dialog' ).css( 'backgroundColor' )
			) );

			// Call the correct function dynamically
			this[action + 'Event']( eventData );
		},
		addEvent: function( eventData ) {
			var self = this;

			this.syncEvent( 'add', eventData, function( eventData ) {
				var eventObject = self.getEventObject( eventData );

				self.$calendar.fullCalendar( 'renderEvent', eventObject, false );
				$( self ).trigger( 'eventAdded', eventData );
			});
		},
		editEvent: function( eventData ) {
			var self = this,
				eventObject = this.getEventObject( eventData );

			for ( var property in eventObject ) {
				if ( eventObject.hasOwnProperty( property ) ) {
					this.currentEvent[property] = eventObject[property];
				}
			}

			eventData = this.getEventData( this.currentEvent );
			this.syncEvent( 'update', eventData, function() {
				self.$calendar.fullCalendar( 'updateEvent', self.currentEvent );
				self.currentEvent = null;
				$( self ).trigger( 'eventUpdated', eventData );
			});
		},
		deleteEvent: function( eventData ) {
			var self = this;

			this.syncEvent( 'delete', eventData, function() {
				self.$calendar.fullCalendar( 'removeEvents', eventData.id );
				self.currentEvent = null;
				$( self ).trigger( 'eventDeleted', eventData );
			});
		},
		// Synchronize the event with the database
		syncEvent: function( action, eventData, callback ) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: this.options.ajaxActions[action],
					event: eventData
				},
				success: function( response ) {
					if ( 'success' === response.status ) {
						if ( typeof callback === 'function' ) {
							callback( response.event );
						}
					} else if ( 'error' === response.status ) {
						window.alert( response.message );
					}
				}
			});
		},
		// Get the event data extracted from an event object
		getEventData: function( eventObject ) {
			// Clone the start and end properties with calendar.moment()
			var calendar = this.$calendar.fullCalendar( 'getCalendar' ),
				eventData = {
					id: eventObject._id,
					type: this.options.eventsType,
					title: ( eventObject.title ? eventObject.title : '' ),
					start: calendar.moment( eventObject.start ).format()
				};

			eventData.end = ( eventObject.end ? calendar.moment( eventObject.end ).subtract( 1, 'days' ).format() : eventData.start );

			return eventData;
		},
		// Get the event object from the event data
		getEventObject: function( eventData ) {
			var eventObject = eventData,
				calendar = this.$calendar.fullCalendar( 'getCalendar' );

			eventObject.start = calendar.moment( eventData.start );
			if ( eventData.end ) {
				eventObject.end = calendar.moment( eventData.end ).add( 1, 'days' );
			}

			return eventObject;
		},
		// Load the event into the modal form
		loadEventForm: function( eventObject ) {
			var eventData = this.getEventData( eventObject ),
				$start = this.$modalForm.find( 'input[name="start"]' ),
				$end = this.$modalForm.find( 'input[name="end"]' );

			// Avoid that start date can be greater than the end date.
			// NOTE: It's important to set the 'startDate' and 'endDate' parameters before update the calendar value.
			$end.wc_od_datepicker( 'option', 'startDate', ( eventData.start ? new Date( eventData.start ) : false ) );
			$start.wc_od_datepicker( 'option', 'endDate', ( eventData.end ? new Date( eventData.end ) : false ) );

			// Update values.
			$start.wc_od_datepicker( 'update', eventData.start );
			$end.wc_od_datepicker( 'update', eventData.end );
			this.$modalForm.find( 'input[name="title"]' ).val( eventData.title );
		},
		setModalAction: function( action ) {
			var $deleteLink = this.$modal.find( '.actions .delete' );
			this.modalAction = action;
			this.$modal.dialog( 'option', 'title', this.options.modalTexts[action] );
			( 'edit' === action ) ? $deleteLink.show() : $deleteLink.hide();
		},
		getTooltipContent: function( eventObject ) {
			var eventData = this.getEventData( eventObject ),
				content = this.options.eventTooltipContent,
				properties = content.match( /{{.+}}/g ),
				property,
				propertyValue;

			for ( var index in properties ) {
				property = properties[ index ].replace( '{{', '' ).replace( '}}', '' );
				if ( ! eventData.hasOwnProperty( property ) || null === eventData[property] || undefined === eventData[property] ) {
					propertyValue = '-';
				} else {
					propertyValue = eventData[property];
				}

				content = content.replace( '{{' + property + '}}', propertyValue );
			}

			return content;
		},
		getBlockUIOptions: function( message, bgColor ) {
			return {
				message: message,
				css: {
					fontSize: '18px',
					color: 'inherit',
					backgroundColor: 'transparent',
					border: 'none'
				},
				overlayCSS: {
					backgroundColor: bgColor,
					opacity: 0.7,
					cursor: 'wait'
				}
			};
		}
	};

	// Extends the WC_OD_Calendar
	$.WC_OD_Delivery_Calendar = function() {
		var defaults = {
			countryStates: []
		};

		$.WC_OD_Calendar.apply( this, arguments );

		this.options = $.extend( {}, defaults, this.options );
		this.options.countryStates = $.parseJSON( this.options.countryStates );
	};

	$.WC_OD_Delivery_Calendar.prototype = $.extend( {}, $.WC_OD_Calendar.prototype, {
		_bindEvents : function() {
			var self = this;

			$.WC_OD_Calendar.prototype._bindEvents.apply( this, arguments );

			// On create modal dialog.
			this.$modal.on( 'dialogcreate', function() {
				var $country = self.$modalForm.find( '[name="country"]' ),
				    $states = self.$modalForm.find( '[name="states"]' );

				// Add select2.
				$country.select2({
					width: '100%',
					allowClear: true
				}).on( 'change', function() {
					$states.empty().val( '' ).trigger( 'change' );
					self._initStatesSelect2( $country, $states );
				});
			});
		},
		// Initialize the states field, a dependent select2 from country select.
		_initStatesSelect2: function( $country, $states ) {
			var states = this.options.countryStates[$country.val()];

			if ( ! states ) {
				states = [];
			}

			$states.select2({
				width: '100%',
				allowClear: true,
				multiple: true,
				data: states
			});
		},
		// Load the event into the modal form
		loadEventForm: function( eventObject ) {
			var eventData = this.getEventData( eventObject ),
			    $country = this.$modalForm.find( '[name="country"]' ),
			    $states = this.$modalForm.find( '[name="states"]' );

			$.WC_OD_Calendar.prototype.loadEventForm.apply( this, arguments );

			$country.val( eventData.country ).trigger( 'change' );
			$states.val( eventData.states ).trigger( 'change' );
			this._initStatesSelect2( $country, $states );
		},
		getEventData: function( eventObject ) {
			var eventData = $.WC_OD_Calendar.prototype.getEventData.apply( this, arguments );

			eventData.country = ( eventObject.country ? eventObject.country : '' );
			eventData.states = ( eventObject.states ? eventObject.states : '' );

			return eventData;
		},
		getEventObject: function( eventData ) {
			var eventObject = $.WC_OD_Calendar.prototype.getEventObject.apply( this, arguments );

			if ( ! eventData.states ) {
				eventObject.states = '';
			}

			return eventObject;
		}
	});

	$.fn.WC_OD_Calendar = function( options ) {
		var instance;

		this.each(function() {
			instance = new $.WC_OD_Calendar( options, this );
			instance._init();
		});

		return this;
	};

	$.fn.WC_OD_Delivery_Calendar = function( options ) {
		var instance;

		this.each(function() {
			instance = new $.WC_OD_Delivery_Calendar( options, this );
			instance._init();
		});

		return this;
	};
});