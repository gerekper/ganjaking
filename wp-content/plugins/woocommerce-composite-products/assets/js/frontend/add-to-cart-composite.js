
/*-----------------------------------------------------------------*/
/*  Global variable for composite apps.                            */
/*-----------------------------------------------------------------*/

var wc_cp_composite_scripts = {};

/*-----------------------------------------------------------------*/
/*  Global utility variables + functions.                          */
/*-----------------------------------------------------------------*/

/**
 * Cache for speed.
 */
var $wc_cp_body     = false,
	$wc_cp_html     = jQuery( 'html' ),
	$wc_cp_window   = jQuery( window ),
	$wc_cp_document = jQuery( document );

/**
 * BlockUI background params.
 */
var wc_cp_block_params = {
	message:    null,
	fadeIn:     200,
	fadeOut:    200,
	overlayCSS: {
		background: 'rgba( 255, 255, 255, 0 )',
		opacity:    0.6
	}
};

/**
 * Toggle-box handling.
 */
function wc_cp_toggle_element( $container, $content, complete, duration ) {

	duration = typeof duration === 'undefined' ? 300 : duration;

	if ( $container.data( 'animating' ) === true ) {
		return false;
	}

	if ( $container.hasClass( 'closed' ) ) {
		setTimeout( function() {
			$content.slideDown( { duration: duration, queue: false, always: function() {
				$container.removeClass( 'opening' );
				$container.data( 'animating', false );
				if ( typeof( complete ) === 'function' ) {
					complete();
				}
			} } );
		}, 40 );
		$container.removeClass( 'closed' ).addClass( 'open opening' );
		$container.find( '.aria_button' ).attr( 'aria-expanded', 'true' );
		$container.data( 'animating', true );
	} else {
		setTimeout( function() {
			$content.slideUp( { duration: duration, queue: false, always: function() {
				$container.removeClass( 'closing' );
				$container.data( 'animating', false );
				if ( typeof( complete ) === 'function' ) {
					complete();
				}
			} } );
		}, 40 );
		$container.removeClass( 'open' ).addClass( 'closed closing' );
		$container.find( '.aria_button' ).attr( 'aria-expanded', 'false' );
		$container.data( 'animating', true );
	}

	return true;
}

/**
 * Converts numbers to formatted price strings. Respects WC price format settings.
 */
function wc_cp_price_format( price, plain ) {

	plain = typeof( plain ) === 'undefined' ? false : plain;

	return wc_cp_woocommerce_number_format( wc_cp_number_format( price ), plain );
}

/**
 * Formats price strings according to WC settings.
 */
function wc_cp_woocommerce_number_format( price, plain ) {

	var remove     = wc_composite_params.currency_format_decimal_sep,
		position   = wc_composite_params.currency_position,
		symbol     = wc_composite_params.currency_symbol,
		trim_zeros = wc_composite_params.currency_format_trim_zeros,
		decimals   = wc_composite_params.currency_format_num_decimals;

	plain = typeof( plain ) === 'undefined' ? false : plain;

	if ( trim_zeros == 'yes' && decimals > 0 ) {
		for ( var i = 0; i < decimals; i++ ) { remove = remove + '0'; }
		price = price.replace( remove, '' );
	}

	var formatted_price  = String( price ),
		formatted_symbol = plain ? symbol : '<span class="woocommerce-Price-currencySymbol">' + symbol + '</span>';

	if ( 'left' === position ) {
		formatted_price = formatted_symbol + formatted_price;
	} else if ( 'right' === position ) {
		formatted_price = formatted_price + formatted_symbol;
	} else if ( 'left_space' === position ) {
		formatted_price = formatted_symbol + ' ' + formatted_price;
	} else if ( 'right_space' === position ) {
		formatted_price = formatted_price + ' ' + formatted_symbol;
	}

	formatted_price = plain ? formatted_price : '<span class="woocommerce-Price-amount amount">' + formatted_price + '</span>';

	return formatted_price;
}

/**
 * Formats price values according to WC settings.
 */
function wc_cp_number_format( number ) {

	var decimals      = wc_composite_params.currency_format_num_decimals;
	var decimal_sep   = wc_composite_params.currency_format_decimal_sep;
	var thousands_sep = wc_composite_params.currency_format_thousand_sep;

	var n = number, c = isNaN( decimals = Math.abs( decimals ) ) ? 2 : decimals;
	var d = typeof( decimal_sep ) === 'undefined' ? ',' : decimal_sep;
	var t = typeof( thousands_sep ) === 'undefined' ? '.' : thousands_sep, s = n < 0 ? '-' : '';
	var i = parseInt( n = Math.abs( +n || 0 ).toFixed(c), 10 ) + '', j = ( j = i.length ) > 3 ? j % 3 : 0;

	return s + ( j ? i.substr( 0, j ) + t : '' ) + i.substr(j).replace( /(\d{3})(?=\d)/g, '$1' + t ) + ( c ? d + Math.abs( n - i ).toFixed(c).slice(2) : '' );
}

/**
 * Rounds price values according to WC settings.
 */
function wc_cp_number_round( number, decimals ) {

	var precision         = typeof( decimals ) === 'undefined' ? wc_composite_params.currency_format_num_decimals : decimals,
		factor            = Math.pow( 10, parseInt( precision, 10 ) ),
		tempNumber        = number * factor,
		roundedTempNumber = Math.round( tempNumber );

	return roundedTempNumber / factor;
}

/**
 * i18n-friendly joining of values in an array of strings.
 */
function wc_cp_join( arr ) {

	var joined_arr = '';
	var count      = arr.length;

	if ( count > 0 ) {

		var loop = 0;

		for ( var i = 0; i < count; i++ ) {

			loop++;

			if ( count == 1 || loop == 1 ) {
				joined_arr = arr[ i ];
			} else {
				joined_arr = wc_composite_params.i18n_comma_sep.replace( '%s', joined_arr ).replace( '%v', arr[ i ] );
			}
		}
	}

	return joined_arr;
}

/**
 * Construct a (formatted) map of selected variation attributes.
 */
function wc_cp_get_variation_data( $variations, formatted, raw ) {

	formatted = formatted || false;
	raw       = raw || false;

	var $attribute_options       = $variations.find( '.attribute_options' ),
		attribute_options_length = $attribute_options.length,
		meta                     = raw ? {} : [],
		formatted_meta           = '';

	if ( attribute_options_length === 0 ) {
		return '';
	}

	$attribute_options.each( function( index ) {

		var $attribute_option = jQuery( this ),
			$attribute_select = $attribute_option.find( 'select' ),
			attribute_name    = $attribute_select.data( 'attribute_name' ) || $attribute_select.attr( 'name' ),
			attribute_label   = $attribute_option.data( 'attribute_label' ),
			selected          = $attribute_select.val();

		if ( ! raw && selected === '' ) {
			meta           = [];
			formatted_meta = '';
			return false;
		}

		var key   = raw ? attribute_name : attribute_label,
			value = raw ? selected : $attribute_option.find( 'select option:selected' ).text();

		if ( raw ) {
			meta[ key ] = value;
		} else {
			meta.push( { meta_key: key, meta_value: value } );
		}

		formatted_meta = formatted_meta + '<span class="meta_element"><span class="meta_key">' + key + ':</span> <span class="meta_value">' + value + '</span>';

		if ( index !== attribute_options_length - 1 ) {
			formatted_meta = formatted_meta + '<span class="meta_element_sep">, </span>';
		}

		formatted_meta = formatted_meta + '</span>';

	} );

	return formatted ? formatted_meta : meta;
}

/**
 * Element-in-viewport check with partial element detection & direction support.
 * Credit: Sam Sehnert - https://github.com/customd/jquery-visible
 */
jQuery.fn.wc_cp_is_in_viewport = function( partial, hidden, direction ) {

	var $w = $wc_cp_window;

	if ( this.length < 1 ) {
		return;
	}

	var $t         = this.length > 1 ? this.eq(0) : this,
		t          = $t.get(0),
		vpWidth    = $w.width(),
		vpHeight   = $w.height(),
		clientSize = hidden === true ? t.offsetWidth * t.offsetHeight : true;

	direction = (direction) ? direction : 'vertical';

	if ( typeof t.getBoundingClientRect === 'function' ) {

		// Use this native browser method, if available.
		var rec      = t.getBoundingClientRect(),
			tViz     = rec.top    >= 0 && rec.top    <  vpHeight,
			bViz     = rec.bottom >  0 && rec.bottom <= vpHeight,
			mViz     = rec.top    <  0 && rec.bottom >  vpHeight,
			lViz     = rec.left   >= 0 && rec.left   <  vpWidth,
			rViz     = rec.right  >  0 && rec.right  <= vpWidth,
			vVisible = partial ? tViz || bViz || mViz : tViz && bViz,
			hVisible = partial ? lViz || rViz : lViz && rViz;

		if ( direction === 'both' ) {
			return clientSize && vVisible && hVisible;
		} else if ( direction === 'vertical' ) {
			return clientSize && vVisible;
		} else if ( direction === 'horizontal' ) {
			return clientSize && hVisible;
		}

	} else {

		var viewTop       = $w.scrollTop(),
			viewBottom    = viewTop + vpHeight,
			viewLeft      = $w.scrollLeft(),
			viewRight     = viewLeft + vpWidth,
			offset        = $t.offset(),
			_top          = offset.top,
			_bottom       = _top + $t.height(),
			_left         = offset.left,
			_right        = _left + $t.width(),
			compareTop    = partial === true ? _bottom : _top,
			compareBottom = partial === true ? _top : _bottom,
			compareLeft   = partial === true ? _right : _left,
			compareRight  = partial === true ? _left : _right;

		if ( direction === 'both' ) {
			return !!clientSize && ( ( compareBottom <= viewBottom ) && ( compareTop >= viewTop ) ) && ( ( compareRight <= viewRight ) && ( compareLeft >= viewLeft ) );
		} else if ( direction === 'vertical' ) {
			return !!clientSize && ( ( compareBottom <= viewBottom ) && ( compareTop >= viewTop ) );
		} else if ( direction === 'horizontal' ) {
			return !!clientSize && ( ( compareRight <= viewRight ) && ( compareLeft >= viewLeft ) );
		}
	}
};

/**
 * Composite app object getter.
 */
jQuery.fn.wc_get_composite_script = function() {

	var $composite_form = jQuery( this );

	if ( ! $composite_form.hasClass( 'composite_form' ) ) {
		return false;
	}

	var script_id = $composite_form.data( 'script_id' );

	if ( typeof( wc_cp_composite_scripts[ script_id ] ) !== 'undefined' ) {
		return wc_cp_composite_scripts[ script_id ];
	}

	return false;
};

/**
 * Composite app object getter.
 */
jQuery.fn.wc_cp_animate_height = function( to, duration, callbacks ) {

	var $el      = jQuery( this ),
	    before   = callbacks && typeof callbacks.before === 'function' ? callbacks.before : false,
	    start    = callbacks && typeof callbacks.start === 'function' ? callbacks.start : false,
	    complete = callbacks && typeof callbacks.complete === 'function' ? callbacks.complete : false;

	if ( before ) {
		before();
	}

	if ( 'css' === wc_composite_params.animate_height_method ) {

		var from = $el.get( 0 ).getBoundingClientRect().height;

		if ( typeof from === 'undefined' ) {
			from = $el.outerHeight();
		}

		$el.addClass( 'animating' ).css( {
			height: from + 'px',
			overflow: 'hidden',
			transition: 'height ' + ( duration - 10 ) / 1000 + 's',
			'-webkit-transition': 'height ' + ( duration - 10 ) / 1000 + 's'
		} );

		setTimeout( function() {

			if ( callbacks && typeof callbacks.start === 'function' ) {
				callbacks.start();
			}

			$el.css( {
				height: to + 'px'
			} );

		}, 1 );

		setTimeout( function() {

			$el.removeClass( 'animating' ).css( {
				height: '',
				overflow: '',
				transition: '',
				'-webkit-transition': ''
			} );

			if ( callbacks && typeof callbacks.complete === 'function' ) {
				callbacks.complete();
			}

		}, duration );

	} else {

		var params = { duration: duration, queue: false, always: function() {

			if ( complete ) {
				complete();
			}

			$el.removeClass( 'animating' );
		} };

		setTimeout( function() {

			if ( start ) {
				start();
			}

			$el.addClass( 'animating' ).animate( { 'height': to }, params );

		}, 1 );
	}
};

/*-----------------------------------------------------------------*/
/*  Encapsulation.                                                 */
/*-----------------------------------------------------------------*/

( function( $, Backbone ) {

	/*-----------------------------------------------------------------*/
	/*  Class Definitions.                                             */
	/*-----------------------------------------------------------------*/

	var wc_cp_classes = {};

	/**
	 * Composite product object. The core of the app.
	 */
	function WC_CP_Composite( data ) {

		var composite                           = this;

		this.composite_id                       = data.$composite_data.data( 'container_id' );

		/*
		 * Common jQuery DOM elements for quick, global access.
		 */
		this.$composite_data                    = data.$composite_data;
		this.$composite_form                    = data.$composite_form;
		this.$composite_add_to_cart_button      = data.$composite_form.find( '.composite_add_to_cart_button' );
		this.$composite_navigation              = data.$composite_form.find( '.composite_navigation' );
		this.$composite_navigation_top          = data.$composite_form.find( '.composite_navigation.top' );
		this.$composite_navigation_bottom       = data.$composite_form.find( '.composite_navigation.bottom' );
		this.$composite_navigation_movable      = data.$composite_form.find( '.composite_navigation.movable' );
		this.$composite_pagination              = data.$composite_form.find( '.composite_pagination' );
		this.$composite_summary                 = data.$composite_form.find( '.composite_summary' );
		this.$composite_summary_widget          = $( '.widget_composite_summary' ).filter( function() { return $( this ).find( '.widget_composite_summary_content_' + composite.composite_id ).length > 0; } );

		this.$components                        = data.$composite_form.find( '.composite_component' );
		this.$steps                             = {};

		this.$composite_availability            = data.$composite_data.find( '.composite_availability' );
		this.$composite_price                   = data.$composite_data.find( '.composite_price' );
		this.$composite_message                 = data.$composite_data.find( '.composite_message' );
		this.$composite_button                  = data.$composite_data.find( '.composite_button' );
		this.$composite_quantity                = this.$composite_button.find( 'input.qty' );

		this.$composite_status                  = data.$composite_form.find( '.composite_status' );
		this.$composite_transition_helper       = data.$composite_form.find( '.scroll_show_component' );
		this.$composite_form_blocker            = data.$composite_form.find( '.form_input_blocker' );

		/*
		 * Object properties used for some real work.
		 */
		this.timers                             = { on_resize_timer: false };

		this.ajax_url                           = wc_composite_params.use_wc_ajax === 'yes' ? woocommerce_params.wc_ajax_url : woocommerce_params.ajax_url;
		this.debug_tab_count                    = 0;

		this.settings                           = data.$composite_data.data( 'composite_settings' );

		this.is_initialized                     = false;
		this.is_finalized                       = false;
		this.has_transition_lock                = false;

		this.blocked_elements                   = [];

		this.steps                              = [];
		this.step_factory                       = new wc_cp_classes.WC_CP_Step_Factory();

		// Stores and updates the active scenarios. Used by component models to calculate active scenarios excl and/or up to specific steps.
		this.scenarios                          = new wc_cp_classes.WC_CP_Scenarios_Manager( this );

		// WP-style actions dispatcher. Dispatches actions in response to key model events.
		this.actions                            = new wc_cp_classes.WC_CP_Actions_Dispatcher( this );

		// WP-style filters manager.
		this.filters                            = new wc_cp_classes.WC_CP_Filters_Manager();

		// Backbone Router.
		this.router                             = false;

		// Composite Data Model.
		this.data_model                         = false;

		// View classes. If necessary, override/extend these before any associated views are instantiated - @see 'init_views'.
		this.view_classes                       = new wc_cp_classes.WC_CP_Views( this );

		// Model classes. If necessary, override/extend these before any associated models are instantiated - @see 'init_models'.
		this.model_classes                      = new wc_cp_classes.WC_CP_Models( this );

		// Composite Views - @see 'init_views'.
		this.composite_viewport_scroller        = false;
		this.composite_summary_view             = false;
		this.composite_pagination_view          = false;
		this.composite_navigation_view          = false;
		this.composite_validation_view          = false;
		this.composite_availability_view        = false;
		this.composite_price_view               = false;
		this.composite_add_to_cart_button_view  = false;
		this.composite_summary_widget_views     = [];

		// API.
		this.api                                = {

			/**
			 * Navigate to a step by id.
			 *
			 * @param  string step_id
			 * @return false | void
			 */
			navigate_to_step: function( step_id ) {

				var step = composite.get_step_by( 'id', step_id );

				if ( false === step ) {
					return false;
				}

				composite.navigate_to_step( step );
			},

			/**
			 * Navigate to the previous step, if one exists.
			 *
			 * @return void
			 */
			show_previous_step: function() {

				composite.show_previous_step();
			},

			/**
			 * Navigate to the next step, if one exists.
			 *
			 * @return void
			 */
			show_next_step: function() {

				composite.show_next_step();
			},

			/**
			 * Get all created instances of WC_CP_Step.
			 *
			 * @return array
			 */
			get_steps: function() {

				return composite.get_steps();
			},

			/**
			 * Get all created instances of WC_CP_Component (inherits from WC_CP_Step).
			 *
			 * @return array
			 */
			get_components: function() {

				return composite.get_components();
			},

			/**
			 * Get the instance of WC_CP_Step based on its step_id. For components, step_id === component_id.
			 *
			 * @param  string  step_id
			 * @return WC_CP_Step | false
			 */
			get_step: function( step_id ) {

				return composite.get_step( step_id );
			},

			/**
			 * Get the instance of WC_CP_Step based on its step_id, step_index, or step_slug.
			 *
			 * - step_id: for components, step_id === component_id
			 * - step_index: zero-based index of a step
			 * - step_slug: sanitized slug obtained from the step title, used mainly by the Backbone Router to keep track of browser history when navigating between steps.
			 *
			 * @param  string  by
			 * @param  string  id
			 * @return WC_CP_Step | false
			 */
			get_step_by: function( by, id ) {

				return composite.get_step_by( by, id );
			},

			/**
			 * Get the step title of a WC_CP_Step instance based on its step_id.
			 *
			 * @param  string  step_id
			 * @return string | false
			 */
			get_step_title: function( step_id ) {

				var step = composite.get_step_by( 'id', step_id );

				if ( false === step ) {
					return false;
				}

				return step.get_title();
			},

			/**
			 * Get the step slug of a WC_CP_Step instance based on its step_id.
			 *
			 * @param  string  step_id
			 * @return string | false
			 */
			get_step_slug: function( step_id ) {

				var step = composite.get_step_by( 'id', step_id );

				if ( false === step ) {
					return false;
				}

				return step.get_slug();
			},

			/**
			 * Get the current step.
			 *
			 * @return WC_CP_Step | false
			 */
			get_current_step: function() {

				return composite.get_current_step();
			},

			/**
			 * Get the previous step.
			 *
			 * @return WC_CP_Step | false
			 */
			get_previous_step: function() {

				return composite.get_previous_step();
			},

			/**
			 * Get the next step.
			 *
			 * @return WC_CP_Step | false
			 */
			get_next_step: function() {

				return composite.get_next_step();
			},

			/**
			 * Get the current composite totals.
			 *
			 * @return object
			 */
			get_composite_totals: function() {

				return composite.data_model.get( 'totals' );
			},

			/**
			 * Get the current stock status of the composite.
			 *
			 * @return string ('in-stock' | 'out-of-stock')
			 */
			get_composite_stock_status: function() {

				return composite.data_model.get( 'is_in_stock' ) ? 'in-stock' : 'out-of-stock';
			},

			/**
			 * Get the current availability string of the composite.
			 *
			 * @return string
			 */
			get_composite_availability: function() {

				var availability = composite.composite_availability_view.get_insufficient_stock_components_string();

				if ( availability === '' && false !== composite.composite_availability_view.$composite_stock_status ) {
					availability = composite.composite_availability_view.$composite_stock_status.clone().wrap( '<div></div>' ).parent().html();
				}

				return availability;
			},

			/**
			 * Get the current validation status of the composite.
			 *
			 * @return string ('pass' | 'fail')
			 */
			get_composite_validation_status: function() {

				return composite.data_model.get( 'passes_validation' ) ? 'pass' : 'fail';
			},

			/**
			 * Get the current validation messages for the composite.
			 *
			 * @return array
			 */
			get_composite_validation_messages: function() {

				return composite.data_model.get( 'validation_messages' );
			},

			/**
			 * Gets composite configuration details.
			 *
			 * @return object | false
			 */
			get_composite_configuration: function() {

				var composite_config = {},
					components       = composite.get_components();

				if ( components.length === 0 ) {
					return false;
				}

				for ( var index = 0, length = components.length; index < length; index++ ) {

					var component        = components[ index ],
						component_config = composite.api.get_component_configuration( component.component_id );

					composite_config[ component.component_id ] = component_config;
				}

				return composite_config;
			},

			/**
			 * Get the component price.
			 *
			 * @param  string  component_id
			 * @return object | false
			 */
			get_component_totals: function( component_id ) {

				if ( false === composite.get_step_by( 'id', component_id ) ) {
					return false;
				}

				return composite.data_model.get( 'component_' + component_id + '_totals' );
			},

			/**
			 * Get the current stock status of a component.
			 *
			 * @param  string  component_id
			 * @return string ('in-stock' | 'out-of-stock')
			 */
			get_component_stock_status: function( component_id ) {

				var component = composite.get_step_by( 'id', component_id );

				if ( false === component ) {
					return false;
				}

				return component.step_validation_model.get( 'is_in_stock' ) ? 'in-stock' : 'out-of-stock';
			},

			/**
			 * Get the current availability status of a component.
			 *
			 * @param  string  component_id
			 * @return string ('in-stock' | 'out-of-stock')
			 */
			get_component_availability: function( component_id ) {

				var component = composite.get_step_by( 'id', component_id );

				if ( false === component ) {
					return false;
				}

				var $availability = component.$component_summary_content.find( '.component_wrap .stock' );

				return $availability.length > 0 ? $availability.clone().wrap( '<div></div>' ).parent().html() : '';
			},

			/**
			 * Get the current validation status of a component.
			 *
			 * @param  string  component_id
			 * @return string ('pass' | 'fail')
			 */
			get_component_validation_status: function( component_id ) {

				var component = composite.get_step_by( 'id', component_id );

				if ( false === component ) {
					return false;
				}

				return component.step_validation_model.get( 'passes_validation' ) ? 'pass' : 'fail';
			},

			/**
			 * Get the current validation messages of a component. Context: 'component' or 'composite'.
			 *
			 * @param  string  component_id
			 * @param  string  context
			 * @return array
			 */
			get_component_validation_messages: function( component_id, context ) {

				var component = composite.get_step_by( 'id', component_id );

				if ( false === component ) {
					return false;
				}

				var messages = context === 'composite' ? component.step_validation_model.get( 'composite_messages' ) : component.step_validation_model.get( 'component_messages' );

				return messages;
			},

			/**
			 * Gets configuration details for a single component.
			 *
			 * @param  string  component_id
			 * @return object | false
			 */
			get_component_configuration: function( component_id ) {

				var component        = composite.get_step_by( 'id', component_id ),
					component_config = false;

				if ( false === component ) {
					return component_config;
				}

				component_config = {
					title:           component.get_title(),
					selection_title: component.get_selected_product_title( false ),
					selection_meta:  component.get_selected_product_meta( false ),
					product_id:      component.get_selected_product( false ),
					variation_id:    component.get_selected_variation( false ),
					product_valid:   component.is_selected_product_valid(),
					variation_valid: component.is_selected_variation_valid(),
					quantity:        component.get_selected_quantity(),
					product_type:    component.get_selected_product_type()
				};

				// Pass through 'component_configuration' filter - @see WC_CP_Filters_Manager class.
				component_config = composite.filters.apply_filters( 'component_configuration', [ component_config, component ] );

				return component_config;
			},

			/**
			 * True if the composite is priced per product.
			 *
			 * @deprecated
			 *
			 * @return boolean
			 */
			is_priced_per_product: function() {
				composite.console_log( 'error', '\nMethod \'WC_CP_Composite::api::is_priced_per_product\' is deprecated since v3.7.0. Use \'WC_CP_Composite::api::is_component_priced_individually\' instead.' );
				return undefined;
			},

			/**
			 * True if the component is priced individually.
			 *
			 * @return boolean
			 */
			is_component_priced_individually: function( component_id ) {

				return composite.data_model.price_data.is_priced_individually[ component_id ] === 'yes';
			}
		};

		/**
		 * Script initialization.
		 */
		this.init = function() {

			/*
			 * Trigger pre-init jQuery event that 3rd party code may use for initialization.
			 */
			composite.$composite_data.trigger( 'wc-composite-initializing', [ composite ] );

			/*
			 * Init composite on the 'initialize_composite' hook - callbacks declared inline since they are not meant to be unhooked.
			 * To extend/override model/view classes, modify them from action callbacks hooked in at an earlier priority than the 'init_models' and 'init_views' calls.
			 */
			this.actions

				/*
				 * Init steps.
				 */
				.add_action( 'initialize_composite', function() {
					composite.init_steps();
				}, 10, this )

				/*
				 * Init models.
				 */
				.add_action( 'initialize_composite', function() {
					composite.init_models();
				}, 20, this )

				/*
				 * Init actions dispatcher. Dispatches actions in response to key model events.
				 */
				.add_action( 'initialize_composite', function() {
					composite.actions.init();
				}, 30, this )

				/*
				 * Trigger resize to add responsive CSS classes to form.
				 */
				.add_action( 'initialize_composite', function() {
					composite.on_resize_handler();
				}, 40, this )

				/*
				 * Init views.
				 */
				.add_action( 'initialize_composite', function() {
					composite.init_views();
				}, 50, this )

				/*
				 * Init scenarios manager.
				 */
				.add_action( 'initialize_composite', function() {
					composite.scenarios.init();
				}, 60, this )

				/*
				 * Init options model.
				 */
				.add_action( 'initialize_composite', function() {

					composite.console_log( 'debug:events', '\nInitializing Options:' );
					composite.debug_indent_incr();

					for ( var index = 0, components = composite.get_components(), length = components.length; index < length; index++ ) {
						components[ index ].component_options_model.refresh_options_state();
					}

					composite.debug_indent_decr();
					composite.console_log( 'debug:events', '\nDone.' );

				}, 61, this )

				/*
				 * Validate steps.
				 */
				.add_action( 'initialize_composite', function() {

					composite.console_log( 'debug:events', '\nValidating Steps:' );
					composite.debug_indent_incr();

					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
						steps[ index ].validate();
					}

					composite.debug_indent_decr();
					composite.console_log( 'debug:events', '\nValidation complete.' );

				}, 70, this )

				/*
				 * Activate initial step.
				 */
				.add_action( 'initialize_composite', function() {
					composite.get_current_step().show_step();
				}, 80, this )

				/*
				 * Init Backbone router.
				 *
				 * Works with Paged & Progressive layout composites displayed in single-product pages.
				 * Browser history will not work with composites displayed in other places, for instance composites placed in WP pages via WC shortcodes.
				 */
				.add_action( 'initialize_composite', function() {
					composite.init_router();
				}, 90, this );


			/*
			 * Run init action.
			 */
			this.actions.do_action( 'initialize_composite' );

			/*
			 * Mark as initialized.
			 */
			composite.is_initialized = true;

			/*
			 * Add post-init action hooks.
			 */
			this.actions

				/**
				 * Init data model state.
				 */
				.add_action( 'composite_initialized', function() {
					composite.data_model.init();
				}, 10, this )

				/*
				 * Finally, render all views.
				 */
				.add_action( 'composite_initialized', function() {
					composite.render_views();
				}, 20, this );

			/*
			 * Run post-init action.
			 */
			this.actions.do_action( 'composite_initialized' );

			/*
			 * Mark as finalized.
			 */
			composite.is_finalized = true;
		};

		/**
		 * Init backbone router to support browser history when transitioning between steps.
		 */
		this.init_router = function() {

			var WC_CP_Router = Backbone.Router.extend( {

				has_initial_route:  false,
				is_initial_route:   false,
				is_history_started: false,

				routes:    {
					':step_slug': 'show_step'
				},

				show_step: function( step_slug ) {

					var encoded_slug = encodeURIComponent( step_slug ),
						step         = composite.get_step_by( 'slug', encoded_slug );

					if ( step ) {

						// Is this the initial route?
						if ( ! this.is_history_started ) {
							this.has_initial_route = true;
							this.is_initial_route  = true;
						}

						// If the requested step cannot be viewed, do not proceed: Show a notice and create a new history entry based on the current step.
						if ( step.is_locked() ) {

							composite.console_log( 'warning', wc_composite_params.i18n_step_not_accessible.replace( /%s/g, step.get_title() ) );
							composite.router.navigate( composite.get_current_step().get_slug() );

						// Otherwise, scroll the viewport to the top and show the requested step.
						} else {

							if ( this.is_history_started ) {
								composite.composite_viewport_scroller.scroll_viewport( composite.$composite_form, { timeout: 0, partial: false, duration: 0, queue: false } );
							}

							step.show_step();
						}
					}
				},

				navigate_to_step: function( step ) {

					// If we're here, the initial route has been triggered already.
					this.is_initial_route = false;

					step.show_step();

					if ( this.is_routing() ) {
						this.navigate( step.get_slug() );
					}
				},

				update_history: function() {

					return 'yes' === composite.settings.update_browser_history;
				},

				is_routing: function() {

					return this.update_history() && composite.is_initialized && false === this.is_initial_route;
				},

				start: function() {

					/*
					 * Only initialize the router:
					 *
					 * - When history updates are allowed.
					 * - In single-product pages with a matching post ID.
					 *
					 * Do not initialize the router in Quick View modals!
					 */

					if ( ! this.update_history() || ! $wc_cp_body.hasClass( 'single-product' ) || ! $wc_cp_body.hasClass( 'postid-' + composite.composite_id ) || composite.$composite_form.parent().hasClass( 'quick-view-content' ) ) {
						return;
					}

					if ( Backbone.history.started ) {
						this.is_history_started = true;
					}

					if ( this.is_history_started ) {
						return;
					}

					// Start recording history and trigger the initial route.
					Backbone.history.start();

					// Set router as initialized.
					this.is_history_started = true;

					// If no initial route exists, find the initial route as defined by the served markup and write it to the history without triggering it.
					if ( composite.settings.layout !== 'single' && false === this.has_initial_route && ! window.location.hash ) {
						this.navigate( composite.get_current_step().get_slug(), { trigger: false } );
					}
				}

			} );

			composite.router = new WC_CP_Router();
			composite.router.start();
		};

		/**
		 * Initialize composite step objects.
		 */
		this.init_steps = function() {

			composite.console_log( 'debug:events', '\nInitializing Steps...' );

			/*
			 * Initialize DOM.
			 */

			if ( composite.settings.layout === 'paged' ) {

				// Componentized layout: replace the step-based process with a summary-based process.
				if ( composite.settings.layout_variation === 'componentized' ) {

					composite.$composite_form.find( '.multistep.active' ).removeClass( 'active' );
					composite.$composite_data.addClass( 'multistep active' );

				// If the composite-add-to-cart.php template is added right after the component divs, it will be used as the final step of the step-based configuration process.
				} else if ( composite.$composite_data.prev().hasClass( 'multistep' ) ) {

					composite.$composite_data.addClass( 'multistep' );
					composite.$composite_data.hide();

					// If the composite was just added to the cart, make the review/summary step active.
					if ( 'no' === composite.settings.update_browser_history && composite.$composite_data.hasClass( 'composite_added_to_cart' ) ) {
						composite.$composite_form.find( '.multistep.active' ).removeClass( 'active' );
						composite.$composite_data.addClass( 'active' );
					}

				} else {
					composite.$composite_data.show();
					composite.$composite_data.find( '.component_title .step_index' ).hide();
				}

			} else if ( composite.settings.layout === 'progressive' ) {

				composite.$components.show();
				composite.$composite_data.show();

			} else if ( composite.settings.layout === 'single' ) {

				composite.$components.show();
				composite.$composite_data.show();
			}

			/*
			 * Initialize step objects.
			 */

			composite.$steps = composite.$composite_form.find( '.multistep' );

			composite.$composite_form.find( '.composite_component, .multistep' ).each( function( index ) {

				var step = composite.step_factory.create_step( composite, $( this ), index );
				composite.steps[ index ] = step;

			} );

			composite.$composite_navigation.removeAttr( 'style' );
		};

		/**
		 * Ajax URL.
		 */
		this.get_ajax_url = function( action ) {

			return wc_composite_params.use_wc_ajax === 'yes' ? this.ajax_url.toString().replace( '%%endpoint%%', action ) : this.ajax_url;
		};

		/**
		 * Shows a step and updates the history as required.
		 */
		this.navigate_to_step = function( step ) {

			if ( typeof( step ) === 'object' && typeof( step.show_step ) === 'function' ) {
				this.router.navigate_to_step( step );
			}
		};

		/**
		 * Shows the step marked as previous from the current one.
		 */
		this.show_previous_step = function() {

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_previous() ) {
					composite.navigate_to_step( steps[ index ] );
					break;
				}
			}
		};

		/**
		 * Shows the step marked as next from the current one.
		 */
		this.show_next_step = function() {

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_next() ) {
					composite.navigate_to_step( steps[ index ] );
					break;
				}
			}
		};

		/**
		 * Returns step objects.
		 */
		this.get_steps = function() {

			return this.steps;
		};

		/**
		 * Returns step objects that are components.
		 */
		this.get_components = function() {

			var components = [];

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_component() ) {
					components.push( steps[ index ] );
				}
			}

			return components;
		};

		/**
		 * Returns a step object by id.
		 */
		this.get_step = function( step_id ) {

			var found = false;

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].step_id == step_id ) {
					found = steps[ index ];
					break;
				}

			}

			return found;
		};

		/**
		 * Returns a step object by id/index.
		 */
		this.get_step_by = function( by, id ) {

			var found = false;

			if ( by !== 'id' && by !== 'index' && by !== 'slug' ) {
				return false;
			}

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( ( by === 'id' && String( steps[ index ].step_id ) === String( id ) ) || ( by === 'index' && String( index ) === String( id ) ) || ( by === 'slug' && String( steps[ index ].get_slug() ).toUpperCase() === String( id ).toUpperCase() ) ) {
					found = steps[ index ];
					break;
				}
			}

			return found;

		};

		/**
		 * Returns the current step object.
		 */
		this.get_current_step = function() {

			var current = false;

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_current() ) {
					current = steps[ index ];
					break;
				}
			}

			return current;
		};

		/**
		 * Current step setter.
		 */
		this.set_current_step = function( step ) {

			var style           = this.settings.layout,
				style_variation = this.settings.layout_variation,
				curr_step_pre   = this.get_current_step(),
				next_step_pre   = this.get_next_step(),
				prev_step_pre   = this.get_previous_step(),
				last_step_pre   = this.get_last_step(),
				next_step       = false,
				prev_step       = false,
				last_step       = false;

			if ( style === 'paged' && style_variation === 'componentized' ) {
				next_step = prev_step = last_step = this.get_step_by( 'id', 'review' );
			} else {
				for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
					if ( false === next_step && steps[ index ].step_index > step.step_index ) {
						if ( steps[ index ].is_visible() ) {
							next_step = steps[ index ];
						}
					}
					if ( steps[ index ].step_index < step.step_index ) {
						if ( steps[ index ].is_visible() ) {
							prev_step = steps[ index ];
						}
					}
					if ( steps[ index ].is_visible() ) {
						last_step = steps[ index ];
					}
				}
			}

			curr_step_pre._is_current = false;
			step._is_current          = true;

			curr_step_pre.$el.removeClass( 'active' );
			step.$el.addClass( 'active' );

			if ( false !== next_step_pre ) {
				next_step_pre._is_next = false;
				next_step_pre.$el.removeClass( 'next' );
			}

			if ( false !== next_step ) {
				next_step._is_next = true;
				next_step.$el.addClass( 'next' );
			}

			if ( false !== prev_step_pre ) {
				prev_step_pre._is_previous = false;
				prev_step_pre.$el.removeClass( 'prev' );
			}

			if ( false !== prev_step ) {
				prev_step._is_previous = true;
				prev_step.$el.addClass( 'prev' );
			}

			if ( false !== last_step_pre ) {
				last_step_pre._is_last = false;
				last_step_pre.$el.removeClass( 'last' );
			}

			if ( false !== last_step ) {
				last_step._is_last = true;
				last_step.$el.addClass( 'last' );
			}
		};

		/**
		 * Returns the previous step object.
		 */
		this.get_previous_step = function() {

			var previous = false;
			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {

				if ( steps[ index ].is_previous() ) {
					previous = steps[ index ];
					break;
				}
			}

			return previous;
		};

		/**
		 * Returns the next step object.
		 */
		this.get_next_step = function() {

			var next = false;

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_next() ) {
					next = steps[ index ];
					break;
				}
			}

			return next;
		};

		/**
		 * Returns the last step object.
		 */
		this.get_last_step = function() {

			var last = false;

			for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
				if ( steps[ index ].is_last() ) {
					last = steps[ index ];
					break;
				}
			}

			return last;
		};

		/**
		 * Handler for viewport resizing.
		 */
		this.on_resize_handler = function() {

			// Add responsive classes to composite form.

			var form_width = composite.$composite_form.width();

			if ( form_width <= wc_composite_params.small_width_threshold ) {
				composite.$composite_form.addClass( 'small_width' );
			} else {
				composite.$composite_form.removeClass( 'small_width' );
			}

			if ( form_width > wc_composite_params.full_width_threshold ) {
				composite.$composite_form.addClass( 'full_width' );
			} else {
				composite.$composite_form.removeClass( 'full_width' );
			}

			if ( wc_composite_params.legacy_width_threshold ) {
				if ( form_width <= wc_composite_params.legacy_width_threshold ) {
					composite.$composite_form.addClass( 'legacy_width' );
				} else {
					composite.$composite_form.removeClass( 'legacy_width' );
				}
			}

			// Reset relocated container if in wrong position.
			if ( composite.is_initialized ) {
				for ( var index = 0, components = composite.get_components(), length = components.length; index < length; index++ ) {

					if ( components[ index ].component_selection_view.is_relocated() ) {

						var relocation_data = components[ index ].component_selection_view.get_new_relocation_data();

						if ( relocation_data.relocate ) {

							var $relocation_target    = components[ index ].component_selection_view.$relocation_target,
								$relocation_reference = relocation_data.reference;

							$relocation_reference.after( $relocation_target );
						}
					}
				}
			}
		};

		/**
		 * Creates all necessary composite- and step/component-level models.
		 */
		this.init_models = function() {

			/*
			 * Step models associated with the validation status and access permission status of a step.
			 */
			for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {
				var step = steps[ step_index ];
				step.step_visibility_model = new composite.model_classes.Step_Visibility_Model( step );
				step.step_validation_model = new composite.model_classes.Step_Validation_Model( step );
				step.step_access_model     = new composite.model_classes.Step_Access_Model( step );
			}

			/*
			 * Component models associated with component options and component selections.
			 */
			for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
				var component = components[ component_index ];
				component.component_options_model   = new composite.model_classes.Component_Options_Model( component );
				component.component_selection_model = new composite.model_classes.Component_Selection_Model( component );
			}

			/*
			 * Composite product data model for storing validation, pricing, availability and quantity data.
			 */
			composite.data_model = new composite.model_classes.Composite_Data_Model();
		};

		/**
		 * Creates:
		 *
		 *  - Composite product views responsible for updating the composite availability, pricing and add-to-cart button located in: i) the composite form and ii) summary widgets.
		 *  - Composite product views responsible for updateing the navigation, pagination and summary elements.
		 *  - All necessary step & component views associated with the display of validation messages, component selection details and component options.
		 */
		this.init_views = function() {

			composite.console_log( 'debug:events', '\nInitializing Views...' );

			/*
			 * Instantiate composite views.
			 */
			this.composite_validation_view = new composite.view_classes.Composite_Validation_View( {
				is_in_widget: false,
				el:           composite.$composite_message,
				model:        composite.data_model,
			} );

			this.composite_price_view = new composite.view_classes.Composite_Price_View( {
				is_in_widget: false,
				el:           composite.$composite_price,
				model:        composite.data_model,
			} );

			this.composite_availability_view = new composite.view_classes.Composite_Availability_View( {
				is_in_widget: false,
				el:           composite.$composite_availability,
				model:        composite.data_model,
			} );

			this.composite_add_to_cart_button_view = new composite.view_classes.Composite_Add_To_Cart_Button_View( {
				is_in_widget: false,
				el:           composite.$composite_button,
				$el_button:   composite.$composite_add_to_cart_button,
				model:        composite.data_model,
			} );

			this.composite_status_view = new composite.view_classes.Composite_Status_View( {
				el:           composite.$composite_status,
				$el_content:  composite.$composite_status.find( '.wrapper' ),
				model:        composite.data_model,
			} );

			if ( composite.$composite_pagination.length > 0 ) {
				composite.composite_pagination_view = new composite.view_classes.Composite_Pagination_View( { el: composite.$composite_pagination } );
			}

			if ( composite.$composite_summary.length > 0 ) {
				composite.composite_summary_view = new composite.view_classes.Composite_Summary_View( { is_in_widget: false, el: composite.$composite_summary } );
			}

			if ( composite.$composite_navigation.length > 0 ) {
				composite.composite_navigation_view = new composite.view_classes.Composite_Navigation_View( { el: composite.$composite_navigation } );
			}

			if ( composite.$composite_summary_widget.length > 0 ) {
				composite.$composite_summary_widget.each( function( index, $widget ) {
					composite.composite_summary_widget_views.push( new composite.view_classes.Composite_Widget_View( { widget_count: index + 1, el: $widget } ) );
				} );
			}

			composite.composite_viewport_scroller = new composite.view_classes.Composite_Viewport_Scroller();

			/*
			 * Initialize step/component views.
			 */
			for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {
				var step = steps[ step_index ];
				step.validation_view = new composite.view_classes.Step_Validation_View( step, { el: step.$component_message, model: step.step_validation_model } );
				step.step_title_view = new composite.view_classes.Step_Title_View( step, { el: step.$step_title } );
			}

			for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
				var component = components[ component_index ];
				component.component_selection_view  = new composite.view_classes.Component_Selection_View( component, { el: component.$component_content, model: component.component_selection_model } );
				component.component_options_view    = new composite.view_classes.Component_Options_View( component, { el: component.$component_options, model: component.component_options_model } );
				component.component_pagination_view = new composite.view_classes.Component_Pagination_View( component, { el: component.$component_pagination, model: component.component_options_model } );
			}
		};

		/**
		 * Renders component options views and the composite pagination, navigation and summary template views.
		 */
		this.render_views = function() {

			composite.console_log( 'debug:views', '\nRendering Views...' );
			composite.debug_indent_incr();

			for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
				var component = components[ component_index ];
				component.component_selection_view.render_default();
				component.component_options_view.render();
				component.component_pagination_view.render();
			}

			for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {
				var step = steps[ step_index ];
				step.step_title_view.render_navigation_state();
				step.step_title_view.render_index();
			}

			if ( false !== composite.composite_pagination_view ) {
				composite.composite_pagination_view.render();
			}
			if ( false !== composite.composite_summary_view ) {
				composite.composite_summary_view.render();
			}
			if ( false !== composite.composite_navigation_view ) {
				composite.composite_navigation_view.render( 'transition' );
			}

			for ( var index = 0, views = composite.composite_summary_widget_views, views_length = views.length; index < views_length; index++ ) {
				views[ index ].composite_summary_view.render();
			}

			composite.debug_indent_decr();
			composite.console_log( 'debug:views', '\nRendering complete.' );

			/*
			 * Get rid of no-js notice and classes.
			 */
			composite.$composite_form.removeClass( 'cp-no-js' );
			composite.$composite_form.find( '.cp-no-js-msg' ).remove();
		};

		/**
		 * Blocks the composite form and adds a waiting ui cue in the passed elements.
		 */
		this.block = function( $waiting_for ) {

			var id = $waiting_for.attr( 'id' );

			id = id || $waiting_for.attr( 'class' );

			this.blocked_elements.push( id );

			if ( this.blocked_elements.length === 1 ) {
				this.$composite_form.block( wc_cp_block_params );
				composite.has_transition_lock = true;
			}
		};

		/**
		 * Unblocks the composite form and removes the waiting ui cue from the passed elements.
		 */
		this.unblock = function( $waiting_for ) {

			var id = $waiting_for.attr( 'id' );

			id = id || $waiting_for.attr( 'class' );

			this.blocked_elements = _.without( this.blocked_elements, id );

			if ( this.blocked_elements.length === 0 ) {
				this.$composite_form.unblock();
				composite.has_transition_lock = false;
			}
		};

		/**
		 * Log stuff in the console.
		 */
		this.console_log = function( context, message ) {

			if ( window.console && typeof( message ) !== 'undefined' ) {

				var log        = false,
					is_error   = false,
					is_warning = false;

				if ( context === 'error' ) {
					log      = true;
					is_error = true;
				} else if ( context === 'warning' ) {
					log        = true;
					is_warning = true;
				} else if ( wc_composite_params.script_debug_level.length > 0 ) {
					if ( _.includes( wc_composite_params.script_debug_level, context ) ) {
						log = true;
					} else {
						for ( var index = 0, length = wc_composite_params.script_debug_level.length; index < length; index++ ) {
							if ( context.indexOf( wc_composite_params.script_debug_level[ index ] ) > -1 ) {
								log = true;
								break;
							}
						}
					}
				}

				if ( log ) {

					var tabs = '';

					if ( context !== 'error' ) {
						for ( var i = composite.debug_tab_count; i > 0; i-- ) {
							tabs = tabs + '	';
						}
					}

					message = typeof( message ) === 'function' ? message() : message;

					if ( typeof( message.substring ) === 'function' && message.substring( 0, 1 ) === '\n' ) {
						message = message.replace( '\n', '\n' + tabs );
					} else {
						message = tabs + message;
					}

					if ( context.indexOf( 'animation' ) > -1 ) {
						message = message + ' (' + window.performance.now() + ')';
					}

					if ( is_error ) {
						window.console.error( message );
					} else if ( is_warning ) {
						window.console.warn( message );
					} else {
						window.console.log( message );
					}
				}
			}
		};

		/**
		 * Increase debug output indent.
		 */
		this.debug_indent_incr = function() {
			this.debug_tab_count = this.debug_tab_count + 2;
		};

		/**
		 * Decrease debug output indent.
		 */
		this.debug_indent_decr = function() {
			this.debug_tab_count = this.debug_tab_count - 2;
		};

		/**
		 * True when updating browser history.
		 */
		this.allow_history_updates = function() {
			composite.console_log( 'warning', '\nMethod \'WC_CP_Composite::allow_history_updates\' is deprecated since v3.14.0. Use \'WC_CP_Composite::router::is_routing\' instead.' );
			return composite.router.is_routing();
		};
	}

	/*
	 * Load classes from external files to keep things tidy.
	 */


	/**
	 * Model classes instantiated in a CP app lifecycle.
	 */
	wc_cp_classes.WC_CP_Models = function( composite ) {


		/**
		 * Composite product data model for storing validation, pricing, availability and quantity data.
		 */
		this.Composite_Data_Model = function( opts ) {

			var Model = Backbone.Model.extend( {

				price_data:  composite.$composite_data.data( 'price_data' ),
				$nyp:        false,

				initialize: function() {

					var params = {
						passes_validation:     true,
						validation_messages:   [],
						status_messages:       [],
						is_in_stock:           true,
						stock_statuses:        [],
						totals:                { price: '', regular_price: '', price_incl_tax: '', price_excl_tax: '' }
					};

					for ( var index = 0, components = composite.get_components(), length = components.length; index < length; index++ ) {
						params[ 'component_' + components[ index ].component_id + '_totals' ] = { price: '', regular_price: '', price_incl_tax: '', price_excl_tax: '' };
					}

					this.set( params );

					// Price suffix data.
					this.price_data.suffix_exists              = wc_composite_params.price_display_suffix !== '';
					this.price_data.suffix_contains_price_incl = wc_composite_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1;
					this.price_data.suffix_contains_price_excl = wc_composite_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1;

					/**
					 * Update model totals when the nyp price changes.
					 */
					composite.actions.add_action( 'component_nyp_changed', this.nyp_changed_handler, 10, this );

					/**
					 * Update model totals state when a new component quantity is selected.
					 */
					composite.actions.add_action( 'component_quantity_changed', this.quantity_changed_handler, 20, this );

					/**
					 * Update model totals state when a new selection is made.
					 */
					composite.actions.add_action( 'component_selection_changed', this.selection_changed_handler, 30, this );

					/**
					 * Update totals when the contents of an existing selection change.
					 */
					composite.actions.add_action( 'component_selection_content_changed', this.selection_content_changed_handler, 30, this );

					/**
					 * Update model availability state in response to component changes.
					 */
					composite.actions.add_action( 'component_availability_changed', this.availability_changed_handler, 10, this );

					/**
					 * Update model validation state when a step validation model state changes.
					 */
					composite.actions.add_action( 'component_validation_message_changed', this.validation_status_changed_handler, 10, this );
					composite.actions.add_action( 'component_validation_status_changed', this.validation_status_changed_handler, 10, this );

					/**
					 * Update a single summary view element price when its totals change.
					 */
					composite.actions.add_action( 'component_totals_changed', this.component_totals_changed_handler, 10, this );

					/**
					 * Update composite totals when a new NYP price is entered at composite level.
					 */
					var $nyp = composite.$composite_data.find( '.nyp' );

					if ( $nyp.length > 0 ) {

						if ( $.fn.wc_nyp_get_script_object ) {

							composite.filters.add_filter( 'composite_validation_status', function( status ) {

								var nyp_script = composite.data_model.$nyp.wc_nyp_get_script_object();

								if ( nyp_script && false === nyp_script.isValid() ) {
									status = false;
								}

								return status;

							}, 10, this );
						}

						this.$nyp                  = $nyp;
						this.price_data.base_price = $nyp.data( 'price' );

						composite.$composite_data.on( 'woocommerce-nyp-updated-item', function() {

							composite.data_model.price_data.base_price         = composite.data_model.$nyp.data( 'price' );
							composite.data_model.price_data.base_regular_price = composite.data_model.$nyp.data( 'price' );

							composite.data_model.update_validation();
							composite.data_model.calculate_subtotals();
							composite.data_model.calculate_totals();
						} );
					}
				},

				/**
				 * Initializes the model and prepares data for consumption by views.
				 */
				init: function() {

					composite.console_log( 'debug:models', '\nInitializing composite data model...' );
					composite.debug_indent_incr();

					this.update_validation();
					this.update_totals();
					this.update_availability();

					composite.debug_indent_decr();
				},

				/**
				 * Updates component totals when a nyp price change event is triggered.
				 */
				nyp_changed_handler: function( component ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.update_totals( component );
				},

				/**
				 * Updates model totals state.
				 */
				selection_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.update_validation();
					this.update_totals();
				},

				/**
				 * Updates model availability state.
				 */
				availability_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.update_availability();
				},

				/**
				 * Updates model totals state.
				 */
				selection_content_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.update_validation();
					this.update_totals();
				},

				/**
				 * Updates model totals state.
				 */
				quantity_changed_handler: function( component ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.update_totals( component );
				},

				/**
				 * Updates model validation state when the state of a step validation model changes.
				 */
				validation_status_changed_handler: function() {
					this.update_validation();
				},

				// Updates totals when component subtotals change.
				component_totals_changed_handler: function() {
					this.calculate_totals();
				},

				/**
				 * Updates the validation state of the model.
				 */
				update_validation: function() {

					var messages = [],
						status   = this.get_validation_status();

					if ( this.is_purchasable() ) {
						messages = this.get_validation_messages();
					} else {
						messages.push( wc_composite_params.i18n_unavailable_text );
					}

					composite.console_log( 'debug:models', '\nUpdating \'Composite_Data_Model\' validation state... Attribute count: "validation_messages": ' + messages.length + ', Attribute: "passes_validation": ' + ( messages.length === 0 ).toString() );

					composite.debug_indent_incr();
					this.set( { validation_messages: messages, passes_validation: status } );
					composite.debug_indent_decr();
				},

				/**
				 * Get aggregate validation status.
				 */
				get_validation_status: function() {

					var passes_validation = true;

					if ( ! this.is_purchasable() ) {
						return false;
					}

					for ( var step_index = 0, steps = composite.get_steps(), length = steps.length; step_index < length; step_index++ ) {
						if ( ! steps[ step_index ].step_validation_model.get( 'passes_validation' ) ) {
							passes_validation = false;
							break;
						}
					}

					// Pass through 'composite_validation_status' filter - @see WC_CP_Filters_Manager class.
					return composite.filters.apply_filters( 'composite_validation_status', [ passes_validation ] );
				},

				/**
				 * Get all validation messages grouped by source. Messages added from the 'Review' step are displayed individually.
				 */
				get_validation_messages: function() {

					var validation_messages = [];

					for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {

						var source = steps[ step_index ].get_title();

						for ( var rm_index = 0, raw_messages = steps[ step_index ].get_validation_messages( 'composite' ), raw_messages_length = raw_messages.length; rm_index < raw_messages_length; rm_index++ ) {

							if ( steps[ step_index ].is_review() ) {
								validation_messages.push( { sources: false, content: raw_messages[ rm_index ].toString() } );
							} else {
								var appended = false;

								if ( validation_messages.length > 0 ) {
									for ( var vm_index = 0, vm_length = validation_messages.length; vm_index < vm_length; vm_index++ ) {
										if ( validation_messages[ vm_index ].content === raw_messages[ rm_index ] ) {
											var sources_new = validation_messages[ vm_index ].sources;
											sources_new.push( source );
											validation_messages[ vm_index ] = { sources: sources_new, content: raw_messages[ rm_index ] };
											appended = true;
											break;
										}
									}
								}

								if ( ! appended ) {
									validation_messages.push( { sources: [ source ], content: raw_messages[ rm_index ].toString() } );
								}
							}

						}
					}

					var messages = [];

					if ( validation_messages.length > 0 ) {
						for ( var vm_index_2 = 0, vm_length_2 = validation_messages.length; vm_index_2 < vm_length_2; vm_index_2++ ) {
							if ( validation_messages[ vm_index_2 ].sources === false ) {
								messages.push( validation_messages[ vm_index_2 ].content );
							} else {
								var sources = wc_cp_join( validation_messages[ vm_index_2 ].sources );
								messages.push( wc_composite_params.i18n_validation_issues_for.replace( '%c', sources ).replace( '%e', validation_messages[ vm_index_2 ].content ) );
							}
						}
					}

					// Pass through 'composite_validation_messages' filter - @see WC_CP_Filters_Manager class.
					messages = composite.filters.apply_filters( 'composite_validation_messages', [ messages ] );

					return messages;
				},

				/**
				 * True if the product is purchasable.
				 */
				is_purchasable: function() {

					if ( this.price_data.is_purchasable === 'no' ) {
						return false;
					}

					return true;
				},

				/**
				 * Get the composite quantity.
				 */
				get_quantity: function() {
					return composite.$composite_quantity.length > 0 ? parseInt( composite.$composite_quantity.val(), 10 ) : 1;
				},

				/**
				 * Updates model availability state.
				 */
				update_availability: function() {

					var stock_statuses = [],
						is_in_stock    = true;

					for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
						stock_statuses.push( components[ component_index ].step_validation_model.get( 'is_in_stock' ) );
					}

					is_in_stock = _.includes( stock_statuses, false ) ? false : true;

					composite.console_log( 'debug:models', '\nUpdating \'Composite_Data_Model\' availability... Attribute: "stock_statuses": ' + stock_statuses.toString() + ', Attribute: "is_in_stock": ' + is_in_stock.toString() );

					composite.debug_indent_incr();
					this.set( {
						stock_statuses: stock_statuses,
						is_in_stock:    is_in_stock,
					} );
					composite.debug_indent_decr();
				},

				/**
				 * Calculates and updates model subtotals.
				 */
				update_totals: function( component ) {

					var model = this;

					composite.console_log( 'debug:models', '\nUpdating \'Composite_Data_Model\' totals...' );

					composite.debug_indent_incr();

					if ( typeof( component ) === 'undefined' ) {

						for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
							model.update_component_prices( components[ component_index ] );
						}

						this.calculate_subtotals();

					} else {
						this.update_component_prices( component );
						this.calculate_subtotals( component );
					}

					composite.debug_indent_decr();
				},

				/**
				 * Calculates totals by applying tax ratios to raw prices.
				 */
				get_taxed_totals: function( price, regular_price, tax_ratios, qty ) {

					qty = typeof( qty ) === 'undefined' ? 1 : qty;

					var tax_ratio_incl = tax_ratios && typeof( tax_ratios.incl ) !== 'undefined' ? Number( tax_ratios.incl ) : false,
						tax_ratio_excl = tax_ratios && typeof( tax_ratios.excl ) !== 'undefined' ? Number( tax_ratios.excl ) : false,
						totals         = {
							price:          qty * price,
							regular_price:  qty * regular_price,
							price_incl_tax: qty * price,
							price_excl_tax: qty * price
						};

					if ( tax_ratio_incl && tax_ratio_excl ) {

						totals.price_incl_tax = wc_cp_number_round( totals.price * tax_ratio_incl );
						totals.price_excl_tax = wc_cp_number_round( totals.price * tax_ratio_excl );

						if ( wc_composite_params.tax_display_shop === 'incl' ) {
							totals.price         = totals.price_incl_tax;
							totals.regular_price = wc_cp_number_round( totals.regular_price * tax_ratio_incl );
						} else {
							totals.price         = totals.price_excl_tax;
							totals.regular_price = wc_cp_number_round( totals.regular_price * tax_ratio_excl );
						}
					}

					return totals;
				},

				/**
				 * Adds model subtotals and calculates model totals.
				 */
				calculate_totals: function( price_data_array ) {

					var model      = this,
						price_data = typeof( price_data_array ) === 'undefined' ? model.price_data : price_data_array;

					composite.console_log( 'debug:models', '\nAdding totals...' );

					var totals = {
						price:          wc_cp_number_round( price_data.base_price_totals.price ),
						regular_price:  wc_cp_number_round( price_data.base_price_totals.regular_price ),
						price_incl_tax: wc_cp_number_round( price_data.base_price_totals.price_incl_tax ),
						price_excl_tax: wc_cp_number_round( price_data.base_price_totals.price_excl_tax )
					};

					price_data.base_display_price = totals.price;

					for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {

						var component_totals = typeof( price_data_array ) === 'undefined' ? model.get( 'component_' + components[ component_index ].component_id + '_totals' ) : price_data_array[ 'component_' + components[ component_index ].component_id + '_totals' ];

						totals.price          += wc_cp_number_round( component_totals.price );
						totals.regular_price  += wc_cp_number_round( component_totals.regular_price );
						totals.price_incl_tax += wc_cp_number_round( component_totals.price_incl_tax );
						totals.price_excl_tax += wc_cp_number_round( component_totals.price_excl_tax );
					}

					// Pass through 'composite_totals' filter - @see WC_CP_Filters_Manager class.
					totals = composite.filters.apply_filters( 'composite_totals', [ totals ] );

					if ( typeof( price_data_array ) === 'undefined' ) {
						composite.debug_indent_incr();
						this.set( { totals: totals } );
						composite.debug_indent_decr();
					}

					return totals;
				},

				/**
				 * Calculates composite subtotals (component totals) and updates the component totals attributes on the model when the calculation is done on the client side.
				 * For components that require a server-side calculation of incl/excl tax totals, a request is prepared and submitted in order to get accurate values.
				 */
				calculate_subtotals: function( triggered_by, price_data_array, qty ) {

					var model      = this,
						price_data = typeof( price_data_array ) === 'undefined' ? model.price_data : price_data_array;

					qty          = typeof( qty ) === 'undefined' ? 1 : parseInt( qty, 10 );
					triggered_by = typeof( triggered_by ) === 'undefined' ? false : triggered_by;

					// Base.
					if ( false === triggered_by ) {

						var base_price            = Number( price_data.base_price ),
							base_regular_price    = Number( price_data.base_regular_price ),
							base_price_tax_ratios = price_data.base_price_tax_ratios;

						price_data.base_price_totals = this.get_taxed_totals( base_price, base_regular_price, base_price_tax_ratios, qty );
					}

					// Components.
					for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {

						if ( false !== triggered_by && triggered_by.component_id !== components[ component_index ].component_id ) {
							continue;
						}

						var component_qty = price_data.quantities[ components[ component_index ].component_id ] * qty,
							totals        = model.calculate_component_subtotals( components[ component_index ], price_data, component_qty );

						if ( typeof( price_data_array ) === 'undefined' ) {

							composite.console_log( 'debug:models', 'Updating \'Composite_Data_Model\' component totals... Attribute: "component_' + components[ component_index ].component_id + '_totals".' );

							composite.debug_indent_incr();
							model.set( 'component_' + components[ component_index ].component_id + '_totals', totals );
							composite.debug_indent_decr();

						} else {
							price_data[ 'component_' + components[ component_index ].component_id + '_totals' ] = totals;
						}
					}

					if ( typeof( price_data_array ) !== 'undefined' ) {
						return price_data;
					}
				},

				/**
				 * Calculates component subtotals on the client side.
				 */
				calculate_component_subtotals: function( component, price_data_array, qty ) {

					var model         = this,
						price_data    = typeof( price_data_array ) === 'undefined' ? model.price_data : price_data_array,
						product_id    = component.get_selected_product_type() === 'variable' ? component.get_selected_variation( false ) : component.get_selected_product( false ),
						tax_ratios    = price_data.price_tax_ratios[ component.component_id ],
						regular_price = price_data.regular_prices[ component.component_id ] + price_data.addons_regular_prices[ component.component_id ],
						price         = price_data.prices[ component.component_id ] + price_data.addons_prices[ component.component_id ],
						totals        = {
							price:          0.0,
							regular_price:  0.0,
							price_incl_tax: 0.0,
							price_excl_tax: 0.0
						};

					composite.console_log( 'debug:models', 'Calculating "' + component.get_title() + '" totals...' );

					if ( wc_composite_params.calc_taxes === 'yes' ) {

						if ( product_id > 0 && qty > 0 && ( price > 0 || regular_price > 0 ) ) {

							totals = model.get_taxed_totals( price, regular_price, tax_ratios, qty );
						}

					} else {

						totals.price          = qty * price;
						totals.regular_price  = qty * regular_price;
						totals.price_incl_tax = qty * price;
						totals.price_excl_tax = qty * price;
					}

					// Pass through 'component_totals' filter - @see WC_CP_Filters_Manager class.
					return composite.filters.apply_filters( 'component_totals', [ totals, component, qty ] );
				},

				/**
				 * Updates the 'price_data' model property with the latest component prices.
				 */
				update_component_prices: function( component ) {

					composite.console_log( 'debug:models', 'Fetching "' + component.get_title() + '" price data...' );

					var quantity = component.get_selected_quantity();

					// Copy prices.
					this.price_data.prices[ component.component_id ]           = component.component_selection_model.get_price();
					this.price_data.regular_prices[ component.component_id ]   = component.component_selection_model.get_regular_price();
					this.price_data.price_tax_ratios[ component.component_id ] = component.component_selection_model.get_tax_ratios();


					// Calculate addons price.
					this.price_data.addons_prices[ component.component_id ]         = Number( component.component_selection_model.get_addons_price() );
					this.price_data.addons_regular_prices[ component.component_id ] = Number( component.component_selection_model.get_addons_regular_price() );

					if ( quantity > 0 ) {
						this.price_data.quantities[ component.component_id ] = parseInt( quantity, 10 );
					} else {
						this.price_data.quantities[ component.component_id ] = 0;
					}
				},

				add_status_message: function( source, content ) {

					var messages = $.extend( true, [], this.get( 'status_messages' ) );

					messages.push( { message_source: source, message_content: content } );

					composite.console_log( 'debug:models', 'Adding "' + source + '" status message: "' + content + '"...' );

					this.set( { status_messages: messages } );
				},

				remove_status_message: function( source ) {

					composite.console_log( 'debug:models', 'Removing "' + source + '" status message...' );

					var messages = _.filter( this.get( 'status_messages' ), function( status_message ) { return status_message.message_source !== source; } );

					this.set( { status_messages: messages } );
				}

			} );

			var obj = new Model( opts );
			return obj;
		};



		/**
		 * Controls permission for access to a step.
		 */
		this.Step_Access_Model = function( step, opts ) {

			var self  = step;
			var Model = Backbone.Model.extend( {

				is_lockable: false,

				initialize: function() {

					var model  = this,
						params = {
						is_locked: false,
					};

					this.set( params );

					/*
					 * Permit lock state changes only if:
					 *
					 * - Layout !== 'Stacked'.
					 * - Layout !== 'Componentized', or 'composite_settings.sequential_componentized_progress' === 'yes'.
					 * - Layout === 'Componentized', 'composite_settings.sequential_componentized_progress' === 'yes' and this is not the Review step.
					 */
					this.is_lockable = composite.settings.layout !== 'single' && ( composite.settings.layout_variation !== 'componentized' || composite.settings.sequential_componentized_progress === 'yes' && false === self.is_review() );

					if ( this.is_lockable ) {

						for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {

							if ( steps[ index ].is_review() ) {
								continue;
							}

							if ( steps[ index ].step_index < self.step_index ) {
								// Update lock state when the validation state of a previous step changes.
								model.listenTo( steps[ index ].step_validation_model, 'change:passes_validation', model.update_lock_state );
								// Update lock state when the lock state of a previous step changes.
								model.listenTo( steps[ index ].step_access_model, 'change:is_locked', model.update_lock_state );
							}
						}
					}

					/**
					 * Lock state also changes according to own step visibility.
					 */
					this.listenTo( self.step_visibility_model, 'change:is_visible', this.update_lock_state );
				},

				update_lock_state: function() {

					var lock = false;

					if ( false === self.is_visible() ) {
						lock = true;
					} else if ( this.is_lockable ) {

						for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {

							if ( steps[ index ].step_index === self.step_index ) {
								break;
							}

							if ( false === steps[ index ].is_visible() ) {
								continue;
							}

							if ( steps[ index ].step_access_model.get( 'is_locked' ) ) {
								lock = true;
								break;
							}

							if ( false === steps[ index ].step_validation_model.get( 'passes_validation' ) ) {
								lock = true;
								break;
							}
						}
					}

					composite.console_log( 'debug:models', '\nUpdating \'Step_Access_Model\': "' + self.get_title() + '", Attribute: "is_locked": ' + lock.toString() );

					if ( this.get( 'is_locked' ) !== lock ) {
						composite.console_log( 'debug:models', 'Lock state changed.\n' );
					} else {
						composite.console_log( 'debug:models', 'Lock state unchanged.\n' );
					}

					composite.debug_indent_incr();
					this.set( { is_locked: lock } );
					composite.debug_indent_decr();
				}

			} );

			var obj = new Model( opts );
			return obj;
		};



		/**
		 * Validates the configuration state of a step.
		 */
		this.Step_Validation_Model = function( step, opts ) {

			var self  = step;
			var Model = Backbone.Model.extend( {

				initialize: function() {

					var params = {
						passes_validation:  true,
						is_in_stock:        true,
						component_messages: [],
						composite_messages: [],
					};

					this.set( params );

					/**
					 * Re-validate step when quantity is changed.
					 */
					composite.actions.add_action( 'component_quantity_changed', this.quantity_changed_handler, 10, this );

					/**
					 * Re-validate step when a new selection is made.
					 */
					composite.actions.add_action( 'component_selection_changed', this.selection_changed_handler, 20, this );

					/**
					 * Re-validate step when the contents of an existing selection change.
					 */
					composite.actions.add_action( 'component_selection_content_changed', this.selection_content_changed_handler, 20, this );
				},

				addons_changed_handler: function( step ) {

					if ( composite.is_initialized ) {

						if ( step.step_id !== self.step_id ) {
							return;
						}

						self.validate();
					}
				},

				quantity_changed_handler: function( step ) {

					if ( composite.is_initialized ) {

						if ( step.step_id !== self.step_id ) {
							return;
						}

						self.validate();
					}
				},

				selection_changed_handler: function() {

					if ( composite.is_initialized ) {

						// Do not change the validation state when a Variable Product is loading.
						if ( self.is_component() ) {
							if ( self.component_selection_model.has_pending_updates() ) {
								return;
							}
						}

						self.validate();
					}
				},

				selection_content_changed_handler: function( step ) {

					if ( composite.is_initialized ) {

						if ( step.step_id !== self.step_id ) {
							return;
						}

						self.validate();
					}
				},

				update: function( is_valid, is_in_stock ) {

					var params = {
						passes_validation:  is_valid,
						is_in_stock:        is_in_stock,
						component_messages: self.get_validation_messages( 'component' ),
						composite_messages: self.get_validation_messages( 'composite' )
					};

					composite.console_log( 'debug:models', '\nUpdating \'Step_Validation_Model\': "' + self.get_title() + '", Attribute: "passes_validation": ' + params.passes_validation.toString() + ', Attribute: "is_in_stock": ' + params.is_in_stock.toString() );

					if ( this.get( 'passes_validation' ) !== params.passes_validation ) {
						composite.console_log( 'debug:models', 'Validation state changed.\n' );
					} else {
						composite.console_log( 'debug:models', 'Validation state unchanged.\n' );
					}

					if ( ! _.isEqual( this.get( 'component_messages' ), params.component_messages ) ) {
						composite.console_log( 'debug:models', 'Validation message changed.\n' );
					} else {
						composite.console_log( 'debug:models', 'Validation message unchanged.\n' );
					}

					if ( this.get( 'is_in_stock' ) !== params.is_in_stock ) {
						composite.console_log( 'debug:models', 'Stock state changed.\n' );
					} else {
						composite.console_log( 'debug:models', 'Stock state unchanged.\n' );
					}

					composite.debug_indent_incr();
					this.set( params );
					composite.debug_indent_decr();
				}

			} );

			var obj = new Model( opts );
			return obj;
		};



		/**
		 * Controls the visibility of a Component.
		 */
		this.Step_Visibility_Model = function( step, opts ) {

			var self = step;

			/**
			 * Controls the visibility state of a component.
			 */
			var Model = Backbone.Model.extend( {

				recursing: false,

				initialize: function() {

					var params = {
						is_visible: true,
					};

					this.set( params );

					if ( self.is_component() ) {
						/**
						 * Update model state when the hidden components change.
						 */
						composite.actions.add_action( 'hidden_components_changed', this.update_visibility_state, 10, this );
					}
				},

				update_visibility_state: function() {

					var is_visible = true;

					composite.console_log( 'debug:models', '\nUpdating "' + self.get_title() + '" visibility...' );

					if ( composite.scenarios.is_component_hidden( self.step_id ) ) {
						is_visible = false;
					}

					composite.debug_indent_incr();

					composite.console_log( 'debug:models', '\nUpdating \'Step_Visibility_Model\': "' + self.get_title() + '", Attribute: "is_visible": ' + is_visible.toString() );

					if ( this.get( 'is_visible' ) !== is_visible ) {
						composite.console_log( 'debug:models', 'Visibility state changed.\n' );
					} else {
						composite.console_log( 'debug:models', 'Visibility state unchanged.\n' );
					}

					composite.debug_indent_incr();

					if ( ! this.recursing ) {
						this.recursing = true;
						this.set( { is_visible: is_visible } );
					} else {
						composite.console_log( 'error', '\nStep visibility model recursion detected. Stepping out...' );
					}

					this.recursing = false;

					composite.debug_indent_decr();

					composite.debug_indent_decr();
				}

			} );

			var obj = new Model( opts );
			return obj;
		};



		/**
		 * Sorting, filtering and pagination data and methods associated with the available component options.
		 */
		this.Component_Options_Model = function( component, opts ) {

			var self  = component;
			var Model = Backbone.Model.extend( {

				available_options_data: [],
				xhr: false,

				initialize: function() {

					this.available_options_data = self.find_options_data();

					var available_options = [];

					if ( this.available_options_data.length > 0 ) {
						available_options = _.pluck( this.available_options_data, 'option_id' );
					}

					var params = {
						filters: self.find_active_filters(),
						orderby: self.find_order_by(),
						page:    self.find_pagination_param( 'page' ),
						pages:   self.find_pagination_param( 'pages' ),

						/*
						 * Available component options, including the current selection, but excluding the empty '' option.
						 */
						available_options: available_options,

						/*
						 * Products and variations state in current view, including the empty '' option. The current selection is excluded if not in view.
						 */
						options_state: {
							active: _.pluck( _.where( this.available_options_data, { is_in_view: true } ), 'option_id' ),
							inactive: [],
							invalid: []
						},

						/**
						 * 'compat_group' and 'conditional_options' scenarios when the options state was calculated.
						 */
						options_in_scenarios: {
							compat_group: composite.scenarios.clean_masked_component_scenarios( composite.scenarios.get_scenarios_by_type( 'compat_group' ), self.component_id ),
							conditional_options: composite.scenarios.get_scenarios_by_type( 'conditional_options', self.component_id )
						}
					};

					this.set( params );

					/**
					 * Refresh options state when a selection changes.
					 */
					composite.actions.add_action( 'component_selection_changed', this.component_selection_changed_handler, 15, this );

					/**
					 * Ensure options state is refreshed when the available options of this component change.
					 */
					composite.actions.add_action( 'available_options_changed_' + self.step_id, this.available_options_changed_handler, 10, this );

					if ( composite.settings.layout !== 'single' ) {

						/*
						 * Reset invalid product/variation selections when transitioning to this step.
						 */
						composite.actions.add_action( 'active_step_changed_' + self.step_id, this.active_step_changed_handler, 10, this );
					}

				},

				active_step_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return;
					}

					this.refresh_options_state();
				},

				available_options_changed_handler: function() {

					this.refresh_options_state();
				},

				component_selection_changed_handler: function( step ) {

					// Only update options if change happened in preceding step.
					if ( composite.settings.layout && self.step_index < step.step_index ) {
						return false;
					}

					this.refresh_options_state();
				},

				reload_options_on_scenarios_change: function() {

					var reload = false;

					if ( self.hide_disabled_products() && self.has_options_style( 'thumbnails' ) ) {
						if ( self.get_max_results() > self.get_results_per_page() ) {
							if ( false === self.append_results() ) {
								reload = true;
							} else if ( _.pluck( _.where( this.available_options_data, { is_in_view: true } ), 'option_id' ).length < self.get_max_results() ) {
								reload = true;
							}
						}
					}

					return reload;
				},

				request_options: function( params, request_type ) {

					var model = this;

					// Page will be updated after data has been fetched.
					this.set( _.omit( params, 'page' ) );

					var data = {
						action:               'woocommerce_show_component_options',
						component_id:         self.component_id,
						composite_id:         composite.composite_id,
						load_page:            params.page ? params.page : 1,
						selected_option:      self.get_selected_product( false ),
						filters:              this.get( 'filters' ),
						orderby:              this.get( 'orderby' ),
						options_in_scenarios: this.reload_options_on_scenarios_change() ? this.get( 'options_in_scenarios' ) : [],
					};

					if ( this.xhr ) {
						this.xhr.abort();
					}

					// Get component options via ajax.
					this.xhr = $.post( composite.get_ajax_url( data.action ), data, function( response ) {

						// Trigger 'component_options_data_loaded' event.
						model.trigger( 'component_options_data_loaded', response, request_type );

						if ( 'success' === response.result ) {

							if ( 'reload' === request_type ) {

								// Update component options data.
								model.available_options_data = response.options_data;

								// Update component scenario data.
								composite.scenarios.set_scenario_data( response.scenario_data, self.component_id );
								composite.scenarios.set_conditional_options_scenario_data( response.conditional_options_data, self.component_id );

								// Update component pagination data.
								model.set( response.pagination_data );

								// Update available options.
								model.refresh_options( _.pluck( model.available_options_data, 'option_id' ) );

							} else if ( 'append' === request_type ) {

								// Merge existing with new component options data, after adding an 'is_appended' prop to the new data.
								model.available_options_data = _.union( _.where( model.available_options_data, { is_in_view: true } ), _.map( response.options_data, function( option_data ) { return _.extend( option_data, { is_appended: true } ); } ) );

								// Merge component scenario data.
								composite.scenarios.merge_scenario_data( response.scenario_data, self.component_id );
								composite.scenarios.merge_conditional_options_scenario_data( response.conditional_options_data, self.component_id );

								// Update component pagination data.
								model.set( response.pagination_data );

								// Update available options.
								model.refresh_options( _.pluck( model.available_options_data, 'option_id' ) );

								// Remove 'is_appended' prop from appended data.
								model.available_options_data = _.map( model.available_options_data, function( option_data ) { return _.omit( option_data, 'is_appended' ); } );
							}

						} else {
							window.alert( response.message );
						}

						// Run 'component_options_loaded' action - @see WC_CP_Actions_Dispatcher class reference.
						composite.actions.do_action( 'component_options_loaded', [ self ] );

					}, 'json' );
				},

				get_option_data: function( option_id, key ) {

					var data  = null,
						model = this;

					if ( option_id !== '' && model.available_options_data.length > 0 ) {
						for ( var index = 0, length = model.available_options_data.length; index < length; index++ ) {
							if ( parseInt( model.available_options_data[ index ].option_id, 10 ) === parseInt( option_id, 10 ) ) {
								if ( key ) {
									data = typeof( model.available_options_data[ index ][ key ] ) !== 'undefined' ? model.available_options_data[ index ][ key ] : null;
								} else {
									data = model.available_options_data[ index ];
								}
								break;
							}
						}
					}

					return data;
				},

				set_option_data: function( option_id, data, key ) {

					var model = this;

					if ( option_id !== '' && model.available_options_data.length > 0 ) {
						for ( var index = 0, length = model.available_options_data.length; index < length; index++ ) {
							if ( parseInt( option_id, 10 ) === parseInt( model.available_options_data[ index ].option_id, 10 ) ) {
								if ( key ) {
									model.available_options_data[ index ][ key ] = data;
								} else {
									model.available_options_data[ index ] = data;
								}
								break;
							}
						}
					}
				},

				intersection_exists: function( product_in_states, active_scenarios ) {

					var intersect = false;

					for ( var s = 0, s_max = product_in_states.length; s < s_max; s++ ) {

						if ( $.inArray( product_in_states[ s ], active_scenarios ) > -1 ) {
							intersect = true;
							break;
						}
					}

					return intersect;
				},

				refresh_options: function( options ) {

					composite.console_log( 'debug:models', '\nUpdating "' + self.get_title() + '" options: ' + _.map( options, function( num ) { return num === '' ? '0' : num; } ) );

					composite.debug_indent_incr();

					if ( _.isEqual( this.get( 'available_options' ), options ) ) {
						// Refresh options state if options have been refreshed but the new set is equal to the old: Edge case fix for when the 'is_in_view' property of an existing option changes in the new set.
						this.refresh_options_state();
					} else {
						this.set( { available_options: options } );
					}
					composite.debug_indent_decr();
				},

				refresh_options_state: function() {

					/*
					 * 1. Update active options.
					 */

					composite.console_log( 'debug:models', '\nUpdating "' + self.get_title() + '" options state...' );

					composite.debug_indent_incr();

					var active_cg_scenarios                = [],
						active_co_scenarios                = [],
						options_state                      = { active: [], inactive: [], invalid: [] },
						component_id                       = self.component_id,
						scenario_data                      = composite.scenarios.get_scenario_data().scenario_data,
						scenario_data_component            = scenario_data[ component_id ],
						conditional_options_data           = composite.scenarios.get_scenario_data().conditional_options_data,
						conditional_options_data_component = conditional_options_data[ component_id ],
						is_optional                        = false,
						invalid_product_found              = false,
						invalid_variation_found            = false;

					// Get active 'compat_group' scenarios up to this component.
					active_cg_scenarios = composite.scenarios.calculate_active_scenarios( 'compat_group', self, true, true );

					composite.console_log( 'debug:models', '\nReference scenarios: [' + active_cg_scenarios + ']' );
					composite.console_log( 'debug:models', 'Removing scenarios where the current component is masked...' );

					active_cg_scenarios = composite.scenarios.clean_masked_component_scenarios( active_cg_scenarios, component_id );

					// Enable all options if all active scenarios ignore this component.
					if ( active_cg_scenarios.length === 0 ) {
						active_cg_scenarios.push( '0' );
					}

					// Get active 'conditional_options' scenarios up to this component.
					active_co_scenarios = composite.scenarios.calculate_active_scenarios( 'conditional_options', self, true, true );

					composite.console_log( 'debug:models', '\nUpdating \'Component_Options_Model\': "' + self.get_title() + '", Attribute: "options_in_scenarios"...' );

					if ( ! _.isEqual( this.get( 'options_in_scenarios' ), { compat_group: active_cg_scenarios, conditional_options: active_co_scenarios } ) ) {
						composite.console_log( 'debug:models', '\nActive options scenarios changed.\n' );
					} else {
						composite.console_log( 'debug:models', '\nActive options scenarios unchanged.\n' );
					}

					composite.debug_indent_incr();
					this.set( {
						options_in_scenarios: {
							compat_group: active_cg_scenarios,
							conditional_options: active_co_scenarios
						}
					} );
					composite.debug_indent_decr();

					/*
					 * Set component 'optional' status by adding the '' product ID to the 'options_state' array.
					 */

					if ( self.maybe_is_optional() ) {

						if ( 0 in scenario_data_component ) {
							if ( this.intersection_exists( scenario_data_component[ 0 ], active_cg_scenarios ) ) {
								is_optional = true;
							}
						}

						if ( 0 in conditional_options_data_component ) {
							if ( this.intersection_exists( conditional_options_data_component[ 0 ], active_co_scenarios ) ) {
								is_optional = false;
							}
						}
					}

					if ( false === self.is_visible() ) {
						is_optional = true;
					}

					if ( is_optional ) {
						composite.console_log( 'debug:models', 'Component set as optional.' );
						options_state.active.push( '' );
					} else {
						options_state.inactive.push( '' );
					}

					/*
					 * Add compatible products to the 'options_state' array.
					 */
					for ( var index = 0, length = this.available_options_data.length; index < length; index++ ) {

						var option_data                 = this.available_options_data[ index ],
							product_id                  = option_data.option_id,
							product_in_states           = ( product_id in scenario_data_component ) ? scenario_data_component[ product_id ] : [],
							product_hidden_in_scenarios = ( product_id in conditional_options_data_component ) ? conditional_options_data_component[ product_id ] : [],
							is_compatible               = false;

						composite.console_log( 'debug:models', 'Updating selection #' + product_id + ':' );
						composite.console_log( 'debug:models', '	Selection in states: [' + product_in_states + ']' );
						composite.console_log( 'debug:models', '	Selection hidden by scenarios: [' + product_hidden_in_scenarios + ']' );

						if ( this.intersection_exists( product_in_states, active_cg_scenarios ) ) {
							is_compatible = true;
						}

						if ( this.intersection_exists( product_hidden_in_scenarios, active_co_scenarios ) ) {
							is_compatible = false;
						}

						if ( is_compatible ) {

							composite.console_log( 'debug:models', '	Selection enabled.' );

							if ( option_data.is_in_view ) {
								options_state.active.push( product_id );
							}

						} else {

							composite.console_log( 'debug:models', '	Selection disabled.' );

							if ( option_data.is_in_view ) {
								options_state.inactive.push( product_id );
							}

							if ( self.get_selected_product( false ) === product_id ) {

								invalid_product_found = true;

								if ( invalid_product_found ) {
									composite.console_log( 'debug:models', '	--- Selection invalid.' );
								}
							}
						}
					}

					/*
					 * Disable incompatible variations.
					 */

					if ( self.get_selected_product_type() === 'variable' ) {

						var chosen_variation = self.get_selected_variation(),
							variation_data   = self.component_selection_model.get_available_variations_data(),
							variation_id,
							variation_in_states,
							variation_hidden_in_scenarios,
							is_variation_compatible;

						composite.console_log( 'debug:models', '	Checking variations...' );

						if ( chosen_variation > 0 ) {
							composite.console_log( 'debug:models', '		--- Stored variation is #' + chosen_variation );
						}

						/*
						 * Update model.
						 */
						for ( var i = 0, i_max = variation_data.length; i < i_max; i++ ) {

							variation_id                  = variation_data[ i ].variation_id.toString();
							is_variation_compatible       = false;
							variation_in_states           = ( variation_id in scenario_data_component ) ? scenario_data_component[ variation_id ] : [];
							variation_hidden_in_scenarios = ( variation_id in conditional_options_data_component ) ? conditional_options_data_component[ variation_id ] : [];

							composite.console_log( 'debug:models', '		Checking variation #' + variation_id + ':' );
							composite.console_log( 'debug:models', '		Selection in states: [' + variation_in_states + ']' );
							composite.console_log( 'debug:models', '		Selection hidden by scenarios: [' + variation_hidden_in_scenarios + ']' );

							if ( this.intersection_exists( variation_in_states, active_cg_scenarios ) ) {
								is_variation_compatible = true;
							}

							if ( this.intersection_exists( variation_hidden_in_scenarios, active_co_scenarios ) ) {
								is_variation_compatible = false;
							}

							if ( is_variation_compatible ) {

								composite.console_log( 'debug:models', '		Variation enabled.' );

								options_state.active.push( variation_id );

							} else {

								composite.console_log( 'debug:models', '		Variation disabled.' );

								options_state.inactive.push( variation_id.toString() );

								if ( self.get_selected_variation( false ).toString() === variation_id ) {

									invalid_variation_found = true;

									if ( invalid_variation_found ) {

										composite.console_log( 'debug:models', '		--- Selection invalid.' );

										options_state.invalid.push( variation_id.toString() );
									}
								}
							}
						}
					}

					composite.console_log( 'debug:models', 'Done.\n' );

					composite.debug_indent_decr();

					var maybe_update_model = true;

					/*
					 * 2. Check selections.
					 */

					if ( composite.filters.apply_filters( 'reset_invalid_selections', [ false, self ] ) ) {

						composite.console_log( 'debug:models', '\nChecking current "' + self.get_title() + '" selections:' );

						if ( invalid_product_found ) {

							if ( self.is_static() ) {

								composite.console_log( 'debug:models', '\nProduct selection invalid - moving on (static component)...\n\n' );

							} else {

								composite.console_log( 'debug:models', '\nProduct selection invalid - resetting...\n\n' );

								maybe_update_model = false;

								composite.debug_indent_incr();

								self.component_selection_view.resetting_product = true;
								self.component_selection_view.set_option( '' );
								self.component_selection_view.resetting_product = false;

								composite.debug_indent_decr();
							}

						} else if ( invalid_variation_found ) {

							maybe_update_model = false;

							composite.console_log( 'debug:models', '\nVariation selection invalid - resetting...\n\n' );

							composite.debug_indent_incr();

							self.component_selection_view.resetting_variation = true;
							self.$component_summary_content.find( '.reset_variations' ).trigger( 'click' );
							self.component_selection_view.resetting_variation = false;

							composite.debug_indent_decr();

						} else  {
							composite.console_log( 'debug:models', '...looking good!' );
						}
					}

					/*
					 * 3. Update model.
					 */

					if ( maybe_update_model ) {

						if ( ! _.isEqual( this.get( 'options_state' ), options_state ) ) {
							composite.console_log( 'debug:models', '\nOptions state changed.\n' );
						} else {
							composite.console_log( 'debug:models', '\nOptions state unchanged.\n' );
						}

						// Set active options in view.
						composite.debug_indent_incr();
						this.set( { options_state: options_state } );
						composite.debug_indent_decr();
					}
				}

			} );

			var obj = new Model( opts );
			return obj;
		};



		/**
		 * Data and methods associated with the current selection.
		 */
		this.Component_Selection_Model = function( component, opts ) {

			var self  = component;
			var Model = Backbone.Model.extend( {

				selected_product_data: false,

				initialize: function() {

					var selected_product      = '',
						selected_product_data = false;

					if ( self.component_options_model.available_options_data.length > 0 ) {
						for ( var index = 0, length = self.component_options_model.available_options_data.length; index < length; index++ ) {

							var option_data = self.component_options_model.available_options_data[ index ];

							if ( option_data.is_selected ) {
								selected_product      = option_data.option_id;
								selected_product_data = $.extend( true, {}, option_data.option_product_data );
								break;
							}
						}
					}

					var params = {
						selected_product:        selected_product,
						selected_variation:      selected_product && 'variable' === selected_product_data.product_type ? selected_product_data.variation_id : '',
						selected_variation_data: false,
						selected_quantity:       0,
						selected_addons:         false,
						// NYP identified by price.
						selected_nyp:            false,
					};

					this.selected_product_data = selected_product_data;

					this.set( params );

					/**
					 * Update WC variations model if scenarios changed.
					 */
					composite.actions.add_action( 'component_options_state_changed_' + self.step_id, this.update_active_variations_data, 0, this );
				},

				get_product_data: function() {
					return this.selected_product_data;
				},

				get_product_image_data: function() {
					return this.selected_product_data.image_data || false;
				},

				get_variation_data: function() {
					return this.get( 'selected_variation_data' );
				},

				get_variation_image_data: function() {

					var variation_id = this.get( 'selected_variation' ),
						variations   = this.get_available_variations_data(),
						vi_data      = false;

					if ( variation_id > 0 && variations ) {
						for ( var index = 0, length = variations.length; index < length; index++ ) {

							var variation = variations[ index ];

							if ( parseInt( variation.variation_id, 10 ) === parseInt( variation_id, 10 ) ) {
								if ( variation.image ) {
									vi_data = {
										image_src:    variation.image.src,
										image_srcset: variation.image.srcset,
										image_sizes:  variation.image.sizes,
										image_title:  variation.image.title
									};
								} else if ( variation.image_src ) {
									vi_data = {
										image_src:    variation.image_src,
										image_srcset: variation.image_srcset,
										image_sizes:  variation.image_sizes,
										image_title:  variation.image_title
									};
								}
								break;
							}
						}
					}

					return vi_data;
				},

				get_meta_data: function() {

					var variation_id = this.get( 'selected_variation' ),
						meta         = [];

					if ( variation_id > 0 ) {
						meta = this.get_variation_data().meta_data || [];
					}

					return meta;
				},

				get_available_variations_data: function() {
					return this.selected_product_data.variations_data || [];
				},

				get_active_variations_data: function() {
					return this.selected_product_data.active_variations_data || [];
				},

				get_type: function() {
					return this.selected_product_data.product_type || 'none';
				},

				get_price: function() {
					return this.selected_product_data.price ? Number( this.selected_product_data.price ) : 0.0;
				},

				set_price: function( value ) {
					this.selected_product_data.price = value;
				},

				get_regular_price: function() {
					return this.selected_product_data.regular_price ? Number( this.selected_product_data.regular_price ) : 0.0;
				},

				set_regular_price: function( value ) {
					this.selected_product_data.regular_price = value;
				},

				get_addons_price: function() {
					return this.selected_product_data.addons_price ? Number( this.selected_product_data.addons_price ) : 0.0;
				},

				get_addons_regular_price: function() {
					return this.selected_product_data.addons_regular_price ? Number( this.selected_product_data.addons_regular_price ) : 0.0;
				},

				set_addons_price: function( value ) {
					this.selected_product_data.addons_price = value;
				},

				set_addons_regular_price: function( value ) {
					this.selected_product_data.addons_regular_price = value;
				},

				get_tax_ratios: function() {
					return this.selected_product_data.tax_ratios || false;
				},

				set_tax_ratios: function( value ) {
					this.selected_product_data.tax_ratios = value;
				},

				get_details_html: function() {
					return this.selected_product_data.details_html || '';
				},

				get_stock_status: function() {
					return this.selected_product_data.stock_status || '';
				},

				set_stock_status: function( value ) {
					this.selected_product_data.stock_status = value;
				},

				load_selection_data: function( product_id, update_selection ) {

					update_selection = update_selection || false;

					var model = this,
						data  = {
							action:        'woocommerce_show_composited_product',
							product_id:    product_id,
							component_id:  self.component_id,
							composite_id:  composite.composite_id
						};

					// Get component selection details via ajax.
					$.ajax( {

						type:     'POST',
						url:      composite.get_ajax_url( data.action ),
						data:     data,
						timeout:  15000,
						dataType: 'json',

						success: function( response ) {

							// Cache result.
							if ( 'success' === response.result ) {

								composite.console_log( 'debug:models', '\nFetched \'Component_Selection_Model\' data: "' + self.get_title() + '", Product ID: #' + ( product_id === '' ? '0' : product_id ) );

								self.component_options_model.set_option_data( product_id, response.product_data, 'option_product_data' );

								model.trigger( 'selected_product_data_loaded', product_id, response.product_data );

								if ( update_selection ) {
									model.update_selected_product( product_id, response.product_data );
								}

							} else {

								model.trigger( 'selected_product_data_load_error', product_id );
							}

						},

						error: function() {

							model.trigger( 'selected_product_data_load_error', product_id );
						}

					} );
				},

				update_selection: function( product_id ) {

					if ( '' === product_id ) {
						this.update_selected_product( '', wc_composite_params.empty_product_data );
						return;
					}

					var product_data = self.component_options_model.get_option_data( product_id, 'option_product_data' );

					if ( product_data ) {

						this.update_selected_product( product_id, product_data );

					} else {

						composite.console_log( 'debug:models', '\nFetching \'Component_Selection_Model\' data: "' + self.get_title() + '", Product ID: #' + ( product_id === '' ? '0' : product_id ) );

						this.load_selection_data( product_id, true );
					}
				},

				update_selected_product: function( selected_product, selected_product_data ) {

					this.selected_product_data = $.extend( true, {}, selected_product_data );

					if ( this.get( 'selected_product' ) !== selected_product ) {

						var selected_quantity = 0;

						if ( selected_product ) {
							selected_quantity = 'yes' === composite.settings.component_qty_restore ? this.get( 'selected_quantity' ) : 1;
						}

						composite.console_log( 'debug:models', '\nUpdating \'Component_Selection_Model\': "' + self.get_title() + '", Attribute: "selected_product": #' + ( selected_product === '' ? '0' : selected_product ) );

						composite.debug_indent_incr();
						this.set( {
							selected_product:        selected_product,
							selected_variation:      '',
							selected_variation_data: false,
							selected_quantity:       selected_quantity,
							selected_addons:         false,
							selected_nyp:            false
						} );
						composite.debug_indent_decr();

						this.trigger( 'selected_product_updated' );
					}
				},

				update_selected_variation: function( selected_variation, selected_variation_data ) {

					if ( this.get( 'selected_variation' ) !== selected_variation || ! _.isEqual( this.get( 'selected_variation_data' ), selected_variation_data ) ) {

						composite.console_log( 'debug:models', '\nUpdating \'Component_Selection_Model\': "' + self.get_title() + '", Attribute: "selected_variation": #' + ( selected_variation === '' ? '0' : selected_variation ) );

						composite.debug_indent_incr();
						this.set( {
							selected_variation:      selected_variation,
							selected_variation_data: selected_variation_data
						} );
						composite.debug_indent_decr();

						this.trigger( 'selected_variation_updated' );
					}
				},

				update_selected_quantity: function( selected_qty ) {

					if ( this.get( 'selected_quantity' ) !== selected_qty ) {

						composite.console_log( 'debug:models', '\nUpdating \'Component_Selection_Model\': "' + self.get_title() + '", Attribute: "selected_quantity": ' + selected_qty );

						composite.debug_indent_incr();
						this.set( { selected_quantity: selected_qty } );
						composite.debug_indent_decr();
					}
				},

				update_selected_addons: function( selected_addons_data, selected_addons_price, selected_addons_regular_price ) {

					selected_addons_price         = selected_addons_price || 0;
					selected_addons_regular_price = selected_addons_regular_price || selected_addons_price;

					this.set_addons_price( selected_addons_price );
					this.set_addons_regular_price( selected_addons_regular_price );

					var serialized_addons_data = JSON.stringify( selected_addons_data );

					if ( ! _.isEqual( this.get( 'selected_addons' ), serialized_addons_data ) ) {

						composite.console_log( 'debug:models', '\nUpdating \'Component_Selection_Model\': "' + self.get_title() + '", Attribute: "selected_addons"...' );

						composite.debug_indent_incr();
						this.set( { selected_addons: serialized_addons_data } );
						composite.debug_indent_decr();
					}
				},

				update_nyp: function( nyp_price ) {

					if ( this.get( 'selected_nyp' ) !== nyp_price ) {

						composite.console_log( 'debug:models', '\nUpdating \'Component_Selection_Model\': "' + self.get_title() + '", Attribute: "nyp_price": ' + nyp_price );

						this.set_price( nyp_price );
						this.set_regular_price( nyp_price );

						composite.debug_indent_incr();
						this.set( { selected_nyp: nyp_price } );
						composite.debug_indent_decr();
					}
				},

				update_active_variations_data: function() {

					if ( 'variable' === this.get_type() ) {

						composite.console_log( 'debug:models', '\nUpdating "' + self.get_title() + '" variations data...' );

						/*
						 * Update WC variations model.
						 */
						var selected_variation     = self.get_selected_variation( false ),
							options_state          = self.component_options_model.get( 'options_state' ),
							variation_data         = this.get_available_variations_data(),
							active_variations_data = [],
							variation_id,
							is_compatible,
							variation;

						for ( var i = 0, i_max = variation_data.length; i < i_max; i++ ) {

							variation_id  = variation_data[ i ].variation_id.toString();
							is_compatible = _.includes( options_state.active, variation_id );

							// Copy all variation objects but set the variation_is_active property to false in order to disable the attributes of incompatible variations.
							// Only if WC v2.3 and disabled variations are set to be visible.
							if ( false === self.hide_disabled_variations() ) {

								var variation_has_empty_attributes = false;

								variation = $.extend( true, {}, variation_data[ i ] );

								if ( ! is_compatible ) {

									if ( parseInt( selected_variation, 10 ) === parseInt( variation_id, 10 ) ) {
										// This prop has no effect other than to make sure that 'Component_Options_View::render' will update the attribute dropdowns when we choose a different variation.
										variation.variation_is_valid = false;
									} else {
										variation.variation_is_active = false;
									}

									// Do not include incompatible variations with empty attributes - they can break stuff when prioritized.
									for ( var attribute_name in variation.attributes ) {

										if ( ! variation.attributes.hasOwnProperty( attribute_name ) ) {
											continue;
										}

										if ( variation.attributes[ attribute_name ] === '' ) {
											variation_has_empty_attributes = true;
											break;
										}
									}
								}

								if ( ! variation_has_empty_attributes ) {
									active_variations_data.push( variation );
								}

							// Copy only compatible variations.
							// Only if disabled variations are set to be hidden.
							} else {
								if ( is_compatible ) {
									active_variations_data.push( variation_data[ i ] );
								} else {
									if ( parseInt( selected_variation, 10 ) === parseInt( variation_id, 10 ) ) {
										variation                    = $.extend( true, {}, variation_data[ i ] );
										// This prop has no effect other than to make sure that 'Component_Options_View::render' will update the attribute dropdowns when we choose a different variation.
										variation.variation_is_valid = false;
										active_variations_data.push( variation );
									}
								}
							}
						}

						// Save compatible variations.
						this.selected_product_data.active_variations_data = active_variations_data;
					}
				},

				/**
				 * True if the model has more state updates lined up.
				 */
				has_pending_updates: function() {

					if ( 'variable' === this.get_type() && false === this.get_variation_data() ) {
						return true;
					}

					return false;
				}

			} );

			var obj = new Model( opts );
			return obj;
		};

	};



	/**
	 * View classes instantiated in a CP app lifecycle.
	 */
	wc_cp_classes.WC_CP_Views = function( composite ) {


		/**
		 * Controls viewport auto-scrolling.
		 */
		this.Composite_Viewport_Scroller = function( opts ) {

			var View = Backbone.View.extend( {

				scroll_viewport_target: false,
				summary_element_scroll_location: false,
				is_scroll_anchored: null,

				initialize: function() {

					var view = this;

					setTimeout( function() {
						view.is_scroll_anchoring_supported();
					}, 100 );

					if ( 'single' === composite.settings.layout ) {

						// Viewport auto-scrolling on the 'show_step' action.
						composite.actions.add_action( 'show_step', this.autoscroll_single, 10, this );

					} else if ( 'paged' === composite.settings.layout ) {

						// Viewport auto-scrolling on the 'active_step_changed' action.
						composite.actions.add_action( 'active_step_changed', this.autoscroll_paged, 120, this );

						// Viewport auto-scrolling on the 'active_step_transition_end' action.
						composite.actions.add_action( 'active_step_transition_end', this.autoscroll_paged_relocated, 10, this );

						if ( 'componentized' === composite.settings.layout_variation ) {
							// Record last known position when transitioning from a summary element.
							composite.actions.add_action( 'active_step_transition', this.save_summary_element_scroll_location, 10, this );
						}

					} else if ( 'progressive' === composite.settings.layout ) {

						// Viewport auto-scrolling on the 'active_step_transition_end' hook.
						composite.actions.add_action( 'active_step_transition_end', this.autoscroll_progressive, 10, this );
					}

					// Viewport auto-scrolling on the 'component_options_update_requested' hook.
					composite.actions.add_action( 'component_options_update_requested', this.component_options_update_requested, 10, this );

					// Viewport auto-scrolling on the 'component_selection_details_updated' hook.
					composite.actions.add_action( 'component_selection_details_updated', this.selection_details_updated, 10, this );

					// Viewport auto-scrolling on the 'component_selection_details_animated' hook.
					composite.actions.add_action( 'component_selection_details_animated', this.selection_details_animated, 10, this );

					// Viewport auto-scrolling on the 'component_selection_details_relocation_ended' hook.
					composite.actions.add_action( 'component_selection_details_relocation_ended', this.selection_details_relocation_ended, 10, this );
				},

				reset_summary_element_scroll_location: function() {
					this.summary_element_scroll_location = false;
				},

				// Record last know position when clicking a summary element.
				save_summary_element_scroll_location: function( step ) {

					if ( ! step.is_component() || ! composite.is_finalized ) {
						return;
					}

					// Save position of summary element on the first transition away from the review step.
					if ( false === this.summary_element_scroll_location ) {

						var $summary_element = composite.composite_summary_view.get_summary_element( step.component_id );

						if ( $summary_element ) {
							this.summary_element_scroll_location = this.get_scroll_location( $summary_element, { viewport_only: false } );
						}
					}
				},

				// Viewport auto-scrolling on the 'component_options_update_requested' hook.
				component_options_update_requested: function( component, request_params, request_type, is_background_request ) {

					if ( 'reload' === request_type && false === is_background_request ) {
						composite.composite_viewport_scroller.scroll_viewport( component.$component_pagination.filter( '.top' ), {
							offset:   50,
							duration: 200,
							partial:  true,
							queue:    false,
							on_complete: function() {
								if ( composite.is_finalized && 'yes' === wc_composite_params.accessible_focus_enabled ) {
									component.$component_pagination.filter( '.top' ).find( '.woocommerce-result-count' ).trigger( 'focus' );
								}
							}
						} );
					}
				},

				// Viewport auto-scrolling on the 'component_selection_details_relocation_ended' hook.
				selection_details_relocation_ended: function( component ) {

					if ( component.component_selection_view.flushing_component_options ) {
						setTimeout( function() {
							component.$component_content.slideDown( 250 );
							// Scroll to component options.
							composite.composite_viewport_scroller.scroll_viewport( 'relative', {
								offset:   -component.$component_summary.outerHeight( true ),
								timeout:  0,
								duration: 250,
								queue:    false
							} );
						}, 200 );
					}
				},

				/**
				 * Viewport auto-scrolling on the 'component_selection_details_updated' hook.
				 */
				selection_details_updated: function( component ) {
					if ( composite.is_finalized && ! component.can_autotransition() && component.is_current() ) {
						this.autoscroll_selection_details( component, 'updated' );
					}
				},

				/**
				 * Viewport auto-scrolling on the 'component_selection_details_animated' hook.
				 */
				selection_details_animated: function( component ) {
					if ( ! component.can_autotransition() ) {
						this.autoscroll_selection_details( component, 'animated' );
					}
				},

				autoscroll_selection_details: function( component, action ) {

					var view    = component.component_selection_view,
						partial = true,
						$target = component.$component_content_scroll_target;

					if ( '' === component.get_selected_product( false ) ) {
						return;
					}

					if ( view.is_relocated() ) {
						if ( 'animated' === action ) {
							$target = view.$relocation_target;
							partial = false;
						} else {
							return;
						}
					} else if ( 'animated' === action ) {
						return;
					}

					if ( composite.is_initialized ) {
						composite.composite_viewport_scroller.scroll_viewport( $target, {
							timeout:           50,
							duration:          250,
							queue:             false,
							partial:           partial,
							scroll_method:     'quarter',
							always_on_complete: true,
							on_complete: function() {

								// Focus and announce change.
								if ( component.has_options_style( 'thumbnails' ) && 'yes' === wc_composite_params.accessible_focus_enabled ) {
									component.$component_summary_content.find( '.composited_product_title' ).trigger( 'focus' );
								}
							}
						} );
					}
				},

				/**
				 * Single layout auto-scrolling behaviour on the 'show_step' hook - single layout.
				 */
				autoscroll_single: function( step ) {

					var do_scroll = composite.is_initialized;

					// Scroll to the desired section.
					if ( do_scroll ) {
						composite.composite_viewport_scroller.scroll_viewport( step.$el, {
							partial:  false,
							duration: 250,
							queue:    false
						} );
					}
				},

				/**
				 * Paged layout auto-scrolling behaviour on the 'show_step' hook.
				 */
				autoscroll_paged: function( step ) {

					if ( ! composite.is_initialized ) {
						return;
					}

					var offset    = false,
					    scroll_to = false;

					// Animating into a component?
					if ( step.is_component() ) {

						// If relocated, auto-scroll is handled by 'autoscroll_paged_relocated'.
						if ( ! step.component_selection_view.is_relocated() ) {
							scroll_to = 'helper';
						}

					// Animating into the summary?
					} else {

						offset = this.summary_element_scroll_location;

						this.reset_summary_element_scroll_location();

						// Scroll to a summary element?
						if ( false !== offset ) {
							scroll_to = 'summary_element';
						} else {
							scroll_to = 'helper';
						}
					}

					if ( 'helper' === scroll_to ) {

						composite.composite_viewport_scroller.scroll_viewport( composite.$composite_transition_helper, {
							timeout:  0,
							partial:  false,
							duration: composite.$composite_form.hasClass( 'small_width' ) && 'componentized' !== composite.settings.layout_variation ? 500 : 240,
							queue:    false
						} );

					} else if ( 'summary_element' === scroll_to ) {

						composite.composite_viewport_scroller.scroll_viewport( 'absolute', {
							timeout:  0,
							partial:  false,
							duration: composite.$composite_form.hasClass( 'small_width' ) && 'componentized' !== composite.settings.layout_variation ? 500 : 240,
							queue:    false,
							offset:   offset
						} );
					}
				},

				/**
				 * Paged layout auto-scrolling behaviour on the 'active_step_transition_end' hook - relocated content.
				 */
				autoscroll_paged_relocated: function( step ) {

					var do_scroll    = composite.is_initialized,
						is_component = step.is_component(),
						component    = is_component ? step : false;

					if ( is_component && component.component_selection_view.is_relocated() ) {
						if ( do_scroll ) {
							composite.composite_viewport_scroller.scroll_viewport( component.$component_content, {
								timeout:       0,
								partial:       false,
								duration:      250,
								queue:         false,
								scroll_method: 'middle'
							} );
						}
					}
				},

				/**
				 * Prog layout auto-scrolling behaviour on the 'active_step_transition_end' hook.
				 */
				autoscroll_progressive: function( step ) {

					var do_scroll = composite.is_initialized;

					// Scroll.
					if ( do_scroll && step.$el.hasClass( 'autoscrolled' ) ) {
						if ( ! step.$step_title.wc_cp_is_in_viewport( false ) ) {
							composite.composite_viewport_scroller.scroll_viewport( step.$el, {
								timeout:  0,
								partial:  false,
								duration: 250,
								queue:    false
							} );
						}
					}
				},

				/**
				 * Calculates the scroll height, given a target element.
				 */
				get_scroll_location: function( target, params ) {

					var scroll_to     = false,
					    $w            = $wc_cp_window,
					    partial       = ( typeof params.partial === 'undefined' ) ? true : params.partial,
					    offset        = ( typeof params.offset === 'undefined' ) ? 50 : params.offset,
					    scroll_method = ( typeof params.scroll_method === 'undefined' ) ? false : params.scroll_method,
					    viewport_only = ( typeof params.viewport_only === 'undefined' ) ? true : params.viewport_only;

					// Scroll viewport by an offset.
					if ( target === 'relative' ) {

						scroll_to = $w.scrollTop() - offset;

					// Scroll viewport to absolute document position.
					} else if ( target === 'absolute' ) {

						scroll_to = offset;

					// Scroll to target element.
					} else if ( target.length > 0 && target.is( ':visible' ) && ( false === viewport_only || false === target.wc_cp_is_in_viewport( partial ) ) ) {

						var window_offset = offset;

						if ( scroll_method === 'bottom' || target.hasClass( 'scroll_bottom' ) ) {
							window_offset = $w.height() - target.outerHeight( true ) - offset;
						} else if ( scroll_method === 'middle' ) {
							window_offset = $w.height() / 3 * 2 - target.outerHeight( true ) - offset;
						} else if ( scroll_method === 'quarter' ) {
							window_offset = $w.height() / 4 + offset;
						} else {
							window_offset = parseInt( wc_composite_params.scroll_viewport_top_offset, 10 ) + offset;
						}

						scroll_to = target.offset().top - window_offset;

						// Ensure element top is in viewport.
						if ( target.offset().top < scroll_to ) {
							scroll_to = target.offset().top;
						}
					}

					return scroll_to;
				},

				/**
				 * Scrolls the viewport.
				 */
				scroll_viewport: function( target, params ) {

					var anim_complete,
					    scroll_to,
					    timeout         = typeof( params.timeout ) === 'undefined' ? 5 : params.timeout,
					    anim_duration   = typeof( params.duration ) === 'undefined' ? 250 : params.duration,
					    anim_queue      = typeof( params.queue ) === 'undefined' ? false : params.queue,
					    always_complete = typeof( params.always_on_complete ) === 'undefined' ? false : params.always_on_complete,
					    $w              = $wc_cp_window,
					    $d              = $wc_cp_document,
					    view            = this;

					if ( typeof( params.on_complete ) === 'undefined' || params.on_complete === false ) {
						anim_complete = function() {
							return false;
						};
					} else {
						anim_complete = params.on_complete;
					}

					var scroll_viewport = function() {

						scroll_to = view.get_scroll_location( target, params );

						if ( scroll_to ) {

							// Prevent out-of-bounds scrolling.
							if ( scroll_to > $d.height() - $w.height() ) {
								scroll_to = $d.height() - $w.height() - 100;
							}

							/*
							 * Avoid scrolling both html and body.
							 * Some browsers can scroll both, some can't. Let's figure out which one works and cache it.
							 */
							if ( ! this.scroll_viewport_target ) {

								var pos = $wc_cp_html.scrollTop();

								this.scroll_viewport_target = $wc_cp_body;

								if ( ! pos ) {
									$wc_cp_html.scrollTop( $wc_cp_html.scrollTop() + 1 );
								} else {
									$wc_cp_html.scrollTop( $wc_cp_html.scrollTop() - 1 );
								}

								if ( pos != $wc_cp_html.scrollTop() ) {
									this.scroll_viewport_target = $wc_cp_html;
								}
							}

							composite.console_log( 'debug:animations', '\nStarting viewport auto-scrolling...' );

							this.scroll_viewport_target.animate( { scrollTop: scroll_to }, {
								duration: anim_duration,
								queue:    anim_queue,
								complete: function() {
									composite.console_log( 'debug:animations', '\nEnded viewport auto-scrolling.' );
								},
								always:   anim_complete
							} );

						} else {
							if ( always_complete ) {
								anim_complete();
							}
						}
					};

					if ( timeout > 0 ) {
						setTimeout( function() {
							scroll_viewport();
						}, timeout );
					} else {
						scroll_viewport();
					}
				},

				/**
				 * Tests if the browser supports scroll anchoring.
				 */
				is_scroll_anchoring_supported: function() {

					if ( null === this.is_scroll_anchored ) {

						var scroll_pos = $wc_cp_window.scrollTop(),
						    $test_div  = $( '<div style="height:5px;"></div>' );

						// Go down a bit.
						window.scroll( 0, scroll_pos + 10 );
						// Add something above the viewport.
						$wc_cp_body.prepend( $test_div );
						// Did the scroll position change?
						this.is_scroll_anchored = $wc_cp_window.scrollTop() !== scroll_pos + 10;
						// Clean up.
						$test_div.remove();
						window.scroll( 0, scroll_pos );
					}

					return this.is_scroll_anchored;
				},

				/**
				 * Hides/shows an element that is above the viewport while keeping the visible viewport area unchanged.
				 */
				illusion_scroll: function( args ) {

					if ( typeof( args ) === 'undefined' || typeof( args[ 'target' ] ) === 'undefined' || ( ! args[ 'target' ] ) ) {
						return null;
					}

					var view          = this,
					    $el           = args[ 'target' ],
					    scroll_pos    = $wc_cp_window.scrollTop(),
					    type          = typeof( args[ 'type' ] ) !== 'undefined' ? args[ 'type' ] : 'hide',
					    do_it         = true,
					    scroll_to     = 0.0,
					    scroll_offset = 0.0;

					if ( 'hide' === type ) {
						if ( 'none' === $el.css( 'display' ) ) {
							return null;
						}
					} else {
						if ( 'none' !== $el.css( 'display' ) ) {
							return null;
						}
					}

					if ( 'hide' !== type ) {
						$el.css( {
							height:  '0',
							display: 'block'
						} );
					}

					do_it = $el.offset().top < scroll_pos && false === $el.wc_cp_is_in_viewport( true );

					if ( do_it ) {

						if ( 'hide' !== type ) {
							$el.css( {
								height:   '',
								position: 'absolute',
							} );
						}

						scroll_offset = $el.get( 0 ).getBoundingClientRect().height;

						if ( typeof scroll_offset === 'undefined' ) {
							scroll_offset = $el.outerHeight();
						}

						do_it = scroll_offset >= 1;
					}

					if ( 'hide' !== type ) {
						$el.css( {
							height:   '',
							position: '',
							display:  'none'
						} );
					}

					if ( do_it ) {

						scroll_to = 'hide' === type ? scroll_pos - Math.round( scroll_offset ) : scroll_pos + Math.round( scroll_offset );

						// Introduce async to hopefully do this between repaints and avoid flicker.
						setTimeout( function() {

							// Scroll as much as the height offset...
							if ( ! view.is_scroll_anchoring_supported() ) {
								window.scroll( 0, scroll_to );
							}

							if ( 'hide' === type ) {
								// ...while hiding the element.
								$el.hide();
							} else {
								// ...while showing the element.
								$el.show();
							}

						}, 10 );
					}

					return do_it;
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View that handles the display of simple status messages.
		 */
		this.Composite_Status_View = function( opts ) {

			var View = Backbone.View.extend( {

				is_active: false,
				template: false,

				worker: false,

				$el_content: false,

				initialize: function( options ) {

					var view = this;

					this.template    = wp.template( 'wc_cp_composite_status' );
					this.$el_content = options.$el_content;

					/**
					 * Update the view when its model state changes.
					 */
					this.listenTo( this.model, 'change:status_messages', this.status_changed );

					var Worker = function() {

						var worker = this;

						this.timer = false;
						this.tasks = [];

						this.last_added_task = [];

						this.is_idle = function() {
							return this.timer === false;
						};

						this.work = function() {
							if ( worker.tasks.length > 0 ) {
								var task = worker.tasks.shift();
								view.render( task );
								worker.timer = setTimeout( function() { worker.work(); }, 400 );
							} else {
								clearTimeout( worker.timer );
								worker.timer = false;
							}
						};

						this.add_task = function( messages ) {

							var task = [];

							// Message added...
							if ( _.pluck( _.where( this.last_added_task, { is_old: false } ), 'message_content' ).length < messages.length ) {
								task = _.map( messages, function( message ) { return { message_content: message, is_old: false }; } );
							// Message removed...
							} else {
								task = _.map( _.where( this.last_added_task, { is_old: false } ), function( data ) { return { message_content: data.message_content, is_old: false === _.includes( messages, data.message_content ) }; } );
							}

							this.last_added_task = task;
							this.tasks.push( task );

							if ( _.where( task, { is_old: true } ).length === task.length ) {
								this.tasks.push( [] );
							}
						};

					};

					this.worker = new Worker();
				},

				/**
				 * Renders the status box.
				 */
				render: function( messages ) {

					var view = this;

					if ( messages.length === 0 ) {

						composite.console_log( 'debug:views', '\nHiding composite status view...' );

						this.$el.removeClass( 'visible' );

						setTimeout( function() {
							view.$el.removeClass( 'active' );
						}, 200 );

						this.is_active = false;

					} else {

						composite.console_log( 'debug:views', '\nUpdating composite status view...' );

						this.$el_content.html( this.template( messages ) );

						if ( false === this.is_active ) {

							this.$el.addClass( 'active' );

							setTimeout( function() {
								view.$el.addClass( 'visible' );
							}, 5 );

							this.is_active = true;

						} else {
							setTimeout( function() {
								view.$el.find( '.message:not(.current)' ).addClass( 'old' );
							}, 100 );
						}
					}
				},

				status_changed: function() {

					var	messages = this.model.get( 'status_messages' );

					if ( messages.length > 0 ) {
						this.worker.add_task( _.pluck( messages, 'message_content' ) );
					} else {
						this.worker.add_task( [] );
					}

					if ( this.worker.is_idle() ) {
						this.worker.work();
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};




		/**
		 * Handles the display of composite validation messages.
		 */
		this.Composite_Validation_View = function( opts ) {

			var View = Backbone.View.extend( {

				render_timer: false,
				is_in_widget: false,
				template:     false,

				initialize: function( options ) {

					this.template     = wp.template( 'wc_cp_validation_message' );
					this.is_in_widget = options.is_in_widget;

					/**
					 * Update the view when the validation messages change.
					 */
					composite.actions.add_action( 'composite_validation_message_changed', this.render, 100, this );
				},

				render: function() {

					var view  = this,
						model = this.model;

					composite.console_log( 'debug:views', '\nScheduled update of composite validation view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '.' );

					clearTimeout( view.render_timer );
					view.render_timer = setTimeout( function() {
						view.render_task( model );
					}, 10 );
				},

				render_task: function( model ) {

					composite.console_log( 'debug:views', '\nUpdating composite validation view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					var messages = model.get( 'validation_messages' );

					if ( messages.length > 0 ) {

						this.$el.html( this.template( messages ) );
						this.$el.removeClass( 'inactive' );

						if ( this.is_in_widget ) {
							this.$el.show();
						} else {
							this.$el.slideDown( 200 );
						}

					} else {

						this.$el.addClass( 'inactive' );

						if ( this.is_in_widget ) {
							this.$el.hide();
						} else {
							this.$el.slideUp( 200 );
						}
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};




		/**
		 * View associated with the price template.
		 */
		this.Composite_Price_View = function( opts ) {

			var View = Backbone.View.extend( {

				render_timer: false,
				is_in_widget: false,

				suffix: '',

				suffix_contains_price_incl: false,
				suffix_contains_price_excl: false,

				$addons_totals:       false,
				show_addons_totals:   false,

				initialize: function( options ) {

					this.is_in_widget = options.is_in_widget;

					// Add-ons support.
					if ( ! this.is_in_widget && 'yes' === wc_composite_params.is_pao_installed ) {

						this.$addons_totals = composite.$composite_data.find( '#product-addons-total' );

						if ( this.has_addons() ) {

							// Totals visible?
							if ( 1 == this.$addons_totals.data( 'show-sub-total' ) ) {

								// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
								this.$addons_totals.data( 'show-sub-total', 0 );
								this.$el.after( this.$addons_totals );
								this.show_addons_totals = true;

								/**
								 * Update addons grand totals with correct prices without triggering an ajax call.
								 */
								composite.$composite_data.on( 'updated_addons', { view: this }, this.updated_addons_handler );
							}

						} else {
							this.$addons_totals = false;
						}
					}

					// Suffix.
					if ( wc_composite_params.price_display_suffix !== '' ) {

						this.suffix = ' <small class="woocommerce-price-suffix">' + wc_composite_params.price_display_suffix + '</small>';

						this.suffix_contains_price_incl = wc_composite_params.price_display_suffix.indexOf( '{price_including_tax}' ) > -1;
						this.suffix_contains_price_excl = wc_composite_params.price_display_suffix.indexOf( '{price_excluding_tax}' ) > -1;
					}

					/**
					 * Update the view when the composite totals change.
					 */
					composite.actions.add_action( 'composite_totals_changed', this.render, 100, this );

					/**
					 * Update the view when the validation status changes.
					 */
					composite.actions.add_action( 'composite_validation_status_changed', this.render, 100, this );
				},

				/**
				 * True if the view includes addons.
				 */
				has_addons: function() {
					return 'yes' === wc_composite_params.is_pao_installed && this.$addons_totals && this.$addons_totals.length > 0;
				},

				/**
				 * Populate prices used by the addons script and re-trigger a 'woocommerce-product-addons-update' event.
				 */
				updated_addons_handler: function( event ) {

					var view = event.data.view;

					composite.actions.do_action( 'composite_totals_changed' );

					event.stopPropagation();
				},

				get_addons_raw_price: function() {

					var addons_raw_price = 0;

					if ( ! this.has_addons() ) {
						return addons_raw_price;
					}

					var addons     = this.$addons_totals.data( 'price_data' ),
						tax_ratios = composite.data_model.price_data.base_price_tax_ratios;

					if ( addons ) {

						for ( var index = 0, length = addons.length; index < length; index++ ) {

							var addon = addons[ index ];

							if ( addon.is_custom_price ) {

								var tax_ratio_incl = tax_ratios && typeof( tax_ratios.incl ) !== 'undefined' ? Number( tax_ratios.incl ) : false,
									tax_ratio_excl = tax_ratios && typeof( tax_ratios.excl ) !== 'undefined' ? Number( tax_ratios.excl ) : false;

								if ( 'incl' === wc_composite_params.tax_display_shop && 'no' === wc_composite_params.prices_include_tax ) {
									addons_raw_price += addon.cost_raw / ( tax_ratio_incl ? tax_ratio_incl : 1 );
								} else if ( 'excl' === wc_composite_params.tax_display_shop && 'yes' === wc_composite_params.prices_include_tax ) {
									addons_raw_price += addon.cost_raw / ( tax_ratio_excl ? tax_ratio_excl : 1 );
								} else {
									addons_raw_price += addon.cost_raw;
								}

							} else {

								if ( 'quantity_based' === addon.price_type ) {
									addons_raw_price += addon.cost_raw_pu;
								} else if ( 'flat_fee' === addon.price_type ) {
									addons_raw_price += addon.cost_raw;
								} else if ( 'percentage_based' === addon.price_type ) {
									addons_raw_price += addon.cost_raw_pct * composite.data_model.price_data.base_price;
								}
							}
						}
					}

					return addons_raw_price;

				},

				get_price_html: function( price_data_array ) {

					var model            = this.model,
						price_data       = typeof( price_data_array ) === 'undefined' ? model.price_data : price_data_array,
						composite_totals = typeof( price_data_array ) === 'undefined' ? model.get( 'totals' ) : price_data_array[ 'totals' ],
						total_string     = wc_composite_params.i18n_total ? '<span class="total">' + wc_composite_params.i18n_total + '</span>' : '',
						price_html       = '';

					if ( this.has_addons() ) {

						price_data = $.extend( true, {}, price_data );

						var addons_raw_price         = price_data.addons_price ? price_data.addons_price : this.get_addons_raw_price(),
							addons_raw_regular_price = price_data.addons_regular_price ? price_data.addons_regular_price : addons_raw_price;

						// Recalculate price html with add-ons price embedded in base price.
						if ( addons_raw_price || addons_raw_regular_price ) {

							if ( addons_raw_price > 0 ) {
								price_data.base_price = Number( price_data.base_price ) + Number( addons_raw_price );
							}

							if ( addons_raw_regular_price > 0 ) {
								price_data.base_regular_price = Number( price_data.base_regular_price ) + Number( addons_raw_regular_price );
							}

							price_data       = model.calculate_subtotals( false, price_data, 1 );
							composite_totals = model.calculate_totals( price_data );
						}
					}

					if ( composite_totals.price === 0.0 && price_data.show_free_string === 'yes' ) {

						price_html = wc_composite_params.i18n_price_format.replace( '%t', total_string ).replace( '%p', wc_composite_params.i18n_free ).replace( '%s', '' );

					} else {

						var formatted_price         = wc_cp_price_format( composite_totals.price ),
							formatted_regular_price = wc_cp_price_format( composite_totals.regular_price ),
							formatted_suffix        = this.get_formatted_price_suffix( composite_totals );

						if ( composite_totals.regular_price > composite_totals.price ) {
							formatted_price = wc_composite_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_price ).replace( '%t', formatted_price );
						}

						price_html = wc_composite_params.i18n_price_format.replace( '%t', total_string ).replace( '%p', formatted_price ).replace( '%s', formatted_suffix );
					}

					price_html = '<p class="price">' + price_html + '</p>';

					return composite.filters.apply_filters( 'composite_price_html', [ price_html, this, price_data_array ] );
				},

				/**
				 * Replace totals in price suffix.
				 */
				get_formatted_price_suffix: function( totals ) {

					var model  = this.model,
						suffix = this.suffix;

					totals = typeof( totals ) === 'undefined' ? model.get( 'totals' ) : totals;

					if ( '' !== suffix ) {

						if ( this.suffix_contains_price_incl ) {
							suffix = suffix.replace( '{price_including_tax}', wc_cp_price_format( totals.price_incl_tax ) );
						}

						if ( this.suffix_contains_price_excl ) {
							suffix = suffix.replace( '{price_excluding_tax}', wc_cp_price_format( totals.price_excl_tax ) );
						}
					}

					return suffix;
				},

				render: function() {

					var view  = this,
						model = this.model;

					composite.console_log( 'debug:views', '\nScheduled update of composite price view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '.' );

					clearTimeout( view.render_timer );
					view.render_timer = setTimeout( function() {
						view.render_task( model );
					}, 10 );
				},

				render_task: function( model ) {

					var show_price = ( model.get( 'passes_validation' ) || 'no' === composite.settings.hide_total_on_validation_fail ) && ( model.price_data.total !== model.price_data.base_display_price || 'yes' === model.price_data.has_price_range );

					composite.console_log( 'debug:views', '\nUpdating composite price view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					if ( show_price ) {

						var price_html = this.get_price_html();

						this.$el.html( price_html );
						this.$el.removeClass( 'inactive' );

						if ( this.is_in_widget ) {
							this.$el.show();
						} else {
							this.$el.slideDown( 200 );
						}

					} else {

						this.$el.addClass( 'inactive' );

						if ( this.is_in_widget ) {
							this.$el.hide();
						} else {
							this.$el.slideUp( 200 );
						}
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};




		/**
		 * View associated with the availability status.
		 */
		this.Composite_Availability_View = function( opts ) {

			var View = Backbone.View.extend( {

				$composite_stock_status: false,
				is_in_widget:            false,
				render_timer:            false,

				initialize: function( options ) {

					this.is_in_widget = options.is_in_widget;

					// Save composite stock status.
					if ( composite.$composite_data.find( '.composite_wrap p.stock' ).length > 0 ) {
						this.$composite_stock_status = composite.$composite_data.find( '.composite_wrap p.stock' ).clone();
					}

					/**
					 * Update the view when the stock statuses change.
					 */
					composite.actions.add_action( 'composite_availability_message_changed', this.render, 100, this );
				},

				render: function() {

					var view  = this,
						model = this.model;

					composite.console_log( 'debug:views', '\nScheduled update of composite availability view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '.' );

					clearTimeout( view.render_timer );
					view.render_timer = setTimeout( function() {
						view.render_task( model );
					}, 10 );
				},

				render_task: function( model ) {

					composite.console_log( 'debug:views', '\nUpdating composite availability view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					/*
					 * Update composite availability string.
					 */
					var insufficient_stock_components_string = this.get_insufficient_stock_components_string();

					if ( insufficient_stock_components_string ) {

						this.$el.html( insufficient_stock_components_string );

						if ( this.is_in_widget ) {
							this.$el.show();
						} else {
							this.$el.slideDown( 200 );
						}

					} else {
						if ( false !== this.$composite_stock_status ) {

							this.$el.html( this.$composite_stock_status );

							if ( this.is_in_widget ) {
								this.$el.show();
							} else {
								this.$el.slideDown( 200 );
							}

						} else {

							if ( this.is_in_widget ) {
								this.$el.hide();
							} else {
								this.$el.slideUp( 200 );
							}
						}
					}
				},

				get_insufficient_stock_components: function() {

					var data = [];

					for ( var index = 0, components = composite.get_components(), length = components.length; index < length; index++ ) {
						if ( ! components[ index ].step_validation_model.get( 'is_in_stock' ) ) {
							data.push( components[ index ].component_id );
						}
					}

					return data;
				},

				get_insufficient_stock_components_string: function() {

					var insufficient_stock_components = this.get_insufficient_stock_components(),
						composite_out_of_stock_string = '';

					if ( insufficient_stock_components.length > 0 ) {
						composite_out_of_stock_string = wc_composite_params.i18n_insufficient_stock.replace( '%s', wc_cp_join( _.map( insufficient_stock_components, function( component_id ) { return composite.api.get_step_title( component_id ); } ) ) );
					}

					return composite_out_of_stock_string;
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with the composite add-to-cart button.
		 */
		this.Composite_Add_To_Cart_Button_View = function( opts ) {

			var View = Backbone.View.extend( {

				render_timer: false,
				is_in_widget: false,
				$el_button:   false,
				$el_qty:      false,

				widget_qty_synced: false,

				initialize: function( options ) {

					var model = this.model;

					this.is_in_widget = options.is_in_widget;
					this.$el_button   = options.$el_button;
					this.$el_qty      = this.$el.find( '.quantity input.qty' );

					/**
					 * Update the view when the validation messages change, or when the stock status of the composite changes.
					 */
					composite.actions.add_action( 'composite_availability_status_changed', this.render, 100, this );
					composite.actions.add_action( 'composite_validation_status_changed', this.render, 100, this );

					/*
					 * Events for non-widgetized view.
					 */
					if ( ! this.is_in_widget ) {

						/**
						 * Preserve hash part of url when submiting the form using Microsoft Edge.
						 */
						if ( window.navigator.userAgent.indexOf( 'Edge' ) > -1 ) {
							composite.$composite_form.on( 'submit', function() {
								composite.$composite_form.attr( 'action', window.location.href );
							} );
						}

						/**
						 * Button click event handler:
						 *
						 * - Check if any issues exist.
						 * - Activate all fields for posting.
						 * - Set invisible selections to empty.
						 */
						this.$el_button.on( 'click', function( event ) {

							if ( model.get( 'passes_validation' ) && model.get( 'is_in_stock' ) ) {

								for ( var step_index = 0, steps = composite.get_steps(), length = steps.length; step_index < length; step_index++ ) {

									var step = steps[ step_index ];

									step.$el.find( 'select, input' ).each( function() {
										$( this ).prop( 'disabled', false );
									} );

									if ( step.is_component() ) {

										var has_addons = step.has_addons();

										if ( ! has_addons ) {

											if ( 'bundle' === step.get_selected_product_type() ) {

												var bundle = step.get_bundle_script( step.component_id );

												if ( bundle ) {

													$.each( bundle.bundled_items, function( index, bundled_item ) {

														if ( bundled_item.has_addons() ) {
															has_addons = true;
															return false;
														}

													} );
												}
											}
										}

										if ( has_addons ) {
											step.$component_summary_content.find( '.wc-pao-required-addon [required]' ).prop( 'required', false );
										}

										if ( false === step.step_visibility_model.get( 'is_visible' ) ) {
											step.$component_summary_content.append( '<input name="wccp_component_selection_nil[' + step.step_id + ']" value="1"/>' );
										}
									}
								}

							} else {
								event.preventDefault();
								window.alert( wc_composite_params.i18n_validation_issues );
							}
						} );
					}

					/*
					 * Events for widgetized view.
					 */
					if ( this.is_in_widget ) {
						/**
						 * Button click event handler: Trigger click in non-widgetized view, located within form.
						 */
						this.$el_button.on( 'click', function() {
							composite.composite_add_to_cart_button_view.$el_button.trigger( 'click' );
						} );

						if ( this.$el_qty.length > 0 ) {

							/**
							 * Copy changed quantity quantity into non-widgetized view.
							 */
							this.$el_qty.on( 'change', { view: this }, function( event ) {

								var view = event.data.view;

								if ( ! view.widget_qty_synced ) {
									composite.console_log( 'debug:views', '\nCopying widget #' + view.is_in_widget + ' quantity value into composite add-to-cart quantity field...' );
									view.widget_qty_synced = true;
									composite.composite_add_to_cart_button_view.$el_qty.val( view.$el_qty.val() ).trigger( 'change' );
									view.widget_qty_synced = false;
								}
							} );

							/**
							 * Copy changed composite quantity into view.
							 */
							composite.composite_add_to_cart_button_view.$el_qty.on( 'change', { view: this }, function( event ) {

								var view = event.data.view;

								composite.console_log( 'debug:views', '\nCopying composite add-to-cart quantity value into widget #' + view.is_in_widget + ' quantity field...' );
								view.$el_qty.val( composite.composite_add_to_cart_button_view.$el_qty.val() ).trigger( 'change' );
							} );
						}
					}
				},

				render: function() {

					var view  = this,
						model = this.model;

					composite.console_log( 'debug:views', '\nScheduled update of composite add-to-cart button view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '.' );

					clearTimeout( view.render_timer );
					view.render_timer = setTimeout( function() {
						view.render_task( model );
					}, 10 );
				},

				render_task: function( model ) {

					composite.console_log( 'debug:views', '\nUpdating composite add-to-cart button view' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					if ( model.get( 'validation_messages' ).length === 0 && model.get( 'is_in_stock' ) ) {

						if ( composite.settings.button_behaviour === 'new' || this.is_in_widget ) {
							this.$el_button.removeClass( 'disabled' );
						} else {
							this.$el.slideDown( 200 );
						}

					} else {
						if ( composite.settings.button_behaviour === 'new' || this.is_in_widget ) {
							this.$el_button.addClass( 'disabled' );
						} else {
							this.$el.slideUp( 200 );
						}
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with the composite pagination template.
		 */
		this.Composite_Pagination_View = function( opts ) {

			var View = Backbone.View.extend( {

				template: false,
				template_html: '',

				initialize: function() {

					this.template = wp.template( 'wc_cp_composite_pagination' );

					/**
					 * Update view when access to a step changes.
					 */
					composite.actions.add_action( 'step_access_changed', this.step_access_changed_handler, 100, this );

					/**
					 * Update outer element classes when the visibility of a step changes.
					 */
					composite.actions.add_action( 'step_visibility_changed', this.step_visibility_changed_handler, 100, this );

					/**
					 * Update view elements on transitioning to a new step.
					 */
					composite.actions.add_action( 'active_step_changed', this.active_step_changed_handler, 100, this );

					/**
					 * On clicking a composite pagination link.
					 */
					this.$el.on( 'click', '.pagination_element a', this.clicked_pagination_element );
				},

				step_visibility_changed_handler: function() {

					this.render();
				},

				step_access_changed_handler: function() {

					this.render();
				},

				active_step_changed_handler: function() {

					this.render();
				},

				/**
				 * Pagination element clicked.
				 */
				clicked_pagination_element: function() {

					$( this ).blur();

					if ( composite.has_transition_lock ) {
						return false;
					}

					if ( $( this ).hasClass( 'inactive' ) ) {
						return false;
					}

					var step_id = $( this ).closest( '.pagination_element' ).data( 'item_id' ),
						step    = composite.get_step( step_id );

					if ( step ) {
						composite.navigate_to_step( step );
					}

					return false;
				},

				/**
				 * Renders all elements state (active/inactive).
				 */
				render: function() {

					var data = [];

					if ( ! composite.is_initialized ) {
						return false;
					}

					composite.console_log( 'debug:views', '\nRendering pagination view elements...' );

					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {

						var step = steps[ index ];

						if ( step.is_visible() ) {

							var item_data = {
								element_id:          step.step_id,
								element_link:        step.get_route(),
								element_title:       step.get_title(),
								element_class:       '',
								element_state_class: ''
							};

							if ( step.is_current() ) {
								item_data.element_state_class = 'inactive';
								item_data.element_class       = 'pagination_element_current';
							} else if ( step.is_locked() ) {
								item_data.element_state_class = 'inactive';
							}

							data.push( item_data );
						}

					}

					// Pass through 'composite_pagination_view_data' filter - @see WC_CP_Filters_Manager class.
					data = composite.filters.apply_filters( 'composite_pagination_view_data', [ data ] );

					var new_template_html = this.template( data );

					if ( new_template_html !== this.template_html ) {
						this.template_html = new_template_html;
						this.$el.html( new_template_html );
					} else {
						composite.console_log( 'debug:views', '...skipped!' );
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with the composite summary template.
		 */
		this.Composite_Summary_View = function( opts ) {

			var View = Backbone.View.extend( {

				update_content_timers: {},
				update_height_timers:  {},
				view_elements:         {},
				is_in_widget:          false,
				template:              false,

				$carousel_wrapper:     false,
				$carousel_buttons:     false,
				$carousel_button_prev: false,
				$carousel_button_next: false,

				carousel_offset:       0,

				initialize: function( options ) {

					var view = this;

					this.template     = wp.template( 'wc_cp_summary_element_content' );
					this.is_in_widget = options.is_in_widget;

					this.$carousel_wrapper = this.$el.parent();

					for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {

						/**
						 * Update a single summary view element content when its validation state changes.
						 */
						steps[ step_index ].step_validation_model.on( 'change:passes_validation', ( function( i ) {
							return function() {
								view.render_element_content( steps[ i ] );
							};
						} ( step_index ) ) );
					}

					for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {

						view.view_elements[ components[ component_index ].component_id ] = {

							$summary_element:         view.$el.find( '.summary_element_' + components[ component_index ].component_id ),
							$summary_element_wrapper: view.$el.find( '.summary_element_' + components[ component_index ].component_id + ' .summary_element_wrapper' ),
							$summary_element_inner:   view.$el.find( '.summary_element_' + components[ component_index ].component_id + ' .summary_element_wrapper_inner' ),

							template_html: '',
							load_height:   0
						};
					}

					/**
					 * Update view when access to a step changes.
					 */
					composite.actions.add_action( 'step_access_changed', this.step_access_changed_handler, 100, this );

					/**
					 * Update outer element classes when the visibility of a step changes.
					 */
					composite.actions.add_action( 'step_visibility_changed', this.step_visibility_changed_handler, 100, this );

					/**
					 * Update a single summary view element content when its quantity changes.
					 */
					composite.actions.add_action( 'component_quantity_changed', this.quantity_changed_handler, 100, this );

					/**
					 * Update a single summary view element content when a new selection is made.
					 */
					composite.actions.add_action( 'component_selection_changed', this.selection_changed_handler, 100, this );

					/**
					 * Update a single summary view element content when the contents of an existing selection change.
					 */
					composite.actions.add_action( 'component_selection_content_changed', this.selection_changed_handler, 100, this );

					/**
					 * Update a single summary view element price when its totals change.
					 */
					composite.actions.add_action( 'component_totals_changed', this.component_totals_changed_handler, 100, this );

					/**
					 * Update all summary view elements on transitioning to a new step.
					 */
					if ( composite.settings.layout !== 'single' ) {
						composite.actions.add_action( 'active_step_transition_start', this.active_step_changed_handler, 100, this );
					}

					/**
					 * On clicking a summary link.
					 */
					this.$el.on( 'click', '.summary_element_select', this.clicked_summary_element );


					if ( this.is_in_widget && this.get_columns() > 1 ) {

						this.$carousel_buttons     = this.$el.closest( '.widget_composite_summary_details_wrapper' ).find( '.summary_carousel_button' );
						this.$carousel_button_prev = this.$carousel_buttons.first();
						this.$carousel_button_next = this.$carousel_buttons.last();

						/**
						 * On clicking a carousel link.
						 */
						this.$carousel_button_prev.on( 'click', { view: this }, this.clicked_carousel_button_prev );
						this.$carousel_button_next.on( 'click', { view: this }, this.clicked_carousel_button_next );

						/**
						 * Move carousel viewport on transitioning to a new step.
						 */
						if ( 'yes' === composite.settings.summary_carousel_autoscroll ) {
							composite.actions.add_action( 'active_step_transition_end', this.sync_carousel_pos, 100, this );
						}

						/**
						 * Recalculate carousel width on resize.
						 */
						$wc_cp_window.resize( function() {

							if ( ! composite.is_initialized ) {
								return false;
							}

							var summary_element_columns = view.get_columns(),
								carousel_viewport_width = view.get_carousel_viewport_width(),
								carousel_elements_count = view.get_summary_element_indexes().length,
								carousel_width          = 0;

							carousel_width = carousel_elements_count > summary_element_columns ? carousel_viewport_width * carousel_elements_count / summary_element_columns : carousel_viewport_width;

							view.$el.css( { width: carousel_width } );
						} );
					}
				},

				step_access_changed_handler: function( step ) {

					this.render_element_state( step );
				},

				step_visibility_changed_handler: function( step ) {

					this.render_element_visibility( step );
					this.render_columns( step.step_index );
				},

				active_step_changed_handler: function() {

					this.render_state();
				},

				selection_changed_handler: function( step ) {

					this.render_element_content( step );
				},

				quantity_changed_handler: function( step ) {

					this.render_element_content( step );
				},

				component_totals_changed_handler: function( step ) {

					this.render_element_content( step );
				},

				/**
				 * Summary element clicked.
				 */
				clicked_summary_element: function() {

					if ( composite.has_transition_lock ) {
						return false;
					}

					if ( $( this ).hasClass( 'disabled' ) ) {
						return false;
					}

					var step_id = $( this ).closest( '.summary_element' ).data( 'item_id' ),
						step    = composite.get_step( step_id );

					if ( step === false ) {
						return false;
					}

					if ( ! step.is_current() || composite.settings.layout === 'single' ) {
						composite.navigate_to_step( step );
					}

					return false;
				},

				clicked_carousel_button_next: function( event ) {

					if ( $( this ).hasClass( 'inactive' ) ) {
						return;
					}

					event.data.view.scroll_carousel( 'incr' );
				},

				clicked_carousel_button_prev: function( event ) {

					if ( $( this ).hasClass( 'inactive' ) ) {
						return;
					}

					event.data.view.scroll_carousel( 'decr' );
				},

				get_columns: function() {

					return parseInt( this.$el.data( 'summary_columns' ), 10 );
				},

				get_carousel_viewport_width: function() {

					var width = null;

					if ( this.get_columns() > 1 && this.is_in_widget ) {

						width = this.$carousel_wrapper.get( 0 ).getBoundingClientRect().width;

						if ( typeof width === 'undefined' ) {
							width = this.$carousel_wrapper.width();
						}
					}

					return width;
				},

				get_summary_element: function( element_id ) {
					return this.view_elements[ element_id ] ? this.view_elements[ element_id ].$summary_element : false;
				},

				get_summary_element_indexes: function() {

					var elements = [];

					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
						if ( steps[ index ].is_component() && steps[ index ].is_visible() ) {
							elements.push( steps[ index ].step_index );
						}
					}

					return elements;
				},

				/**
				 * Returns a single element's price (scheduler).
				 */
				get_element_price_html: function( step ) {

					var price_data = composite.data_model.price_data,
						price_html = '';

					if ( step.is_component() && step.is_subtotal_visible() && step.passes_validation() ) {

						var component    = step,
							component_id = component.component_id,
							product_id   = component.get_selected_product_type() === 'variable' ? component.get_selected_variation( false ) : component.get_selected_product( false ),
							qty          = component.get_selected_quantity();

						// Update price.
						if ( product_id > 0 && qty > 0 ) {

							var component_totals = composite.data_model.get( 'component_' + component_id + '_totals' );

							if ( price_data.is_priced_individually[ component_id ] === 'no' && component_totals.price === 0.0 && component_totals.regular_price === 0.0 ) {

								price_html = '';

							} else {

								var formatted_price         = wc_cp_price_format( component_totals.price ),
									formatted_regular_price = wc_cp_price_format( component_totals.regular_price );

								if ( component_totals.regular_price > component_totals.price ) {
									formatted_price = wc_composite_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_price ).replace( '%t', formatted_price );
								}

								price_html = '<span class="price summary_element_content">' + formatted_price + '</span>';
							}
						}
					}

					return price_html;
				},

				sync_carousel_pos: function( step ) {

					if ( ! step.is_component() ) {
						return;
					}

					var carousel_columns          = this.get_columns(),
						carousel_elements_indexes = this.get_summary_element_indexes(),
						carousel_elements_count   = carousel_elements_indexes.length,
						carousel_viewport_width   = this.get_carousel_viewport_width(),
						carousel_offset           = this.carousel_offset,
						carousel_width            = carousel_elements_count > carousel_columns ? carousel_viewport_width * carousel_elements_count / carousel_columns : carousel_viewport_width,
						carousel_column_width     = 1 / carousel_elements_count * carousel_width,
						current_step_offset       = 0;

					// Find position of viewed step in carousel.
					for ( var index = 0, length = carousel_elements_indexes.length; index < length; index++ ) {

						var carousel_element_index = carousel_elements_indexes[ index ];

						if ( carousel_element_index === step.step_index ) {
							current_step_offset = index * carousel_column_width;
							break;
						}
					}

					// If further to the right, scroll the viewport until it's visible.
					if ( current_step_offset > carousel_viewport_width + carousel_offset - 1 ) {
						this.scroll_carousel( current_step_offset - ( carousel_columns - 1 ) * carousel_column_width );
					}

					// If further to the left, scroll the viewport to it.
					if ( current_step_offset < carousel_offset - 1 ) {
						this.scroll_carousel( current_step_offset );
					}
				},

				scroll_carousel: function( scroll_to ) {

					var $this = $( this );

					if ( $this.hasClass( 'inactive' ) ) {
						return;
					}

					if ( 'incr' !== scroll_to && 'decr' !== scroll_to ) {
						scroll_to = parseInt( scroll_to, 10 );
					}

					var carousel_columns        = this.get_columns(),
						carousel_elements_count = this.get_summary_element_indexes().length,
						carousel_viewport_width = this.get_carousel_viewport_width(),
						carousel_offset         = this.carousel_offset,
						carousel_width          = carousel_elements_count > carousel_columns ? carousel_viewport_width * carousel_elements_count / carousel_columns : carousel_viewport_width,
						carousel_column_width   = 1 / carousel_elements_count * carousel_width;

					if ( 'incr' === scroll_to ) {
						carousel_offset += Math.round( carousel_columns * composite.settings.summary_carousel_scroll_coeff ) * carousel_column_width;
					} else if ( 'decr' === scroll_to ) {
						carousel_offset -= Math.round( carousel_columns * composite.settings.summary_carousel_scroll_coeff ) * carousel_column_width;
					} else {
						carousel_offset = scroll_to;
					}

					// Viewport out of bounds?
					if ( carousel_offset + carousel_viewport_width - carousel_width > 1 ) {
						carousel_offset = carousel_width - carousel_viewport_width;
					}

					if ( carousel_offset < 1 ) {
						carousel_offset = 0;
					}

					// Viewport at first element?
					if ( carousel_offset < 1 ) {
						this.$carousel_button_prev.addClass( 'inactive' );
					} else {
						this.$carousel_button_prev.removeClass( 'inactive' );
					}

					// Viewport at last element?
					if ( carousel_offset + carousel_viewport_width + carousel_column_width - carousel_width > 1 ) {
						this.$carousel_button_next.addClass( 'inactive' );
					} else {
						this.$carousel_button_next.removeClass( 'inactive' );
					}

					this.carousel_offset = carousel_offset;

					this.$el.css( { transform: 'translateX(-' + carousel_offset + 'px)' } );
				},

				render_columns: function( after_index ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					var summary_element_loop    = 0,
						summary_element_columns = this.get_columns(),
						view                    = this;

					composite.console_log( 'debug:views', '\nUpdating summary view element columns...' );

					after_index = typeof( after_index ) === 'undefined' ? 0 : after_index;

					// Rendering a carousel?
					if ( summary_element_columns > 1 && view.is_in_widget ) {

						var carousel_width          = 0,
							carousel_column_width   = 0,
							carousel_elements_count = this.get_summary_element_indexes().length,
							carousel_viewport_width = this.get_carousel_viewport_width();

						after_index    = 0;
						carousel_width = carousel_elements_count > summary_element_columns ? carousel_viewport_width * carousel_elements_count / summary_element_columns : carousel_viewport_width;

						carousel_column_width = 1 / carousel_elements_count * carousel_width;

						if ( carousel_elements_count > summary_element_columns ) {
							view.$carousel_buttons.removeClass( 'disabled' );
						} else {
							view.$carousel_buttons.addClass( 'disabled' );
						}

						// Viewport out of bounds?
						if ( view.carousel_offset + carousel_viewport_width - carousel_width > 1 ) {
							view.carousel_offset = carousel_width - carousel_viewport_width;
						}

						// Viewport at first element?
						if ( view.carousel_offset < 1 ) {
							view.$carousel_button_prev.addClass( 'inactive' );
						} else {
							view.$carousel_button_prev.removeClass( 'inactive' );
						}

						// Viewport includes last element?
						if ( view.carousel_offset + carousel_viewport_width + carousel_column_width - carousel_width > 1 ) {
							view.$carousel_button_next.addClass( 'inactive' );
						} else {
							view.$carousel_button_next.removeClass( 'inactive' );
						}

						view.$el.css( { width: carousel_width, transform: 'translateX(-' + view.carousel_offset + 'px)' } );
					}

					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {

						var step = steps[ index ];

						if ( typeof view.view_elements[ step.step_id ] === 'undefined' ) {
							continue;
						}

						if ( step.step_index < after_index ) {
							if ( step.is_visible() ) {
								summary_element_loop++;
							}
							continue;
						}

						var summary_element_classes = '';

						if ( step.is_visible() ) {

							summary_element_loop++;

							if ( ( ( summary_element_loop - 1 ) % summary_element_columns ) == 0 || summary_element_columns == 1 ) {
								summary_element_classes += ' first';
							}

							if ( summary_element_loop % summary_element_columns == 0 ) {
								summary_element_classes += ' last';
							}
						}

						if ( summary_element_columns > 1 && view.is_in_widget ) {
							view.view_elements[ step.step_id ].$summary_element.removeClass( 'first last' );
						} else {
							view.view_elements[ step.step_id ].$summary_element.removeClass( 'first last' ).addClass( summary_element_classes );
						}
					}
				},

				/**
				 * Renders all elements visibility.
				 */
				render_visibility: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					var view = this;

					composite.console_log( 'debug:views', '\nRendering summary view element visibility' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );
					composite.debug_indent_incr();
					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
						view.render_element_visibility( steps[ index ] );
					}
					composite.debug_indent_decr();
				},

				/**
				 * Renders all elements state (active/inactive).
				 */
				render_state: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					var view = this;

					composite.console_log( 'debug:views', '\nRendering summary view element states' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );
					composite.debug_indent_incr();
					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
						view.render_element_state( steps[ index ] );
					}
					composite.debug_indent_decr();
				},

				/**
				 * Render content.
				 */
				render_content: function() {

					var view = this;

					composite.console_log( 'debug:views', '\nRendering summary view element contents' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );
					composite.debug_indent_incr();
					for ( var index = 0, steps = composite.get_steps(), length = steps.length; index < length; index++ ) {
						view.render_element_content( steps[ index ] );
					}
					composite.debug_indent_decr();
				},

				/**
				 * Render view.
				 */
				render: function() {

					composite.console_log( 'debug:views', '\nRendering summary view elements' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );
					composite.debug_indent_incr();
					this.render_visibility();
					this.render_state();
					this.render_columns();
					this.render_content();
					composite.debug_indent_decr();
				},

				/**
				 * Renders a single element's content (scheduler).
				 */
				render_element_content: function( step ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					var view = this;

					if ( typeof this.view_elements[ step.step_id ] === 'undefined' ) {
						return false;
					}

					if ( step.component_selection_model.has_pending_updates() ) {
						return false;
					}

					composite.console_log( 'debug:views', '\nScheduled update of "' + step.get_title() + '" summary view element content' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '.' );

					if ( typeof( this.update_content_timers[ step.step_index ] ) !== 'undefined' ) {
						clearTimeout( view.update_content_timers[ step.step_index ] );
					}

					this.update_content_timers[ step.step_index ] = setTimeout( function() {
						view.render_element_content_task( step );
					}, 10 );
				},

				/**
				 * Renders a single element's content.
				 */
				render_element_content_task: function( step ) {

					if ( ! step.is_component() ) {
						return;
					}

					composite.console_log( 'debug:views', '\nRendering "' + step.get_title() + '" summary view element content' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					var view                = this,
						throttle            = 200,
						component           = step,
						component_id        = component.component_id,

						$item_summary_outer = this.view_elements[ component_id ].$summary_element_wrapper,
						$item_summary_inner = this.view_elements[ component_id ].$summary_element_inner,

						template_html       = this.view_elements[ component_id ].template_html,

						content_data        = {
							element_index:           step.get_title_index(),
							element_title:           step.get_title(),
							element_selection_title: '',
							element_action:          '',
							element_label:           '',
							element_button_classes:  '',
							element_image_src:       '',
							element_image_srcset:    '',
							element_image_sizes:     '',
							element_image_title:     '',
							element_price:           ''
						},

						image_data          = false,
						load_height         = 0;

					// Lock height if animating.
					if ( ! view.update_height_timers[ step.step_index ] ) {

						load_height = $item_summary_inner.get( 0 ).getBoundingClientRect().height;

						if ( typeof load_height === 'undefined' ) {
							load_height = $item_summary_inner.outerHeight();
						}

						this.view_elements[ component_id ].load_height = load_height;
						$item_summary_outer.css( 'height', this.view_elements[ component_id ].load_height  );
					}

					// Selection title.
					content_data.element_selection_title = component.get_selected_product_title( true, false );
					// Is in widget?
					content_data.element_is_in_widget    = this.is_in_widget;
					// Link.
					content_data.element_button_link     = component.get_route();

					// Action text.
					if ( content_data.element_selection_title && component.passes_validation() ) {

						if ( component.is_static() && ! component.is_selected_product_configurable() ) {
							content_data.element_action = wc_composite_params.i18n_summary_static_component;
						} else {
							content_data.element_action = wc_composite_params.i18n_summary_configured_component;
						}

					} else {

						var selected_product      = step.get_selected_product(),
							selected_product_type = step.get_selected_product_type();

						content_data.element_action = wc_composite_params.i18n_summary_empty_component;

						if ( selected_product > 0 ) {
							if ( selected_product_type !== 'simple' && selected_product_type !== 'invalid-product' ) {
								content_data.element_action = wc_composite_params.i18n_summary_pending_component;
							}
						}
					}

					content_data.element_label = wc_composite_params.i18n_summary_action_label.replace( '%a', content_data.element_action ).replace( '%c', content_data.element_title );

					// Selection image data.
					image_data = component.get_selected_product_image_data( false );

					if ( false === image_data ) {
						image_data = component.get_placeholder_image_data();
					}

					if ( image_data ) {
						content_data.element_image_src    = image_data.image_src;
						content_data.element_image_srcset = image_data.image_srcset ? image_data.image_srcset : '';
						content_data.element_image_sizes  = image_data.image_sizes ? image_data.image_sizes : '';
						content_data.element_image_title  = image_data.image_title;
					}

					// Selection price.
					content_data.element_price = this.get_element_price_html( step );

					// Pass through 'component_summary_element_content_data' filter - @see WC_CP_Filters_Manager class.
					content_data = composite.filters.apply_filters( 'component_summary_element_content_data', [ content_data, component, this ] );

					var new_template_html = this.template( content_data );

					if ( new_template_html !== template_html ) {

						this.view_elements[ component_id ].template_html = new_template_html;

						// Update content.
						$item_summary_inner.html( new_template_html );

					} else {
						composite.console_log( 'debug:views', '...skipped!' );
					}

					// Update element class.
					if ( component.passes_validation() ) {
						$item_summary_outer.addClass( 'configured' );
					} else {
						$item_summary_outer.removeClass( 'configured' );
					}

					// Run 'component_summary_content_updated' action to allow 3rd party code to add data to the summary - @see WC_CP_Actions_Dispatcher class.
					composite.actions.do_action( 'component_summary_content_updated', [ component, this ] );

					var animate_height = function() {

						// Preload images before animating.
						var $image = $item_summary_inner.find( '.summary_element_image img' ),
							task   = new wc_cp_classes.WC_CP_Async_Task( function() {

							var wait       = false,
								async_task = this;

							if ( image_data.image_src && $image.is( ':visible' ) ) {

								if ( $image.height() === 0 && false === $image.get( 0 ).complete && async_task.get_async_time() < 5000 ) {
									wait = true;
									return false;
								}
							}

							if ( ! wait ) {
								this.done();
							}

						}, 50 );

						// Animate.
						task.complete( function() {

							// Measure height.
							var new_height     = $item_summary_inner.outerHeight( true ),
								animate_height = false;

							if ( view.$el.is( ':visible' ) && Math.abs( new_height - view.view_elements[ component_id ].load_height ) > 1 ) {
								animate_height = true;
							} else {
								$item_summary_outer.css( { height: 'auto' } );
							}

							if ( animate_height ) {

								$item_summary_outer.wc_cp_animate_height( new_height, 200, {

									start: function() {

										composite.console_log( 'debug:animations', 'Starting updated summary element content animation...' );

									},

									complete: function() {

										composite.console_log( 'debug:animations', 'Ended updated summary element content animation.' );

										$item_summary_outer.css( { height: 'auto' } );

									}

								} );
							}

						} );
					};

					if ( typeof( this.update_height_timers[ step.step_index ] ) !== 'undefined' ) {
						clearTimeout( view.update_height_timers[ step.step_index ] );
					}

					this.update_height_timers[ step.step_index ] = setTimeout( function() {
						animate_height();
						view.update_height_timers[ step.step_index ] = 0;
					}, throttle );
				},

				/**
				 * Renders a single element's state (active/inactive).
				 */
				render_element_visibility: function( step ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					if ( typeof this.view_elements[ step.step_id ] === 'undefined' ) {
						return false;
					}

					var $element = this.view_elements[ step.step_id ].$summary_element;

					composite.console_log( 'debug:views', '\nUpdating "' + step.get_title() + '" summary view element visibility' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					if ( false === step.is_visible() ) {
						$element.addClass( 'hidden' );
					} else {
						$element.removeClass( 'hidden' );
					}
				},

				/**
				 * Renders a single element's state (active/inactive).
				 */
				render_element_state: function( step ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					if ( typeof this.view_elements[ step.step_id ] === 'undefined' ) {
						return false;
					}

					var $element_wrapper = this.view_elements[ step.step_id ].$summary_element_wrapper;

					composite.console_log( 'debug:views', '\nUpdating "' + step.get_title() + '" summary view element state' + ( this.is_in_widget ? ' (widget #' + this.is_in_widget + ')' : '' ) + '...' );

					if ( step.is_current() ) {

						$element_wrapper.removeClass( 'disabled' );

						if ( composite.settings.layout !== 'single' ) {
							$element_wrapper.addClass( 'selected' );
						}

					} else {

						if ( step.is_locked() ) {

							$element_wrapper.removeClass( 'selected' );
							$element_wrapper.addClass( 'disabled' );

						} else {

							$element_wrapper.removeClass( 'disabled' );
							$element_wrapper.removeClass( 'selected' );
						}
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with navigation view elements.
		 */
		this.Composite_Navigation_View = function( opts ) {

			var View = Backbone.View.extend( {

				render_timer:          false,
				render_movable_timer:  false,

				updated_buttons_data:  {},

				navi_in_step:          false,

				template:              false,

				$el_progressive:       composite.$composite_form.find( '.composite_navigation.progressive' ),
				$el_paged_top:         composite.$composite_navigation_top,
				$el_paged_bottom:      composite.$composite_navigation_bottom,
				$el_paged_movable:     composite.$composite_navigation_movable,

				initialize: function() {

					this.template = wp.template( 'wc_cp_composite_navigation' );

					/**
					 * Update navigation view elements when a new selection is made.
					 */
					composite.actions.add_action( 'component_selection_changed', this.selection_changed_handler, 110, this );

					/**
					 * Render movable navigation when the product selection view content is updated.
					 */
					composite.actions.add_action( 'component_selection_details_updated', this.selection_details_updated_handler, 10, this );

					/**
					 * Update navigation view elements when the contents of an existing selection are changed.
					 */
					composite.actions.add_action( 'component_selection_content_changed', this.selection_content_changed_handler, 100, this );

					/**
					 * Update navigation view elements on transitioning to a new step.
					 */
					composite.actions.add_action( 'active_step_transition_start', this.active_step_transition_start_handler, 110, this );

					/**
					 * Update movable navi visibility when appending more options.
					 */
					composite.actions.add_action( 'component_options_state_rendered', this.options_state_rendered_handler, 20, this );

					/**
					 * On clicking the Previous/Next navigation buttons.
					 */
					this.$el.on( 'click', '.page_button', this.clicked_navigation_button );
				},

				/**
				 * Render movable navigation when the product selection view content is updated in the current component.
				 */
				selection_details_updated_handler: function( component ) {
					if ( component.is_current() ) {
						this.render_movable_task();
					}
				},

				/**
				 * Update navigation view elements when the contents of an existing selection are changed.
				 */
				selection_content_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.render_change();
				},

				/**
				 * Updates navigation view elements when a new selection is made.
				 * Handled by the composite actions dispatcher.
				 */
				selection_changed_handler: function( step ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					// Autotransition to next.
					if ( step.can_autotransition() ) {
						composite.show_next_step();
						return false;
					}

					this.render_change();
				},

				/**
				 * Update navigation view elements on transitioning to a new step.
				 */
				active_step_transition_start_handler: function() {

					var view = this;

					clearTimeout( view.render_timer );
					view.render( 'transition' );
				},

				/**
				 * Update movable navi visibility in relocated containers when appending more options.
				 */
				options_state_rendered_handler: function( step, changed ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					if ( step.is_current() && _.includes( changed, 'thumbnails' ) && step.component_selection_view.is_relocated() ) {
						this.render_movable();
					}
				},

				/**
				 * Previous/Next navigation button clicked.
				 */
				clicked_navigation_button: function() {

					var $this = $( this );

					if ( $this.hasClass( 'inactive' ) ) {
						return false;
					}

					if ( composite.has_transition_lock ) {
						return false;
					}

					if ( $this.hasClass( 'next' ) ) {

						if ( composite.get_next_step() ) {
							composite.show_next_step();
						} else {
							composite.composite_viewport_scroller.scroll_viewport( composite.$composite_form.find( '.scroll_final_step' ), { partial: false, duration: 250, queue: false } );
						}

					} else {
						composite.show_previous_step();
					}

					return false;
				},

				update_buttons: function() {

					var view = this,
						data = {
							prev_btn: { btn_classes: '', btn_text: '', btn_link: '', btn_label: '' },
							next_btn: { btn_classes: '', btn_text: '', btn_link: '', btn_label: '' },
						};

					if ( false !== this.updated_buttons_data.button_next_link ) {
						data.next_btn.btn_link = this.updated_buttons_data.button_next_link;
					}

					if ( false !== this.updated_buttons_data.button_prev_link ) {
						data.prev_btn.btn_link = this.updated_buttons_data.button_prev_link;
					}

					if ( false !== this.updated_buttons_data.button_next_html ) {
						data.next_btn.btn_text = this.updated_buttons_data.button_next_html;
					}

					if ( false !== this.updated_buttons_data.button_prev_html ) {
						data.prev_btn.btn_text = this.updated_buttons_data.button_prev_html;
					}

					if ( false !== this.updated_buttons_data.button_next_label ) {
						data.next_btn.btn_label = this.updated_buttons_data.button_next_label;
					}

					if ( false !== this.updated_buttons_data.button_prev_label ) {
						data.prev_btn.btn_label = this.updated_buttons_data.button_prev_label;
					}

					if ( false === this.updated_buttons_data.button_next_visible ) {
						data.next_btn.btn_classes = 'invisible';
					}

					if ( false === this.updated_buttons_data.button_prev_visible ) {
						data.prev_btn.btn_classes = 'invisible';
					}

					if ( false === this.updated_buttons_data.button_next_active ) {
						data.next_btn.btn_classes += ' inactive';
					}

					this.$el.html( view.template( data ) );
				},

				render_change: function() {

					var view = this;

					composite.console_log( 'debug:views', '\nScheduling navigation UI update...' );

					clearTimeout( view.render_timer );
					view.render_timer = setTimeout( function() {
						view.render( 'change' );
					}, 40 );
				},

				render: function( event_type ) {

					composite.console_log( 'debug:views', '\nRendering navigation UI...' );

					var current_step        = composite.get_current_step(),
						next_step           = composite.get_next_step(),
						prev_step           = composite.get_previous_step(),
						view                = this;

					this.updated_buttons_data = {
						button_next_link:    false,
						button_prev_link:    false,
						button_next_label:   false,
						button_prev_label:   false,
						button_next_html:    false,
						button_prev_html:    false,
						button_next_visible: false,
						button_prev_visible: false,
						button_next_active:  false,
					};

					if ( event_type === 'transition' && composite.settings.layout === 'paged' && composite.settings.layout_variation === 'componentized' ) {
						if ( current_step.is_review() ) {
							this.$el_paged_bottom.hide();
						} else {
							this.$el_paged_bottom.show();
						}
					}

					if ( next_step ) {
						this.updated_buttons_data.button_next_link = next_step.get_route();
					}

					if ( prev_step ) {
						this.updated_buttons_data.button_prev_link = prev_step.get_route();
					}

					if ( current_step.is_component() ) {

						// Selectively show next/previous navigation buttons.
						if ( next_step && composite.settings.layout_variation !== 'componentized' ) {

							this.updated_buttons_data.button_next_html    = wc_composite_params.i18n_next_step.replace( '%s', next_step.get_title() );
							this.updated_buttons_data.button_next_label   = next_step.is_review() ? wc_composite_params.i18n_final_step : wc_composite_params.i18n_next_step_label.replace( '%s', next_step.get_title() );
							this.updated_buttons_data.button_next_visible = true;

						} else if ( next_step && composite.settings.layout === 'paged' ) {
							this.updated_buttons_data.button_next_html    = wc_composite_params.i18n_final_step;
							this.updated_buttons_data.button_next_label   = wc_composite_params.i18n_final_step;
							this.updated_buttons_data.button_next_visible = true;
						}
					}

					// Paged previous/next.
					if ( current_step.passes_validation() || ( composite.settings.layout_variation === 'componentized' && current_step.is_component() ) ) {

						if ( next_step ) {
							this.updated_buttons_data.button_next_active = true;
						}

						if ( prev_step && composite.settings.layout === 'paged' && prev_step.is_component() ) {
							this.updated_buttons_data.button_prev_html    = wc_composite_params.i18n_previous_step.replace( '%s', prev_step.get_title() );
							this.updated_buttons_data.button_prev_label   = wc_composite_params.i18n_previous_step_label.replace( '%s', prev_step.get_title() );
							this.updated_buttons_data.button_prev_visible = true;
						} else {
							this.updated_buttons_data.button_prev_html  = '';
							this.updated_buttons_data.button_prev_label = '';
						}

					} else {

						if ( prev_step && prev_step.is_component() ) {

							var product_id = prev_step.get_selected_product();

							if ( product_id > 0 || product_id === '0' || product_id === '' && prev_step.is_optional() ) {

								if ( composite.settings.layout === 'paged' ) {
									this.updated_buttons_data.button_prev_html    = wc_composite_params.i18n_previous_step.replace( '%s', prev_step.get_title() );
									this.updated_buttons_data.button_prev_label   = wc_composite_params.i18n_previous_step_label.replace( '%s', prev_step.get_title() );
									this.updated_buttons_data.button_prev_visible = true;
								}
							}
						}
					}

					/*
					 * Move navigation into the next component when using the progressive layout without toggles.
					 */
					if ( composite.settings.layout === 'progressive' ) {

						var $navi = view.$el_progressive;

						if ( view.navi_in_step !== current_step.step_id ) {

							$navi.slideUp( { duration: 200, always: function() {

								view.update_buttons();
								$navi.appendTo( current_step.$inner_el ).hide();

								view.navi_in_step = current_step.step_id;

								setTimeout( function() {

									var show_navi = false;

									if ( ! current_step.is_last() ) {
										if ( current_step.passes_validation() && ! next_step.has_toggle() ) {
											show_navi = true;
										}
									}

									if ( show_navi ) {
										$navi.slideDown( { duration: 200, queue: false } );
									}

								}, 200 );

							} } );

						} else {

							view.update_buttons();

							var show_navi = false;

							if ( ! current_step.is_last() ) {
								if ( current_step.passes_validation() && ! next_step.has_toggle() ) {
									show_navi = true;
								}
							}

							if ( show_navi ) {
								$navi.slideDown( 200 );
							} else {
								$navi.slideUp( 200 );
							}
						}

					/*
					 * Move navigation when using a paged layout with thumbnails.
					 */
					} else if ( composite.settings.layout === 'paged' ) {

						if ( view.navi_in_step !== current_step.step_id ) {
							current_step.$el.prepend( view.$el_paged_top );
							current_step.$el.append( view.$el_paged_bottom );
							view.navi_in_step = current_step.step_id;
						}

						view.update_buttons();

						if ( event_type === 'transition' ) {
							view.render_movable_task();
						}
					}
				},

				render_movable: function() {

					var view = this;

					composite.console_log( 'debug:views', '\nScheduling movable navigation visibility update...' );

					clearTimeout( view.render_movable_timer );
					view.render_movable_timer = setTimeout( function() {
						view.render_movable_task();
					}, 10 );
				},

				render_movable_task: function() {

					var current_step = composite.get_current_step(),
						view         = this;

					if ( current_step.is_component() && current_step.has_options_style( 'thumbnails' ) ) {

						if ( current_step.get_selected_product( false ) > 0 ) {

							// Measure distance from bottom navi and only append navi in content if far enough.
							var navi_in_content    = current_step.$component_content.find( '.composite_navigation' ).length > 0,
								bottom_navi_nearby = false;

							if ( current_step.append_results() ) {

								if ( current_step.component_selection_view.is_relocated() ) {

									var visible_thumbnails       = current_step.$component_options.find( '.component_option_thumbnail_container' ).not( '.hidden' ),
										selected_thumbnail       = current_step.$component_options.find( '.component_option_thumbnail.selected' ).closest( '.component_option_thumbnail_container' ),
										selected_thumbnail_index = visible_thumbnails.index( selected_thumbnail ) + 1,
										thumbnail_columns        = composite.$composite_form.width() > wc_composite_params.small_width_threshold && false === composite.$composite_form.hasClass( 'legacy_width' ) ? current_step.component_options_view.get_columns() : 1;

									if ( Math.ceil( selected_thumbnail_index / thumbnail_columns ) === Math.ceil( visible_thumbnails.length / thumbnail_columns ) ) {
										bottom_navi_nearby = true;
									}
								}
							}

							if ( ! navi_in_content && ! bottom_navi_nearby ) {
								view.$el_paged_movable.appendTo( current_step.$component_summary );
								navi_in_content = true;
							}

							if ( navi_in_content ) {
								if ( bottom_navi_nearby || current_step.is_static() ) {
									view.$el_paged_movable.addClass( 'hidden' );
								} else {
									view.$el_paged_movable.removeClass( 'hidden' );
								}
							}
						}
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with the Composite Summary Widget and its elements.
		 */
		this.Composite_Widget_View = function( opts ) {

			var View = Backbone.View.extend( {

				show_hide_timer: false,

				initialize: function( options ) {

					this.$el.removeClass( 'cp-no-js' );

					this.validation_view = new composite.view_classes.Composite_Validation_View( {
						is_in_widget: options.widget_count,
						el:           this.$el.find( '.widget_composite_summary_error .composite_message' ),
						model:        composite.data_model,
					} );

					this.price_view = new composite.view_classes.Composite_Price_View( {
						is_in_widget: options.widget_count,
						el:           this.$el.find( '.widget_composite_summary_price .composite_price' ),
						model:        composite.data_model,
					} );

					this.availability_view = new composite.view_classes.Composite_Availability_View( {
						is_in_widget: options.widget_count,
						el:           this.$el.find( '.widget_composite_summary_availability .composite_availability' ),
						model:        composite.data_model,
					} );

					this.add_to_cart_button_view = new composite.view_classes.Composite_Add_To_Cart_Button_View( {
						is_in_widget: options.widget_count,
						el:           this.$el.find( '.widget_composite_summary_button .composite_button' ),
						$el_button:   this.$el.find( '.widget_composite_summary_button .composite_button .composite_add_to_cart_button' ),
						model:        composite.data_model,
					} );

					this.composite_summary_view = new composite.view_classes.Composite_Summary_View( {
						is_in_widget: options.widget_count,
						el:           this.$el.find( '.widget_composite_summary_elements' ),
					} );

					// Run 'widget_view_initialized' action - @see WC_CP_Composite_Dispatcher class.
					composite.actions.do_action( 'widget_view_initialized', [ options, this ] );

					/**
					 * Show/hide the widget when transitioning to a new step.
					 */
					if ( composite.settings.layout === 'paged' ) {
						composite.actions.add_action( 'active_step_changed', this.active_step_changed_handler, 100, this );
					} else {
						this.show_hide();
					}
				},

				active_step_changed_handler: function() {

					this.show_hide();
				},

				show_hide: function() {

					var view = this;

					clearTimeout( view.show_hide_timer );
					this.show_hide_timer = setTimeout( function() {
						view.show_hide_task();
					}, 20 );
				},

				show_hide_task: function() {

					var view           = this,
						delay          = 0,
						is_review      = composite.get_current_step().is_review(),
						is_first_load  = typeof this.$el.data( 'is_hidden' ) === 'undefined',
						show_in_review = typeof( composite.settings.show_widget_in_review_step ) !== 'undefined' && 'yes' === composite.settings.show_widget_in_review_step ? 'yes' : 'no';

					if ( is_review && 'no' === show_in_review ) {

						this.$el.data( 'is_hidden', true );

						composite.console_log( 'debug:animations', 'Starting widget slide-up animation...' );

						if ( this.$el.hasClass( 'widget_position_default' ) ) {
							delay = 250;
							this.$el.addClass( 'summary_widget_inactive' );
						}

						setTimeout( function() {
							view.$el.slideUp( {
								duration: 250,
								always: function() {
									composite.console_log( 'debug:animations', 'Ended widget slide-up animation.' );
									view.$el.addClass( 'summary_widget_hidden' ).show();
								}
							} );
						}, delay );

					} else if ( this.$el.data( 'is_hidden' ) || is_first_load ) {

						this.$el.data( 'is_hidden', false );

						composite.console_log( 'debug:animations', 'Starting widget slide-down animation...' );

						if ( is_first_load ) {

							this.$el.show().removeClass( 'summary_widget_hidden' );

						} else {

							this.$el.hide().removeClass( 'summary_widget_hidden' ).slideDown( {
								duration: 250,
								always: function() {
									composite.console_log( 'debug:animations', 'Ended widget slide-down animation.' );
								}
							} );
						}

						setTimeout( function() {
							view.$el.removeClass( 'summary_widget_inactive' );
						}, 10 );
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * Handles the display of step validation messages.
		 */
		this.Step_Validation_View = function( step, opts ) {

			var self = step;
			var View = Backbone.View.extend( {

				render_timer: false,
				render_html:  false,
				template:     false,

				event_type:       '',
				rendered_product: '',

				initialize: function() {

					var view      = this;
					this.template = wp.template( 'wc_cp_validation_message' );

					this.listenTo( this.model, 'change:component_messages', function() {

						if ( ! self.is_current() || typeof( self.$component_message ) === 'undefined' ) {
							return false;
						}

						composite.console_log( 'debug:views', '\nScheduling "' + self.get_title() + '" validation message update...' );

						clearTimeout( view.render_timer );
						view.render_timer = setTimeout( function() {

							view.prepare_render( 'change' );

							if ( self.can_autotransition() ) {
								return false;
							}

							view.render();

							view.rendered_product = self.component_selection_view.get_rendered_product();

						}, 10 );
					} );

					/**
					 * Prepare display of component messages when transitioning to this step.
					 */
					if ( composite.settings.layout !== 'single' ) {
						composite.actions.add_action( 'active_step_changed_' + self.step_id, this.active_step_changed_handler, 100, this );
					}

					/**
					 * Display component messages after transitioning to this step.
					 */
					if ( composite.settings.layout !== 'single' ) {
						composite.actions.add_action( 'active_step_transition_end_' + self.step_id, this.active_step_transition_end_handler, 100, this );
					}

					/**
					 * Hide top message out of view.
					 */
					composite.actions.add_action( 'component_selection_details_relocation_started', this.relocation_started_handler, 100, this );

					/**
					 * Move top message to original location.
					 */
					composite.actions.add_action( 'component_selection_details_relocation_ended', this.relocation_ended_handler, 100, this );

					/**
					 * Move top message into new relocation container.
					 */
					composite.actions.add_action( 'component_selection_details_relocation_container_created', this.relocation_container_created_handler, 100, this );
				},

				/**
				 * Hide top message out of view.
				 */
				relocation_started_handler: function( step ) {

					if ( step.step_id === self.step_id ) {

						var $component_message_top = self.$component_message.filter( '.top' );

						composite.console_log( 'debug:views', '\nHiding "' + self.get_title() + '" validation message to prepare for component details relocation...' );

						if ( $component_message_top ) {

							var done = composite.composite_viewport_scroller.illusion_scroll( {
								target: $component_message_top,
								type:   'hide'
							} );

							if ( false === done ) {
								$component_message_top.slideUp( 200 );
							}
						}
					}
				},

				/**
				 * Move top message into new relocation container.
				 */
				relocation_container_created_handler: function( step ) {

					if ( step.step_id === self.step_id ) {

						composite.console_log( 'debug:views', '\nMoving "' + self.get_title() + '" validation message into relocation target...' );

						var $component_message_top = self.$component_message.filter( '.top' );

						if ( $component_message_top ) {
							self.component_selection_view.$relocation_target.prepend( $component_message_top );
						}
					}
				},

				/**
				 * Move top message to original location.
				 */
				relocation_ended_handler: function( step ) {

					if ( step.step_id === self.step_id ) {

						var $component_message_top = self.$component_message.filter( '.top' );

						composite.console_log( 'debug:views', '\nMoving "' + self.get_title() + '" validation message back to its original position...' );

						if ( $component_message_top ) {
							self.component_selection_view.$relocation_origin.after( $component_message_top );
						}
					}
				},

				/**
				 * Shows component messages when transitioning to this step.
				 */
				active_step_changed_handler: function() {

					if ( ! self.is_current() || typeof( self.$component_message ) === 'undefined' ) {
						return false;
					}

					this.prepare_render( 'transition' );

					if ( false === this.render_html ) {
						this.render();
					}

					this.rendered_product = self.component_selection_view.get_rendered_product();
				},

				/**
				 * Shows component messages when transitioning to this step.
				 */
				active_step_transition_end_handler: function() {

					if ( ! self.is_current() || typeof( self.$component_message ) === 'undefined' ) {
						return false;
					}

					if ( false !== this.render_html ) {
						clearTimeout( this.render_timer );
						this.render();
					}

					this.rendered_product = self.component_selection_view.get_rendered_product();
				},

				/**
				 * Prepares validation messages for rendering.
				 */
				prepare_render: function( event_type ) {

					this.event_type = '' === this.event_type ? event_type : this.event_type;

					var display_message;

					composite.console_log( 'debug:views', '\nPreparing "' + self.get_title() + '" validation message update...' );

					this.render_html = false;

					if ( self.passes_validation() || ( composite.settings.layout_variation === 'componentized' && self.is_component() ) ) {
						display_message = false;
					} else {
						display_message = true;
					}

					if ( display_message ) {

						// Don't show the prompt if it's the last component of the progressive layout.
						if ( ! self.is_last() || ! composite.settings.layout === 'progressive' ) {

							// We actually have something to display here.
							var validation_messages = self.get_validation_messages();

							if ( validation_messages.length > 0 ) {
								this.render_html = this.template( validation_messages );
							}
						}
					}

					if ( this.event_type === 'transition' && false === this.render_html ) {
						if ( composite.settings.layout === 'progressive' ) {
							if ( self.has_toggle() ) {
								self.$component_message.hide();
							}
						} else if ( composite.settings.layout === 'paged' ) {
							self.$component_message.hide();
						}
					}
				},

				/**
				 * Renders validation messages.
				 */
				render: function() {

					var view = this;

					composite.console_log( 'debug:views', '\nUpdating "' + self.get_title() + '" validation message...' );

					if ( composite.settings.layout === 'progressive' ) {

						var delay = this.event_type === 'transition' ? 200 : 1;

						setTimeout( function() {

							if ( false !== view.render_html ) {
								self.$component_message.html( view.render_html );
							}

							if ( false === view.render_html ) {
								self.$component_message.slideUp( 200 );
							} else {
								self.$component_message.slideDown( 200 );
							}

						}, delay );

					} else if ( composite.settings.layout === 'paged' ) {

						self.$component_message.each( function( index, el ) {

							var done   = false,
								$el    = $( el ),
								is_top = $el.hasClass( 'top' );

							var delay = is_top && 'change' === view.event_type && self.component_selection_view.is_relocated() && view.rendered_product !== self.component_selection_view.get_rendered_product() ? self.component_selection_view.get_animation_duration( 'close' ) + 50 : 0;

							setTimeout( function() {

								if ( false !== view.render_html ) {
									self.$component_message.html( view.render_html );
								}

								if ( is_top ) {
									done = composite.composite_viewport_scroller.illusion_scroll( {
										target: $el,
										type:   false === view.render_html ? 'hide' : 'show'
									} );
								}

								if ( false === done ) {
									if ( false === view.render_html ) {
										$el.slideUp( 200 );
									} else {
										$el.slideDown( 200 );
									}
								}

							}, delay );

						} );
					}

					this.event_type = '';
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * Updates step title elements by listening to step model changes.
		 */
		this.Step_Title_View = function( step, opts ) {

			var self = step;
			var View = Backbone.View.extend( {

				$step_title_index: false,

				initialize: function() {

					this.$step_title_index = self.$step_title.find( '.step_index' );

					if ( step.is_component && self.has_toggle() ) {

						/**
						 * On clicking toggled component titles.
						 */
						this.$el.on( 'click', this.clicked_title_handler );

						if ( composite.settings.layout === 'progressive' ) {

							/**
							 * Update view when access to the step changes.
							 */
							composite.actions.add_action( 'step_access_changed', this.step_access_changed_handler, 100, this );

							/**
							 * Update view on transitioning to a new step.
							 */
							composite.actions.add_action( 'active_step_changed', this.active_step_changed_handler, 100, this );
						}
					}

					if ( false !== this.$step_title_index ) {
						/**
						 * Update step title indexes.
						 */
						composite.actions.add_action( 'step_visibility_changed', this.step_visibility_changed_handler, 100, this );
					}
				},

				clicked_title_handler: function() {

					if ( ! self.has_toggle() ) {
						return false;
					}

					if ( 'single' === composite.settings.layout ) {
						wc_cp_toggle_element( self.$el, self.$component_inner );
					} else {

						if ( self.is_current() ) {
							if ( 'progressive' === composite.settings.layout ) {
								step.toggle_step( 'open', true );
							} else {
								return false;
							}
						}

						if ( $( this ).hasClass( 'inactive' ) ) {
							return false;
						}

						composite.navigate_to_step( self );
					}

					return false;
				},

				step_access_changed_handler: function( step ) {

					if ( step.step_id === self.step_id ) {
						this.render_navigation_state();
					}
				},

				active_step_changed_handler: function() {

					this.render_navigation_state();
				},

				/**
				 * Update progressive component title based on lock state.
				 */
				render_navigation_state: function() {

					if ( composite.settings.layout === 'progressive' && self.has_toggle() ) {

						composite.console_log( 'debug:views', '\nUpdating "' + self.get_title() + '" component title state...' );

						if ( self.is_current() ) {
							this.$el.removeClass( 'inactive' );
						} else {
							if ( self.is_locked() ) {
								this.$el.addClass( 'inactive' );
							} else {
								this.$el.removeClass( 'inactive' );
							}
						}
					}
				},

				/**
				 * Render step title index.
				 */
				render_index: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					if ( false === this.$step_title_index ) {
						return false;
					}

					// Count number of hidden components before this one.
					var title_index = step.get_title_index();

					// Refresh index in step title.
					this.$step_title_index.text( title_index );
				},

				step_visibility_changed_handler: function( step ) {

					if ( self.step_index < step.step_index ) {
						return false;
					}

					this.render_index();
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * View associated with the component options pagination template.
		 */
		this.Component_Pagination_View = function( component, opts ) {

			var self = component;
			var View = Backbone.View.extend( {

				template: false,

				initialize: function() {

					this.template = wp.template( 'wc_cp_options_pagination' );

					/**
					 * Update the view when its model state changes.
					 */
					this.listenTo( this.model, 'change:page change:pages', this.render );

					/**
					 * Reload component options upon requesting a new page.
					 */
					self.$el.on( 'click', '.component_pagination a.component_pagination_element', { view: this }, this.load_page );

					/**
					 * Append component options upon clicking the 'Load More' button.
					 */
					self.$el.on( 'click', '.component_pagination .component_options_load_more', { view: this }, this.load_more );

				},

				load_page: function() {

					var page = parseInt( $( this ).data( 'page_num' ), 10 );

					if ( page > 0 ) {

						// Block container.
						composite.block( self.$component_options );
						self.component_options_view.$blocked_element = self.$component_options;

						self.component_options_view.update_options( { page: page }, 'reload' );
					}

					return false;
				},

				load_more: function() {

					var page  = parseInt( self.component_options_model.get( 'page' ), 10 ),
						pages = parseInt( self.component_options_model.get( 'pages' ), 10 );

					if ( page > 0 && page < pages ) {

						// Block container.
						composite.block( self.$component_options );
						self.component_options_view.$blocked_element = self.$component_options;

						self.component_options_view.update_options( { page: page + 1 }, 'append' );
					}

					return false;
				},

				/**
				 * Renders the view.
				 */
				render: function() {

					if ( ! composite.is_initialized ) {
						return false;
					}

					var	model = this.model,
						data  = {
							page:                model.get( 'page' ),
							pages:               model.get( 'pages' ),
							range_mid:           self.get_pagination_range(),
							range_end:           self.get_pagination_range( 'end' ),
							pages_in_range:      ( ( self.get_pagination_range() + self.get_pagination_range( 'end' ) ) * 2 ) + 1,
							i18n_page_of_pages:  wc_composite_params.i18n_page_of_pages.replace( '%p', model.get( 'page' ) ).replace( '%t', model.get( 'pages' ) )
						};

					composite.console_log( 'debug:views', '\nRendering "' + self.get_title() + '" options pagination...' );

					if ( self.append_results() ) {

						if ( data.page < data.pages ) {
							this.$el.slideDown( 200 );
						} else {
							this.$el.slideUp( 200 );
						}

					} else {

						this.$el.html( this.template( data ) );
					}
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * Updates the model data from UI interactions and listens to the component options model for updated content.
		 */
		this.Component_Options_View = function( component, opts ) {

			var self = component;
			var View = Backbone.View.extend( {

				templates: {
					dropdown:   false,
					thumbnails: false,
					radios:     false
				},

				reference_price: 0.0,
				reference_option: false,

				update_action:    '',
				load_height:      0,
				$blocked_element: false,

				append_results_retry_count: 0,

				must_reload_options: false,
				is_lazy_load_pending: false,

				has_invalid_empty_option: false,

				changes: {
					dropdown:   { changed: false, to: '' },
					thumbnails: { changed: false, to: '' },
					radios:     { changed: false, to: '' },
					variations: { changed: false, to: [] }
				},

				initialize: function() {

					this.templates.dropdown   = wp.template( 'wc_cp_options_dropdown' );
					this.templates.thumbnails = wp.template( 'wc_cp_options_thumbnails' );
					this.templates.radios     = wp.template( 'wc_cp_options_radio_buttons' );

					/**
					 * Reload component options upon activating a filter.
					 */
					self.$el.on( 'click', '.component_filter_option .toggle_filter_option', { view: this }, this.activate_filter );

					/**
					 * Reload component options upon resetting a filter.
					 */
					self.$el.on( 'click', '.component_filters .reset_component_filter', { view: this }, this.reset_filter );

					/**
					 * Reload component options upon resetting all filters.
					 */
					self.$el.on( 'click', '.component_filters .reset_component_filters', { view: this }, this.reset_filters );

					/**
					 * Reload component options upon reordering.
					 */
					self.$el.on( 'change', '.component_ordering select', { view: this }, this.order_by );

					/**
					 * Toggle filters.
					 */
					self.$el.on( 'click', '.component_filter_title .component_filter_name', { view: this }, this.toggle_filter );

					/**
					 * Navigate to step on clicking the blocked area in progressive mode.
					 */
					if ( composite.settings.layout === 'progressive' ) {
						self.$el.on( 'click', '.block_component_selections_inner', { view: this }, this.clicked_blocked_area );
					}

					/**
					 * Change selection when clicking a thumbnail or thumbnail tap area.
					 */
					if ( self.has_options_style( 'thumbnails' ) ) {
						self.$el.on( 'click', '.component_option_thumbnail_select', { view: this }, this.selected_thumbnail );
						self.$el.on( 'click', '.component_option_thumbnail_link', { view: this }, this.selected_thumbnail );
					}

					/**
					 * Change selection when clicking a radio button.
					 */
					if ( self.has_options_style( 'radios' ) ) {
						self.$el.on( 'change', '.component_option_radio_buttons input', { view: this }, this.clicked_radio );
					}

					/**
					 * Update view after appending/reloading component options.
					 */
					composite.actions.add_action( 'component_options_loaded_' + self.step_id, this.updated_options, 10, this );

					/**
					 * Render component options in view.
					 */
					composite.actions.add_action( 'component_options_state_changed_' + self.step_id, this.render, 10, this );

					/**
					 * Update component options in view when selections change.
					 */
					composite.actions.add_action( 'component_selection_changed', this.component_selection_changed_handler, 100, this );
					composite.actions.add_action( 'component_selection_content_changed', this.component_selection_changed_handler, 100, this );

					/**
					 * Reload options if the scenarios used to render them have changed.
					 */
					this.listenTo( this.model, 'change:options_in_scenarios', this.options_in_scenarios_changed );

					/**
					 * Reload options when opening a new step, if needed.
					 */
					if ( 'paged' === composite.settings.layout || 'progressive' === composite.settings.layout ) {
						composite.actions.add_action( 'show_step', this.maybe_reload_options, 10, this );
					}
				},

				/**
				 * Updates relative prices in view when component totals change.
				 */
				component_selection_changed_handler: function( step ) {

					if ( self.step_id !== step.step_id ) {
						return;
					}

					if ( self.component_selection_model.has_pending_updates() ) {
						return;
					}

					if ( ( self.is_priced_individually() && 'relative' === self.get_price_display_format() ) || this.has_invalid_empty_option ) {
						self.component_options_view.render();
					}
				},

				maybe_reload_options: function( step ) {

					step = typeof( step ) === 'undefined' ? self : step;

					if ( self.step_id !== step.step_id || ! composite.is_initialized ) {
						return false;
					}

					if ( self.is_lazy_loaded() || this.must_reload_options ) {

						if ( this.must_reload_options ) {
							this.scenarios_changed_load_options();
						} else if ( self.is_lazy_loaded() ) {
							this.lazy_load_options();
						}
					}

				},

				lazy_load_options: function() {

					var view = this;

					self.set_lazy_loaded( false );

					composite.console_log( 'debug:views', '\nLazy loading "' + self.get_title() + '" options...' );

					// Block options container.
					composite.block( self.$component_options );
					view.$blocked_element = self.$component_options;

					// Add status message.
					composite.data_model.add_status_message( self.component_id, wc_composite_params.i18n_loading_options.replace( '%s', self.get_title() ) );

					setTimeout( function() {
						view.update_options( { page: self.component_options_model.get( 'page' ) }, 'reload', true );
					}, 500 );
				},

				options_in_scenarios_changed: function() {

					if ( this.model.reload_options_on_scenarios_change() ) {

						this.must_reload_options = true;

						if ( 'single' === composite.settings.layout ) {
							this.scenarios_changed_load_options();
						} else {
							composite.console_log( 'debug:views', '\nScheduling "' + self.get_title() + '" options reload...' );
						}
					}
				},

				scenarios_changed_load_options: function() {

					var view = this;

					composite.console_log( 'debug:views', '\nReloading "' + self.get_title() + '" options...' );

					this.must_reload_options = false;
					self.set_lazy_loaded( false );

					// Block options container.
					composite.block( self.$component_options );
					this.$blocked_element = self.$component_options;

					// Add status message.
					composite.data_model.add_status_message( self.component_id, wc_composite_params.i18n_loading_options.replace( '%s', self.get_title() ) );

					setTimeout( function() {
						view.update_options( { page: 1 }, 'reload', true );
					}, 500 );
				},

				clicked_blocked_area: function() {

					composite.navigate_to_step( self );
					return false;
				},

				selected_thumbnail: function() {

					var $button    = $( this ),
						$thumbnail = $button.closest( '.component_option_thumbnail' );

					if ( self.$el.hasClass( 'disabled' ) || $thumbnail.hasClass( 'disabled' ) ) {
						return true;
					}

					if ( ! $thumbnail.hasClass( 'selected' ) ) {
						var value = $thumbnail.data( 'val' );
						self.$component_options_select.val( value ).trigger( 'change' );
					}

					return false;
				},

				clicked_radio: function() {

					var $this      = $( this ),
						$container = $this.closest( '.component_option_radio_button' );

					if ( self.$el.hasClass( 'disabled' ) || $container.hasClass( 'disabled' ) ) {
						return true;
					}

					if ( ! $container.hasClass( 'selected' ) ) {
						var value = $this.val();
						self.$component_options_select.val( value ).trigger( 'change' );
					}
				},

				toggle_filter: function() {

					var $this                     = $( this ),
						$component_filter         = $this.closest( '.component_filter' ),
						$component_filter_content = $component_filter.find( '.component_filter_content' );

					wc_cp_toggle_element( $component_filter, $component_filter_content, false, 200 );

					return false;
				},

				activate_filter: function( event ) {

					var $this = $( this );

					// Do nothing if the component is disabled.
					if ( self.$el.hasClass( 'disabled' ) ) {
						return false;
					}

					var view                     = event.data.view,
						$component_filter_option = $this.closest( '.component_filter_option' );

					if ( ! $component_filter_option.hasClass( 'selected' ) ) {

						var $component_filter_container = $this.closest( '.component_filter' ),
							is_multiselect              = 'yes' === $component_filter_container.data( 'multiselect' );

						if ( ! is_multiselect ) {

							var $component_filter_options_selected = $component_filter_container.find( '.component_filter_option.selected' );

							$component_filter_options_selected.removeClass( 'selected' );
							$component_filter_options_selected.find( '.toggle_filter_option' ).attr( 'aria-checked', 'false' );
						}

						$component_filter_option.addClass( 'selected' );
						$component_filter_option.find( '.toggle_filter_option' ).attr( 'aria-checked', 'true' );


					} else {
						$component_filter_option.removeClass( 'selected' );
						$component_filter_option.find( '.toggle_filter_option' ).attr( 'aria-checked', 'false' );
					}

					// Add/remove 'active' classes.
					view.update_filters_ui();

					// Block container.
					composite.block( self.$component_filters );
					view.$blocked_element = self.$component_filters;

					view.update_options( { page: 1, filters: self.find_active_filters() }, 'reload' );

					return false;
				},

				reset_filter: function( event ) {

					// Get active filters.
					var $this                     = $( this ),
						view                      = event.data.view,
						$component_filter_options = $this.closest( '.component_filter' ).find( '.component_filter_option.selected' );

					if ( $component_filter_options.length == 0 ) {
						return false;
					}

					$component_filter_options.removeClass( 'selected' );
					$component_filter_options.find( '.toggle_filter_option' ).attr( 'aria-checked', 'false' );

					// Add/remove 'active' classes.
					view.update_filters_ui();

					// Block container.
					composite.block( self.$component_filters );
					view.$blocked_element = self.$component_filters;

					view.update_options( { page: 1, filters: self.find_active_filters() }, 'reload' );

					return false;
				},

				reset_filters: function( event ) {

					$( this ).blur();

					// Get active filters.
					var view                      = event.data.view,
						$component_filter_options = self.$component_filters.find( '.component_filter_option.selected' );

					if ( $component_filter_options.length == 0 ) {
						return false;
					}

					$component_filter_options.removeClass( 'selected' );
					$component_filter_options.find( '.toggle_filter_option' ).attr( 'aria-checked', 'false' );

					// Add/remove 'active' classes.
					view.update_filters_ui();

					// Block container.
					composite.block( self.$component_filters );
					view.$blocked_element = self.$component_filters;

					view.update_options( { page: 1, filters: self.find_active_filters() }, 'reload' );

					return false;
				},

				/**
				 * Add active/filtered classes to the component filters markup, can be used for styling purposes.
				 */
				update_filters_ui: function() {

					var $filters  = self.$component_filters.find( '.component_filter' ),
						all_empty = true;

					if ( $filters.length == 0 ) {
						return false;
					}

					$filters.each( function() {

						var $filter = $( this );

						if ( $filter.find( '.component_filter_option.selected' ).length == 0 ) {
							$filter.removeClass( 'active' );
						} else {
							$filter.addClass( 'active' );
							all_empty = false;
						}

					} );

					if ( all_empty ) {
						self.$component_filters.removeClass( 'filtered' );
					} else {
						self.$component_filters.addClass( 'filtered' );
					}
				},

				order_by: function( event ) {

					var $this   = $( this ),
						view    = event.data.view,
						orderby = $this.val();

					// Block container.
					composite.block( self.$component_options );
					view.$blocked_element = self.$component_options;

					view.update_options( { page: 1, orderby: orderby }, 'reload' );

					return false;
				},

				get_columns: function() {

					var columns = null;

					if ( self.has_options_style( 'thumbnails' ) ) {

						columns = parseInt( self.$component_thumbnail_options.data( 'component_option_columns' ), 10 );

						if ( isNaN( columns ) ) {
							columns = typeof self.$component_thumbnail_options.data( 'columns' ) !== 'undefined' ? parseInt( self.$component_thumbnail_options.data( 'columns' ), 10 ) : 1;
						}

					} else if ( self.has_options_style( 'radios' ) ) {
						columns = 1;
					}

					return columns;
				},

				/*
				 * Get reference option data, used by 'WC_CP_Component.get_formatted_option_price_html'.
				 */
				get_reference_option_data: function() {

					return {
						option_id:    this.reference_option,
						option_price: this.reference_price
					};
				},

				/*
				 * Get placeholder/empty option title.
				 */
				get_empty_option_title: function( template_name ) {

					var template = template_name && wc_composite_params[ template_name ] ? wc_composite_params[ template_name ] : wc_composite_params.i18n_no_option;

					return composite.filters.apply_filters( 'component_empty_option_title', [ template.replace( '%s', self.get_title() ), self ] );
				},

				/**
				 * Renders options in the DOM in response to model changes.
				 */
				render: function( dropdown_only ) {

					if ( ! composite.is_initialized ) {
						return false;
					}

					this.is_lazy_load_pending = false;

					// Lazy-load options when rendering the current view for the first time.
					if ( self.is_lazy_loaded() ) {

						this.is_lazy_load_pending = true;

						if ( self.is_current() || 'single' === composite.settings.layout ) {
							this.lazy_load_options();
						}
					}

					dropdown_only = typeof( dropdown_only ) === 'undefined' ? false : dropdown_only;

					composite.console_log( 'debug:views', '\nRendering "' + self.get_title() + '" options in view...' );

					var view             = this,
						model            = self.component_options_model,
						price_format     = self.get_price_display_format(),
						active_options   = model.get( 'options_state' ).active,
						selected_product = self.get_selected_product( false ),
						options_data     = $.extend( true, [], model.available_options_data ),
						change_what      = [];

					view.changes.dropdown.changed   = false;
					view.changes.thumbnails.changed = false;
					view.changes.radios.changed     = false;
					view.changes.variations.changed = false;

					/*
					 * Store reference option data, used by 'WC_CP_Component.get_formatted_option_price_html'.
					 */
					if ( ( ! composite.is_finalized || selected_product === view.reference_option ) && 'relative' === self.get_price_display_format() && self.has_valid_selections( false ) ) {
						view.reference_price  = composite.data_model.calculate_component_subtotals( self, composite.data_model.price_data, 1 ).price;
						view.reference_option = selected_product;
					}

					/*
					 * Hide or grey-out inactive products.
					 */

					for ( var option_data_index = 0, options_data_length = options_data.length; option_data_index < options_data_length; option_data_index++ ) {

						var option_data   = options_data[ option_data_index ],
							product_id    = option_data.option_id,
							is_compatible = _.includes( active_options, product_id );

						if ( ! is_compatible ) {
							options_data[ option_data_index ].is_disabled = true;
						} else {
							options_data[ option_data_index ].is_disabled = false;
						}

						options_data[ option_data_index ].is_hidden   = options_data[ option_data_index ].is_disabled && self.hide_disabled_products();
						options_data[ option_data_index ].is_selected = options_data[ option_data_index ].option_id === selected_product;

						options_data[ option_data_index ].option_button_text   = option_data.is_configurable ? wc_composite_params.i18n_configure_option_button : wc_composite_params.i18n_select_option_button;
						options_data[ option_data_index ].option_button_label  = option_data.is_configurable ? wc_composite_params.i18n_configure_option_button_label.replace( '%s', option_data.option_title ) : wc_composite_params.i18n_select_option_button_label.replace( '%s', option_data.option_title );
						options_data[ option_data_index ].option_display_title = option_data.option_title;
						options_data[ option_data_index ].option_price_html    = self.get_formatted_option_price_html( options_data[ option_data_index ] );
					}

					// Dropdown template data.
					var dropdown_options_data = $.extend( true, [], options_data );

					for ( var dropdown_options_data_index = 0, dropdown_options_data_length = dropdown_options_data.length; dropdown_options_data_index < dropdown_options_data_length; dropdown_options_data_index++ ) {

						var dropdown_option_data = dropdown_options_data[ dropdown_options_data_index ];

						dropdown_options_data[ dropdown_options_data_index ].is_selected = dropdown_option_data.is_selected && self.is_selected_product_valid();

						// Only append price if visible.
						if ( dropdown_option_data.option_price_html && self.has_options_style( 'dropdowns' ) ) {

							var i18n_option_display_title_string = 'relative' === price_format ? wc_composite_params.i18n_dropdown_title_relative_price : wc_composite_params.i18n_dropdown_title_price;

							dropdown_options_data[ dropdown_options_data_index ].option_display_title = this.is_lazy_load_pending ? wc_composite_params.i18n_lazy_loading_options : i18n_option_display_title_string.replace( '%t', dropdown_option_data.option_display_title ).replace( '%p', dropdown_option_data.option_price_html );
						}
					}

					var show_empty_option     = false,
						show_switching_option = false,
						empty_option_disabled = false,
						empty_option_template;

					view.has_invalid_empty_option = false;

					// Always add an empty option when there are no valid options to select - necessary to allow resetting an existing invalid selection.
					if ( active_options.length === 0 ) {

						show_empty_option     = true;
						empty_option_template = 'i18n_no_options';

					} else {

						empty_option_template = self.is_optional() ? 'i18n_no_option' : 'i18n_select_option';

						if ( self.maybe_is_optional() ) {

							show_empty_option = true;

							if ( false === self.is_selected_product_valid() ) {
								show_switching_option = true;
							}

							if ( false === self.is_optional() ) {

								if ( '' === selected_product ) {
									show_switching_option = true;
								}

								empty_option_disabled = true;
								empty_option_template = 'i18n_no_option';
							}

						} else if ( false === self.is_static() && self.show_placeholder_option() ) {
							show_empty_option = true;
						} else if ( '' === selected_product && false === self.show_placeholder_option() ) {
							show_empty_option = true;
							view.has_invalid_empty_option = true;
						} else if ( false === self.is_selected_product_valid() && false === self.show_placeholder_option() ) {
							show_switching_option = true;
							view.has_invalid_empty_option = true;
						}
					}

					if ( show_empty_option ) {

						var empty_option_data = {
							option_id:            '',
							option_display_title: view.get_empty_option_title( empty_option_template ),
							is_disabled:          empty_option_disabled,
							is_hidden:            empty_option_disabled && self.hide_disabled_products(),
							is_selected:          selected_product === '' && false === show_switching_option
						};

						if ( 'relative' === price_format && self.has_options_style( 'dropdowns' ) && self.maybe_is_optional() ) {

							empty_option_data.option_price_html = '';
							empty_option_data.option_price_data = {
								price:             0.0,
								regular_price:     0.0,
								max_price:         0.0,
								max_regular_price: 0.0,
								min_qty:           1,
								discount:          ''
							};

							var empty_option_price_html = self.get_formatted_option_price_html( empty_option_data );

							if ( empty_option_price_html ) {
								empty_option_data.option_display_title = wc_composite_params.i18n_dropdown_title_relative_price.replace( '%t', empty_option_data.option_display_title ).replace( '%p', empty_option_price_html );
							}
						}

						if ( this.is_lazy_load_pending ) {
							empty_option_data.option_display_title = wc_composite_params.i18n_lazy_loading_options;
						}

						dropdown_options_data.unshift( empty_option_data );
					}

					if ( show_switching_option ) {
						dropdown_options_data.unshift( {
							option_id:            '',
							option_display_title: view.get_empty_option_title( 'i18n_select_option' ),
							is_disabled:          false,
							is_hidden:            false,
							is_selected:          false
						} );
					}

					// Render Dropdown template.
					view.changes.dropdown.changed = true;
					view.changes.dropdown.to      = view.templates.dropdown( dropdown_options_data );

					if ( false === dropdown_only ) {

						// Thumbnails template.
						if ( self.has_options_style( 'thumbnails' ) ) {

							var thumbnail_options_data = _.where( options_data, { is_in_view: true } ),
								thumbnail_columns      = view.get_columns(),
								thumbnail_loop         = 0;

							if ( thumbnail_options_data.length > 0 ) {

								for ( var thumbnail_option_data_index = 0, thumbnail_options_data_length = thumbnail_options_data.length; thumbnail_option_data_index < thumbnail_options_data_length; thumbnail_option_data_index++ ) {

									var thumbnail_option_data = thumbnail_options_data[ thumbnail_option_data_index ];

									thumbnail_options_data[ thumbnail_option_data_index ].outer_classes  = thumbnail_option_data.is_hidden ? 'hidden' : '';
									thumbnail_options_data[ thumbnail_option_data_index ].inner_classes  = thumbnail_option_data.is_disabled ? 'disabled' : '';
									thumbnail_options_data[ thumbnail_option_data_index ].inner_classes += thumbnail_option_data.option_id === selected_product ? ' selected' : '';
									thumbnail_options_data[ thumbnail_option_data_index ].inner_classes += thumbnail_option_data.is_appended ? ' appended' : '';

									if ( false === thumbnail_option_data.is_hidden ) {

										thumbnail_loop++;

										if ( ( ( thumbnail_loop - 1 ) % thumbnail_columns ) == 0 || thumbnail_columns == 1 ) {
											thumbnail_options_data[ thumbnail_option_data_index ].outer_classes += ' first';
										}

										if ( thumbnail_loop % thumbnail_columns == 0 ) {
											thumbnail_options_data[ thumbnail_option_data_index ].outer_classes += ' last';
										}
									}
								}
							}

							if ( this.is_lazy_load_pending ) {
								thumbnail_options_data.is_lazy_loading = true;
							}

							// Render Thumbnails template.
							var new_template_html = view.templates.thumbnails( thumbnail_options_data );

							// Ignore 'selected' class changes in comparison.
							if ( new_template_html.replace( / selected/g, '' ) !== view.changes.thumbnails.to.replace( / selected/g, '' ) ) {
								view.changes.thumbnails.changed = true;
								view.changes.thumbnails.to      = new_template_html;
							} else {
								composite.console_log( 'debug:views', '...skipped!' );
							}

						// Radio buttons template.
						} else if ( self.has_options_style( 'radios' ) ) {

							var radio_options_data  = _.where( options_data, { is_in_view: true } ),
								show_empty_radio    = false,
								disable_empty_radio = self.maybe_is_optional() && false === self.is_optional(),
								hide_empty_radio    = disable_empty_radio && self.hide_disabled_products();

							if ( self.maybe_is_optional() ) {
								show_empty_radio = true;
							} else if ( false === self.is_static() && self.show_placeholder_option() ) {
								show_empty_radio = true;
								hide_empty_radio = true;
							}

							if ( show_empty_radio ) {

								var empty_radio_data = {
									option_id:             '',
									option_display_title:  wc_composite_params.i18n_no_option.replace( '%s', self.get_title() ),
									is_disabled:           disable_empty_radio,
									is_hidden:             hide_empty_radio,
									is_selected:           selected_product === ''
								};

								if ( 'relative' === price_format && self.maybe_is_optional() ) {

									empty_radio_data.option_price_html = '';
									empty_radio_data.option_price_data = {
										price:             0.0,
										regular_price:     0.0,
										max_price:         0.0,
										max_regular_price: 0.0,
										min_qty:           1,
										discount:          ''
									};

									var empty_radio_price_html = self.get_formatted_option_price_html( empty_radio_data );

									if ( empty_radio_price_html ) {
										empty_radio_data.option_price_html = empty_radio_price_html;
									}
								}

								radio_options_data.unshift( empty_radio_data );
							}

							if ( radio_options_data.length > 0 ) {

								for ( var radio_option_data_index = 0, radio_options_data_length = radio_options_data.length; radio_option_data_index < radio_options_data_length; radio_option_data_index++ ) {

									var radio_option_data = radio_options_data[ radio_option_data_index ];

									radio_options_data[ radio_option_data_index ].outer_classes  = radio_option_data.is_hidden ? 'hidden' : '';
									radio_options_data[ radio_option_data_index ].inner_classes  = radio_option_data.is_disabled ? 'disabled' : '';
									radio_options_data[ radio_option_data_index ].inner_classes += radio_option_data.option_id === selected_product ? ' selected' : '';

									radio_options_data[ radio_option_data_index ].option_suffix   = radio_option_data.option_id === '' ? '0' : radio_option_data.option_id;
									radio_options_data[ radio_option_data_index ].option_group_id = self.component_id;
								}
							}

							if ( this.is_lazy_load_pending ) {
								radio_options_data.is_lazy_loading = true;
							}

							// Render Radio buttons template.
							view.changes.radios.changed = true;
							view.changes.radios.to      = view.templates.radios( radio_options_data );
						}

						/*
						 * Hide or grey-out inactive variations.
						 */

						if ( 'variable' === self.get_selected_product_type() ) {

							var compatible_variation_data = self.component_selection_model.get_active_variations_data();

							view.changes.variations.changed = ! _.isEqual( view.changes.variations.to, compatible_variation_data ) && view.changes.variations.to.length > 0;
							view.changes.variations.to      = compatible_variation_data;
						}
					}

					change_what = _.keys( _.pick( view.changes, function( value ) { return value.changed; } ) );

					if ( change_what.length > 0 ) {

						// Run 'component_options_state_render' action - @see WC_CP_Composite_Dispatcher class.
						composite.actions.do_action( 'component_options_state_render', [ self, change_what ] );

						if ( view.changes.dropdown.changed ) {
							self.$component_options_select.html( view.changes.dropdown.to );
						}

						if ( view.changes.thumbnails.changed ) {

							self.$component_thumbnail_options.html( view.changes.thumbnails.to );

							if ( 'reload' === view.update_action ) {
								self.$component_selections.removeClass( 'refresh_component_options' );
							}
						}

						if ( view.changes.radios.changed ) {
							self.$component_radio_button_options.html( view.changes.radios.to );
						}

						if ( view.changes.variations.changed ) {

							// If the variable product hasn't been rendered yet, don't proceed. Variations data will be updated by 'WC_CP_Component:init_scripts'.
							if ( self.component_selection_view.get_rendered_product() === selected_product ) {

								composite.console_log( 'debug:views', '\nVariations data in "' + self.get_title() + '" has changed. Reinitializing variations view...' );

								// Update WC variations model state.
								self.$component_summary_content.data( 'product_variations', self.component_selection_model.get_active_variations_data() );
								// Update the variations script.
								composite.debug_indent_incr();
								self.$component_summary_content.triggerHandler( 'reload_product_variations' );
								composite.debug_indent_decr();
							}
						}

						// Run 'component_options_state_rendered' action - @see WC_CP_Composite_Dispatcher class.
						composite.actions.do_action( 'component_options_state_rendered', [ self, change_what ] );
					}
				},

				/**
				 * Update options after collecting user input.
				 */
				update_options: function( params, update_action, is_background_request ) {

					is_background_request = typeof( is_background_request ) === 'undefined' ? false : is_background_request;

					this.update_action = update_action;

					if ( 'reload' === update_action ) {
						self.$component_selections.addClass( 'refresh_component_options' );
					}

					this.load_height = self.$component_options.get( 0 ).getBoundingClientRect().height;

					if ( typeof this.load_height === 'undefined' ) {
						this.load_height = self.$component_options.outerHeight();
					}

					// Lock height.
					self.$component_options.css( 'height', this.load_height );

					setTimeout( function() {
						self.component_options_model.request_options( params, update_action );
					}, 200 );

					// Run 'component_options_update_requested' action - @see WC_CP_Composite_Dispatcher class.
					composite.actions.do_action( 'component_options_update_requested', [ self, params, update_action, is_background_request ] );
				},

				/**
				 * Update view after appending/reloading component options.
				 */
				updated_options: function() {

					if ( false === this.$blocked_element ) {
						return false;
					}

					if ( 'append' === this.update_action && self.hide_disabled_products() ) {

						if ( self.$component_thumbnail_options.find( '.appended:not(.disabled)' ).length < self.get_results_per_page() ) {

							var retry = this.model.get( 'page' ) < this.model.get( 'pages' );

							if ( retry && this.append_results_retry_count > 10 ) {
								if ( false === window.confirm( wc_composite_params.i18n_reload_threshold_exceeded.replace( '%s', self.get_title() ) ) ) {
									retry = false;
								}
							}

							if ( retry ) {
								this.append_results_retry_count++;
								this.model.request_options( { page: this.model.get( 'page' ) + 1 }, 'append' );
								return false;
							} else {
								this.append_results_retry_count = 0;
							}
						}
					}

					if ( ( self.is_current() || 'single' === composite.settings.layout ) && this.is_lazy_load_pending ) {
						this.render();
					}

					// Preload images before proceeding.
					var $thumbnails_container = self.$component_thumbnail_options.find( '.component_option_thumbnails_container' ),
						$thumbnail_images     = $thumbnails_container.find( '.component_option_thumbnail_container:not(.hidden) img' ),
						view                  = this,
						task                  = new wc_cp_classes.WC_CP_Async_Task( function() {

						var wait       = false,
							async_task = this;

						if ( $thumbnail_images.length > 0 && $thumbnails_container.is( ':visible' ) ) {
							$thumbnail_images.each( function() {

								var $img = $( this );

								if ( $img.height() === 0 && false === $img.get( 0 ).complete && async_task.get_async_time() < 20000 ) {
									wait = true;
									return false;
								}
							} );
						}

						if ( ! wait ) {
							this.done();
						}

					}, 50 );

					task.complete( function() {
						view.animate_options();
					} );
				},

				/**
				 * Allows filtering animation durations.
				 */
				get_animation_duration: function( context ) {
					// Pass through 'component_component_options_animation_duration' filter - @see WC_CP_Filters_Manager class.
					return composite.filters.apply_filters( 'component_component_options_animation_duration', [ 250, context, self ] );
				},

				/**
				 * Animate view when reloading/appending options.
				 */
				animate_options: function() {

					if ( 'append' === this.update_action ) {
						appended = self.$component_thumbnail_options.find( '.appended' );
						appended.removeClass( 'appended' );
					}

					var view           = this,
						new_height     = self.$component_options_inner.outerHeight( true ),
						animate_height = false;

					if ( Math.abs( new_height - view.load_height ) > 1 ) {
						animate_height = true;
					} else {
						self.$component_options.css( 'height', 'auto' );
					}

					var appended = {};

					// Animate component options container.
					if ( animate_height ) {

						composite.console_log( 'debug:animations', 'Starting component options height animation...' );

						self.$component_options.wc_cp_animate_height( new_height, view.get_animation_duration( this.update_action ), { complete: function() {

							self.$component_options.css( { height: 'auto' } );

							composite.console_log( 'debug:animations', 'Ended component options height animation.' );

							setTimeout( function() {
								view.unblock();
							}, 100 );

						} } );

					} else {
						setTimeout( function() {
							view.unblock();
						}, 250 );
					}

					// Run 'component_options_updated' action - @see WC_CP_Actions_Dispatcher class.
					composite.actions.do_action( 'component_options_updated', [ self ] );
				},

				/**
				 * Unblock blocked view element.
				 */
				unblock: function() {

					self.$component_selections.removeClass( 'refresh_component_options' );

					composite.unblock( this.$blocked_element );

					this.$blocked_element = false;

					// Remove status message.
					composite.data_model.remove_status_message( self.component_id );

					this.update_action = '';

					composite.actions.do_action( 'component_options_refreshed', [ self ] );
				},

				/**
				 * True if the view is updating.
				 */
				is_updating: function() {

					return false !== this.$blocked_element;
				}

			} );

			var obj = new View( opts );
			return obj;
		};



		/**
		 * Updates the model data from UI interactions and listens to the component selection model for updated content.
		 */
		this.Component_Selection_View = function( component, opts ) {

			var self = component;
			var	View = Backbone.View.extend( {

				templates:                    {},

				$relocation_origin:           false,
				relocated:                    false,

				relocating:                   false,
				relocating_to_origin:         false,
				$relocation_target:           false,
				$relocation_reference:        false,
				load_height:                  0,

				render_addons_totals_timer:   false,
				flushing_component_options:   false,
				blocked:                      false,

				rendered_product:             '',

				initialize: function() {

					this.templates = {
						selection_title:      wp.template( 'wc_cp_component_selection_title' ),
						selection_title_html: ''
					};

					/**
					 * Update model on changing a component option.
					 */
					self.$el.on( 'change', '.component_options select.component_options_select', { view: this }, this.option_changed );

					/**
					 * Update model data when a new variation is selected.
					 */
					self.$el.on( 'woocommerce_variation_has_changed', { view: this }, function( event ) {

						var variation_id   = self.$component_summary_content.find( '.single_variation_wrap .variations_button input.variation_id' ).val(),
							$variations    = self.$component_summary_content.find( '.variations' ),
							variation_data = {
								meta_data: []
							};

						variation_id = variation_id || '';

						if ( variation_id && $variations.length > 0 ) {
							variation_data.meta_data = wc_cp_get_variation_data( $variations, false );
						}

						// Update model.
						event.data.view.model.update_selected_variation( variation_id, variation_data );

						if ( self.$component_variations_reset_wrapper ) {
							if ( event.data.view.model.get( 'selected_variation' ) ) {
								self.$component_variations_reset_wrapper.slideDown( 200 );
							} else {
								self.$component_variations_reset_wrapper.slideUp( 200 );
							}
						}

						if ( self.step_validation_model.get( 'is_in_stock' ) ) {
							// Ensure min/max constraints are always honored.
							self.$component_quantity.trigger( 'change' );
						}

						// Remove images class from composited_product_images div in order to avoid styling issues.
						if ( ! self.has_wc_core_gallery_class ) {
							self.$component_summary_content.find( '.composited_product_images' ).removeClass( 'images' );
						}
					} );

					/**
					 * Add 'images' class to composited_product_images div when initiating a variation selection change.
					 */
					self.$el.on( 'woocommerce_variation_select_change', function() {

						// Required by the variations script to flip images.
						if ( ! self.has_wc_core_gallery_class ) {
							self.$component_summary.find( '.composited_product_images' ).addClass( 'images' );
						}

						// Reset component prices.
						self.component_selection_model.set_price( 0.0 );
						self.component_selection_model.set_regular_price( 0.0 );
						self.component_selection_model.set_tax_ratios( false );

						// Reset availability.
						self.component_selection_model.set_stock_status( '' );

					} );

					/**
					 * Update composite totals and form inputs when a new variation is selected.
					 */
					self.$el.on( 'found_variation', function( event, variation ) {

						// Update component prices.
						self.component_selection_model.set_price( variation.price );
						self.component_selection_model.set_regular_price( variation.regular_price );
						self.component_selection_model.set_tax_ratios( variation.tax_ratios );

						// Update availability.
						if ( ! variation.is_in_stock ) {
							self.component_selection_model.set_stock_status( 'out-of-stock' );
						}

					} );

					/**
					 * Update model upon changing quantities.
					 */
					self.$el.on( 'input change', '.component_wrap input.qty', { view: this }, function( event ) {

						var view     = event.data.view,
							keep_in_range  = 'change' === event.type && ( wc_composite_params.force_min_max_qty_input === 'yes' || view.is_blocked() ),
							quantity = view.get_updated_quantity( keep_in_range );

						if ( keep_in_range ) {
							$( this ).val( quantity );
						}

						self.component_selection_model.update_selected_quantity( quantity );
					} );

					/**
					 * Initialize prettyPhoto/phptoSwipe script when component selection scripts are initialized.
					 */
					self.$el.on( 'wc-composite-component-loaded', function() {

						// Init PhotoSwipe if present.
						if ( 'yes' === wc_composite_params.photoswipe_enabled && typeof PhotoSwipe !== 'undefined' ) {

							var $product_image = self.$component_summary_content.find( '.composited_product_images' );

							if ( $.fn.wc_product_gallery ) {
								$product_image.wc_product_gallery( { zoom_enabled: false, flexslider_enabled: false } );
							} else {
								composite.console_log( 'warning', 'Failed to initialize PhotoSwipe for composited product images. Your theme declares PhotoSwipe support, but function \'$.fn.wc_product_gallery\' is undefined.' );
							}

							var $placeholder = $product_image.find( 'a.placeholder_image' );

							if ( $placeholder.length > 0 ) {
								$placeholder.on( 'click', function() {
									return false;
								} );
							}

						// Otherwise, fall back to prettyPhoto.
						} else if ( $.isFunction( $.fn.prettyPhoto ) ) {

							var $prettyphoto_images = self.$component_summary_content.find( 'a[data-rel^="prettyPhoto"]' ),
								$active_images      = $prettyphoto_images.not( '.placeholder_image' ),
								$inactive_images    = $prettyphoto_images.filter( '.placeholder_image' );

							if ( $active_images.length > 0 ) {
								$active_images.prettyPhoto( {
									hook: 'data-rel',
									social_tools: false,
									theme: 'pp_woocommerce',
									horizontal_padding: 20,
									opacity: 0.8,
									deeplinking: false
								} );
							}

							if ( $inactive_images.length > 0 ) {
								$inactive_images.on( 'click', function() {
									return false;
								} );
							}
						}
					} );

					/**
					 * On clicking the clear options button.
					 */
					self.$el.on( 'click', '.clear_component_options', function() {

						if ( 'yes' === wc_composite_params.accessible_focus_enabled && self.$step_title_aria ) {
							self.$step_title_aria.trigger( 'focus' );
						}

						if ( $( this ).hasClass( 'reset_component_options' ) ) {
							return false;
						}

						var empty_option = self.$component_options_select.find( 'option[value=""]' );

						if ( empty_option.length > 0 && false === empty_option.first().prop( 'disabled' ) ) {
							self.$component_options_select.val( '' ).trigger( 'change' );
						}

						return false;
					} );

					/**
					 * On clicking the reset options button.
					 */
					self.$el.on( 'click', '.reset_component_options', function() {

						var empty_option = self.$component_options_select.find( 'option[value=""]' );

						self.unblock_step_inputs();

						self.set_active();

						if ( empty_option.length > 0 && false === empty_option.first().prop( 'disabled' ) ) {
							self.$component_options_select.val( '' ).trigger( 'change' );
						}

						self.block_next_steps();

						return false;
					} );

					/**
					 * Update model upon changing addons selections.
					 */
					self.$el.on( 'updated_addons', { view: this }, this.updated_addons_handler );

					/**
					 * Update composite totals when a new NYP price is entered.
					 */
					self.$el.on( 'woocommerce-nyp-updated-item', this.updated_nyp_handler );

					/*
					 * When entering a component with relocated selection details,
					 * reset the position of the relocated container if the 'relocated_content_reset_on_return' flag is set to 'yes'.
					 */
					if ( wc_composite_params.relocated_content_reset_on_return === 'yes' ) {
						composite.actions.add_action( 'active_step_transition', this.active_step_transition_handler, 100, this );
					}

					/**
					 * Update "Clear selection" button in view.
					 */
					composite.actions.add_action( 'component_options_state_changed_' + self.step_id, this.options_state_changed_handler, 100, this );

					/*
					 * When rendering a component with relocated selection details,
					 * back up and put back the relocated container after rendering the JS template contents.
					 */
					composite.actions.add_action( 'component_options_state_render', this.options_state_render_handler, 10, this );
					composite.actions.add_action( 'component_options_state_rendered', this.options_state_rendered_handler, 10, this );

					// Auto-select variable product attributes.
					composite.actions.add_action( 'component_options_state_rendered', this.options_state_rendered_autoselect_attributes, 30, this );

					/**
					 * Update the selection title when the product selection view content is changed.
					 */
					composite.actions.add_action( 'component_selection_details_updated_' + self.step_id, this.refresh_selection_title, 10, this );

					/**
					 * Render selection details into view.
					 */
					composite.actions.add_action( 'component_selection_changed', this.component_selection_changed_handler, 100, this );

					if ( self.maybe_autotransition() ) {
						composite.actions.add_action( 'active_step_transition_end', this.active_step_transition_end_handler, 10, this );
					}

					this.listenTo( this.model, 'selected_product_data_load_error', this.selection_data_load_error );

					/**
					 * Reset relocated content before flushing outdated component options.
					 */
					this.listenTo( self.component_options_model, 'component_options_data_loaded', this.component_options_flush_handler );

					/**
					 * Update the selection title when the quantity is changed.
					 */
					composite.actions.add_action( 'component_quantity_changed', this.quantity_changed_handler, 100, this );

					/**
					 * Update the addons totals when addons change.
					 */
					composite.actions.add_action( 'component_addons_changed', this.addons_changed_handler, 100, this );

					/**
					 * Update the addons totals when a new variation selection is made.
					 */
					composite.actions.add_action( 'component_selection_changed', this.addons_changed_handler, 100, this );

				},

				/**
				 * Get the updated quantity.
				 */
				get_updated_quantity: function( keep_in_range ) {

					var $input = self.$component_quantity,
						qty    = parseFloat( $input.val() ),
						min    = parseFloat( $input.attr( 'min' ) ),
						max    = parseFloat( $input.attr( 'max' ) );

					if ( keep_in_range ) {
						if ( min >= 0 && ( qty < min || isNaN( qty ) ) ) {
							qty = min;
						}

						if ( max > 0 && qty > max ) {
							qty = max;
						}
					}

					if ( isNaN( qty ) ) {
						qty = 0;
					}

					return parseInt( qty, 10 );
				},

				/**
				 * Get the currently rendered product.
				 */
				get_rendered_product: function() {
					return this.rendered_product;
				},

				/**
				 * Allows filtering animation durations.
				 */
				get_animation_duration: function( open_or_close ) {

					var duration = ( self.is_current() || 'single' === composite.settings.layout ) ? 220 : 0;

					open_or_close = open_or_close !== 'open' && open_or_close !== 'close' ? 'open' : open_or_close;

					// Pass through 'component_animation_duration' filter - @see WC_CP_Filters_Manager class.
					return composite.filters.apply_filters( 'component_selection_change_animation_duration', [ duration, open_or_close, self ] );
				},

				/**
				 * Resets the position of the relocated container when the active step changes.
				 */
				active_step_transition_handler: function( step ) {

					if ( self.step_id === step.step_id ) {
						if ( this.is_relocated() ) {
							this.reset_relocated_content();
						}
					}
				},

				/**
				 * Redraw the selection title.
				 */
				options_state_changed_handler: function() {

					if ( ! composite.is_initialized ) {
						return;
					}

					if ( this.get_rendered_product() !== this.model.get( 'selected_product' ) ) {
						return;
					}

					this.update_selection_title();
				},

				/**
				 * Backup the relocated container before rendering the thumbnails view.
				 */
				options_state_render_handler: function( step, changed ) {

					if ( self.step_id === step.step_id ) {

						if ( _.includes( changed, 'thumbnails' ) && this.is_relocated() ) {

							// Save component content.
							self.$el.append( this.$relocation_target.hide() );
						}
					}
				},

				/**
				 * Put back the relocated container after rendering thumbnails view.
				 */
				options_state_rendered_handler: function( step, changed ) {

					if ( self.step_id === step.step_id ) {

						if ( _.includes( changed, 'thumbnails' ) && this.is_relocated() ) {

							var relocation_params = this.get_new_relocation_data();

							if ( relocation_params.relocate ) {

								this.$relocation_reference = relocation_params.reference;

								this.$relocation_reference.after( this.$relocation_target );

								this.$relocation_target.show();
							}
						}
					}
				},

				/**
				 * Auto-select variable product attributes when variation dropdowns need to be re-rendered.
				 */
				options_state_rendered_autoselect_attributes: function( step, changed ) {

					if ( self.step_id === step.step_id && step.autoselect_attributes() ) {

						if ( _.includes( changed, 'variations' ) ) {

							if ( this.autoselect_attributes() ) {
								self.$component_summary_content.find( '.variations select' ).last().trigger( 'change' );
							}
						}
					}
				},

				/**
				 * Auto-select variable product attributes.
				 */
				autoselect_attributes: function() {

					var active_variations    = _.where( self.component_selection_model.get_active_variations_data(), { variation_is_active: true } ),
						attribute_option_set = false,
						attribute_name       = '',
						attribute_values     = {};

					for ( var i = 0, i_max = active_variations.length; i < i_max; i++ ) {

						if ( ! active_variations.hasOwnProperty( i ) ) {
							continue;
						}

						var variation_attributes = active_variations[ i ].attributes;

						for ( attribute_name in variation_attributes ) {

							if ( ! variation_attributes.hasOwnProperty( attribute_name ) ) {
								continue;
							}

							if ( '' === variation_attributes[ attribute_name ] ) {

								attribute_values[ attribute_name ] = '';

							} else {

								if ( '' !== attribute_values[ attribute_name ] ) {

									if ( typeof attribute_values[ attribute_name ] === 'undefined' ) {
										attribute_values[ attribute_name ] = [];

									}

									attribute_values[ attribute_name ].push( variation_attributes[ attribute_name ] );
									attribute_values[ attribute_name ] = _.uniq( attribute_values[ attribute_name ] );
								}
							}
						}
					}

					for ( attribute_name in attribute_values ) {

						if ( ! attribute_values.hasOwnProperty( attribute_name ) ) {
							continue;
						}

						if ( '' === attribute_values[ attribute_name ] ) {
							continue;
						}

						if ( 1 === attribute_values[ attribute_name ].length ) {

							var attribute_value         = attribute_values[ attribute_name ].pop(),
								attribute_escaped_value = attribute_value.replace( /"/g, '\\\"' );

							var $attribute_field  = self.$component_summary_content.find( '.variations select[data-attribute_name="' + attribute_name + '"]' ),
								$attribute_option = $attribute_field.val() ? false : $attribute_field.find( 'option[value="' + attribute_escaped_value + '"]' );

							if ( $attribute_option ) {
								attribute_option_set = true;
								$attribute_field.val( attribute_value );
							}
						}
					}

					return attribute_option_set;
				},

				/**
				 * Updates the selection title and the selected product addons when the quantity is changed.
				 */
				quantity_changed_handler: function( step ) {

					if ( step.step_id === self.step_id ) {

						this.update_selection_title( this.model );

						var addons_data = this.get_updated_addons_data();

						if ( addons_data ) {
							self.component_selection_model.update_selected_addons( addons_data.data, addons_data.raw_price, addons_data.raw_regular_price );
						}
					}
				},

				/**
				 * Updates the addons totals when addons change.
				 */
				addons_changed_handler: function( step ) {

					if ( step.step_id !== self.step_id ) {
						return false;
					}

					if ( ! composite.is_initialized ) {
						return false;
					}

					var view = this;

					clearTimeout( view.render_addons_totals_timer );
					view.render_addons_totals_timer = setTimeout( function() {
						view.render_addons_totals();
					}, 10 );
				},

				/**
				 * Updates the model upon changing addons selections.
				 */
				get_updated_addons_data: function() {

					if ( ! self.has_addons() ) {
						return false;
					}

					var addons_raw_price         = 0,
						addons_raw_regular_price = 0,
						addons_data              = self.$component_addons_totals ? self.$component_addons_totals.data( 'price_data' ) : [],
						quantity                 = self.get_selected_quantity(),
						tax_ratios               = composite.data_model.price_data.price_tax_ratios[ self.component_id ];

					for ( var addon_data_index = 0, addons_data_length = addons_data.length; addon_data_index < addons_data_length; addon_data_index++ ) {

						var addon = addons_data[ addon_data_index ];

						if ( addon.is_custom_price ) {

							var addon_raw_price = 0.0,
								tax_ratio_incl  = tax_ratios && typeof( tax_ratios.incl ) !== 'undefined' ? Number( tax_ratios.incl ) : false,
								tax_ratio_excl  = tax_ratios && typeof( tax_ratios.excl ) !== 'undefined' ? Number( tax_ratios.excl ) : false;

							if ( 'incl' === wc_composite_params.tax_display_shop && 'no' === wc_composite_params.prices_include_tax ) {
								addon_raw_price = addon.cost_raw / ( tax_ratio_incl ? tax_ratio_incl : 1 );
							} else if ( 'excl' === wc_composite_params.tax_display_shop && 'yes' === wc_composite_params.prices_include_tax ) {
								addon_raw_price = addon.cost_raw / ( tax_ratio_excl ? tax_ratio_excl : 1 );
							} else {
								addon_raw_price = addon.cost_raw;
							}

							// Custom Price fields always behave as Flat-Fee.
							addon_raw_price = quantity ? addon_raw_price / quantity : 0;

							addons_raw_regular_price += addon_raw_price;
							addons_raw_price         += addon_raw_price;

						} else {

							if ( 'quantity_based' === addon.price_type ) {
								addons_raw_regular_price += addon.cost_raw_pu;
								addons_raw_price         += addon.cost_raw_pu;
							} else if ( 'flat_fee' === addon.price_type ) {
								addons_raw_regular_price += quantity ? addon.cost_raw / quantity : 0;
								addons_raw_price         += quantity ? addon.cost_raw / quantity : 0;
							} else if ( 'percentage_based' === addon.price_type ) {
								addons_raw_regular_price += addon.cost_raw_pct * composite.data_model.price_data.regular_prices[ self.component_id ];
								addons_raw_price         += addon.cost_raw_pct * composite.data_model.price_data.prices[ self.component_id ];
							}
						}

						addons_data[ addon_data_index ].qty               = quantity;
						addons_data[ addon_data_index ].raw_price         = addons_raw_price;
						addons_data[ addon_data_index ].raw_regular_price = addons_raw_regular_price;
					}

					addons_raw_price         = addons_raw_price || 0.0;
					addons_raw_regular_price = addons_raw_regular_price || addons_raw_price;

					return {
						data: addons_data,
						raw_price: addons_raw_price,
						raw_regular_price: addons_raw_regular_price
					};
				},

				/**
				 * Handles addons selection changes.
				 */
				updated_addons_handler: function( event ) {

					if ( $( event.target ).hasClass( 'bundled_item_cart_content' ) ) {
						return;
					}

					if ( ! self.has_addons() ) {
						return;
					}

					var view        = event.data.view,
						addons_data = view.get_updated_addons_data();

					// Always restore totals state because PAO empties it before the 'updated_addons' event.
					if ( self.component_addons_totals_html ) {
						self.$component_addons_totals.html( self.component_addons_totals_html );
					}

					if ( addons_data ) {
						self.component_selection_model.update_selected_addons( addons_data.data, addons_data.raw_price, addons_data.raw_regular_price );
					}

					event.stopPropagation();
				},

				/**
				 * Renders a component subtotal.
				 */
				render_addons_totals: function() {

					if ( ! self.has_addons() ) {
						return;
					}

					var price_data   = composite.data_model.price_data,
						tax_ratios   = composite.data_model.price_data.price_tax_ratios[ self.component_id ],
						addons_price = price_data.addons_prices[ self.component_id ];

					addons_price = addons_price || 0.0;

					if ( self.show_addons_totals ) {

						if ( self.passes_validation() ) {

							var qty           = self.get_selected_quantity(),
								addons_totals = composite.data_model.get_taxed_totals( addons_price, addons_price, tax_ratios, qty );

							if ( addons_totals.price > 0 ) {

								var price              = Number( price_data.prices[ self.component_id ] ),
									total              = price + Number( addons_price ),
									totals             = composite.data_model.get_taxed_totals( total, total, tax_ratios, qty ),
									price_html         = wc_cp_price_format( totals.price ),
									price_html_suffix  = composite.composite_price_view.get_formatted_price_suffix( totals ),
									addons_totals_html = '<span class="price">' + '<span class="subtotal">' + wc_composite_params.i18n_subtotal + '</span>' + price_html + price_html_suffix + '</span>';

								// Save for later use.
								self.component_addons_totals_html = addons_totals_html;

								self.$component_addons_totals.html( addons_totals_html ).slideDown( 200 );

							} else {
								self.$component_addons_totals.slideUp( 200 );
							}

						} else {
							self.$component_addons_totals.slideUp( 200 );
						}
					}
				},

				/**
				 * Updates the composite data model upon changing addons selections.
				 */
				updated_nyp_handler: function() {

					if ( ! self.is_nyp() ) {
						return;
					}

					var $nyp      = self.$component_summary_content.find( '.nyp' ),
						nyp_price = $nyp.length > 0 ? Number( $nyp.data( 'price' ) ) : 0;

					self.component_selection_model.update_nyp( nyp_price );
				},

				/**
				 * Refreshes the selection title every time it changes.
				 */
				refresh_selection_title: function() {

					// Clear selection title template to resolve an issue with rendering after clearing and selecting the same product.
					this.templates.selection_title_html = '';
					// Update title.
					this.update_selection_title( this.model );
				},

				/**
				 * Renders the selected product title and the "Clear selection" button.
				 */
				update_selection_title: function( model ) {

					var view = this;

					model = typeof( model ) === 'undefined' ? view.model : model;

					if ( self.get_selected_product( false ) > 0 ) {
						composite.console_log( 'debug:views', '\nUpdating "' + self.get_title() + '" selection title...' );
						view.update_selection_title_task( model );
					}
				},

				/**
				 * Gets the selected product title and appends quantity data.
				 */
				get_updated_selection_title: function( model ) {

					var selection_qty            = parseInt( model.get( 'selected_quantity' ), 10 ),
						selection_title          = self.get_selected_product_title( false ),
						selection_qty_string     = selection_qty > 1 ? wc_composite_params.i18n_qty_string.replace( '%s', selection_qty ) : '',
						selection_title_incl_qty = wc_composite_params.i18n_title_string.replace( '%t', selection_title ).replace( '%q', selection_qty_string ).replace( '%p', '' );

					return selection_title_incl_qty;
				},

				/**
				 * Renders the selected product title and the "Clear selection" button.
				 */
				update_selection_title_task: function( model ) {

					var $title_html = self.$component_summary_content.find( '.composited_product_title_wrapper' ),
						view        = this,
						data        = {
							tag:                  'h4',
							show_title:           'yes' === $title_html.data( 'show_title' ),
							show_selection_ui:    self.is_static() ? false : true,
							show_reset_ui:        ( self.show_placeholder_option() && false === self.maybe_is_optional() ) || self.is_optional() || false === self.is_selected_product_valid(),
							selection_title:      view.get_updated_selection_title( model ),
							selection_title_aria: wc_composite_params.i18n_selection_title_aria.replace( '%s', self.get_selected_product_title( false ) ),
							selection_data:       model.get_product_data()
						};

					var new_template_html = view.templates.selection_title( data );

					if ( new_template_html !== view.templates.selection_title_html ) {
						view.templates.selection_title_html = new_template_html;
						$title_html.html( new_template_html );
					}

					// Remove clearing button if the loaded product is invalid and the current selection can't be reset.
					if ( 'invalid-product' === self.get_selected_product_type() ) {
						var empty_option = self.$component_options_select.find( 'option[value=""]' );
						if ( empty_option.length === 0 || empty_option.first().prop( 'disabled' ) ) {
							self.$component_summary_content.find( '.clear_component_options' ).remove();
						}
					}
				},

				/**
				 * Blocks the composite form and adds a waiting ui cue in the working element.
				 */
				block: function() {

					this.blocked = true;
					composite.block( self.$component_options );
				},

				/**
				 * Unblocks the composite form and removes the waiting ui cue from the working element.
				 */
				unblock: function() {

					this.blocked = false;
					self.$component_content.removeClass( 'updating' );
					self.$component_thumbnail_options.find( '.loading' ).removeClass( 'loading' );

					composite.unblock( self.$component_options );
				},

				/**
				 * Whether the view is updating/blocked.
				 */
				is_blocked: function() {

					return this.blocked;
				},

				/**
				 * Collect component option change input.
				 */
				option_changed: function( event ) {

					var view                = event.data.view,
						selected_product_id = $( this ).val();

					view.set_option( selected_product_id );

					return false;
				},

				/**
				 * Update model on changing a component option.
				 */
				set_option: function( option_id ) {

					var view = this;

					// Exit if triggering 'change' for the existing selection.
					if ( self.get_selected_product( false ) === option_id ) {
						return false;
					}

					// Toggle thumbnail/radio selection state.
					if ( self.has_options_style( 'thumbnails' ) ) {
						self.$component_thumbnail_options.find( '.selected' ).removeClass( 'selected' );
						self.$component_thumbnail_options.find( '#component_option_thumbnail_' + option_id ).addClass( 'selected loading' );
					} else if ( self.has_options_style( 'radios' ) ) {
						var $selected = self.$component_radio_button_options.find( '.selected' );
						$selected.removeClass( 'selected' );
						$selected.find( 'input' ).prop( 'checked', false );
						self.$component_options.find( '#component_option_radio_button_' + ( option_id === '' ? '0' : option_id ) ).addClass( 'selected' ).find( 'input' ).prop( 'checked', true );
					}

					if ( option_id !== '' ) {

						// Block composite form + add waiting cues.
						this.block();

						// Add updating class to content.
						self.$component_content.addClass( 'updating' );

						setTimeout( function() {
							// Request product details from model and let the model update itself.
							view.model.update_selection( option_id );
						}, 120 );

					} else {

						// Handle selection resets within the view, but update the model data.
						view.model.update_selection( '' );
					}
				},

				/**
				 * Re-set current option.
				 */
				 selection_data_load_error: function() {

					var option_id = self.get_selected_product( false );

					self.$component_options_select.val( option_id ).trigger( 'change' );

					this.unblock();

					// Toggle thumbnail/radio selection state.
					if ( self.has_options_style( 'thumbnails' ) ) {
						self.$component_thumbnail_options.find( '.selected' ).removeClass( 'selected' );
						self.$component_thumbnail_options.find( '#component_option_thumbnail_' + option_id ).addClass( 'selected' );
					} else if ( self.has_options_style( 'radios' ) ) {
						var $selected = self.$component_radio_button_options.find( '.selected' );
						$selected.removeClass( 'selected' );
						$selected.find( 'input' ).prop( 'checked', false );
						self.$component_options.find( '#component_option_radio_button_' + ( option_id === '' ? '0' : option_id ) ).addClass( 'selected' ).find( 'input' ).prop( 'checked', true );
					}

					window.alert( wc_composite_params.i18n_selection_request_timeout );
				},

				/**
				 * Render the initial model state.
				 */
				render_default: function() {

					var view             = this,
						selected_product = this.model.get( 'selected_product' );

					this.rendered_product = selected_product;

					if ( selected_product ) {
						view.render_content();
					} else {
						view.reset_content();
					}
				},

				component_selection_changed_handler: function( step ) {

					if ( self.step_id !== step.step_id ) {
						return false;
					}

					if ( self.can_autotransition() ) {
						return false;
					}

					this.maybe_render();
				},

				active_step_transition_end_handler: function() {

					this.maybe_render();
				},

				maybe_render: function() {

					var selected_product = this.model.get( 'selected_product' );

					if ( ! composite.is_initialized ) {
						return false;
					}

					if ( this.get_rendered_product() === selected_product ) {
						return false;
					}

					this.render();
				},

				/**
				 * Update view with new selection details passed by model.
				 */
				render: function() {

					var view             = this,
						selected_product = this.model.get( 'selected_product' );

					this.rendered_product = selected_product;

					composite.console_log( 'debug:views', '\nPreparing "' + self.get_title() + '" selection view...' );

					view.prepare_relocation();

					if ( selected_product ) {

						if ( view.is_relocating() ) {

							if ( view.is_relocating_to_origin() ) {

								composite.console_log( 'debug:animations', 'Starting component content height animation...' );

								// Animate component content height to 0.
								// Then, reset relocation and update content.
								self.$component_content.wc_cp_animate_height( 0, view.get_animation_duration( 'close' ), { complete: function() {

									composite.console_log( 'debug:animations', 'Ended component content height animation.' );

									// Put content back to origin position.
									view.reset_relocated_content();
									// Render content.
									view.render_content();

								} } );

								view.load_height = 0;

							} else {

								var do_illusion_scroll = self.$component_content.offset().top < view.$relocation_reference.offset().top && false === self.$component_content.wc_cp_is_in_viewport( true );

								// Animate component content height to 0 while scrolling as much as its height (if needed).
								// Then, update content.
								if ( do_illusion_scroll ) {

									var illusion_scroll_to     = 0,
										illusion_scroll_offset = view.load_height;

									// Introduce async to hopefully do this between repaints and avoid flicker.
									setTimeout( function() {

										illusion_scroll_to = $wc_cp_window.scrollTop() - Math.round( illusion_scroll_offset );

										setTimeout( function() {

											// Scroll as much as the height offset...
											if ( ! composite.composite_viewport_scroller.is_scroll_anchoring_supported() ) {
												window.scroll( 0, illusion_scroll_to );
											}

											// while setting height to 0.
											self.$component_content.css( { height: 0 } );

											setTimeout( function() {

												// Render content.
												view.render_content();

											}, 10 );

										}, 50 );

									}, 50 );

								} else {

									composite.console_log( 'debug:animations', 'Starting component content height animation...' );

									self.$component_content.wc_cp_animate_height( 0, view.get_animation_duration( 'close' ), { complete: function() {

										composite.console_log( 'debug:animations', 'Ended component content height animation.' );

										// Render content.
										view.render_content();

									} } );
								}

								view.load_height = 0;
							}

						} else {

							// Lock height.
							self.$component_content.css( { height: view.load_height } );

							// Render content.
							view.render_content();
						}

					} else {

						composite.console_log( 'debug:animations', 'Starting component content height animation...' );

						// Animate component content height.
						self.$component_content.wc_cp_animate_height( 0, view.get_animation_duration( 'close' ), { complete: function() {

							composite.console_log( 'debug:animations', 'Ended component content height animation.' );

							// Reset content.
							view.reset_content();

							self.$component_content.css( { height: 'auto' } );

						} } );
					}
				},

				/**
				 * Prepare the view for relocation.
				 */
				prepare_relocation: function() {

					var view             = this,
						selected_product = this.model.get( 'selected_product' ),
						can_relocate     = this.can_relocate();

					view.load_height = self.$component_content.get( 0 ).getBoundingClientRect().height;

					if ( typeof view.load_height === 'undefined' ) {
						view.load_height = self.$component_content.outerHeight();
					}

					view.relocating           = false;
					view.relocating_to_origin = false;

					// Save initial location of component_content div.
					if ( can_relocate ) {
						if ( false === view.$relocation_origin ) {
							view.$relocation_origin = $( '<div class="component_content_origin">' );
							self.$component_content.before( view.$relocation_origin );
						}
					}

					// Check if the selection details container needs to be relocated.
					if ( can_relocate && selected_product !== '' && self.is_current() ) {

						var relocation_params = view.get_new_relocation_data();

						if ( relocation_params.relocate ) {
							view.$relocation_reference = relocation_params.reference;
							view.relocating            = relocation_params.relocate;
						}

					} else if ( view.is_relocated() ) {
						view.relocating           = true;
						view.relocating_to_origin = true;
					}

					if ( view.relocating ) {

						// Run 'component_selection_details_relocation_started' action - @see WC_CP_Actions_Dispatcher class.
						composite.actions.do_action( 'component_selection_details_relocation_started', [ self ] );

						self.$component_content.addClass( 'relocating' );
					}
				},

				/**
				 * Execute relocation.
				 */
				maybe_relocate_content: function() {

					var view = this;

					// Relocate content.
					if ( view.is_relocating() ) {

						// If the view is already relocated, then move the existing relocation target/container.
						if ( view.is_relocated() ) {

							view.$relocation_reference.after( view.$relocation_target );

						// Otherwise, create a relocation target/container and move content into it.
						} else {

							view.$relocation_target = $( '<li class="component_option_content_container">' );
							view.$relocation_reference.after( view.$relocation_target );
							self.$component_content.appendTo( view.$relocation_target );

							// Run 'component_selection_details_relocation_container_created' action - @see WC_CP_Actions_Dispatcher class.
							composite.actions.do_action( 'component_selection_details_relocation_container_created', [ self ] );
						}

						view.relocated = true;

						self.$component_content.addClass( 'relocated' );
					}
				},

				/**
				 * Renders new selection details in view.
				 */
				render_content: function() {

					var view    = this,
						content = view.model.get_product_data().product_html;

					composite.console_log( 'debug:views', '\nRendering "' + self.get_title() + '" selection view content...' );

					// Reset scripts/classes before replacing markup.
					self.reset_scripts();

					// Execute any pending relocation.
					view.maybe_relocate_content();

					// Put content in place.
					self.$component_summary_content.addClass( 'populated' );
					self.$component_summary_content.html( content );

					view.rendered_content();

					// Animate.
					if ( composite.is_finalized ) {

						// Preload and animate content.

						var $images = self.is_current() ? self.$component_summary_content.find( 'img' ) : [],
							task    = new wc_cp_classes.WC_CP_Async_Task( function() {

							var wait       = false,
								async_task = this;

							if ( $images.length > 0 ) {
								$images.each( function() {

									var $image = $( this );

									if ( $image.is( ':visible' ) && $image.height() === 0 && false === $image.get( 0 ).complete && async_task.get_async_time() < 10000 ) {
										wait = true;
										return false;
									}
								} );
							}

							if ( ! wait ) {
								this.done();
							}

						}, 50 );

						task.complete( function() {

							setTimeout( function() {

								if ( view.is_relocated() ) {
									self.$component_content.removeClass( 'relocating' );
								}

								// Animate content height.
								view.animate_rendered_content();

							}, 300 );

						} );
					}
				},

				/**
				 * Trigger scripts after updating view with selection content.
				 */
				rendered_content: function() {

					composite.console_log( 'debug:views', '\nInitializing "' + self.get_title() + '" view content scripts...' );
					composite.debug_indent_incr();

					if ( this.model.get( 'selected_product' ) > 0 ) {
						self.init_scripts();
					} else {
						self.init_scripts( false );
					}

					composite.debug_indent_decr();
					composite.console_log( 'debug:views', '\nDone initializing "' + self.get_title() + '" view content scripts.' );

					// Run 'component_selection_details_updated' action - @see WC_CP_Actions_Dispatcher class.
					composite.actions.do_action( 'component_selection_details_updated', [ self ] );
				},

				/**
				 * Similar to a render but called when clearing.
				 */
				reset_content: function() {

					// Reset scripts/classes before emptying markup.
					self.reset_scripts();

					// Reset content.
					self.$component_summary_content.html( '' );
					self.$component_summary_content.removeClass( 'populated' );

					// Remove appended navi.
					if ( self.$el.find( '.composite_navigation.movable' ).length > 0 ) {
						composite.$composite_navigation_movable.addClass( 'hidden' );
					}

					this.reset_relocated_content();
					this.rendered_content();

					self.$component_content.removeClass( 'relocating' );
				},

				/**
				 * Animates updated content height.
				 */
				animate_rendered_content: function() {

					// Measure height.
					var new_height     = self.$component_summary.outerHeight( true ),
						animate_height = false,
						view           = this;

					if ( ( self.is_current() || 'single' === composite.settings.layout ) && ( view.is_relocating() || Math.abs( new_height - this.load_height ) > 1 ) ) {
						animate_height = true;
					} else {
						self.$component_content.css( 'height', 'auto' );
					}

					// Animate component content height and scroll to selected product details.
					if ( animate_height ) {

						composite.console_log( 'debug:animations', 'Starting updated content height animation...' );

						self.$component_content.wc_cp_animate_height( new_height, view.get_animation_duration( 'open' ), { complete: function() {

							composite.console_log( 'debug:animations', 'Ended updated content height animation.' );

							// Reset height.
							self.$component_content.css( { height: 'auto' } );

							// Unblock.
							view.unblock();

							// Run 'component_selection_details_animated' action - @see WC_CP_Actions_Dispatcher class.
							composite.actions.do_action( 'component_selection_details_animated', [ self ] );

						} } );

					} else {

						// Unblock.
						view.unblock();

						// Run 'component_selection_details_animated' action - @see WC_CP_Actions_Dispatcher class.
						composite.actions.do_action( 'component_selection_details_animated', [ self ] );
					}
				},

				/**
				 * Move relocated view back to its original position before reloading component options into the 'Component_Options_View'.
				 */
				component_options_flush_handler: function( response, render_type ) {

					if ( this.is_relocated() && render_type === 'reload' && response.result === 'success' ) {
						this.flushing_component_options = true;

						self.$component_content.hide();

						this.reset_relocated_content();

						this.flushing_component_options = false;
					}
				},

				/**
				 * Move relocated view back to its original position.
				 */
				reset_relocated_content: function() {

					var view = this;

					if ( this.is_relocated() ) {

						// Move content to origin.
						view.$relocation_origin.after( self.$component_content );

						// Run 'component_selection_details_relocation_ended' action - @see WC_CP_Actions_Dispatcher class.
						composite.actions.do_action( 'component_selection_details_relocation_ended', [ self ] );

						// Remove origin and relocation container.
						view.$relocation_origin.remove();
						view.$relocation_target.remove();

						// Reset props.
						view.$relocation_origin    = false;
						view.$relocation_target    = false;
						view.$relocation_reference = false;

						view.relocated  = false;
						view.relocating = false;

						self.$component_content.removeClass( 'relocated' );
					}
				},

				/**
				 * True while initializing product scripts, e.g. the variation/bundle script.
				 */
				is_initializing_view_content: function() {
					return self.initializing_scripts;
				},

				/**
				 * True if the view is allowed to relocate below the thumbnail.
				 */
				can_relocate: function() {

					var can_relocate = false;

					if ( 'paged' === composite.settings.layout && self.append_results() && self.has_options_style( 'thumbnails' ) ) {

						if ( 'off' !== self.get_relocation_mode() ) {

							if ( this.is_relocated() ) {

								can_relocate = true;

							} else if ( 'forced' === self.get_relocation_mode() ) {

								can_relocate = true;

							} else if ( 'adaptive' === self.get_relocation_mode() ) {

								var page  = parseInt( self.component_options_model.get( 'page' ), 10 );

								if ( page > 1 ) {
									can_relocate = true;
								}
							}
						}
					}

					return can_relocate;
				},

				/**
				 * True if the component_content container is relocated below the thumbnail.
				 */
				is_relocated: function() {

					return this.relocated;
				},

				/**
				 * True if the component_content container is being relocated below the thumbnail.
				 */
				is_relocating: function() {

					return this.relocating;
				},

				/**
				 * True if the component_content container is being relocated to its origin.
				 */
				is_relocating_to_origin: function() {

					return this.relocating_to_origin;
				},

				/**
				 * Get new relocation parameters for this view, when allowed. Returns:
				 *
				 * - A thumbnail (list item) to be used as the relocation reference (the relocated content should be right after this element).
				 * - A boolean indicating whether the view should be moved under the reference element.
				 */
				get_new_relocation_data: function() {

					var relocation_needed          = false,
						$relocation_reference      = false,
						$selected_thumbnail        = self.$component_options.find( '.component_option_thumbnail.selected' ).closest( '.component_option_thumbnail_container' ),
						thumbnail_to_column_ratio  = $selected_thumbnail.outerWidth( true ) / self.$component_options.outerWidth(),
						$last_thumbnail_in_row     = ( $selected_thumbnail.hasClass( 'last' ) || thumbnail_to_column_ratio > 0.6 ) ? $selected_thumbnail : $selected_thumbnail.nextAll( '.last' ).first();

					if ( $last_thumbnail_in_row.length > 0 ) {
						$relocation_reference = $last_thumbnail_in_row;
					} else {
						$relocation_reference = self.$component_options.find( '.component_option_thumbnail_container' ).last();
					}

					if ( $relocation_reference.next( '.component_option_content_container' ).length === 0 ) {
						relocation_needed = true;
					}

					return {
						relocate:  relocation_needed,
						reference: $relocation_reference
					};
				}

			} );

			var obj = new View( opts );
			return obj;
		};

	};



	/**
	 * Actions dispatcher that triggers actions in response to specific events.
	 * When multiple models (or both models & views) need to respond to a specific event, model handlers must be run before view handlers (and in the right sequence) to ensure that views have access to correctly updated model data.
	 *
	 * Without a dispatcher:
	 *
	 *  - declaring those handlers in the right sequence can make our code hard to read, and
	 *  - it is very hard for 3rd party code to add handlers at a specific point in the callback execution queue.
	 *
	 * The dispatcher:
	 *
	 *  - translates key events into actions and provides an API for declaring callbacks for specific actions in the desired priority, and
	 *  - makes code a lot easier to read since internal functionality is abstracted (models/views listen to key, internal events directly).
	 *
	 *
	 * A complete reference of all application actions & callbacks is provided in the "Actions Reference" below.
	 *
	 */
	wc_cp_classes.WC_CP_Actions_Dispatcher = function( composite ) {

		/*
		 *--------------------------*
		 *                          *
		 *   Actions Reference      *
		 *                          *
		 *--------------------------*
		 *
		 *--------------------------*
		 *   1. Steps/Components    *
		 *--------------------------*
		 *
		 *
		 * Action 'show_step':
		 *
		 * Triggered when navigating to a step.
		 *
		 * @param  WC_CP_Step  step
		 *
		 * @hooked Action 'show_step_{step.step_id}'              - 0
		 * @hooked Composite_Viewport_Scroller::autoscroll_single - 10
		 *
		 *
		 *
		 * Action 'show_step_{step.step_id}':
		 *
		 * Triggered when navigating to the step with id === step_id.
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'active_step_changed':
		 *
		 * Triggered when the active step changes.
		 *
		 * @param  WC_CP_Step  step
		 *
		 * @hooked Action 'active_step_changed_{step.step_id}'             - 0
		 * @hooked Composite_Pagination_View::active_step_changed_handler  - 100
		 * @hooked Composite_Summary_View::active_step_changed_handler     - 100
		 * @hooked Composite_Widget_View::active_step_changed_handler      - 100
		 * @hooked Step_Validation_View::active_step_changed_handler       - 100
		 * @hooked Composite_Viewport_Scroller::autoscroll_paged           - 120
		 *
		 *
		 *
		 * Action 'active_step_changed_{step.step_id}':
		 *
		 * Triggered when the step with id === step_id becomes active.
		 *
		 * @hooked Component_Options_Model::active_step_changed_handler - 10
		 *
		 *
		 *
		 * Action 'active_step_transition_start':
		 *
		 * Triggered when the transition animation to an activated step starts.
		 *
		 * @param  WC_CP_Step  step
		 *
		 * @hooked Action 'active_step_transition_start_{step.step_id}'            - 0
		 * @hooked Component_Selection_View::active_step_transition_start_handler  - 100
		 * @hooked Composite_Summary_View::active_step_changed_handler             - 100
		 * @hooked Composite_Navigation_View::active_step_transition_start_handler - 110
		 *
		 *
		 *
		 * Action 'active_step_transition_start_{step.step_id}':
		 *
		 * Triggered when the transition animation to the activated step with id === step_id starts.
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'active_step_transition_end':
		 *
		 * Triggered when the transition animation to an activated step ends.
		 *
		 * @param  WC_CP_Step  step
		 *
		 * @hooked Action 'active_step_transition_end_{step.step_id}'           - 0
		 * @hooked Composite_Viewport_Scroller::autoscroll_progressive          - 10
		 * @hooked Composite_Viewport_Scroller::autoscroll_paged_relocated      - 10
		 * @hooked Composite_Selection_View::active_step_transition_end_handler - 10
		 * @hooked Composite_Summary_View::sync_carousel_pos                    - 100
		 *
		 *
		 *
		 * Action 'active_step_transition_end_{step.step_id}':
		 *
		 * Triggered when the transition animation to the activated step with id === step_id ends.
		 *
		 * @hooked Step_Validation_View::active_step_transition_end_handler - 100
		 *
		 *
		 *
		 * Action 'component_selection_details_updated':
		 *
		 * Triggered when the component selection view is updated.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Composite_Viewport_Scroller::selection_details_updated       - 10
		 * @hooked Composite_Navigation_View::selection_details_updated_handler - 10
		 *
		 *
		 *
		 * Action 'component_selection_changed':
		 *
		 * Triggered when the product/variation selection of a Component changes.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked WC_CP_Scenarios_Manager::selection_changed_handler            - 10
		 * @hooked Component_Options_Model::component_selection_changed_handler  - 15
		 * @hooked Step_Validation_Model::selection_changed_handler              - 20
		 * @hooked Composite_Data_Model::selection_changed_handler               - 30
		 * @hooked Composite_Summary_View::selection_changed_handler             - 100
		 * @hooked Component_Options_View::component_totals_changed_handler      - 100
		 * @hooked Component_Selection_View::component_selection_changed_handler - 100
		 * @hooked Composite_Navigation_View::selection_changed_handler          - 110
		 *
		 *
		 *
		 * Action 'component_selection_content_changed':
		 *
		 * Triggered when options/content associated with a selected product change, requiring re-validation, re-calculation of totals and re-freshes of all associated views.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Step_Validation_Model::selection_content_changed_handler     - 20
		 * @hooked Composite_Data_Model::selection_content_changed_handler      - 30
		 * @hooked Composite_Summary_View::selection_changed_handler            - 100
		 * @hooked Composite_Navigation_View::selection_content_changed_handler - 100
		 * @hooked Component_Options_View::component_totals_changed_handler     - 100
		 *
		 *
		 *
		 * Action 'component_quantity_changed':
		 *
		 * Triggered when the quantity of a selected product/variation changes.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Step_Validation_Model::quantity_changed_handler    - 10
		 * @hooked Composite_Data_Model::quantity_changed_handler     - 20
		 * @hooked Composite_Summary_View::quantity_changed_handler   - 100
		 * @hooked Component_Selection_View::quantity_changed_handler - 100
		 *
		 *
		 *
		 * Action 'component_availability_changed':
		 *
		 * Triggered when the availability of a selected product/variation changes.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Composite_Data_Model::availability_changed_handler - 10
		 *
		 *
		 *
		 * Action 'component_addons_changed':
		 *
		 * Triggered when the Product Add-ons associated with a selected product/variation change.
		 *
		 * @param  WC_CP_Component  triggered_by
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'component_nyp_changed':
		 *
		 * Triggered when the price of a selected Name-Your-Price product/variation changes.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Composite_Data_Model::nyp_changed_handler   - 10
		 *
		 *
		 *
		 * Action 'component_validation_message_changed':
		 *
		 * Triggered when the validation notices associated with a Component change.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Composite_Data_Model::validation_status_changed_handler - 10
		 *
		 *
		 *
		 * Action 'component_options_state_changed':
		 *
		 * Triggered when the in-view active/enabled Component Options of a Component change.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Action 'component_options_state_changed_{step.step_id}' - 0
		 *
		 *
		 *
		 * Action 'component_options_state_changed_{step.step_id}':
		 *
		 * Triggered when the in-view active/enabled Component Options of the Component with id === step_id change.
		 *
		 * @hooked Component_Selection_Model::update_active_variations_data - 0
		 * @hooked Component_Options_View::render                           - 10
		 * @hooked Component_Selection_View::options_state_changed_handler  - 100
		 *
		 *
		 *
		 * Action 'available_options_changed':
		 *
		 * Triggered when the Component Options available in a Component change.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked WC_CP_Scenarios_Manager::available_options_changed_handler - 10
		 * @hooked Action 'available_options_changed_{step.step_id}'          - 0
		 *
		 *
		 *
		 * Action 'available_options_changed_{step.step_id}':
		 *
		 * Triggered when the Component Options available in the Component with id === step_id change.
		 *
		 * @hooked Component_Options_Model::available_options_changed_handler - 10
		 *
		 *
		 *
		 * Action 'component_options_state_render':
		 *
		 * Triggered before the active Component Options are rendered by the Component_Options_View Backbone view.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Component_Selection_View::options_state_render_handler - 10
		 *
		 *
		 *
		 * Action 'component_options_state_rendered':
		 *
		 * Triggered after the active Component Options have been rendered by the Component_Options_View Backbone view.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Component_Selection_View::options_state_rendered_handler  - 10
		 * @hooked Composite_Navigation_View::options_state_rendered_handler - 20
		 *
		 *
		 *
		 *
		 * Action 'component_options_loaded':
		 *
		 * Triggered after a new set of Component Options has been loaded and rendered by the Component_Options_View Backbone view.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Action 'component_options_loaded_{step.step_id}' - 0
		 *
		 *
		 *
		 * Action 'component_options_loaded_{step.step_id}':
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'component_scripts_initialized':
		 *
		 * Triggered when the details associated with a new product selection are rendered by the Component_Selection_View, once the associated product type scripts have been initialized.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Action 'component_scripts_initialized_{step.step_id}' - 0
		 *
		 *
		 *
		 * Action 'component_scripts_initialized_{step.step_id}':
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'component_scripts_reset':
		 *
		 * Triggered before unloading the details associated with a new product selection, once all attached script listeners have been unloaded.
		 *
		 * @param  WC_CP_Component  component
		 *
		 * @hooked Action 'component_scripts_reset_{step.step_id}' - 0
		 *
		 *
		 *
		 * Action 'component_scripts_reset_{step.step_id}':
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'component_totals_changed':
		 *
		 * Triggered when the price of a Component changes.
		 *
		 * @param WC_CP_Component  component
		 *
		 * @hooked Composite_Data_Model::component_totals_changed_handler   - 10
		 * @hooked Composite_Summary_View::component_totals_changed_handler - 100
		 *
		 *
		 *
		 * Action 'validate_step':
		 *
		 * Triggered during step validation, before the Step_Validation_Model has been updated with the validation results.
		 *
		 * @param  WC_CP_Step  step
		 * @param  boolean     is_valid
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'component_summary_content_updated':
		 *
		 * Triggered when the content associated with a specific Component in a Composite_Summary_View view changes.
		 *
		 * @param  WC_CP_Component         component
		 * @param  Composite_Summary_View  view
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Action 'step_access_changed':
		 *
		 * Triggered when access to a specific Step is toggled.
		 *
		 * @param  WC_CP_Step   step
		 *
		 * @hooked Composite_Pagination_View::step_access_changed_handler - 100
		 * @hooked Composite_Summary_View::step_access_changed_handler    - 100
		 * @hooked Step_Title_View::step_access_changed_handler           - 100
		 *
		 *
		 *
		 * Action 'step_visibility_changed':
		 *
		 * Triggered when the visibility of a specific Step is toggled.
		 *
		 * @param  WC_CP_Step   step
		 *
		 * @hooked WC_CP_Step::step_visibility_changed_handler                - 10
		 * @hooked Composite_Pagination_View::step_visibility_changed_handler - 100
		 * @hooked Composite_Summary_View::step_visibility_changed_handler    - 100
		 * @hooked Step_Title_View::step_visibility_changed_handler           - 100
		 *
		 *
		 *
		 *--------------------------*
		 *   2. Scenarios           *
		 *--------------------------*
		 *
		 *
		 * Action 'active_scenarios_changed':
		 *
		 * Triggered when the active scenarios change in response to a product/variation selection change in a Component.
		 *
		 * @param  WC_CP_Component  triggered_by
		 *
		 *
		 *
		 * Action 'active_scenarios_updated':
		 *
		 * Triggered when the active scenarios are updated (but not necessarily changed) in response to a product/variation selection change in a Component.
		 *
		 * @param  WC_CP_Component  triggered_by
		 *
		 *
		 *
		 * Action 'hidden_components_changed':
		 *
		 * Triggered when the list of hidden components changes.
		 *
		 * @param  WC_CP_Component  triggered_by
		 *
		 * @hooked Step_Visibility_Model::update_visibility_state - 10
		 *
		 *
		 *
		 *--------------------------*
		 *   3. Composite           *
		 *--------------------------*
		 *
		 *
		 * Action 'initialize_composite':
		 *
		 * Action that handles app initialization by prioritizing the execution of the required functions.
		 *
		 * @hooked @see WC_CP_Composite::init
		 *
		 *
		 *
		 * Action 'composite_initialized':
		 *
		 * Action that handles app post-initialization by prioritizing the execution of the required functions.
		 *
		 * @hooked @see WC_CP_Composite::init
		 *
		 *
		 *
		 * Action 'composite_totals_changed':
		 *
		 * Triggered when the composite price/totals change.
		 *
		 * @hooked Composite_Price_View::render               - 100
		 *
		 *
		 *
		 * Action 'composite_validation_status_changed':
		 *
		 * Triggered when the validation status of the Composite changes.
		 *
		 * @hooked Composite_Add_To_Cart_Button_View::render - 100
		 *
		 *
		 *
		 * Action 'composite_validation_message_changed':
		 *
		 * Triggered when the validation notice of the Composite changes.
		 *
		 * @hooked Composite_Validation_View::render - 100
		 * @hooked Composite_Price_View::render      - 100
		 *
		 *
		 *
		 * Action 'composite_availability_status_changed':
		 *
		 * Triggered when the availability status of the Composite changes.
		 *
		 * @hooked Composite_Add_To_Cart_Button_View::render - 100
		 *
		 *
		 *
		 * Action 'composite_availability_message_changed':
		 *
		 * Triggered when the availability html message of the Composite changes.
		 *
		 * @hooked Composite_Availability_View::render - 100
		 *
		 */

		var dispatcher                  = this,
			component_selection_changes = {},
			actions                     = {},
			functions                   = {

				add_action: function( hook, callback, priority, context ) {

					var hookObject = {
						callback : callback,
						priority : priority,
						context : context
					};

					var hooks = actions[ hook ];
					if ( hooks ) {
						hooks.push( hookObject );
						hooks = this.sort_actions( hooks );
					} else {
						hooks = [ hookObject ];
					}

					actions[ hook ] = hooks;
				},

				remove_action: function( hook, callback, context ) {

					var handlers, handler, i;

					if ( ! actions[ hook ] ) {
						return;
					}
					if ( ! callback ) {
						actions[ hook ] = [];
					} else {
						handlers = actions[ hook ];
						if ( ! context ) {
							for ( i = handlers.length; i--; ) {
								if ( handlers[ i ].callback === callback ) {
									handlers.splice( i, 1 );
								}
							}
						} else {
							for ( i = handlers.length; i--; ) {
								handler = handlers[ i ];
								if ( handler.callback === callback && handler.context === context ) {
									handlers.splice( i, 1 );
								}
							}
						}
					}
				},

				sort_actions: function( hooks ) {

					var tmpHook, j, prevHook;
					for ( var i = 1, len = hooks.length; i < len; i++ ) {
						tmpHook = hooks[ i ];
						j = i;
						while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
							hooks[ j ] = hooks[ j - 1 ];
							--j;
						}
						hooks[ j ] = tmpHook;
					}

					return hooks;
				},

				do_action: function( hook, args ) {

					var handlers = actions[ hook ];

					if ( ! handlers ) {
						return false;
					}

					for ( var i = 0, len = handlers.length; i < len; i++ ) {
						handlers[ i ].callback.apply( handlers[ i ].context, args );
					}

					return true;
				}

			};

		this.init = function() {

			composite.console_log( 'debug:events', '\nInitializing Actions Dispatcher...' );

			/*
			 *--------------------------*
			 *   1. Components          *
			 *--------------------------*
			 */

			/*
			 * Dispatch actions for key events triggered by step objects and their models.
			 */
			for ( var step_index = 0, steps = composite.get_steps(), steps_length = steps.length; step_index < steps_length; step_index++ ) {

				( function( step ) {

					if ( step.is_component() ) {

						/*
						 * Dispatch action when a selection change event is triggered.
						 */
						step.component_selection_model.on( 'change:selected_product change:selected_variation change:selected_variation_data', function( e ) {

							var changed = $.extend( true, {}, e.changed );

							if ( ! _.isEqual( changed, component_selection_changes[ step.step_id ] ) ) {

								component_selection_changes[ step.step_id ] = changed;

								// Run 'component_selection_changed' action - @see WC_CP_Actions_Dispatcher class description.
								dispatcher.do_action( 'component_selection_changed', [ step, e.changed ] );
							}
						} );

						/*
						 * Dispatch action when a quantity change event is triggered.
						 */
						step.component_selection_model.on( 'change:selected_quantity', function() {
							// Run 'component_quantity_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_quantity_changed', [ step ] );
						} );

						/*
						 * Dispatch action when a selected addons change event is triggered.
						 */
						step.component_selection_model.on( 'change:selected_addons', function() {
							// Run 'component_selection_content_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_selection_content_changed', [ step ] );
							// Run 'component_addons_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_addons_changed', [ step ] );
						} );

						/*
						 * Dispatch action when a nyp change event is triggered.
						 */
						step.component_selection_model.on( 'change:selected_nyp', function() {
							// Run 'component_nyp_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_nyp_changed', [ step ] );
							// Run 'component_selection_content_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_selection_content_changed', [ step ] );
						} );

						/*
						 * Dispatch action when the options state changes.
						 */
						step.component_options_model.on( 'change:options_state', function() {
							// Run 'component_options_state_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_options_state_changed', [ step ] );
						} );

						/*
						 * Dispatch action when the available options change.
						 */
						step.component_options_model.on( 'change:available_options', function() {
							// Run 'available_options_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'available_options_changed', [ step ] );
						} );

						/*
						 * Dispatch action when the component totals change.
						 */
						composite.data_model.on( 'change:component_' + step.step_id + '_totals', function() {
							// Run 'component_totals_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_totals_changed', [ step ] );
						} );

						/**
						 * Event triggered by custom product types to indicate that the state of the component selection has changed.
						 */
						step.$el.on ( 'woocommerce-composited-product-update', function() {
							// Run 'component_selection_changed' action - @see WC_CP_Actions_Dispatcher class description.
							dispatcher.do_action( 'component_selection_changed', [ step ] );
						} );
					}

					/*
					 * Dispatch action when the access state of a step changes.
					 */
					step.step_access_model.on( 'change:is_locked', function() {
						// Run 'step_access_changed' action - @see WC_CP_Actions_Dispatcher class description.
						dispatcher.do_action( 'step_access_changed', [ step ] );
					} );

					/*
					 * Dispatch action when the visibility of a step changes.
					 */
					step.step_visibility_model.on( 'change:is_visible', function() {
						// Run 'step_visibility_changed' action - @see WC_CP_Actions_Dispatcher class description.
						dispatcher.do_action( 'step_visibility_changed', [ step ] );
					} );

					/*
					 * Dispatch action when the validation state of a step changes.
					 */
					step.step_validation_model.on( 'change:composite_messages', function() {
						// Run 'component_validation_message_changed' action - @see WC_CP_Actions_Dispatcher class description.
						dispatcher.do_action( 'component_validation_message_changed', [ step ] );
					} );

					/*
					 * Dispatch action when the validation state of a step changes.
					 */
					step.step_validation_model.on( 'change:passes_validation', function() {
						// Run 'component_validation_message_changed' action - @see WC_CP_Actions_Dispatcher class description.
						dispatcher.do_action( 'component_validation_status_changed', [ step ] );
					} );

					/*
					 * Dispatch action when the availability state of a step changes.
					 */
					step.step_validation_model.on( 'change:is_in_stock', function() {
						// Run 'component_availability_changed' action - @see WC_CP_Actions_Dispatcher class description.
						dispatcher.do_action( 'component_availability_changed', [ step ] );
					} );

				} ) ( steps[ step_index ] );
			}

			/*
			 * Dispatch step action associated with the 'show_step' action.
			 */
			dispatcher.add_action( 'show_step', function( step ) {
				// Run 'show_step_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'show_step_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'active_step_changed' action.
			 */
			dispatcher.add_action( 'active_step_changed', function( step ) {
				// Run 'active_step_changed_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'active_step_changed_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'active_step_transition_start' action.
			 */
			dispatcher.add_action( 'active_step_transition_start', function( step ) {
				// Run 'active_step_transition_start_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'active_step_transition_start_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'active_step_transition_end' action.
			 */
			dispatcher.add_action( 'active_step_transition_end', function( step ) {
				// Run 'active_step_transition_end_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'active_step_transition_end_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'component_options_state_changed' action.
			 */
			dispatcher.add_action( 'component_options_state_changed', function( step ) {
				// Run 'component_options_state_changed_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'component_options_state_changed_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'available_options_changed' action.
			 */
			dispatcher.add_action( 'available_options_changed', function( step ) {
				// Run 'available_options_changed_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'available_options_changed_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'component_selection_details_updated' action.
			 */
			dispatcher.add_action( 'component_selection_details_updated', function( step ) {
				// Run 'component_selection_details_updated_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'component_selection_details_updated_' + step.step_id );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'component_options_loaded' action.
			 */
			dispatcher.add_action( 'component_options_loaded', function( step ) {
				// Run 'component_options_loaded_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'component_options_loaded_' + step.step_id );
				// Trigger event for back-compat.
				step.$el.trigger( 'wc-composite-component-options-loaded', [ step, composite ] );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'component_scripts_initialized' action.
			 */
			dispatcher.add_action( 'component_scripts_initialized', function( step ) {
				// Run 'component_scripts_initialized_{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'component_scripts_initialized_' + step.step_id );
				// Trigger event for back-compat.
				step.$el.trigger( 'wc-composite-component-loaded', [ step, composite ] );
			}, 0, this );

			/*
			 * Dispatch step action associated with the 'component_scripts_reset' action.
			 */
			dispatcher.add_action( 'component_scripts_reset', function( step ) {
				// Run 'component_scripts_reset{step.step_id}' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'component_scripts_reset_' + step.step_id );
				// Trigger event for back-compat.
				step.$el.trigger( 'wc-composite-component-unloaded', [ step, composite ] );
			}, 0, this );


			/*
			 *--------------------------*
			 *   2. Scenarios           *
			 *--------------------------*
			 */

			/*
			 * Dispatch action when the active scenarios change.
			 */
			composite.scenarios.on( 'active_scenarios_changed', function( triggered_by ) {
				// Run 'active_scenarios_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'active_scenarios_changed', [ triggered_by ] );
			} );

			/*
			 * Dispatch action when the active scenarios are updated.
			 */
			composite.scenarios.on( 'active_scenarios_updated', function( triggered_by ) {
				// Run 'active_scenarios_updated' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'active_scenarios_updated', [ triggered_by ] );
			} );

			/*
			 * Dispatch action when the hidden components are updated.
			 */
			composite.scenarios.on( 'hidden_components_changed', function( triggered_by ) {
				// Run 'hidden_components_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'hidden_components_changed', [ triggered_by ] );
			} );

			/*
			 *--------------------------*
			 *   3. Composite           *
			 *--------------------------*
			 */

			/*
			 * Dispatch action when the composite totals change.
			 */
			composite.data_model.on( 'change:totals', function() {
				// Run 'composite_totals_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'composite_totals_changed' );
			} );

			/*
			 * Dispatch action when the composite validation status changes.
			 */
			composite.data_model.on( 'change:passes_validation', function() {
				// Run 'composite_validation_status_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'composite_validation_status_changed' );
			} );

			/*
			 * Dispatch action when the composite validation message changes.
			 */
			composite.data_model.on( 'change:validation_messages', function() {
				// Run 'composite_validation_message_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'composite_validation_message_changed' );
			} );

			/*
			 * Dispatch action when the composite availability status changes.
			 */
			composite.data_model.on( 'change:is_in_stock', function() {
				// Run 'composite_availability_status_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'composite_availability_status_changed' );
			} );

			/*
			 * Dispatch action when the composite availability message changes.
			 */
			composite.data_model.on( 'change:stock_statuses', function() {
				// Run 'composite_availability_message_changed' action - @see WC_CP_Actions_Dispatcher class description.
				dispatcher.do_action( 'composite_availability_message_changed' );
			} );

		};

		/**
		 * Adds an action handler to the dispatcher.
		 */
		this.add_action = function( action, callback, priority, context ) {

			if ( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				functions.add_action( action, callback, priority, context );
			}

			return dispatcher;
		};

		/**
		 * Performs an action if it exists.
		 */
		this.do_action = function( action, args ) {

			if ( typeof action === 'string' ) {
				functions.do_action( action, args );
			}

			return dispatcher;
		};

		/**
		 * Removes the specified action.
		 */
		this.remove_action = function( action, callback ) {

			if ( typeof action === 'string' ) {
				functions.remove_action( action, callback );
			}

			return dispatcher;
		};

	};



	/**
	 * Filters manager that handles filtering of various function outputs.
	 *
	 * A complete reference of all application filters & callbacks is provided in the "Filters Reference" below.
	 */
	wc_cp_classes.WC_CP_Filters_Manager = function() {

		/*
		 *--------------------------*
		 *                          *
		 *   Filters Reference      *
		 *                          *
		 *--------------------------*
		 *
		 *--------------------------*
		 *   1. Composite           *
		 *--------------------------*
		 *
		 *
		 * Filter 'composite_validation_messages':
		 *
		 * Filters the individual Composite validation notice messages before updating model state.
		 *
		 * @param  array  messages   Validation messages.
		 * @return array
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'composite_totals':
		 *
		 * Filters the Composite totals before updating model state.
		 *
		 * @param  object  totals   Composite prices.
		 * @return object
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'composite_pagination_view_data':
		 *
		 * Filters the data passed to the pagination view template.
		 *
		 * @param  array  data   Template data.
		 * @return array
		 *
		 * @hooked void
		 *
		 *
		 *
		 *--------------------------*
		 *   2. Components          *
		 *--------------------------*
		 *
		 *
		 * Filter 'component_totals':
		 *
		 * Filters the totals of a Component before updating the data model state.
		 *
		 * @param  object            totals      Component prices.
		 * @param  WC_CP_Component   component   Component object.
		 * @param  string            qty         Component quanity.
		 * @return object
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'step_validation_messages':
		 *
		 * Filters the validation notices associated with a step.
		 *
		 * @param  array        messages   Validation messages.
		 * @param  string       scope      Scope for validation messages ('composite', 'component').
		 * @param  WC_CP_Step   step       Step object.
		 * @return array
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'step_is_valid':
		 *
		 * Filters the validation status of a step before updating the Step_Validation_Model state.
		 *
		 * @param  boolean      is_valid   Validation state.
		 * @param  WC_CP_Step   step       Step object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'step_is_locked':
		 *
		 * @param  boolean      is_locked   Access state.
		 * @param  WC_CP_Step   step        Step object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_is_optional':
		 *
		 * Filters the optional status of a Component.
		 *
		 * @param  boolean          is_optional   True if optional.
		 * @param  WC_CP_Component  step          Component object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_selection_title':
		 *
		 * Filters the raw product title of the current Component selection.
		 *
		 * @param  string            title        The title.
		 * @param  WC_CP_Component   component    Component object.
		 * @return string
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_selection_formatted_title':
		 *
		 * Filters the formatted title of the current Component selection.
		 *
		 * @param  string            fomatted_title  Formatted title.
		 * @param  string            title           The returned title.
		 * @param  string            qty             The quantity of the selected product.
		 * @param  string            formatted_meta  The formatted meta.
		 * @param  WC_CP_Component   component       Component object.
		 * @return string
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_selection_meta':
		 *
		 * Filters the meta array associated with the current Component selection.
		 *
		 * @param  array             meta         The returned meta array.
		 * @param  WC_CP_Component   component    WC_CP_Component  Component object.
		 * @return array
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_selection_formatted_meta':
		 *
		 * Filters the formatted meta associated with the current Component selection.
		 *
		 * @param  string           formatted_meta   The returned formatted meta.
		 * @param  array            meta             The meta array.
		 * @param  WC_CP_Component  component        Component object.
		 * @return string
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_configuration':
		 *
		 * Filters the configuration data object associated with a Component.
		 *
		 * @param  object           config           The returned component configuration data object.
		 * @param  WC_CP_Component  component        Component object.
		 * @return object
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_selection_change_animation_duration':
		 *
		 * Filters the configuration data object associated with a Component.
		 *
		 * @param  integer          duration         The animation duration.
		 * @param  string           open_or_close    The animation context ('open'|'close').
		 * @param  WC_CP_Component  component        Component object.
		 * @return integer
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_summary_element_content_data':
		 *
		 * Filters the summary element content data.
		 *
		 * @param  object                  content_data     The summary element data passed to the js template.
		 * @param  WC_CP_Component         component        Component object.
		 * @param  Composite_Summary_View  view             Component summary view object.
		 * @return object
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_hide_disabled_products':
		 *
		 * Allows you to filter the output of 'WC_CP_Component::hide_disabled_products()'.
		 *
		 * @param  boolean          hide_disabled_products       Whether to hide disabled product options.
		 * @param  WC_CP_Component  component                    Component object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_hide_disabled_variations':
		 *
		 * Allows you to filter the output of 'WC_CP_Component::hide_disabled_variations()'.
		 *
		 * @param  boolean          hide_disabled_variations     Whether to hide disabled product variations.
		 * @param  WC_CP_Component  component                    Component object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'composite_price_html';
		 *
		 * Filters the Composite price html total.
		 *
		 * @param  string price_html            Html price string.
		 * @param  Composite_Price_View view    Component price view object.
		 * @param  array price_data_array       Price data for which the price string is generated.
		 * @return string
		 *
		 * @hooked void
		 *
		 *
		 *
		 * Filter 'component_is_valid';
		 *
		 * Filters Component validation results.
		 *
		 * @param  boolean          is_valid            Result.
		 * @param  boolean          check_scenarios     Whether to check selection validity against scenarios.
		 * @param  WC_CP_Component  component           Component object.
		 * @return boolean
		 *
		 * @hooked void
		 *
		 */

		var manager   = this,
			filters   = {},
			functions = {

				add_filter: function( hook, callback, priority, context ) {

					var hookObject = {
						callback : callback,
						priority : priority,
						context : context
					};

					var hooks = filters[ hook ];
					if ( hooks ) {
						hooks.push( hookObject );
						hooks = this.sort_filters( hooks );
					} else {
						hooks = [ hookObject ];
					}

					filters[ hook ] = hooks;
				},

				remove_filter: function( hook, callback, context ) {

					var handlers, handler, i;

					if ( ! filters[ hook ] ) {
						return;
					}
					if ( ! callback ) {
						filters[ hook ] = [];
					} else {
						handlers = filters[ hook ];
						if ( ! context ) {
							for ( i = handlers.length; i--; ) {
								if ( handlers[ i ].callback === callback ) {
									handlers.splice( i, 1 );
								}
							}
						} else {
							for ( i = handlers.length; i--; ) {
								handler = handlers[ i ];
								if ( handler.callback === callback && handler.context === context) {
									handlers.splice( i, 1 );
								}
							}
						}
					}
				},

				sort_filters: function( hooks ) {

					var tmpHook, j, prevHook;
					for ( var i = 1, len = hooks.length; i < len; i++ ) {
						tmpHook = hooks[ i ];
						j = i;
						while( ( prevHook = hooks[ j - 1 ] ) && prevHook.priority > tmpHook.priority ) {
							hooks[ j ] = hooks[ j - 1 ];
							--j;
						}
						hooks[ j ] = tmpHook;
					}

					return hooks;
				},

				apply_filters: function( hook, args ) {

					var handlers = filters[ hook ];

					if ( ! handlers ) {
						return args[ 0 ];
					}

					for ( var i = 0, len = handlers.length; i < len; i++ ) {
						args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
					}

					return args[ 0 ];
				}

			};

		/**
		 * Adds a filter.
		 */
		this.add_filter = function( filter, callback, priority, context ) {

			if ( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				functions.add_filter( filter, callback, priority, context );
			}

			return manager;
		};

		/**
		 * Applies all filter callbacks.
		 */
		this.apply_filters = function( filter, args ) {

			if ( typeof filter === 'string' ) {
				return functions.apply_filters( filter, args );
			}
		};

		/**
		 * Removes the specified filter callback.
		 */
		this.remove_filter = function( filter, callback ) {

			if ( typeof filter === 'string' ) {
				functions.remove_filter( filter, callback );
			}

			return manager;
		};

	};



	/**
	 * Updates the active scenarios when:
	 *
	 *  - Refreshing/appending new component options: Adds an 'available_options_changed' action handler.
	 *  - Selecting a new product/variation: Adds a 'component_selection_changed' action handler ('component_selection_changed' action dispatched when a 'change:selected_product' and 'change:selected_variation' event is triggered by a Component_Selection_Model).
	 *
	 * Triggers the 'active_scenarios_updated' and 'active_scenarios_changed' events which are picked up by Component_Options_Model models to update their options state (handlers added to the corresponding dispatcher actions).
	 */
	wc_cp_classes.WC_CP_Scenarios_Manager = function( composite ) {

		var manager      = this,
			manager_data = {
				scenario_data:     composite.$composite_data.data( 'scenario_data' ),
				active_scenarios:  {},
				hidden_components: []
			};

		_.extend( manager, Backbone.Events );

		/**
		 * Initialize after components have been created.
		 */
		this.init = function() {

			// Initialize array for storing active scenarios for managed scenario actions.
			var scenario_actions = this.get_scenario_actions(),
				scenario_action;

			for ( var i = scenario_actions.length - 1; i >= 0; i-- ) {

				scenario_action = scenario_actions[ i ];

				if ( this.is_scenario_action_managed( scenario_action ) ) {
					manager_data.active_scenarios[ scenario_action ] = [];
				}
			}

			/**
			 * Update the active scenarios when refreshing/appending new component options.
			 */
			composite.actions.add_action( 'available_options_changed', this.available_options_changed_handler, -10, this );

			/**
			 * Update the active scenarios when selecting a new product/variation.
			 */
			composite.actions.add_action( 'component_selection_changed', this.selection_changed_handler, 10, this );

			// Initialize scenarios.
			composite.console_log( 'debug:events', '\nInitializing Scenarios Manager...' );
			composite.debug_indent_incr();

			manager.update_active_scenarios( _.first( composite.steps ), false, false );

			composite.debug_indent_decr();
			composite.console_log( 'debug:events', '\nScenarios Manager initialized.\n' );
		};

		/**
		 * Returns all scenarios by type.
		 */
		this.get_scenarios_by_type = function( type ) {

			return this.filter_scenarios_by_type( this.get_scenario_data().scenarios, type );
		};

		/**
		 * Returns all scenario action identifiers.
		 */
		this.get_scenario_actions = function() {

			return _.keys( manager_data.scenario_data.action_settings );
		};

		/**
		 * Returns all scenario action identifiers.
		 */
		this.get_scenario_action_settings = function( type ) {

			return manager_data.scenario_data.action_settings[ type ] || [];
		};

		/**
		 * Returns true if the active Scenarios of a specific scenario action are re-calculated by the Manager.
		 * See 'get_active_scenarios_by_type'.
		 */
		this.is_scenario_action_managed = function( type ) {

			return 'yes' === manager.get_scenario_action_settings( type ).is_managed;
		};

		/**
		 * Returns true if the active Scenarios of a specific scenario action are calculated using a specific calculation modifier.
		 * See 'calculate_active_scenarios_by_type'.
		 */
		this.is_scenario_action_calculation = function( type, modifier ) {

			return _.includes( manager.get_scenario_action_settings( type ).calculation, modifier );
		};

		/**
		 * Returns active scenarios.
		 */
		this.get_active_scenarios = function() {

			return manager_data.active_scenarios;
		};

		/**
		 * Returns active scenarios by type.
		 */
		this.get_active_scenarios_by_type = function( type ) {

			return manager_data.active_scenarios[ type ] || [];
		};

		/**
		 * Returns current scenario data.
		 */
		this.get_scenario_data = function() {

			return manager_data.scenario_data;
		};

		/**
		 * Replaces stored scenario data for a given component, for instance when refreshing the component options view.
		 */
		this.set_scenario_data = function( scenario_data, component_id ) {

			component_id = component_id ? component_id : false;

			if ( false === component_id ) {
				manager_data.scenario_data = scenario_data;
			} else {
				manager_data.scenario_data.scenario_data[ component_id ] = scenario_data;
			}
		};

		/**
		 * Append scenario data to a given component, in order to include data for more products.
		 */
		this.merge_scenario_data = function( scenario_data, component_id ) {

			component_id = component_id ? component_id : false;

			if ( false === component_id ) {

				for ( var c_id in scenario_data ) {

					if ( ! scenario_data.hasOwnProperty( c_id ) ) {
						continue;
					}

					for ( var c_product_id in scenario_data[ c_id ] ) {

						if ( ! scenario_data[ c_id ].hasOwnProperty( c_product_id ) ) {
							continue;
						}

						manager_data.scenario_data.scenario_data[ c_id ][ c_product_id ] = scenario_data[ c_id ][ c_product_id ];
					}
				}

			} else {

				for ( var product_id in scenario_data ) {

					if ( ! scenario_data.hasOwnProperty( product_id ) ) {
						continue;
					}

					manager_data.scenario_data.scenario_data[ component_id ][ product_id ] = scenario_data[ product_id ];
				}
			}
		};

		/**
		 * Replaces stored conditional options scenario data for a given component, for instance when refreshing the component options view.
		 */
		this.set_conditional_options_scenario_data = function( component_scenario_data, component_id ) {

			manager_data.scenario_data.conditional_options_data[ component_id ] = component_scenario_data;
		};

		/**
		 * Append scenario data to a given component, in order to include data for more products.
		 */
		this.merge_conditional_options_scenario_data = function( component_scenario_data, component_id ) {

			for ( var product_id in component_scenario_data ) {

				if ( ! component_scenario_data.hasOwnProperty( product_id ) ) {
					continue;
				}

				manager_data.scenario_data.conditional_options_data[ component_id ][ product_id ] = component_scenario_data[ product_id ];
			}
		};

		this.selection_changed_handler = function( triggered_by ) {

			composite.console_log( 'debug:scenarios', '\nUpdating active scenarios in response to "' + triggered_by.get_title() + '" selection state change...' );

			composite.debug_indent_incr();
			this.update_active_scenarios( triggered_by );
			composite.debug_indent_decr();
		};

		this.available_options_changed_handler = function( triggered_by ) {

			composite.console_log( 'debug:scenarios', '\nUpdating active scenarios in response to "' + triggered_by.get_title() + '" options state change...' );

			composite.debug_indent_incr();
			this.update_active_scenarios( triggered_by );
			composite.debug_indent_decr();
		};

		/**
		 * Updates active scenarios and triggers an event if changed.
		 */
		this.update_active_scenarios = function( triggered_by ) {

			// Backup current state to compare with new one.
			var active_scenarios_pre  = manager_data.active_scenarios,
				hidden_components_pre = manager_data.hidden_components,
				active_scenarios      = {},
				updated_scenarios     = [],
				scenario_actions      = this.get_scenario_actions(),
				scenario_action;

			// Active scenarios for the 'conditional_components' action must be calculated first.
			active_scenarios[ 'conditional_components' ] = this.calculate_active_scenarios( 'conditional_components' );

			// Trigger event if the hidden components changed :)
			if ( hidden_components_pre.length !== manager_data.hidden_components.length || hidden_components_pre.length !== _.intersection( hidden_components_pre, manager_data.hidden_components ).length ) {

				composite.console_log( 'debug:scenarios', '\nHidden components changed: - [' + hidden_components_pre + '] => [' + manager_data.hidden_components + ']' );

				updated_scenarios.push( 'conditional_components' );

				this.trigger( 'hidden_components_changed', triggered_by );

			} else {

				composite.console_log( 'debug:scenarios', '\nHidden components unchanged.' );
			}

			// Then, calculate active scenarios for other managed scenario actions.
			for ( var i = 0; i < scenario_actions.length; i++ ) {

				scenario_action = scenario_actions[ i ];

				// Already calculated?
				if ( 'conditional_components' === scenario_action ) {
					continue;
				}

				// Only re-calculate active scenarios for managed scenario actions here.
				if ( ! this.is_scenario_action_managed( scenario_action ) ) {
					continue;
				}

				active_scenarios[ scenario_action ] = this.calculate_active_scenarios( scenario_action );

				if ( active_scenarios_pre[ scenario_action ].length !== active_scenarios[ scenario_action ].length || active_scenarios_pre[ scenario_action ].length !== _.intersection( active_scenarios_pre[ scenario_action ], active_scenarios[ scenario_action ] ).length ) {
					updated_scenarios.push( scenario_action );
				}
			}

			// Only trigger event if the active scenarios of one or more types (scenario actions) changed :)
			if ( updated_scenarios.length ) {

				// Save new scenarios.
				manager_data.active_scenarios = active_scenarios;

				for ( var k = updated_scenarios.length - 1; k >= 0; k-- ) {

					scenario_action = updated_scenarios[ k ];

					composite.console_log( 'debug:scenarios', '\nActive "' + scenario_action + '" scenarios changed: - [' + active_scenarios_pre[ scenario_action ] + '] => [' + active_scenarios[ scenario_action ] + ']' );
				}

				this.trigger( 'active_scenarios_changed', triggered_by, updated_scenarios );

			} else {

				composite.console_log( 'debug:scenarios', '\nActive scenarios unchanged.' );
			}

			this.trigger( 'active_scenarios_updated', triggered_by );
		};

		/**
		 * Extract active scenarios from current selections.
		 * Scenarios can be calculated up to, or excluding the step passed as reference.
		 */
		this.calculate_active_scenarios = function( type, ref_step, up_to_ref, excl_ref ) {

			var ref_step_index = ref_step ? ref_step.step_index : false,
				scenarios      = manager.get_scenarios_by_type( type );

			if ( 'compat_group' === type && scenarios.length === 0 ) {
				scenarios.push( '0' );
			}

			if ( 0 === scenarios.length ) {
				return [];
			}

			if ( ref_step && ref_step.is_review() ) {
				ref_step_index = 1000;
			}

			var	is_preemptive = this.is_scenario_action_calculation( type, 'preemptive' ),
				is_masked     = this.is_scenario_action_calculation( type, 'masked' ),
				is_strict     = this.is_scenario_action_calculation( type, 'strict' ),
				skip_invalid  = this.is_scenario_action_calculation( type, 'skip_invalid' );

			var active_scenarios            = is_preemptive ? scenarios : [],
				scenario_shaping_components = [],
				hidden_components           = [];

			composite.console_log( 'debug:scenarios', '\n' + 'Calculating active "' + type + '" Scenarios...\n' );

			for ( var component_index = 0, components = composite.get_components(), components_length = components.length; component_index < components_length; component_index++ ) {

				var component = components[ component_index ];

				// Component hidden?
				if ( component.step_index > 0 ) {

					/*
					 * This block is basically a hack, made possible by the fact that we can calculate Component visibility sequentially. We could have written this more cleanly by:
					 * - moving most of this block in 'update_active_scenarios',
					 * - running 'calculate_active_scenarios' for every Component, up to the previous one, and
					 * - finding if the evaluated Component is hidden by looking at the returned Scenarios, which is pretty much what's done here.
					 */
					if ( 'conditional_components' === type ) {

						var is_visible          = true,
							active_cc_scenarios = active_scenarios;

						if ( is_masked && scenario_shaping_components.length ) {
							composite.console_log( 'debug:scenarios', 'Removing "conditional_component" scenarios where all scenario shaping components (' + scenario_shaping_components + ') are masked...' );
							active_cc_scenarios = manager.filter_unmatched_scenarios( active_cc_scenarios, scenario_shaping_components );
						}

						if ( is_strict ) {
							composite.console_log( 'debug:scenarios', 'Removing "conditional_component" scenarios with conditions that are partially matched...' );
							active_cc_scenarios = manager.clean_partially_matched_scenarios( active_cc_scenarios, scenario_shaping_components );
						}

						composite.console_log( 'debug:scenarios', 'Removing "conditional_component" scenarios that contain hidden components which require a selection in order to be matched...' );
						active_cc_scenarios = manager.clean_hidden_component_scenarios( active_cc_scenarios, hidden_components );

						composite.console_log( 'debug:scenarios', 'Calculating "' + component.get_title() + '" visibility...' );

						composite.debug_indent_incr();

						composite.console_log( 'debug:scenarios', 'Active "Hide Components" Scenarios: [' + active_cc_scenarios + ']' );

						// Get conditional components data.
						var conditional_components = manager.get_scenario_data().scenario_settings.conditional_components;

						// Find if the component is hidden in the active scenarios.
						if ( active_cc_scenarios.length > 0 && typeof( conditional_components ) !== 'undefined' ) {

							// Set hide status.
							for ( var scenario_id in conditional_components ) {

								if ( ! conditional_components.hasOwnProperty( scenario_id ) ) {
									continue;
								}

								var components_hidden_in_scenario = conditional_components[ scenario_id ];

								if ( _.includes( active_cc_scenarios, scenario_id.toString() ) ) {
									if ( _.includes( components_hidden_in_scenario, component.component_id.toString() ) ) {
										is_visible = false;
									}
								}
							}
						}

						if ( is_visible ) {
							composite.console_log( 'debug:scenarios', 'Component is visible.' );
						} else {
							hidden_components.push( component.component_id.toString() );
							composite.console_log( 'debug:scenarios', 'Component is hidden.' );
						}

						composite.debug_indent_decr();

						if ( ! is_visible ) {
							continue;
						}

					} else if ( ! component.is_visible() ) {
						continue;
					}
				}

				// Omit reference component when excluded.
				if ( ref_step && excl_ref && parseInt( component.step_index, 10 ) === parseInt( ref_step_index, 10 ) ) {
					continue;
				}

				// Exit when reaching beyond reference component.
				if ( ref_step && up_to_ref && component.step_index > ref_step_index ) {
					break;
				}

				var product_id   = component.get_selected_product( false ),
					product_type = component.get_selected_product_type(),
					variation_id = 'variable' === product_type ? component.get_selected_variation( false ) : '';

				// Evaluate empty selections if the component is optional, or if the calculation type permits it.
				if ( product_id === '' ) {
					if ( component.maybe_is_optional() || ! skip_invalid ) {
						product_id = '0';
					} else {
						continue;
					}
				}

				if ( product_id !== null && product_id >= 0 ) {

					var scenario_data        = manager.get_scenario_data().scenario_data,
						item_scenario_data   = scenario_data[ component.component_id ],
						product_in_scenarios = [];

					if ( 'variable' === product_type ) {

						if ( is_preemptive || variation_id ) {

							product_in_scenarios = ( product_id in item_scenario_data ) ? manager.filter_scenarios_by_type( item_scenario_data[ product_id ], type ) : [];

						} else if ( ! variation_id ) {

							product_in_scenarios = ( product_id + '_empty' in item_scenario_data ) ? manager.filter_scenarios_by_type( item_scenario_data[ product_id + '_empty' ], type ) : [];

							if ( ! product_in_scenarios.length ) {
								composite.console_log( 'debug:scenarios', 'Selection #' + product_id + ' of "' + component.get_title() + '" not contributing to the active "' + type + '" Scenarios.' );
								continue;
							}
						}

					} else {

						product_in_scenarios = ( product_id in item_scenario_data ) ? manager.filter_scenarios_by_type( item_scenario_data[ product_id ], type ) : [];
					}

					composite.console_log( 'debug:scenarios', 'Selection #' + product_id + ' of "' + component.get_title() + '" in Scenarios: [' + product_in_scenarios + ']' );

					var product_intersection = ! is_preemptive && scenario_shaping_components.length === 0 ? product_in_scenarios : _.intersection( active_scenarios, product_in_scenarios );

					if ( product_type === 'variable' && variation_id > 0 ) {

						if ( product_intersection.length > 0 ) {

							var variation_in_scenarios = ( variation_id in item_scenario_data ) ? manager.filter_scenarios_by_type( item_scenario_data[ variation_id ], type ) : [];

							composite.console_log( 'debug:scenarios', 'Variation selection #' + variation_id + ' of "' + component.get_title() + '" in Scenarios: [' + variation_in_scenarios +']' );

							product_intersection = _.intersection( product_intersection, variation_in_scenarios );
						}
					}

					if ( ! skip_invalid || product_intersection.length > 0 ) {

						scenario_shaping_components.push( component.component_id );

						composite.console_log( 'debug:scenarios', 'Active Scenarios: [' + product_intersection + ']' );
						active_scenarios = product_intersection;
					}
				}
			}

			if ( 'conditional_components' === type ) {
				manager_data.hidden_components = hidden_components;
			}

			// Filter out any scenarios where all scenario shaping components are masked.
			if ( is_masked && scenario_shaping_components.length ) {
				composite.console_log( 'debug:scenarios', 'Removing scenarios where all scenario shaping components (' + scenario_shaping_components + ') are masked...' );
				active_scenarios = manager.filter_unmatched_scenarios( active_scenarios, scenario_shaping_components );
			}

			// Filter out any scenarios that contain any non-shaping AND non-masked components.
			if ( is_strict ) {
				composite.console_log( 'debug:scenarios', 'Removing scenarios that contain any non-shaping + non-masked components...' );
				active_scenarios = manager.clean_partially_matched_scenarios( active_scenarios, scenario_shaping_components );
			}

			composite.console_log( 'debug:scenarios', 'Calculated scenarios: [' + active_scenarios + ']\n' );

			return active_scenarios;
		};

		/**
		 * Filters out scenarios where all shaping components are masked.
		 */
		this.filter_unmatched_scenarios = function( scenarios, scenario_shaping_components ) {

			var masked           = this.get_scenario_data().scenario_settings.masked_components,
				scenarios_length = scenarios.length,
				clean            = [];

			if ( scenario_shaping_components.length > 0 ) {

				if ( scenarios_length > 0 ) {
					for ( var i = 0; i < scenarios_length; i++ ) {

						var scenario_id = scenarios[ i ];

						// If all scenario shaping components are masked, filter out the scenario.
						var all_components_masked_in_scenario = true;

						for ( var k = 0, k_max = scenario_shaping_components.length; k < k_max; k++ ) {

							var component_id = scenario_shaping_components[ k ];

							if ( $.inArray( component_id.toString(), masked[ scenario_id ] ) == -1 ) {
								all_components_masked_in_scenario = false;
								break;
							}
						}

						if ( ! all_components_masked_in_scenario ) {
							clean.push( scenario_id );
						}
					}
				}

			} else {
				clean = scenarios;
			}

			if ( clean.length === 0 && scenarios_length > 0 ) {
				clean = scenarios;
			}

			return clean;
		};

		/**
		 * Filters scenarios by type.
		 */
		this.filter_scenarios_by_type = function( scenarios, type ) {

			var filtered         = [],
				scenarios_length = scenarios.length,
				scenario_id      = '';

			if ( scenarios_length > 0 ) {
				for ( var i = 0; i < scenarios_length; i++ ) {

					scenario_id = scenarios[ i ];

					if ( '0' === scenario_id && 'compat_group' === type ) {
						filtered.push( scenario_id );
					} else if ( 'all' === type || $.inArray( type, this.get_scenario_data().scenario_settings.scenario_actions[ scenario_id ] ) > -1 ) {
						filtered.push( scenario_id );
					}
				}
			}

			return filtered;
		};

		/**
		 * Filters out scenarios where a component is masked.
		 */
		this.clean_masked_component_scenarios = function( scenarios, component_id ) {

			var masked           = this.get_scenario_data().scenario_settings.masked_components,
				scenarios_length = scenarios.length,
				clean            = [],
				scenario_id      = '';

			if ( scenarios_length > 0 ) {
				for ( var i = 0; i < scenarios_length; i++ ) {

					scenario_id = scenarios[ i ];

					if ( $.inArray( component_id.toString(), masked[ scenario_id ] ) == -1 ) {
						clean.push( scenario_id );
					}

				}
			}

			return clean;
		};

		/**
		 * Returns scenarios where a component is masked.
		 */
		this.get_masked_component_scenarios = function( scenarios, component_id ) {

			var masked           = this.get_scenario_data().scenario_settings.masked_components,
				scenarios_length = scenarios.length,
				dirty            = [],
				scenario_id      = '';

			if ( scenarios_length > 0 ) {
				for ( var i = 0; i < scenarios_length; i++ ) {

					scenario_id = scenarios[ i ];

					if ( $.inArray( component_id.toString(), masked[ scenario_id ] ) > -1 ) {
						dirty.push( scenario_id );
					}

				}
			}

			return dirty;
		};

		/**
		 * Filters out scenarios that specify a component in the conditions.
		 */
		this.get_unmasked_component_scenarios = function( scenarios, component_id ) {

			var masked           = this.get_scenario_data().scenario_settings.masked_components,
				scenarios_length = scenarios.length,
				unmasked         = [],
				scenario_id      = '';

			if ( scenarios_length > 0 ) {
				for ( var i = 0; i < scenarios_length; i++ ) {

					scenario_id = scenarios[ i ];

					if ( $.inArray( component_id.toString(), masked[ scenario_id ] ) === -1 ) {
						unmasked.push( scenario_id );
					}

				}
			}

			return unmasked;
		};

		/**
		 * Filters out scenarios that require a component to be have a specific value in order to be fully matched (not masked or 'any').
		 */
		this.get_unmatched_component_scenarios = function( scenarios, component_id ) {

			var masked           = this.get_scenario_data().scenario_settings.masked_components,
				any              = this.get_scenario_data().scenario_settings.any_components,
				scenarios_length = scenarios.length,
				unmatched        = [],
				scenario_id      = '';

			if ( scenarios_length > 0 ) {
				for ( var i = 0; i < scenarios_length; i++ ) {

					scenario_id = scenarios[ i ];

					if ( $.inArray( component_id.toString(), masked[ scenario_id ] ) === -1 && $.inArray( component_id.toString(), any[ scenario_id ] ) === -1 ) {
						unmatched.push( scenario_id );
					}

				}
			}

			return unmatched;
		};

		/*
		 * Filters out partially-matched scenarios if their conditions contain non-shaping components that must have a specific value (not masked or 'any').
		 */
		this.clean_partially_matched_scenarios = function( scenarios, scenario_shaping_components ) {

			var non_shaping_components = _.difference( _.pluck( composite.get_components(), 'component_id' ), scenario_shaping_components );

			for ( var index = 0, length = non_shaping_components.length; index < length; index++ ) {
				scenarios = _.difference( scenarios, this.get_unmatched_component_scenarios( scenarios, non_shaping_components[ index ] ) );
			}

			return scenarios;
		};

		/*
		 * Filters out any matched scenarios if their conditions contain hidden components that must have a specific value (not masked or 'any'):
		 */
		this.clean_hidden_component_scenarios = function( scenarios, hidden_components ) {

			for ( var index = 0, length = hidden_components.length; index < length; index++ ) {
				scenarios = _.difference( scenarios, this.get_unmasked_component_scenarios( scenarios, hidden_components[ index ] ) );
			}

			return scenarios;
		};

		/**
		 * Returns the visibility status of a component.
		 */
		this.is_component_hidden = function( component_id ) {
			return _.includes( manager_data.hidden_components, component_id.toString() );
		};

		/**
		 * Returns all hidden components.
		 */
		this.get_hidden_components = function() {
			return manager_data.hidden_components;
		};
	};



	/**
	 * Factory class for creating new step objects.
	 */
	wc_cp_classes.WC_CP_Step_Factory = function() {

		/**
		 * Step class.
		 */
		function WC_CP_Step( composite, $step, index ) {

			this.composite  = composite;
			this.$step      = $step;
			this.step_index = index;

			this.init_step();
		}

		/**
		 * Initialize Step props.
		 */
		WC_CP_Step.prototype.init_step = function() {

			this.step_id                = this.$step.data( 'item_id' );
			this.step_title             = this.$step.data( 'nav_title' );
			this.step_slug              = this.composite.settings.slugs[ this.step_id ];

			this._component_messages    = [];
			this._composite_messages    = [];

			this._is_component          = this.$step.hasClass( 'component' );
			this._is_review             = this.$step.hasClass( 'cart' );

			this._is_current            = this.$step.hasClass( 'active' );
			this._is_previous           = this.$step.hasClass( 'prev' );
			this._is_next               = this.$step.hasClass( 'next' );
			this._is_last               = this.$step.hasClass( 'last' );

			this._toggled               = this.$step.hasClass( 'toggled' );

			this._autotransition        = this.$step.hasClass( 'autotransition' );

			this._autoselect_attributes = this.$step.hasClass( 'autoselect_attributes' );

			this.$el                    = this.$step;
			this.$inner_el              = this.$step.find( '.component_inner' );

			this.$step_title            = this.$step.find( '.step_title_wrapper' );
			this.$step_title_aria       = this.$step_title.find( '.aria_title' );

			/**
			 * Update current step pointers when the visibility of a step changes.
			 */
			this.composite.actions.add_action( 'step_visibility_changed', this.step_visibility_changed_handler, 10, this );
		};

		/**
		 * Current step updates pointers when the visibility of a step changes.
		 */
		WC_CP_Step.prototype.step_visibility_changed_handler = function() {

			var composite = this.composite;

			if ( composite.settings.layout !== 'paged' ) {
				if ( false === this.is_visible() ) {
					if ( ! composite.is_initialized ) {
						this.$el.hide();
					} else {
						this.$el.slideUp( 200 );
					}
				} else {
					this.$el.slideDown( 200 );
				}
			}

			if ( ! composite.is_initialized ) {
				return false;
			}

			if ( this.is_current() ) {
				composite.set_current_step( composite.get_current_step() );
			}
		};

		/**
		 * True if the step is configured to transition automatically to the next when a valid selection is made.
		 */
		WC_CP_Step.prototype.maybe_autotransition = function() {
			return this._autotransition && 'single' !== this.composite.settings.layout;
		};

		/**
		 * True if the step is configured to transition automatically to the next when a valid selection is made and no configuration is required in this step.
		 */
		WC_CP_Step.prototype.can_autotransition = function() {
			return this.maybe_autotransition() && this.passes_validation() && this.is_in_stock( false ) && this.get_selected_product() > 0 && 'invalid-product' !== this.get_selected_product_type() && false === this.is_selected_product_configurable() && ( 'progressive' !== this.composite.settings.layout || ! this.is_last() );
		};

		/**
		 * True if the step is configured to autoselect variable product attributes when a single, well-defined variation is active.
		 */
		WC_CP_Step.prototype.autoselect_attributes = function() {
			return this._autoselect_attributes;
		};

		/**
		 * Reads the navigation permission of this step.
		 */
		WC_CP_Step.prototype.is_animating = function() {
			return this.$el.hasClass( 'animating' );
		};

		/**
		 * True if the step UI is toggled.
		 */
		WC_CP_Step.prototype.has_toggle = function() {
			return this._toggled;
		};

		/**
		 * Reads the navigation permission of this step.
		 */
		WC_CP_Step.prototype.is_locked = function() {

			var is_locked = this.step_access_model.get( 'is_locked' );

			// Pass through 'step_is_locked' filter - @see WC_CP_Filters_Manager class.
			return this.composite.filters.apply_filters( 'step_is_locked', [ is_locked, this ] );
		};

		/**
		 * True if the step is visible.
		 */
		WC_CP_Step.prototype.is_visible = function() {
			return this.step_visibility_model.get( 'is_visible' );
		};

		/**
		 * Forbids navigation to this step.
		 */
		WC_CP_Step.prototype.lock = function() {
			this.step_access_model.set( { locked: true } );
		};

		/**
		 * Permits navigation to this step.
		 */
		WC_CP_Step.prototype.unlock = function() {
			this.step_access_model.set( { locked: false } );
		};

		/**
		 * Numeric index of this step for use in titles.
		 */
		WC_CP_Step.prototype.get_title_index = function() {

			var step                = this,
				composite           = this.composite,
				hidden_steps_before = _.filter( composite.get_steps(), function( check_step ) {
				if ( false === check_step.step_visibility_model.get( 'is_visible' ) && check_step.step_index < step.step_index ) {
					return check_step;
				}
			} ).length;

			return this.step_index + 1 - hidden_steps_before;
		};

		WC_CP_Step.prototype.get_title = function() {
			return this.step_title;
		};

		WC_CP_Step.prototype.get_slug = function() {
			return this.step_slug;
		};

		WC_CP_Step.prototype.get_route = function() {
			return '#' + this.step_slug;
		};

		WC_CP_Step.prototype.get_element = function() {
			return this.$el;
		};

		WC_CP_Step.prototype.is_review = function() {
			return this._is_review;
		};

		WC_CP_Step.prototype.is_component = function() {
			return this._is_component;
		};

		WC_CP_Step.prototype.get_component = function() {

			if ( this._is_component ) {
				return this;
			} else {
				return false;
			}
		};

		WC_CP_Step.prototype.is_current = function() {
			return this._is_current;
		};

		WC_CP_Step.prototype.is_next = function() {
			return this._is_next;
		};

		WC_CP_Step.prototype.is_previous = function() {
			return this._is_previous;
		};

		WC_CP_Step.prototype.is_last = function() {
			return this._is_last;
		};

		/**
		 * Brings a new step into view - called when clicking on a navigation element.
		 */
		WC_CP_Step.prototype.show_step = function() {

			if ( this.is_locked() || this.is_animating() ) {
				return false;
			}

			var	is_current = this.is_current(),
				composite  = this.composite;

			if ( 'single' === composite.settings.layout ) {
				// Toggle open if possible.
				if ( composite.is_initialized ) {
					this.toggle_step( 'open', true );
				}
			}

			if ( ! is_current || ! composite.is_initialized ) {
				// Move active component.
				this.set_active();
			}

			// Run 'show_step' action - @see WC_CP_Actions_Dispatcher class description.
			composite.actions.do_action( 'show_step', [ this ] );
		};

		/**
		 * Gets the duration of a step transition.
		 */
		WC_CP_Step.prototype.get_step_transition_duration = function( prop ) {

			var duration = 0;

			if ( 'opacity' === prop ) {
				duration = 200;
			} else if ( 'height' === prop ) {
				duration = 150;
			} else if ( 'toggle' === prop ) {
				duration = 300;
			}

			// Pass through 'component_step_transition_animation_duration' filter - @see WC_CP_Filters_Manager class.
			return this.composite.filters.apply_filters( 'component_step_transition_animation_duration', [ duration, prop, this ] );
		};

		/**
		 * Sets a step as active by hiding the previous one and updating the steps' markup.
		 */
		WC_CP_Step.prototype.set_active = function() {

			var step          = this,
				composite     = this.composite,
				style         = composite.settings.layout,
				curr_step     = composite.get_current_step(),
				$el_out       = curr_step.$el,
				$el_in        = step.$el,
				el_in_height  = 0,
				el_out_height = 0;

			composite.set_current_step( step );

			// Run 'active_step_transition' action - @see WC_CP_Actions_Dispatcher class description.
			composite.actions.do_action( 'active_step_transition', [ this ] );

			if ( curr_step.step_id !== step.step_id ) {

				if ( style === 'paged' ) {

					// Prevent clicks while animating.
					composite.$composite_form_blocker.addClass( 'blocked' );

					composite.has_transition_lock = true;

					setTimeout( function() {

						var duration_opacity = step.get_step_transition_duration( 'opacity' ) - 10,
						    duration_height  = step.get_step_transition_duration( 'height' ) - 10,
						    el_out_classes   = 'faded',
						    el_in_classes    = 'faded invisible';

						$el_out.css( {
							transition: 'opacity ' + duration_opacity / 1000 + 's',
							'-webkit-transition': 'opacity ' + duration_opacity / 1000 + 's'
						} );

						setTimeout( function() {

							$el_out.addClass( el_out_classes );
							$el_in.addClass( el_in_classes );

						}, 1 );

						setTimeout( function() {

							// Measure height.
							el_out_height = $el_out.get( 0 ).getBoundingClientRect().height;
							if ( typeof el_out_height === 'undefined' ) {
								el_out_height = $el_out.outerHeight();
							}

							// Lock height.
							$el_out.addClass( 'invisible' );
							$el_out.css( {
								height: el_out_height + 'px',
								overflow: 'hidden',
								transition: 'height ' + duration_height / 1000 + 's'
							} );

							$el_in.css( {
								height: '0px',
								overflow: 'hidden',
								transition: 'height ' + duration_height / 1000 + 's',
								'-webkit-transition': 'height ' + duration_height / 1000 + 's'
							} ).show();

							// Run 'active_step_transition_start' action - @see WC_CP_Actions_Dispatcher class description.
							composite.actions.do_action( 'active_step_transition_start', [ step ] );

							composite.console_log( 'debug:animations', 'Starting transition...' );

							setTimeout( function() {
								// Measure incoming component height.
								el_in_height = $el_in.get( 0 ).scrollHeight;
								// Hide old view with a sliding effect.
								$el_out.css( {
									height: '0px'
								} );
								// Show new view with a sliding effect.
								$el_in.css( {
									height: el_in_height + 'px'
								} );
							}, 1 );

							setTimeout( function() {

								$el_out.hide();
								$el_out.removeClass( 'faded invisible' );
								$el_out.css( {
									height: '',
									overflow: '',
									transition: '',
									'-webkit-transition': ''
								} );

								$el_in.css( {
									height: '',
									overflow: '',
									transition: 'opacity ' + duration_opacity / 1000 + 's',
									'-webkit-transition': 'opacity ' + duration_opacity / 1000 + 's'
								} );

								setTimeout( function() {

									composite.console_log( 'debug:animations', 'Transition ended.' );

									// Run 'active_step_transition_end' action - @see WC_CP_Actions_Dispatcher class description.
									composite.actions.do_action( 'active_step_transition_end', [ step ] );

									$el_in.css( {
										transition: '',
										'-webkit-transition': ''
									} );

									if ( 'yes' === wc_composite_params.accessible_focus_enabled && step.$step_title_aria ) {
										step.$step_title_aria.trigger( 'focus' );
									}

								}, duration_opacity + 10 );

								setTimeout( function() {
									$el_in.removeClass( 'faded invisible' );
								}, 1 );

								composite.has_transition_lock = false;
								composite.$composite_form_blocker.removeClass( 'blocked' );

							}, duration_height + 10 );

						}, duration_opacity + 10 );

					}, 5 );

				} else {

					if ( style === 'progressive' ) {

						// Update blocks.
						step.update_block_state();
					}

					composite.has_transition_lock = true;

					setTimeout( function() {
						// Run 'active_step_transition_start' action - @see WC_CP_Actions_Dispatcher class description.
						composite.actions.do_action( 'active_step_transition_start', [ step ] );
					}, 5 );

					setTimeout( function() {
						// Run 'active_step_transition_end' action - @see WC_CP_Actions_Dispatcher class description.
						composite.actions.do_action( 'active_step_transition_end', [ step ] );

						composite.has_transition_lock = false;

					}, step.get_step_transition_duration( 'toggle' ) + 50 );

				}

			} else {
				step.$el.show();
			}

			// Run 'active_step_changed' action - @see WC_CP_Actions_Dispatcher class description.
			composite.actions.do_action( 'active_step_changed', [ this ] );
		};

		/**
		 * Updates the block state of a progressive step that's brought into view.
		 */
		WC_CP_Step.prototype.update_block_state = function() {

			var step  = this,
				style = this.composite.settings.layout;

			if ( style !== 'progressive' ) {
				return false;
			}

			for ( var index = 0, steps = this.composite.get_steps(), length = steps.length; index < length; index++ ) {

				if ( steps[ index ].step_index < step.step_index ) {

					steps[ index ].block_step_inputs();

					// Do not close when the component is set to remain open when blocked.
					if ( ! steps[ index ].$el.hasClass( 'block-open' ) ) {
						steps[ index ].toggle_step( 'closed', true );
					}
				}
			}

			this.unblock_step_inputs();
			this.unblock_step();

			this.block_next_steps();
		};

		/**
		 * Unblocks access to step in progressive mode.
		 */
		WC_CP_Step.prototype.unblock_step = function() {
			this.toggle_step( 'open', true );
			this.$el.removeClass( 'blocked' );
		};

		/**
		 * Blocks access to all later steps in progressive mode.
		 */
		WC_CP_Step.prototype.block_next_steps = function() {

			var min_block_index = this.step_index;

			for ( var index = 0, steps = this.composite.get_steps(), length = steps.length; index < length; index++ ) {

				if ( index > min_block_index ) {

					if ( steps[ index ].$el.hasClass( 'disabled' ) ) {
						steps[ index ].unblock_step_inputs();
					}

					steps[ index ].block_step();
				}
			}
		};

		/**
		 * Blocks access to step in progressive mode.
		 */
		WC_CP_Step.prototype.block_step = function() {
			this.$el.addClass( 'blocked' );
			this.toggle_step( 'closed', false );
		};

		/**
		 * Toggle step in progressive mode.
		 */
		WC_CP_Step.prototype.toggle_step = function( state, active, complete ) {

			if ( this.has_toggle() ) {

				if ( state === 'open' ) {
					if ( this.$el.hasClass( 'closed' ) ) {
						wc_cp_toggle_element( this.$el, this.$inner_el, complete, this.get_step_transition_duration( 'toggle' ) );
					}

				} else if ( state === 'closed' ) {
					if ( this.$el.hasClass( 'open' ) ) {
						wc_cp_toggle_element( this.$el, this.$inner_el, complete, this.get_step_transition_duration( 'toggle' ) );
					}
				}

				if ( active ) {
					this.$step_title.removeClass( 'inactive' );
				} else {
					this.$step_title.addClass( 'inactive' );
				}
			}
		};

		/**
		 * Unblocks step inputs.
		 */
		WC_CP_Step.prototype.unblock_step_inputs = function() {

			this.$el.removeClass( 'disabled' );

			var reset_options = this.$el.find( '.clear_component_options' );
			reset_options.html( wc_composite_params.i18n_clear_selection ).removeClass( 'reset_component_options' );
		};

		/**
		 * Blocks step inputs.
		 */
		WC_CP_Step.prototype.block_step_inputs = function() {

			this.$el.addClass( 'disabled' );

			if ( ! this.has_toggle() || this.$el.hasClass( 'block-open' ) ) {
				var reset_options = this.$el.find( '.clear_component_options' );
				reset_options.html( wc_composite_params.i18n_reset_selection ).addClass( 'reset_component_options' );
			}
		};

		/**
		 * True if access to the step is blocked (progressive mode).
		 */
		WC_CP_Step.prototype.is_blocked = function() {
			return this.$el.hasClass( 'blocked' );
		};

		/**
		 * True if access to the step inputs is blocked (progressive mode).
		 */
		WC_CP_Step.prototype.has_blocked_inputs = function() {
			return this.$el.hasClass( 'disabled' );
		};

		/**
		 * Adds a validation message.
		 */
		WC_CP_Step.prototype.add_validation_message = function( message, scope ) {

			scope = typeof( scope ) === 'undefined' ? 'component' : scope;

			if ( scope === 'composite' ) {
				this._composite_messages.push( message.toString() );
			} else {
				this._component_messages.push( message.toString() );
			}
		};

		/**
		 * Get all validation messages.
		 */
		WC_CP_Step.prototype.get_validation_messages = function( scope ) {

			var messages;

			scope = typeof( scope ) === 'undefined' ? 'component' : scope;

			if ( scope === 'composite' ) {
				messages = this._composite_messages;
			} else {
				messages = this._component_messages;
			}

			// Pass through 'step_validation_messages' filter - @see WC_CP_Filters_Manager class.
			return this.composite.filters.apply_filters( 'step_validation_messages', [ messages, scope, this ] );
		};

		/**
		 * Validate component selection and stock status and add validation messages.
		 */
		WC_CP_Step.prototype.validate = function() {

			var is_valid    = true,
				is_in_stock = true;

			this._component_messages = [];
			this._composite_messages = [];

			if ( this.is_component() ) {

				var product_id   = this.get_selected_product(),
					product_type = this.get_selected_product_type();

				is_valid = this.has_valid_selections();

				if ( ! is_valid ) {
					if ( product_id > 0 ) {

						if ( product_type === 'invalid-product' ) {

							this.add_validation_message( wc_composite_params.i18n_item_unavailable_text, 'composite' );

						} else {

							if ( product_type === 'variable' ) {

								if ( ! this.is_selected_variation_valid() ) {
									this.add_validation_message( wc_composite_params.i18n_selected_product_options_invalid );
									this.add_validation_message( wc_composite_params.i18n_selected_product_options_invalid, 'composite' );
								} else {
									this.add_validation_message( wc_composite_params.i18n_select_product_options );
									this.add_validation_message( wc_composite_params.i18n_select_product_options_for, 'composite' );
								}
							}

							if ( this.has_required_addons() && ! this.has_valid_required_addons() ) {
								this.add_validation_message( wc_composite_params.i18n_select_product_addons );
								this.add_validation_message( wc_composite_params.i18n_select_product_addons_for, 'composite' );
							}

							if ( this.is_nyp() && ! this.is_valid_nyp() ) {
								this.add_validation_message( wc_composite_params.i18n_enter_valid_price );
								this.add_validation_message( wc_composite_params.i18n_enter_valid_price_for, 'composite' );
							}
						}

					} else {

						if ( ! this.is_selected_product_valid() ) {
							this.add_validation_message( wc_composite_params.i18n_selected_product_invalid );
							this.add_validation_message( wc_composite_params.i18n_selected_product_invalid, 'composite' );
						} else {
							this.add_validation_message( wc_composite_params.i18n_select_component_option );
							this.add_validation_message( wc_composite_params.i18n_select_component_option_for, 'composite' );
						}
					}
				}

				if ( ! this.is_in_stock() ) {
					is_in_stock = false;
				}
			}

			// Pass through 'step_is_valid' filter - @see WC_CP_Filters_Manager class.
			is_valid = this.composite.filters.apply_filters( 'step_is_valid', [ is_valid, this ] );

			// Run 'validate_step' action - @see WC_CP_Actions_Dispatcher class description.
			this.composite.actions.do_action( 'validate_step', [ this, is_valid ] );

			this.step_validation_model.update( is_valid, is_in_stock );
		};

		/**
		 * Checks if any validation messages exist.
		 */
		WC_CP_Step.prototype.passes_validation = function() {

			return this.step_validation_model.get( 'passes_validation' );
		};

		/**
		 * Prototype-based inheritance.
		 */
		var get_prototype = function( o ) {
			var F = function() {};
			F.prototype = o.prototype;
			return new F();
		};

		/**
		 * Component class - inherits the methods WC_CP_Step.
		 */
		function WC_CP_Component( composite, $component, index ) {

			// This allows us to inherit any props of WC_CP_Step created in its constructor.
			WC_CP_Step.call( this, composite, $component, index );

			this.init_component();
		}

		WC_CP_Component.prototype             = get_prototype( WC_CP_Step );
		WC_CP_Component.prototype.constructor = WC_CP_Component;

		/**
		 * Initialize Component props.
		 */
		WC_CP_Component.prototype.init_component = function() {

			this.initializing_scripts = false;

			this.component_index = this.step_index;
			this.component_id    = this.$step.attr( 'data-item_id' );
			this.component_title = this.$step.data( 'nav_title' );

			this._hide_disabled_products   = this.$step.hasClass( 'hide-incompatible-products' );
			this._hide_disabled_variations = this.$step.hasClass( 'hide-incompatible-variations' );
			this._is_static                = this.$step.hasClass( 'static' );
			this._is_lazy_loaded           = this.$step.hasClass( 'lazy-load' );

			this.$component_summary         = this.$step.find( '.component_summary' );
			this.$component_summary_content = this.$step.find( '.component_summary > .content' );
			this.$component_selections      = this.$step.find( '.component_selections' );
			this.$component_content         = this.$step.find( '.component_content' );
			this.$component_options         = this.$step.find( '.component_options' );
			this.$component_filters         = this.$step.find( '.component_filters' );
			this.$component_ordering        = this.$step.find( '.component_ordering select' );
			this.$component_options_inner   = this.$step.find( '.component_options_inner' );
			this.$component_inner           = this.$step.find( '.component_inner' );
			this.$component_pagination      = this.$step.find( '.component_pagination' );
			this.$component_message         = this.$step.find( '.component_message' );

			this.$component_quantity             = this.$component_summary_content.find( '.component_wrap input.qty' );
			this.$component_options_select       = this.$component_options.find( 'select.component_options_select' );
			this.$component_thumbnail_options    = this.$component_options.find( '.component_option_thumbnails' );
			this.$component_radio_button_options = this.$component_options.find( '.component_option_radio_buttons' );

			this.$component_content_scroll_target = this.$step.find( '.scroll_show_component_details' );

			this.component_addons_totals_html = '';
			this.$component_addons_totals     = false;
			this.$required_addons             = false;
			this.$component_selection_gallery = false;

			this.$component_variations_reset_wrapper = false;

			this.show_addons_totals        = false;
			this.has_wc_core_gallery_class = false;

			if ( 0 === this.$component_content_scroll_target.length ) {
				this.$component_content_scroll_target = this.$component_content;
			}
		};

		/**
		 * True when component options are appended using a 'Load More' button, instead of paginated.
		 */
		WC_CP_Component.prototype.is_lazy_loaded = function() {
			return this._is_lazy_loaded;
		};

		/**
		 * True when component options are appended using a 'Load More' button, instead of paginated.
		 */
		WC_CP_Component.prototype.set_lazy_loaded = function( value ) {
			this._is_lazy_loaded = value;
		};

		/**
		 * True when component options are appended using a 'Load More' button, instead of paginated.
		 */
		WC_CP_Component.prototype.append_results = function() {
			return 'yes' === this.composite.settings.pagination_data[ this.step_id ].append_results;
		};

		/**
		 * Results per page.
		 */
		WC_CP_Component.prototype.get_results_per_page = function() {
			return this.composite.settings.pagination_data[ this.step_id ].results_per_page;
		};

		/**
		 * Max results.
		 */
		WC_CP_Component.prototype.get_max_results = function() {
			return this.composite.settings.pagination_data[ this.step_id ].max_results;
		};

		/**
		 * Pagination range.
		 */
		WC_CP_Component.prototype.get_pagination_range = function( mid_or_end ) {

			if ( typeof( mid_or_end ) === 'undefined' ) {
				mid_or_end = 'mid';
			}

			var prop = mid_or_end === 'end' ? 'pagination_range_end' : 'pagination_range';

			return this.composite.settings.pagination_data[ this.step_id ][ prop ];
		};

		/**
		 * Relocation mode.
		 */
		WC_CP_Component.prototype.get_relocation_mode = function() {

			return this.composite.settings.pagination_data[ this.step_id ].relocation_mode;
		};

		/**
		 * Gets the selected option id from the component selection model.
		 */
		WC_CP_Component.prototype.get_selected_product = function( check_invalid, check_visibility ) {

			if ( typeof( check_invalid ) === 'undefined' ) {
				check_invalid = true;
			}

			if ( typeof( check_visibility ) === 'undefined' ) {
				check_visibility = false;
			}

			if ( check_invalid && ! this.is_selected_product_valid() ) {
				return null;
			}

			if ( check_visibility && ! this.is_visible() ) {
				return null;
			}

			return this.component_selection_model.get( 'selected_product' );
		};

		/**
		 * Gets the selected option id from the component selection model.
		 */
		WC_CP_Component.prototype.get_selected_variation = function( check_invalid ) {

			if ( typeof( check_invalid ) === 'undefined' ) {
				check_invalid = true;
			}

			if ( check_invalid && ! this.is_selected_variation_valid() ) {
				return null;
			}

			return this.component_selection_model.get( 'selected_variation' );
		};

		/**
		 * Gets the selected product/variation quantity from the component selection model.
		 */
		WC_CP_Component.prototype.get_selected_quantity = function() {

			if ( false === this.is_visible() ) {
				return 0;
			}

			return this.component_selection_model.get( 'selected_quantity' );
		};

		/**
		 * Get the product type of the selected product.
		 */
		WC_CP_Component.prototype.get_selected_product_type = function() {
			return this.component_selection_model.get_type();
		};

		/**
		 * Gets the (formatted) product title from the component selection model.
		 */
		WC_CP_Component.prototype.get_selected_product_title = function( formatted, check_invalid ) {

			check_invalid = typeof( check_invalid ) === 'undefined' ? false : check_invalid;
			formatted     = typeof( formatted ) === 'undefined' ? false : formatted;

			if ( check_invalid && ! this.is_selected_product_valid() ) {
				return '';
			}

			var selected_product = this.get_selected_product( false ),
				qty              = this.get_selected_quantity(),
				title            = '',
				formatted_title  = '',
				formatted_meta   = '',
				formatted_qty    = '';

			if ( selected_product === '' ) {
				title = wc_composite_params.i18n_no_selection;
			} else if ( selected_product !== '' && this.component_options_model.available_options_data.length > 0 ) {
				for ( var index = 0, available_options_data = this.component_options_model.available_options_data, length = available_options_data.length; index < length; index++ ) {
					if ( available_options_data[ index ].option_id === selected_product ) {
						title = available_options_data[ index ].option_title;
						break;
					}
				}
			}

			// Pass through 'component_selection_title' filter - @see WC_CP_Filters_Manager class.
			title = this.composite.filters.apply_filters( 'component_selection_title', [ title, this ] );

			if ( title && formatted ) {

				if ( '' === selected_product ) {
					formatted_title = '<span class="content_product_title none">' + title + '</span>';
				} else {

					formatted_qty   = qty > 1 ? '<strong>' + wc_composite_params.i18n_qty_string.replace( '%s', qty ) + '</strong>' : '';
					formatted_title = wc_composite_params.i18n_title_string.replace( '%t', title ).replace( '%q', formatted_qty ).replace( '%p', '' );
					formatted_meta  = this.get_selected_product_meta( true );

					if ( formatted_meta ) {
						formatted_title = wc_composite_params.i18n_selected_product_string.replace( '%t', formatted_title ).replace( '%m', formatted_meta );
					}

					formatted_title = '<span class="content_product_title">' + formatted_title + '</span>';
				}

				// Pass through 'component_selection_formatted_title' filter - @see WC_CP_Filters_Manager class.
				formatted_title = this.composite.filters.apply_filters( 'component_selection_formatted_title', [ formatted_title, title, qty, formatted_meta, this ] );
			}

			return formatted ? formatted_title : title;
		};

		/**
		 * Gets (formatted) meta for the selected product.
		 */
		WC_CP_Component.prototype.get_selected_product_meta = function( formatted ) {

			formatted = typeof( formatted ) === 'undefined' ? false : formatted;

			var formatted_meta = '',
				meta           = this.component_selection_model.get_meta_data();

			// Pass through 'component_selection_meta' filter - @see WC_CP_Filters_Manager class.
			meta = this.composite.filters.apply_filters( 'component_selection_meta', [ meta, this ] );

			if ( meta.length > 0 && formatted ) {

				formatted_meta = '<ul class="content_product_meta">';

				for ( var meta_index = 0, meta_length = meta.length; meta_index < meta_length; meta_index++ ) {

					formatted_meta = formatted_meta + '<li class="meta_element"><span class="meta_key">' + meta[ meta_index ].meta_key + ':</span> <span class="meta_value">' + meta[ meta_index ].meta_value + '</span>';

					if ( meta_index !== meta_length - 1 ) {
						formatted_meta = formatted_meta + '<span class="meta_element_sep">, </span>';
					}

					formatted_meta = formatted_meta + '</li>';
				}

				formatted_meta = formatted_meta + '</ul>';

				// Pass through 'component_selection_formatted_meta' filter - @see WC_CP_Filters_Manager class.
				formatted_meta = this.composite.filters.apply_filters( 'component_selection_formatted_meta', [ formatted_meta, meta, this ] );
			}

			return formatted ? formatted_meta : meta;
		};

		/**
		 * Gets image src for the selected product/variation.
		 */
		WC_CP_Component.prototype.get_selected_product_image_data = function( check_invalid ) {

			check_invalid = typeof( check_invalid ) === 'undefined' ? true : check_invalid;

			if ( check_invalid && ! this.is_selected_product_valid() ) {
				return false;
			}

			var selected_variation_image_data = this.get_selected_variation( check_invalid ) > 0 ? this.component_selection_model.get_variation_image_data() : false;

			return this.get_selected_variation( check_invalid ) > 0 && selected_variation_image_data ? selected_variation_image_data : this.component_selection_model.get_product_image_data();
		};

		WC_CP_Component.prototype.is_selected_product_configurable = function() {

			var product_data = this.component_options_model.get_option_data( this.get_selected_product() );

			if ( product_data && product_data.is_configurable ) {
				return true;
			}

			return false;
		};

		/**
		 * True if the currently selected product is incompatible based on the active scenarios.
		 */
		WC_CP_Component.prototype.is_selected_product_valid = function( active_options ) {

			if ( typeof( active_options ) === 'undefined' ) {
				active_options = this.component_options_model.get( 'options_state' ).active;
			}

			return this.component_selection_model.get( 'selected_product' ) === '' || _.includes( active_options, this.component_selection_model.get( 'selected_product' ) );
		};

		/**
		 * True if the currently selected variation is incompatible based on the active scenarios.
		 */
		WC_CP_Component.prototype.is_selected_variation_valid = function( active_options ) {

			if ( typeof( active_options ) === 'undefined' ) {
				active_options = this.component_options_model.get( 'options_state' ).active;
			}

			return this.component_selection_model.get( 'selected_variation' ) === '' || _.includes( active_options, this.component_selection_model.get( 'selected_variation' ) );
		};

		/**
		 * Validates the current selection.
		 */
		WC_CP_Component.prototype.has_valid_selections = function( check_scenarios ) {

			check_scenarios = typeof( check_scenarios ) === 'undefined' ? true : check_scenarios;

			var product_id   = this.get_selected_product( check_scenarios ),
				variation_id = this.get_selected_variation( check_scenarios ),
				product_type = this.get_selected_product_type(),
				valid        = false;

			// Always valid if invisible.
			if ( ! this.is_visible() ) {

				valid = true;

			// Check if valid selection present.
			} else if ( '' === product_id ) {

				if ( this.is_optional() || ( false === check_scenarios && this.maybe_is_optional() ) ) {
					valid = true;
				}

			} else if ( product_id > 0 && 'invalid-product' !== product_type ) {

				if ( 'variable' === product_type ) {

					if ( variation_id || ( this.get_selected_quantity() === 0 && this.composite.is_initialized ) ) {
						valid = true;
					}

				} else if ( 'simple' === product_type || 'bundle' === product_type || 'none' === product_type ) {
					valid = true;
				} else if ( this.get_selected_quantity() === 0 ) {
					valid = true;
				}
			}

			if ( valid && this.is_visible() && this.has_required_addons() ) {
				valid = this.has_valid_required_addons();
			}

			if ( valid && this.is_visible() && this.is_nyp() ) {
				valid = this.is_valid_nyp();
			}

			// Pass through 'component_is_valid' filter - @see WC_CP_Filters_Manager class.
			return this.composite.filters.apply_filters( 'component_is_valid', [ valid, check_scenarios, this ] );
		};

		/**
		 * Validates required addons.
		 */
		WC_CP_Component.prototype.has_valid_required_addons = function() {
			var $addons = this.$component_summary_content.find( 'input, textarea, select' ).filter( '[required]' );
			return $addons.filter( function() { return '' === this.value; } ).length === 0;
		};

		/**
		 * When true, hide incompatible/disabled products.
		 */
		WC_CP_Component.prototype.hide_disabled_products = function() {
			return this.composite.filters.apply_filters( 'component_hide_disabled_products', [ this._hide_disabled_products, this ] );
		};

		/**
		 * When true, hide incompatible/disabled variations.
		 */
		WC_CP_Component.prototype.hide_disabled_variations = function() {
			return this.composite.filters.apply_filters( 'component_hide_disabled_variations', [ this._hide_disabled_variations, this ] );
		};

		/**
		 * Find a param for the selected product in the DOM.
		 */
		WC_CP_Component.prototype.find_selected_product_param = function() {
			this.composite.console_log( 'error', '\nMethod \'WC_CP_Component::find_selected_product_param\' has been deprecated with no alternatives or fallbacks since v4.0.0. Please update your code!' );
			return false;
		};

		/**
		 * Find a pagination param in the DOM.
		 */
		WC_CP_Component.prototype.find_pagination_param = function( param ) {

			var data  = this.$component_pagination.first().data( 'pagination_data' ),
				value = 1;

			if ( data ) {
				if ( param === 'page' ) {
					value = data.page;
				} else if ( param === 'pages' ) {
					value = data.pages;
				}
			}

			return value;
		};

		/**
		 * Find active order by value in the DOM.
		 */
		WC_CP_Component.prototype.find_order_by = function() {

			var orderby = '';

			if ( this.$component_ordering.length > 0 ) {
				orderby = this.$component_ordering.val();
			}

			return orderby;
		};

		/**
		 * Find active component filters in the DOM.
		 */
		WC_CP_Component.prototype.find_active_filters = function() {

			var component_filters = this.$component_filters;
			var filters           = {};

			if ( component_filters.length == 0 ) {
				return filters;
			}

			component_filters.find( '.component_filter_option.selected' ).each( function() {

				var filter_type = $( this ).closest( '.component_filter' ).data( 'filter_type' );
				var filter_id   = $( this ).closest( '.component_filter' ).data( 'filter_id' );
				var option_id   = $( this ).data( 'option_id' );

				if ( filter_type in filters ) {

					if ( filter_id in filters[ filter_type ] ) {

						filters[ filter_type ][ filter_id ].push( option_id );

					} else {

						filters[ filter_type ][ filter_id ] = [];
						filters[ filter_type ][ filter_id ].push( option_id );
					}

				} else {

					filters[ filter_type ]              = {};
					filters[ filter_type ][ filter_id ] = [];
					filters[ filter_type ][ filter_id ].push( option_id );
				}

			} );

			return filters;
		};

		/**
		 * Find component options data in the DOM.
		 */
		WC_CP_Component.prototype.find_options_data = function() {
			return this.$component_options.data( 'options_data' );
		};

		/**
		 * False if the component has an out-of-stock availability class.
		 */
		WC_CP_Component.prototype.is_in_stock = function( check_quantity ) {

			check_quantity = typeof check_quantity === 'undefined' ? true : check_quantity;

			var is_in_stock = true;

			if ( ( ! check_quantity || this.get_selected_quantity() > 0 ) && 'out-of-stock' === this.component_selection_model.get_stock_status() ) {
				if ( this.get_selected_product_type() !== 'variable' || this.get_selected_variation( false ) > 0 ) {
					is_in_stock = false;
				}
			}

			return is_in_stock;
		};

		WC_CP_Component.prototype.is_nyp = function() {

			var product_data = this.component_options_model.get_option_data( this.get_selected_product() );

			if ( product_data && product_data.is_nyp ) {
				return true;
			}

			return false;
		};

		WC_CP_Component.prototype.is_valid_nyp = function() {

			var $nyp = this.$component_summary_content.find( '.nyp' ),
				nyp_script;

			if ( $nyp && $.fn.wc_nyp_get_script_object ) {
				nyp_script = $nyp.wc_nyp_get_script_object();
			}

			return nyp_script ? nyp_script.isValid() : true;
		};

		/**
		 * Gets the options style for this component.
		 */
		WC_CP_Component.prototype.has_options_style = function( style ) {
			return this.$el.hasClass( 'options-style-' + style );
		};

		/**
		 * Get the bundle script object.
		 *
		 */
		WC_CP_Component.prototype.get_bundle_script = function() {

			var bundle = false;

			if ( typeof( wc_pb_bundle_scripts[ this.component_id ] ) !== 'undefined' ) {
				bundle = wc_pb_bundle_scripts[ this.component_id ];
			}

			return bundle;
		};

		/**
		 * True if the selected option has addons.
		 */
		WC_CP_Component.prototype.has_addons = function() {

			var product_data = this.component_options_model.get_option_data( this.get_selected_product() );

			if ( product_data && product_data.has_addons ) {
				return true;
			}

			return false;
		};

		/**
		 * True if the selected option includes required addons.
		 */
		WC_CP_Component.prototype.has_required_addons = function() {

			var product_data = this.component_options_model.get_option_data( this.get_selected_product() );

			if ( product_data && product_data.has_required_addons ) {
				return true;
			}

			return false;
		};

		/**
		 * Initialize component scripts dependent on product type - called when selecting a new Component Option.
		 * When called with init = false, no type-dependent scripts will be initialized.
		 */
		WC_CP_Component.prototype.init_scripts = function( init ) {

			if ( typeof( init ) === 'undefined' ) {
				init = true;
			}

			this.$component_quantity          = this.$component_summary_content.find( '.component_wrap input.qty' );
			this.$component_selection_gallery = false;
			this.has_wc_core_gallery_class    = false;
			this.component_addons_totals_html = '';
			this.$component_addons_totals     = false;
			this.$required_addons             = false;
			this.show_addons_totals           = false;

			if ( init ) {

				this.initializing_scripts = true;

				this.init_qty_input();

				var product_type     = this.get_selected_product_type(),
					$summary_content = this.$component_summary_content;

				this.$component_selection_gallery = this.$component_summary_content.find( '.composited_product_images' );

				if ( this.$component_selection_gallery ) {
					this.has_wc_core_gallery_class = this.$component_selection_gallery.hasClass( 'images' );
				}

				if ( this.has_addons() ) {

					this.$required_addons = $summary_content.find( '.wc-pao-required-addon, .required-product-addon' );

					if ( 'bundle' === product_type ) {
						this.$component_addons_totals = $summary_content.find( '.bundle_data #product-addons-total' );
					} else {
						this.$component_addons_totals = $summary_content.find( '#product-addons-total' );
					}

					// Totals visible?
					if ( 1 == this.$component_addons_totals.data( 'show-sub-total' ) ) {
						// Ensure addons ajax is not triggered at all, as we calculate tax on the client side.
						this.$component_addons_totals.data( 'show-sub-total', 0 );
						this.show_addons_totals = 'bundle' !== product_type;
					}

				} else {
					this.$component_addons_totals = false;
				}

				if ( 'variable' === product_type ) {

					if ( ! $summary_content.hasClass( 'cart' ) ) {
						$summary_content.addClass( 'cart' );
					}

					if ( ! $summary_content.hasClass( 'variations_form' ) ) {
						$summary_content.addClass( 'variations_form' );
					}

					// Populate variations data.
					this.$component_summary_content.data( 'product_variations', this.component_selection_model.get_active_variations_data() );

					// Un-select initial set if incompatible.
					var $variations = this.$component_summary_content.find( '.variations' );

					if ( $variations.length > 0 ) {

						this.$component_variations_reset_wrapper = $variations.find( '.reset_variations_wrapper' );

						if ( this.$component_variations_reset_wrapper.length === 0 ) {
							this.$component_variations_reset_wrapper = false;
						}

						var variations_data     = this.component_selection_model.get_active_variations_data(),
							selected_attributes = wc_cp_get_variation_data( $variations, false, true ),
							matching_variations = this.get_matching_variations( variations_data, selected_attributes );

						if ( matching_variations.length === 0 ) {
							$variations.find( 'select' ).val( '' );
						}

						// Autoselect single-value attributes?
						if ( this.autoselect_attributes() ) {
							this.component_selection_view.autoselect_attributes();
						}
					}

					// Initialize variations script.
					$summary_content.wc_variation_form();

					// Fire change in order to save 'variation_id' input.
					$summary_content.find( '.variations select' ).last().trigger( 'change' );

					// Complete all pending animations.
					$summary_content.find( 'div' ).stop( true, true );

				} else if ( 'bundle' === product_type ) {

					if ( ! $summary_content.hasClass( 'bundle_form' ) ) {
						$summary_content.addClass( 'bundle_form' );
					}

					// Initialize bundles script now.
					$summary_content.find( '.bundle_data' ).wc_pb_bundle_form();

					// Complete all pending animations.
					$summary_content.find( 'div' ).stop( true, true );

				} else {

					if ( ! $summary_content.hasClass( 'cart' ) ) {
						$summary_content.addClass( 'cart' );
					}
				}

				this.initializing_scripts = false;
			}

			// Run 'component_scripts_initialized' action - @see WC_CP_Actions_Dispatcher class description.
			this.composite.actions.do_action( 'component_scripts_initialized', [ this ] );
		};

		/**
		 * Find matching variations for attributes.
		 */
		WC_CP_Component.prototype.get_matching_variations = function( variations, attributes ) {

			var matching = [];

			for ( var i = 0, len = variations.length; i < len; i++ ) {
				var variation = variations[i];

				if ( variation.variation_is_active && this.is_matching_variation( variation.attributes, attributes ) ) {
					matching.push( variation );
				}
			}

			return matching;
		};

		/**
		 * See if attributes match.
		 */
		WC_CP_Component.prototype.is_matching_variation = function( variation_attributes, attributes ) {

			var match = true;

			for ( var attr_name in variation_attributes ) {

				if ( ! variation_attributes.hasOwnProperty( attr_name ) ) {
					continue;
				}

				var val1 = variation_attributes[ attr_name ],
					val2 = attributes[ attr_name ];

				if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
					match = false;
				}
			}

			return match;
		};

		/**
		 * Resets all listeners before loading new product content and re-initializing any external scripts.
		 */
		WC_CP_Component.prototype.reset_scripts = function() {

			this.$component_summary_content.removeClass( 'variations_form bundle_form cart' );
			this.$component_summary_content.off().find( '*' ).off();

			// Run 'component_scripts_reset' action - @see WC_CP_Actions_Dispatcher class description.
			this.composite.actions.do_action( 'component_scripts_reset', [ this ] );
		};

		/**
		 * Get the step that corresponds to this component.
		 */
		WC_CP_Component.prototype.get_step = function() {
			return this.composite.get_step( this.component_id );
		};

		/**
		 * True if a Component is static (single option).
		 */
		WC_CP_Component.prototype.is_static = function() {
			return this._is_static;
		};

		/**
		 * True if a Component is optional taking the active scenarios into account.
		 */
		WC_CP_Component.prototype.is_optional = function() {

			var is_optional = _.includes( this.component_options_model.get( 'options_state' ).active, '' );

			// Pass through 'component_is_optional' filter - @see WC_CP_Filters_Manager class.
			return this.composite.filters.apply_filters( 'component_is_optional', [ is_optional, this ] );
		};

		/**
		 * Whether a Component is set as optional.
		 */
		WC_CP_Component.prototype.maybe_is_optional = function() {
			return 'yes' === this.composite.settings.optional_data[ this.step_id ];
		};

		/**
		 * Whether to show a placeholder option or not.
		 */
		WC_CP_Component.prototype.show_placeholder_option = function() {
			return 'yes' === this.composite.settings.show_placeholder_option[ this.step_id ];
		};

		/**
		 * Selected option price visibility.
		 */
		WC_CP_Component.prototype.is_selected_product_price_visible = function() {
			return 'yes' === this.composite.settings.selected_product_price_visibility_data[ this.step_id ];
		};

		/**
		 * Subtotal price visibility.
		 */
		WC_CP_Component.prototype.is_subtotal_visible = function() {
			return 'yes' === this.composite.settings.subtotal_visibility_data[ this.step_id ];
		};

		/**
		 * True if the component is priced individually.
		 */
		WC_CP_Component.prototype.is_priced_individually = function() {
			return 'yes' === this.composite.data_model.price_data.is_priced_individually[ this.component_id ];
		};

		/**
		 * Price display format.
		 */
		WC_CP_Component.prototype.get_price_display_format = function() {
			return this.composite.settings.price_display_data[ this.step_id ].format;
		};

		/**
		 * Formatted component option price.
		 */
		WC_CP_Component.prototype.get_formatted_option_price_html = function( option_data ) {

			var self              = this,
				composite         = this.composite,
				price_format      = self.get_price_display_format(),
				option_price_html = option_data.option_price_html,
				option_price_data = option_data.option_price_data;

			if ( self.is_priced_individually() && 'relative' === price_format ) {

				var reset_price            = false,
					is_relative_to_default = 'yes' === composite.settings.price_display_data[ self.step_id ].is_relative_to_default,
					is_relative_price      = true,
					is_reference_option    = is_relative_to_default && option_data.option_id === self.component_options_view.reference_option,
					reference_option_data  = self.component_options_view.get_reference_option_data(),
					has_valid_selections   = self.has_valid_selections( false );

				if ( is_relative_to_default && false === reference_option_data.option_id ) {
					is_relative_to_default = false;
				}

				if ( false === has_valid_selections && false === is_relative_to_default ) {
					is_relative_price = 'yes' !== composite.settings.price_display_data[ self.step_id ].show_absolute_if_invalid;
				}

				if ( is_relative_price ) {
					if ( is_reference_option ) {
						reset_price = true;
					} else if ( false === is_relative_to_default ) {
						if ( false === has_valid_selections || option_data.is_selected ) {
							reset_price = true;
						}
					}
				}

				if ( reset_price ) {

					option_price_html = '';

				} else {

					var	component_totals        = composite.data_model.calculate_component_subtotals( self, composite.data_model.price_data, 1 ),
						reference_price         = is_relative_to_default ? reference_option_data.option_price : component_totals.price,
						is_own_price            = option_data.is_selected && is_relative_price && is_relative_to_default && has_valid_selections,
						base_price              = is_relative_price ? reference_price : 0.0,
						option_price            = is_own_price ? component_totals.price : parseFloat( option_price_data.price ),
						option_regular_price    = is_own_price ? component_totals.regular_price : parseFloat( option_price_data.regular_price ),
						relative_price          = parseFloat( option_price ) - parseFloat( base_price ),
						relative_regular_price  = 0.0,
						relative_max_price      = 0.0,
						is_range                = false === is_own_price && ( option_price < option_price_data.max_price || '' === option_price_data.max_price ),
						per_unit_suffix         = option_price_data.min_qty > 1 ? wc_composite_params.i18n_per_unit_string : '',
						discount_suffix         = '',
						formatted_price         = '',
						formatted_regular_price = '',
						formatted_max_price     = '';

					formatted_price = self.get_formatted_price_html( relative_price, is_relative_price );

					// Plain price string without extra markup.
					if ( self.has_options_style( 'dropdowns' ) ) {

						if ( option_regular_price > option_price ) {

							if ( option_price_data.discount ) {
								discount_suffix = wc_composite_params.i18n_discount_string.replace( '%s', wc_cp_number_round( option_price_data.discount, 1 ) );
							}

							if ( ! discount_suffix && option_regular_price > option_price ) {
								discount_suffix = wc_composite_params.i18n_discount_string.replace( '%s', wc_cp_number_round( 100 * ( option_regular_price - option_price ) / option_regular_price, 1 ) );
							}

							// Pass through 'formatted_option_price_discount_suffix' filter - @see WC_CP_Filters_Manager class.
							discount_suffix = composite.filters.apply_filters( 'formatted_option_price_discount_suffix', [ discount_suffix, option_price_data, this ] );
						}

						if ( is_range ) {

							if ( '' === option_price_data.max_price ) {

								formatted_price = wc_composite_params.i18n_price_from_string_plain.replace( '%p', formatted_price );

							} else {

								relative_max_price  = parseFloat( option_price_data.max_price ) - parseFloat( base_price );
								formatted_max_price = self.get_formatted_price_html( relative_max_price, is_relative_price, relative_price );
								formatted_price     = wc_composite_params.i18n_price_range_string_plain.replace( '%f', formatted_price ).replace( '%t', formatted_max_price );
							}
						}

						formatted_price = wc_composite_params.i18n_price_string.replace( '%p', formatted_price ).replace( '%q', per_unit_suffix ).replace( '%d', discount_suffix );

					// Price string with markup.
					} else {

						if ( option_regular_price > option_price && ( ! is_range || '' === option_price_data.max_price ) ) {

							relative_regular_price  = parseFloat( option_regular_price ) - parseFloat( base_price );
							formatted_regular_price = self.get_formatted_price_html( relative_regular_price, is_relative_price, relative_price );
							formatted_price         = wc_composite_params.i18n_strikeout_price_string.replace( '%f', formatted_regular_price ).replace( '%t', formatted_price );
						}

						if ( is_range ) {

							if ( '' === option_price_data.max_price ) {

								formatted_price = wc_composite_params.i18n_price_from_string.replace( '%p', formatted_price );

							} else {

								relative_max_price  = parseFloat( option_price_data.max_price ) - parseFloat( base_price );
								formatted_max_price = self.get_formatted_price_html( relative_max_price, is_relative_price, relative_price );

								if ( false === is_relative_price ) {
									formatted_price = wc_composite_params.i18n_price_range_string_absolute.replace( '%f', formatted_price ).replace( '%t', formatted_max_price );
								} else {
									formatted_price = wc_composite_params.i18n_price_range_string.replace( '%f', formatted_price ).replace( '%t', formatted_max_price );
								}
							}
						}
					}

					option_price_html = formatted_price;
				}

				if ( ! option_price_html ) {
					if ( self.has_options_style( 'thumbnails' ) ) {
						option_price_html = '&nbsp;';
					} else if ( self.has_options_style( 'radios' ) ) {
						option_price_html = '&mdash;';
					}
				}
			}

			// Pass through 'formatted_option_price_html' filter - @see WC_CP_Filters_Manager class.
			return composite.filters.apply_filters( 'formatted_option_price_html', [ option_price_html, option_data, this ] );
		};

		/**
		 * Formats a signed relative price.
		 */
		WC_CP_Component.prototype.get_formatted_price_html = function( price, relative, ref_price ) {

			var formatted_price_sign = '',
				formatted_price      = wc_cp_price_format( Math.abs( price ), this.has_options_style( 'dropdowns' ) );

			relative  = typeof( relative ) === 'undefined' ? false : relative;
			ref_price = typeof( ref_price ) === 'undefined' ? false : ref_price;

			if ( relative ) {
				if ( price > 0 || ( price == 0 && ! ref_price ) ) {
					formatted_price_sign = '+';
				} else if ( price < 0 ) {
					formatted_price_sign = '-';
				} else {
					if ( ref_price >= 0 ) {
						formatted_price_sign = '+';
					} else {
						formatted_price_sign = '-';
					}
				}
			}

			formatted_price_sign = formatted_price_sign && this.has_options_style( 'dropdowns' ) ? formatted_price_sign : '<span class="relative-price-prefix">' + formatted_price_sign + '</span>';
			formatted_price      = wc_composite_params.i18n_price_signed.replace( '%s', formatted_price_sign ).replace( '%p', formatted_price );

			return formatted_price;
		};

		/**
		 * Initialize quantity input.
		 */
		WC_CP_Component.prototype.init_qty_input = function() {

			// Quantity buttons.
			if ( wc_composite_params.show_quantity_buttons === 'yes' ) {
				this.$component_summary_content.find( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
			}

			if ( 'hidden' === this.$component_quantity.attr( 'type' ) ) {
				this.$component_quantity.attr( 'min', this.$component_quantity.val() );
				this.$component_quantity.attr( 'max', this.$component_quantity.val() );
			}

			var initial_qty = this.composite.is_finalized ? this.get_selected_quantity() : this.$component_quantity.val();

			if ( 'yes' === this.composite.settings.component_qty_restore && this.get_selected_product( false ) ) {
				this.$component_quantity.val( initial_qty );
			}

			this.$component_quantity.trigger( 'change' );
		};

		/**
		 * Get component placeholder image.
		 */
		WC_CP_Component.prototype.get_placeholder_image_data = function() {
			return typeof( this.composite.settings.image_data[ this.step_id ] ) === 'undefined' ? false : this.composite.settings.image_data[ this.step_id ];
		};

		/**
		 * Factory methods.
		 */
		this.create_component = function( composite, $component, index ) {

			return new WC_CP_Component( composite, $component, index );
		};

		this.create_step = function( composite, $step, index ) {

			if ( $step.hasClass( 'component' ) ) {
				return this.create_component( composite, $step, index );
			}

			return new WC_CP_Step( composite, $step, index );
		};
	};



	/**
	 * Implements a simple promise-like task runner.
	 */
	wc_cp_classes.WC_CP_Async_Task = function( task_callback, interval ) {

		var _task              = this,
			_done              = false,
			_waited            = 0,
			_complete_callback = function( result ) { return result; };

		interval      = interval || 100;
		task_callback = task_callback.bind( this );

		/**
		 * True if the task is done working.
		 */
		this.is_done = function() {
			return _done;
		};

		/**
		 * Return total time waiting.
		 */
		this.get_async_time = function() {
			return _waited;
		};

		/**
		 * Runs the task.
		 */
		this.run = function( result ) {

			setTimeout( function() {

				result = task_callback( result );

				if ( ! _task.is_done() ) {
					_waited += interval;
					_task.run( result );
				} else {
					_complete_callback( result );
				}
			}, interval );
		};

		/**
		 * Runs when the task is complete.
		 */
		this.done = function() {
			_done = true;
		};

		/**
		 * Runs when the task is complete.
		 */
		this.complete = function( done ) {
			_complete_callback = done;
		};

		this.run();
	};


	/*-----------------------------------------------------------------*/
	/*  Initialization.                                                */
	/*-----------------------------------------------------------------*/

	$wc_cp_document.ready( function() {

		$wc_cp_body = $( document.body );

		/**
		 * QuickView compatibility.
		 */
		$wc_cp_body.on( 'quick-view-displayed', function() {

			$( '.quick-view .composite_form .composite_data' ).each( function() {
				$( this ).wc_composite_form();
			} );
		} );

		/**
		 * Responsive form CSS (we can't rely on media queries since we must work with the .composite_form width, not screen width).
		 */
		$wc_cp_window.resize( function() {

			for ( var index in wc_cp_composite_scripts ) {

				if ( ! wc_cp_composite_scripts.hasOwnProperty( index ) ) {
					continue;
				}

				clearTimeout( wc_cp_composite_scripts[ index ].timers.on_resize_timer );

				wc_cp_composite_scripts[ index ].timers.on_resize_timer = setTimeout( ( function( i ) {
					return function() {
						wc_cp_composite_scripts[ i ].on_resize_handler();
					};
				} ) ( index ), 50 );
			}
		} );

		/**
		 * Composite app initialization on '.composite_data' jQuery objects.
		 */
		$.fn.wc_composite_form = function() {

			if ( ! $( this ).hasClass( 'composite_data' ) ) {
				return true;
			}

			var composite_id    = $( this ).data( 'container_id' ),
				$composite_form = $( this ).closest( '.composite_form' );

			if ( typeof( wc_cp_composite_scripts[ composite_id ] ) !== 'undefined' ) {
				$composite_form.find( '*' ).off();
				for ( var component_index = 0, components = wc_cp_composite_scripts[ composite_id ].get_components(), components_length = components.length; component_index < components_length; component_index++ ) {
					components[ component_index ].reset_scripts();
				}
			}

			wc_cp_composite_scripts[ composite_id ] = new WC_CP_Composite( { $composite_form: $composite_form, $composite_data: $( this ) } );

			$composite_form.data( 'script_id', composite_id );

			wc_cp_composite_scripts[ composite_id ].init();
		};

		/*
		 * Initialize form script.
		 */
		$( '.composite_form .composite_data' ).each( function() {
			$( this ).wc_composite_form();
		} );

	} );

} ) ( jQuery, Backbone );
