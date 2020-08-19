<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Date_Time_Calculator extends GP_Plugin {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since 0.9
	 * @access private
	 * @var GF_Notification_Scheduler $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the Gravity Forms Notification Scheduler Add-On Add-On.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_version Contains the version.
	 */
	protected $_version = GP_DATE_TIME_CALCULATOR_VERSION;
	/**
	 * Defines the minimum Gravity Forms version required.
	 * @since 0.9
	 * @access protected
	 * @var string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '2.4.2.3';
	/**
	 * Defines the plugin slug.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gp-date-time-calculator';
	/**
	 * Defines the main plugin file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gp-date-time-calculator/gp-date-time-calculator.php';
	/**
	 * Defines the full path to this class file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;
	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string
	 */
	protected $_url = 'https://gravitywiz.com/documentation/gravity-forms-date-time-calculator/';
	/**
	 * Defines the title of this add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_title The title of the add-on.
	 */
	protected $_title = 'GP Date Time Calculator';
	/**
	 * Defines the short title of the add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_short_title The short title.
	 */
	protected $_short_title = 'Date Time Calculator';

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since 0.9
	 * @access public
	 * @static
	 * @return GP_Date_Time_Calculator $_instance An instance of the GP_Date_Time_Calculator class
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GP_Date_Time_Calculator();
		}

		return self::$_instance;
	}

    public function init() {

	    parent::init();

	    // # UI

        add_filter( 'gform_field_standard_settings_1600', array( $this, 'field_settings_ui' ) );
        add_filter( 'gform_custom_merge_tags', array( $this, 'add_date_time_fields_to_calc_merge_tags_select' ), 10, 4 );

        // # Submission

	    add_filter( 'gform_after_submission', array( $this, 'check_calculations' ), 10, 2 );
	    add_filter( 'gform_calculation_formula', array( $this, 'modify_calculation_formula' ), 10, 4 );

    }

    public function tooltips( $tooltips ) {
	    $tooltips[ $this->_slug . '_unit' ] = sprintf(
		    '<h6>%s</h6> %s',
		    __( 'Date Calculation Unit', 'gravityperks' ),
		    __( 'Select the unit of measurement in which the date calculation should be returned. For example, if you select "Days", the final result of this field will be calculated in days.',
			    'gravityperks' )
	    );

	    return $tooltips;
    }

    // # Settings

    public function add_date_time_fields_to_calc_merge_tags_select( $merge_tags, $form_id, $fields, $element_id ) {

        if( $element_id != 'field_calculation_formula' ) {
            return $merge_tags;
        }

        foreach( $fields as $field ) {
            if( in_array( GFFormsModel::get_input_type( $field ), array( 'date', 'time' ) ) ) {
                $merge_tags[] = array(
                    'tag'   => sprintf( '{%s:%d}', GFCommon::get_label( $field ), $field['id'] ),
                    'label' => GFCommon::get_label( $field )
                );
            }
        }

        return $merge_tags;
    }

    public function field_settings_ui() {
        ?>

        <li class="<?php echo $this->key( 'field_setting' ); ?> field_setting" style="display:none;">

            <label class="section_label" for="<?php echo $this->key( 'unit' ); ?>">
                <?php _e( 'Date Calculation Unit', 'gravityperks' ); ?>
                <?php gform_tooltip( $this->_slug . '_unit' ) ?>
            </label>

            <select id="<?php echo $this->key( 'unit' ); ?>">
                <?php foreach( $this->get_date_time_units() as $unit ): ?>
                    <option value="<?php echo $unit['slug']; ?>"><?php echo $unit['label']; ?></option>
                <?php endforeach; ?>
            </select>

        </li>

        <?php

        $this->field_settings_js();

    }

    public function field_settings_js() {
        ?>

        <script type="text/javascript">

            ( function( $ ) {

                $( document ).ready( function() {

                    // # Elements & Variables

                    var getKey         = function( key ) { return '<?php echo $this->key( '' ); ?>' + key; },
                        mergeTagRegEx  = /{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/i,
                        $unitSetting   = $( '.' + getKey( 'field_setting' ) ),
                        $unitElem      = $( '#' + getKey( 'unit' ) ),
                        $formulaElem   = $( '#field_calculation_formula' ),
                        $formulaSelect = $( '#field_calculation_formula_variable_select' );

                    // # Events

                    $unitElem.change( function() {
                        SetFieldProperty( getKey( 'unit' ), $( this ).val() );
                        toggleSettings();
                    } );

                    $formulaElem.change( function() {
                        toggleSettings();
                    } );

                    $formulaSelect.change( function() {
                        toggleSettings();
                    } );

                    $( document ).bind( 'gform_load_field_settings', function( event, field, form ) {

                        // populate current value back into setting on load
                        $unitElem.val( field[ getKey( 'unit' ) ] );

                        toggleSettings();

                    } );

                    // # Helpers

                    function toggleSettings() {

                        var field             = GetSelectedField(),
                            dateTimeInputType = formulaContainsDateTimeField( field.calculationFormula );

                        if( dateTimeInputType !== false ) {
                            $unitSetting.show();
                            if( ! field[ getKey( 'unit' ) ] ) {
                                var defaultUnit = dateTimeInputType == 'date' ? 'days' : 'hours';
                                $unitElem.val( defaultUnit ).change();
                            }
                        } else {
                            $unitSetting.hide();
                            SetFieldProperty( getKey( 'unit' ), false );
                        }

                    }

                    function formulaContainsDateTimeField( formula ) {

                        var matches = getMatchGroups( formula, mergeTagRegEx );

                        for( var i = 0; i < matches.length; i++ ) {

                            var fieldId   = matches[i][1],
                                field     = GetFieldById( fieldId );

                            if( ! field ) {
                                continue;
                            }

                            var inputType = GetInputType( field );

                            if( $.inArray( inputType, [ 'date', 'time' ] ) != -1 ) {
                                return inputType;
                            }

                        }

                        return false;
                    }

                } );

            } )( jQuery );

        </script>

        <?php
    }

    // # Functionality

    public function scripts() {

	    $scripts = array(
		    array(
			    'handle'    => 'moment',
			    'src'       => $this->get_base_url() . '/scripts/moment.min.js',
			    'version'   => $this->_version,
			    'enqueue'   => false,
		    ),
		    array(
			    'handle'    => 'gpdtc-frontend',
			    'src'       => $this->get_base_url() . '/scripts/frontend.js',
			    'version'   => $this->_version,
			    'deps'      => array( 'moment', 'jquery' ),
			    'callback'  => array( $this, 'add_init_script' ),
			    'enqueue'   => array(
				    array( $this, 'is_applicable_form' ),
			    )
		    ),
	    );

	    return array_merge( parent::scripts(), $scripts );

    }

    public function add_init_script( $form ) {

        $field_data = $this->get_date_fields_options( $form );

        if( empty( $field_data ) ) {
            return $form;
        }

        $args = array(
            'formId'         => $form['id'],
            'dateFieldsData' => $field_data
        );

        $script = 'new GWDTCalc( ' . json_encode( $args ) . ' );';
        $slug   = 'gw_date_time_calc';

        // Must manually require since plugins like Partial Entries and Nested Forms call gform_pre_render outside of the rendering context.
        require_once( GFCommon::get_base_path() . '/form_display.php' );

        GFFormDisplay::add_init_script( $form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );

	    wp_localize_script( 'gpdtc-frontend', 'GPDTC', array(
		    'GMT_OFFSET' => get_option( 'gmt_offset' ),
	    ) );

	    return $form;
    }

	public function get_date_fields_options( $form ) {

		$field_data = array();

		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return $field_data;
		}

		foreach ( $form['fields'] as $field ) {

			$options = $this->get_calc_field_options( $field, $form );

			if ( ! $options ) {
				continue;
			}

			$field_data[ $field['id'] ] = $options;

		}

		return $field_data;

	}

	public function is_applicable_field( $field ) {

		$form    = GFAPI::get_form( $field->formId );
		$options = $this->get_calc_field_options( $field, $form );

		return $options && is_array( $options );

	}

	public function get_field_date_formula_matches( $field ) {

		$is_calc_enabled = rgar( $field, 'enableCalculation' ) == true;
		$formula         = rgar( $field, 'calculationFormula' );

		if ( ! $is_calc_enabled || empty( $formula ) ) {
			return null;
		}

		preg_match_all( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/mi', $formula, $field_matches, PREG_SET_ORDER );
		preg_match_all( '/{today}|{now}/', $formula, $helper_matches, PREG_SET_ORDER );

		return array(
			'fields'  => $field_matches,
			'helpers' => $helper_matches,
		);

	}

	public function get_calc_field_options( $field, $form ) {

		if ( is_numeric( $field ) ) {
			$field = GFAPI::get_field( $form, $field );
		}

		$matches = $this->get_field_date_formula_matches( $field );

		if ( ! $matches ) {
			return null;
		}

		$formula_fields = array();

		foreach ( $matches['fields'] as $match ) {

			list( $full_match, $tag_field_id ) = $match;

			$tag_field  = GFFormsModel::get_field( $form, $tag_field_id );
			$input_type = GFFormsModel::get_input_type( $tag_field );

			if ( $input_type == 'time' ) {
				$formula_fields[] = array(
					'id'         => $tag_field['id'],
					'type'       => 'time',
					'timeFormat' => rgar( $tag_field, 'timeFormat' )
				);
			} elseif ( $input_type == 'date' ) {
				$formula_fields[] = array(
					'id'         => $tag_field['id'],
					'type'       => 'date',
					'dateType'   => $tag_field['dateType'], // 'datefield', 'datepicker', 'datedropdown'
					'dateFormat' => $tag_field['dateFormat'] ? $tag_field['dateFormat'] : 'mdy'
				);
			}

		}

		if ( ! empty( $formula_fields ) || ! empty( $matches['helpers'] ) ) {
			return array(
				'unit'   => $this->get_unit( rgar( $field, $this->key( 'unit' ) ) ),
				'fields' => $formula_fields
			);
		}

		return null;

	}

	public function key( $key ) {
		$prefix = isset( $this->prefix ) ? $this->prefix : $this->_slug . '_';

		return $prefix . $key;
	}

    public function get_date_field_options( $date_field_id, $calc_field_id, $form ) {

        $calc_field_options = $this->get_calc_field_options( $calc_field_id, $form );

        if( ! $calc_field_options ) {
            return false;
        }

        foreach( $calc_field_options['fields'] as $date_field ) {
            if( $date_field['id'] == $date_field_id ) {
                return $date_field;
            }
        }

        return false;
    }

    public function get_date_time_units() {
        return array(
            'seconds' => array(
                'label' => __( 'Seconds', 'gravityperks' ),
                'slug'  => 'seconds',
                'unit'  => 1
            ),
            'minutes' => array(
                'label' => __( 'Minutes', 'gravityperks' ),
                'slug'  => 'minutes',
                'unit'  => 60
            ),
            'hours' => array(
                'label' => __( 'Hours', 'gravityperks' ),
                'slug'  => 'hours',
                'unit'  => 60 * 60
            ),
            'days' => array(
                'label' => __( 'Days', 'gravityperks' ),
                'slug'  => 'days',
                'unit'  => 60 * 60 * 24
            ),
            'weeks' => array(
                'label' => __( 'Weeks', 'gravityperks' ),
                'slug'  => 'weeks',
                'unit'  => 60 * 60 * 24 * 7
            ),
            'months' => array(
                'label' => __( 'Months', 'gravityperks' ),
                'slug'  => 'months',
                'unit'  => 60 * 60 * 24 * 30
            ),
            'years' => array(
                'label' => __( 'Years', 'gravityperks' ),
                'slug'  => 'years',
                'unit'  => 60 * 60 * 24 * 365
            )
        );
    }

    public function get_unit( $unit_slug ) {

        $unit_slug = $unit_slug ? $unit_slug : 'seconds';
        $units = $this->get_date_time_units();

        return $units[$unit_slug];
    }

    public function is_applicable_form( $form ) {
        $field_data = $this->get_date_fields_options( $form );
        return ! empty( $field_data );
    }

    public function modify_calculation_formula( $formula, $field, $form, $entry ) {

        preg_match_all( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/mi', $formula, $matches, PREG_SET_ORDER );

        $calc_field_id = $field['id'];

        foreach( $matches as $match ) {

            list( $full_match, $input_id, , , $modifier ) = array_pad( $match, 5, false );

            $field_id = intval( $input_id );
            $calc_field_options = $this->get_calc_field_options( $calc_field_id, $form );
            $date_field_options = $this->get_date_field_options( $field_id, $calc_field_id, $form );

            if( ! $date_field_options ) {
                continue;
            }

            $value = $this->get_date_field_value( $date_field_options, $form, $entry );

            if( $modifier ) {

                $value = $this->handle_modifiers( $value, $input_id, $modifier, $entry );

            } else {

                $value = $this->convert_to_unit( $value, $calc_field_options['unit'] );

            }

	        // Wrap value in parentheses to handle double-negatives
	        $formula = str_replace( $full_match, '(' . $value . ')', $formula );

        }

	    preg_match_all( '/{(today|now)}/mi', $formula, $matches, PREG_SET_ORDER );
	    foreach( $matches as $match ) {

		    $full_match = $match[0];
		    $calc_field_options = $this->get_calc_field_options( $calc_field_id, $form );

		    $time = $full_match === '{today}' ? strtotime( 'today 00:00:00', current_time( 'timestamp' ) ) : current_time( 'timestamp' );
		    $value = $this->convert_to_unit( $time, $calc_field_options['unit'] );

		    // Wrap value in parentheses to handle double-negatives
		    $formula = str_replace( $full_match, '(' . $value . ')', $formula );

	    }

	    // @todo Process these merge tags *before* regular Date merge tags; better alignment with how these merge tags are processed in JS.
        preg_match_all( '/{(weekdays|weekendDays):\(?([0-9.]+)\)?,\(?([0-9.]+)\)?}/', $formula, $custom_matches, PREG_SET_ORDER );

        foreach( $custom_matches as $match ) {

            list( $full_match, $custom_type, $start_date, $end_date ) = $match;

            $value = 0;

            if( $start_date > 0 && $end_date > 0 ) {

	            // Nested Date fields are already converted. We need to unconvert them.
            	$start_date *= $calc_field_options['unit']['unit'];
            	$end_date   *= $calc_field_options['unit']['unit'];

                switch( $custom_type ) {
                    case 'weekdays':
                        $value = $this->calc_week_days( $start_date, $end_date );
                        break;
                    case 'weekendDays':
                        $value = $this->calc_weekend_days( $start_date, $end_date );
                        break;
                    default:
                        break;
                }

            }

            // Wrap value in parentheses to handle double-negatives
            $formula = str_replace( $full_match, '(' . $value . ')', $formula );

        }

        return $formula;
    }


	/**
     * Check calculations after submission for possible deviation between value calculated on frontend with JavaScript
     * and the value calculated on submission with PHP.
     *
     * If there is an issue, an e-mail will be dispatched to the site admin and a note will be added to the entry
     * to aid debugging.
     *
	 * @param $entry
	 * @param $form
	 */
    public function check_calculations( $entry, $form ) {

	    foreach ( $form['fields'] as &$field ) {
		    if ( ! $this->is_applicable_field( $field ) ) {
			    continue;
		    }

		    /**
		     * It's important to pull straight from POST since create_lead re-formats the numbers in ways we don't
		     * want this moment.
		     */
		    switch ( $field->type ) {
			    case 'product':
				    $input_name = 'input_' . $field->id . '_2';
				    $input = $field->id . '.2';
				    break;

			    default:
				    $input_name = 'input_' . $field->id;
				    $input = $field->id;
				    break;
		    }

		    $raw_submitted_value = rgpost( $input_name );
		    $raw_entry_value = $entry[$input];

		    if ( $raw_submitted_value === null || $raw_submitted_value === '' ) {
			    continue;
		    }

		    $submitted_value = self::prep_number_for_calcs( $raw_submitted_value, $field );
		    $entry_value = self::prep_number_for_calcs( $raw_entry_value, $field );

		    $max_delta = apply_filters_deprecated( 'gpdtc_validation_max_delta', array( 0.009, $entry_value, $field->calculationFormula, $field, $form, $entry ), '1.0-beta-4.0', 'gpdtc_warning_max_delta' );
		    $max_delta = apply_filters( 'gpdtc_warning_max_delta', $max_delta, $entry_value, $field->calculationFormula, $field, $form, $entry );

		    $delta = round( abs( floatval( $submitted_value ) - floatval( $entry_value ) ), 9 );

		    if ( $delta > $max_delta ) {
			    $this->_send_warning_email( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta );
			    $this->_add_warning_note( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta );
		    }
	    }

    }

	/**
     * Clean number to run calculations on it.
     *
	 * @param $value
	 * @param $field GF_Field
	 */
    public static function prep_number_for_calcs($value, $field) {
	    $number_format = rgar( $field, 'numberFormat', 'decimal_dot' );

	    return GFCommon::round_number(
            GFCommon::clean_number( $value, $number_format ),
            $field->calculationRounding
        );
    }

	/**
	 * Generate text for warning message about calculations that exceed the max allowable delta between frontend
	 * and backend calculations.
	 *
	 * @param $field GF_Field
	 * @param $form GF_Form
	 * @param $entry GF_Entry
	 * @param $submitted_value number
	 * @param $entry_value number
	 * @param $delta float
	 * @param $max_delta float
	 * @param $is_email boolean
	 */
	protected function _get_warning_message( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta, $is_email ) {

		$message = array();

		$message[] = __( 'GP Date Time Calculator detected that the following entry may have a mismatching calculation result.',
			'gp-date-time-calculator' );
		$message[] = __( 'The calculation result that the user saw while filling out the form may not be the value that was calculated after submission.',
			'gp-date-time-calculator' );
		$message[] = __( 'Please contact support@gravitywiz.com if this issue persists.', 'gp-date-time-calculator' );
		$message[] = '--------------------------------------';

		if ( $is_email ) {
			$message[] = sprintf( __( 'Form: %s (ID: %d)', 'gp-date-time-calculator' ), $form['title'], $form['id'] );
			$message[] = sprintf( __( 'Entry ID: %d', 'gp-date-time-calculator' ), $entry['id'] );
		}

		$message[] = sprintf( __( 'Field: %s (ID: %d)', 'gp-date-time-calculator' ), GFCommon::get_label( $field ),
			$field['id'] );
		$message[] = sprintf( __( 'Field Formula: %s', 'gp-date-time-calculator' ), $field['calculationFormula'] );
		$message[] = sprintf( __( 'Submitted Value: %s', 'gp-date-time-calculator' ), $submitted_value );
		$message[] = sprintf( __( 'Value calculated after submission: %s', 'gp-date-time-calculator' ), $entry_value );
		$message[] = sprintf( __( 'Difference between submitted value and value after submission: %s  (Max allowable difference before warning: %s)',
			'gp-date-time-calculator' ), $delta, $max_delta );

		return implode( $is_email ? "\n\n" : "\n", $message );

	}

	protected function _send_warning_email( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta ) {

	    $send_delta_email = apply_filters_deprecated( 'gpdtc_email_admin_on_rejected_calc', array( true, $field, $form, $entry ), '1.0-beta-4.0', 'gpdtc_send_delta_warning_admin_email' );

		if ( ! apply_filters( 'gpdtc_send_delta_warning_admin_email', $send_delta_email, $field, $form, $entry ) ) {
			return;
		}

		$message = $this->_get_warning_message( $field, $form, $entry, $submitted_value, $entry_value, $delta,
			$max_delta, true );

		$to = apply_filters_deprecated( 'gpdtc_rejected_calc_email_to', array( get_option( 'admin_email' ), $field, $form, $entry ), '1.0-beta-4.0', 'gpdtc_delta_warning_email_recipient' );
		$to = apply_filters( 'gpdtc_delta_warning_email_recipient', $to, $field, $form, $entry );

		wp_mail( $to, 'GP Date Time Calculator: Possible Calculation Issue', $message );

	}

	protected function _add_warning_note( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta ) {

		if ( ! apply_filters( 'gpdtc_add_delta_warning_entry_note', true, $field, $form, $entry ) ) {
			return;
		}

		$note = $this->_get_warning_message( $field, $form, $entry, $submitted_value, $entry_value, $delta, $max_delta,
			false );

		GFFormsModel::add_note( $entry['id'], 0, 'GP Date Time Calculator', $note, 'warning' );

	}

    public function get_date_field_value( $date_field_options, $form, $entry ) {

        $date_field = GFFormsModel::get_field( $form, $date_field_options['id'] );
        $is_visible = ! GFFormsModel::is_field_hidden( $form, $date_field, array(), $entry );
        $value      = 0;

        if( ! $is_visible ) {
            return $value;
        }

	    $date = rgar( $entry, $date_field_options['id'] );

	    /**
	     * PHP doesn't always return the right date if only time is provided so we'll coax it into using the correct
	     * one.
	     */
	    $current_date_prefix = '';

	    if ( $date_field->type === 'time' ) {
		    $current_date_prefix = current_time( 'Y-m-d' ) . ' ';
	    }

	    $value = strtotime( $current_date_prefix . $date );

	    return $value;
    }

    public function convert_to_unit( $value, $unit ) {
	    if ( ! $value ) {
		    return 0;
	    }

	    $a = new DateTimeImmutable( '@0' );
	    $b = new DateTimeImmutable( '@' . $value );

        switch( $unit['slug'] ) {
            case 'seconds': return $b->getTimestamp() - $a->getTimestamp();
            case 'minutes': return ($b->getTimestamp() - $a->getTimestamp()) / 60;
            case 'hours': return ($b->getTimestamp() - $a->getTimestamp()) / 60 / 60;
            case 'days': return ($b->getTimestamp() - $a->getTimestamp()) / 60 / 60 / 24;
            case 'weeks': return ($b->getTimestamp() - $a->getTimestamp()) / 60 / 60 /24 / 7;
	        case 'months': return gpdtc_month_diff($a, $b);
            case 'years': return gpdtc_month_diff($a, $b) / 12;
        }

	    return new WP_Error( 'invalid_unit', __( 'Invalid unit provided in conversion.' ) );

    }

    public function calc_week_days( $start_date, $end_date ) {
        return $this->calc_days( $start_date, $end_date, 'weekdays' );
    }

    public function calc_weekend_days( $start_date, $end_date ) {
        return $this->calc_days( $start_date, $end_date, 'weekendDays' );
    }

    public function calc_days( $start_date, $end_date, $mode = 'weekdays' ) {

        $start_date = strtotime( '0:00', $start_date );
        $end_date   = strtotime( '23:59', $end_date );

        $weekdays = $weekend_days = 0;

        while( $start_date < $end_date ) {
            if( date( 'D', $start_date ) == 'Sun' || date( 'D', $start_date ) == 'Sat' ) {
                $weekend_days++;
            } else {
                $weekdays++;
            }
            $start_date = strtotime( '+1 day', $start_date );
        }

        $days = $mode == 'weekdays' ? $weekdays : $weekend_days;

        return $days;
    }

    public function handle_modifiers( $value, $input_id, $modifier, $entry ) {

        switch( $modifier ) {
            case 'age':

                // @props: https://gist.github.com/richardW8k/ac8b0f24c70114e82fb3
                $dob   = rgar( $entry, $input_id );
                $today = new DateTime();
                $diff  = $today->diff( new DateTime( $dob ) );
                $value = $diff->y;

                break;
            default:
                break;
        }

        return $value;
    }



    // # Documentation

    public function documentation() {
        return array(
            'type'  => 'url',
            'value' => '#'
        );
    }

}

function gp_date_time_calculator() {
	return GP_Date_Time_Calculator::get_instance();
}

GFAddOn::register( 'GP_Date_Time_Calculator' );
