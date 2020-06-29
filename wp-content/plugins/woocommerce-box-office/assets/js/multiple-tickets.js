/* globals jQuery, Backbone, ticketFormParams */
( function( $ ) {

	var App,
		TicketListView,
		TicketView;


	App = $.extend(
		{
			'formSelector': '.wc-box-office-ticket-form',
			'templateDOMSelector': '.wc-box-office-ticket-fields:first',
			'fieldsTemplate': {}
		},
		ticketFormParams
	);


	TicketListView = Backbone.View.extend({
		initialize: function(opts) {
			this.qty = App.is_admin
				? $( '[name="quantity"]' )
				: this.$el.parent().find( '[name="quantity"]:first' );

			this.qty.on( 'change', $.proxy( this.updateQty, this ) );
			this.addToCart = $( '.wc-box-office-ticket-form' ).parent().find( '.single_add_to_cart_button' );
			this.tickets = [];
			this.prevTicketEls = [];
		},

		updateQty: function() {
			this.removeTickets();
			this.render();
		},

		render: function() {
			var qty = parseInt(this.qty.val(), 10),
				activeFieldsNum = 0;

			// In case product's sold individually is enabled.
			if ( ! this.qty.length ) {
				qty = 1;
			}

			for (var i = 0; i < qty; i++) {
				var ticket = new TicketView( { index: i, parent: this } );

				this.tickets.push( ticket );
				this.$el.append( ticket.render().el );

				// First, check from previous posted values.
				if ( App.posted_data && App.posted_data[ i ] ) {
					for ( var fieldKey in App.posted_data[ i ] ) {
						var value = App.posted_data[ i ][ fieldKey ];
						if ( value ) {
							this.setElValue( $( '[id^="field_' + fieldKey + '"]', ticket.$el ), value );
						}
					}
				}

				// This is happen after qty updated, not on initial render like
				// above.
				if ( this.prevTicketEls[ i ] ) {
					var isActive = this.prevTicketEls[ i ].hasClass( 'active' );

					if ( isActive ) {
						ticket.toggle();
						activeFieldsNum += 1;
					}

					this.copyValuesFromPreviousElement( i );
				}
			}

			// Remove old posted values.
			App.posted_data = [];

			// If no fields active, toggle the first.
			if ( ! activeFieldsNum && this.tickets[0] ) {
				this.tickets[0].toggle();
			}

			this.updateAddToCartText();

			return this;
		},

		copyValuesFromPreviousElement: function( i ) {
			var self = this;
			$( '.ticket-field-input', this.prevTicketEls[ i ] ).each( function() {
				var id = $( this ).attr( 'id' ),
					value = $( this ).val();

				// Previous element has value, copy it to new element in the new view.
				if ( value ) {
					self.setElValue( $( '#' + id, this.$el ), value );
				}
			} );
		},

		setElValue: function( $el, value ) {
			switch ( $el.prop( 'nodeName' ) ) {
				case 'INPUT':
					if ( 'radio' === $el.attr( 'type' ) ) {
						$el.each( function() {
							$( this ).prop( 'checked', value === this.value );
						} );
					} else {
						$el.val( value );
					}
					break;
				default:
					$el.val( value );
			}
		},

		removeTickets: function() {
			while ( this.prevTicketEls.length ) {
				this.prevTicketEls.pop().remove();
			}

			while ( this.tickets.length ) {
				var ticket = this.tickets.shift();

				this.prevTicketEls.push( ticket.$el.clone() );
				ticket.remove();
			}
		},

		updateAddToCartText: function() {
			var qty = parseInt(this.qty.val(), 10),
				txt = App.i18n_add_to_cart_singular;

			if ( qty > 1 ) {
				txt = App.i18n_add_to_cart_plural;
			}

			this.addToCart.text( txt );
		}
	});


	TicketView = Backbone.View.extend({
		tagName: 'div',
		active: false,
		className: 'wc-box-office-ticket-fields',
		events: {
			'click .wc-box-office-ticket-fields-title a': 'toggle'
		},

		initialize: function(opts) {
			this.index = opts.index || 0;
			this.parent = opts.parent;
		},

		toggle: function( e ) {
			this.active = ! this.active;
			this.$el.toggleClass( 'active', this.active );

			if ( e ) {
				e.preventDefault();
			}
		},

		render: function() {
			this.$el.html( App.getFieldsTemplate( this.parent ) );
			this.updateFieldIndex();
			this.updateTitle();

			return this;
		},

		updateFieldIndex: function() {
			var parent = this.$el,
				fields = $( '[name^="' + App.field_name_prefix + '"]', parent ),
				index = this.index;

			fields.each(function() {
				var suffix = $( this ).attr( 'name' ).substr( App.field_name_prefix.length ),
					newFieldName = App.field_name_prefix.replace( '[0]', '[' + index + ']' ) + suffix,
					oldId = $( this ).attr( 'id' ),
					newId = oldId ? oldId + '_' + index : '';

				$( this ).attr( 'name', newFieldName );
				$( this ).attr( 'id', newId );

				// Update label that refers to this field.
				$( 'label[for="' + oldId + '"]', parent ).attr( 'for', newId );
			});

			this.$el.attr( 'data-index', index );
		},

		updateTitle: function() {
			$( '.wc-box-office-ticket-fields-title a', this.$el ).html(
				App.i18n_ticket_title_prefix + ( this.index + 1 )
			);
		}
	});


	App.getFieldsTemplate = function( form ) {
		if ( ! App.fieldsTemplate[ form.cid ] && form.$el.find( App.templateDOMSelector ).length ) {
			var templateDOM = form.$el.find( App.templateDOMSelector );
			App.fieldsTemplate[ form.cid ] = templateDOM.clone().removeAttr( 'style' ).html();

			// Remove it as we're going to use Backbone View.
			templateDOM.remove();
		}
		return App.fieldsTemplate[ form.cid ];
	};


	App.run = function() {
		$( App.formSelector ).each( function() {
			var el = $( this );
			new TicketListView( { el: el } ).render();
		} );
	};


	$( App.run );

} )( jQuery );
