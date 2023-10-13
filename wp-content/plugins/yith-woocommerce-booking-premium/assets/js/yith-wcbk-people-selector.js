/* global yith_people_selector_params */

( function ( $, window, document ) {
	$.fn.yith_wcbk_people_selector = function ( options ) {
		var self             = this,
			$peopleSelector  = $( this ),
			$fields          = false,
			$totals          = '',
			$fieldsContainer = false,
			$dom             = {
				main  : false,
				open  : false,
				close : false,
				toggle: false,
				widget: $( '.yith_wcbk_booking_product_form_widget' )
			},
			_init            = function () {
				self.value = 0;
				_initData();

				_initFields();

				_initActions();

				_update( false );

				_close();

				return $peopleSelector;
			},
			_initActions     = function () {
				// Use Event Listener directly, to avoid bubbling (and issues with e.stopPropagation).
				document.addEventListener( 'click', _handleClick, true );

				var id     = $peopleSelector.attr( 'id' ),
					$label = id ? $( 'label[for="' + id + '"]' ) : false;

				$peopleSelector.on( 'open', _open );
				$peopleSelector.on( 'close', _close );

				$fields.on( 'change', _update );

				if ( $label && $label.length ) {
					$label.on( 'click', function () {
						$dom.toggle.trigger( 'click' );
					} );
				}
			},
			_initData        = function () {
				var min = $peopleSelector.data( 'min' );
				var max = $peopleSelector.data( 'max' );

				if ( typeof min !== 'undefined' ) {
					self.min = parseInt( min, 10 );
				} else {
					self.min = false;
				}

				if ( typeof max !== 'undefined' ) {
					self.max = parseInt( max, 10 );
				} else {
					self.max = false;
				}
			},
			_initFields      = function () {
				$totals          = $peopleSelector.find( options.totals_selector ) || '';
				$fieldsContainer = $peopleSelector.find( options.fieldsContainer_selector ) || '';
				$fields          = $peopleSelector.find( options.field_selector ) || false;

				$dom.main   = $peopleSelector.find( '.' + options.class );
				$dom.open   = $peopleSelector.find( options.openHandler_selector );
				$dom.close  = $peopleSelector.find( options.closeHandler_selector );
				$dom.toggle = $peopleSelector.find( options.toggleHandler_selector );

				_updateTotals(); // update totals to init self.value;

				if ( $fields ) {
					$fields.each( function () {
						$( this ).yith_wcbk_people_selector_field( {
																	   canIncreaseGlobal: function () {
																		   if ( self.max !== false ) {
																			   return self.value < self.max;
																		   }
																		   return true;
																	   },
																	   onUpdate         : function () {
																		   $fields.trigger( 'enableDisableButtons' );
																	   }
																   } );
					} );
				}
			},
			_isOpened        = function () {
				return !!$peopleSelector.hasClass( options.opened_class );
			},
			_updateTotals    = function () {
				var _tot = 0;
				if ( $fields ) {
					$fields.each( function () {
						_tot += parseInt( $( this ).yith_wcbk_people_selector_field( 'getValue' ) );
					} );
				}
				self.value = _tot;
			},
			_update          = function ( _triggerChange ) {
				var triggerChange = _triggerChange || true,
					_totals;
				_updateTotals();

				if ( self.value > 1 ) {
					_totals = options.plural_label.replace( '%s', self.value.toString() );
				} else if ( self.value === 1 ) {
					_totals = options.singular_label;
				} else {
					_totals = options.zero_label;
				}

				$totals.text( _totals );

				if ( triggerChange ) {
					$peopleSelector.trigger( 'change' );
				}
			},
			_open            = function () {
				$peopleSelector.removeClass( options.closed_class ).addClass( options.opened_class );
				if ( $fieldsContainer ) {
					$fieldsContainer.show();
				}
				options.onOpen();
			},
			_close           = function () {
				$peopleSelector.removeClass( options.opened_class ).addClass( options.closed_class );
				if ( $fieldsContainer ) {
					$fieldsContainer.hide();
				}
				options.onClose();
			},
			_toggle          = function ( event ) {
				if ( typeof event === 'object' && typeof event.stopPropagation !== 'undefined' ) {
					event.stopPropagation();
				}
				if ( _isOpened() ) {
					_close();
				} else {
					_open();
				}
			},
			_handleClick     = function ( e ) {
				if ( $dom.close.length && $dom.close.get( 0 ).contains( e.target ) ) {
					_close();
				} else if ( $dom.open.length && $dom.open.get( 0 ).contains( e.target ) ) {
					_open();
				} else if ( $dom.toggle.length && $dom.toggle.get( 0 ).contains( e.target ) ) {
					_toggle();
				} else if ( $peopleSelector.get( 0 ).contains( e.target ) ) {
					// do nothing.
				} else {
					_close();
				}
			},
			defaults         = {
				zero_label              : yith_people_selector_params.i18n_zero_person,
				singular_label          : yith_people_selector_params.i18n_one_person,
				plural_label            : yith_people_selector_params.i18n_more_persons,
				class                   : 'yith-wcbk-people-selector',
				totals_selector         : '.yith-wcbk-people-selector__totals',
				closeHandler_selector   : '.yith-wcbk-people-selector__close-handler',
				openHandler_selector    : '.yith-wcbk-people-selector__open-handler',
				toggleHandler_selector  : '.yith-wcbk-people-selector__toggle-handler',
				fieldsContainer_selector: '.yith-wcbk-people-selector__fields-container',
				field_selector          : '.yith-wcbk-people-selector__field',
				opened_class            : 'yith-wcbk-people-selector--opened',
				closed_class            : 'yith-wcbk-people-selector--closed',
				onOpen                  : function () {
				},
				onClose                 : function () {
				}
			};
		options              = $.extend( {}, defaults, options );


		self.val = function () {
			return self.value;
		};

		return _init();
	};


	$.fn.yith_wcbk_people_selector_field = function ( options ) {
		var self         = this,
			$field       = $( this ),
			$value       = false,
			$total       = false,
			$plus        = false,
			$minus       = false,
			_init        = function () {
				options = typeof options !== 'undefined' ? options : {};
				if ( options === 'getValue' ) {
					return $field.data( 'value' ) || 0;
				}

				self.options = $.extend( {}, defaults, options );

				_initData();

				_initField();

				_initActions();

				setValue( getValue(), false );

				return $field;
			},
			_initField   = function () {
				$value = $field.find( self.options.value_selector ) || false;
				$total = $field.find( self.options.total_selector ) || false;
				$plus  = $field.find( self.options.plus_selector ) || false;
				$minus = $field.find( self.options.minus_selector ) || false;
			},
			_initActions = function () {
				$field.on( 'click', self.options.plus_selector, function () {
					if ( self.canIncrease() ) {
						increase();
					}
				} );
				$field.on( 'click', self.options.minus_selector, function () {
					if ( self.canDecrease() ) {
						decrease();
					}
				} );

				$field.on( 'enableDisableButtons', self.enableDisableButtons );
			},
			_initData    = function () {
				var min = $field.data( 'min' );
				var max = $field.data( 'max' );

				if ( typeof min !== 'undefined' ) {
					self.min = parseInt( min );
				} else {
					self.min = false;
				}

				if ( typeof max !== 'undefined' ) {
					self.max = parseInt( max );
				} else {
					self.max = false;
				}
			},
			_update      = function ( _triggerChange ) {
				var triggerChange = typeof _triggerChange !== 'undefined' ? _triggerChange : true,
					current_value = getValue();
				if ( $total ) {
					$total.text( current_value );
				}
				if ( $value ) {
					$value.val( current_value );
				}

				if ( triggerChange ) {
					$value.trigger( 'change' );
				}

				self.enableDisableButtons();

				self.options.onUpdate();
			},
			setValue     = function ( _value, _triggerChange ) {
				if ( $value && typeof _value !== 'undefined' ) {
					var value = _value;
					if ( self.min !== false ) {
						value = Math.max( self.min, parseInt( value ) );
					}

					if ( self.max !== false ) {
						value = Math.min( self.max, parseInt( value ) );
					}

					value = parseInt( value );

					$value.val( value );

					$field.data( 'value', value );

					_update( _triggerChange );
				}
			},
			getValue     = function () {
				var value = 0;
				if ( $value ) {
					value = $value.val() || 0;
				}
				return parseInt( value );
			},
			decrease     = function ( _value ) {
				var value         = typeof _value !== 'undefined' && !isNaN( _value ) ? parseInt( _value ) : 1,
					current_value = getValue();

				value = parseInt( value );
				setValue( current_value - value );
			},
			increase     = function ( _value ) {
				var value         = typeof _value !== 'undefined' && !isNaN( _value ) ? parseInt( _value ) : 1,
					current_value = getValue();
				value             = parseInt( value );
				setValue( current_value + value );
			},
			defaults     = {
				value_selector     : '.yith-wcbk-people-selector__field__value',
				total_selector     : '.yith-wcbk-people-selector__field__total',
				plus_selector      : '.yith-wcbk-people-selector__field__plus',
				minus_selector     : '.yith-wcbk-people-selector__field__minus',
				buttonDisabledClass: 'yith-wcbk-people-selector__field__button--disabled',
				canIncreaseGlobal  : function () {
					return true;
				},
				canDecreaseGlobal  : function () {
					return true;
				},
				onUpdate           : function () {
				}
			};

		self.canIncrease = function () {
			var can = true;
			if ( self.max !== false ) {
				can = getValue() < self.max;
			}
			return can && self.options.canIncreaseGlobal();
		};

		self.canDecrease = function () {
			var can = true;
			if ( self.min !== false ) {
				can = getValue() > self.min;
			}
			return can && self.options.canDecreaseGlobal();
		};

		self.enableDisableButtons = function () {
			if ( self.canIncrease() ) {
				$plus.removeClass( self.options.buttonDisabledClass );
			} else {
				$plus.addClass( self.options.buttonDisabledClass );
			}

			if ( self.canDecrease() ) {
				$minus.removeClass( self.options.buttonDisabledClass );
			} else {
				$minus.addClass( self.options.buttonDisabledClass );
			}
		};

		return _init();
	};
} )
( jQuery, window, document );