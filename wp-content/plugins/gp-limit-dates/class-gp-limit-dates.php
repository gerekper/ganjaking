<?php

class GP_Limit_Dates extends GWPerk {

	public $version                   = GP_LIMIT_DATES_VERSION;
	public $min_gravity_perks_version = '2.2.3';
	public $min_gravity_forms_version = '1.9.11.10';
	public $prefix                    = 'gpLimitDates';

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if ( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-limit-dates', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->register_scripts();
		$this->register_tooltips();
		$this->enqueue_field_settings();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11 );
		add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
		add_action( 'gform_field_validation', array( $this, 'validate' ), 10, 4 );
		add_action( 'gform_field_content', array( $this, 'handle_inline_datepicker' ), 10, 2 );

	}

	public function register_scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		wp_register_script( 'gp-limit-dates', $this->get_base_url() . "/js/gp-limit-dates{$min}.js", array( 'gform_datepicker_init' ), $this->version, true );
		wp_localize_script(
			'gp-limit-dates',
			'GPLimitDatesData',
			array(
				'serverTimezoneOffset' => (int) get_option( 'gmt_offset' ) * 60,
				'strings'              => array(
					'invalidDate' => wp_strip_all_tags( __( 'Invalid Date', 'gp-limit-dates' ) ),
				),
			)
		);

		$this->register_noconflict_script( 'gp-limit-dates' );

		wp_register_style( 'gp-limit-dates', $this->get_base_url() . "/css/gp-limit-dates{$min}.css", array(), $this->version );
		$this->register_noconflict_styles( 'gp-limit-dates' );

		wp_register_script( 'gp-limit-dates-admin', $this->get_base_url() . "/js/gp-limit-dates-admin{$min}.js", array( 'gaddon_repeater', 'jquery-ui-datepicker' ), $this->version, true );
		wp_localize_script(
			'gp-limit-dates-admin',
			'GPLD_ADMIN',
			array(
				'key'               => $this->key( '' ),
				'dateSelectOptions' => $this->get_date_select_options(),
			)
		);

		$this->register_noconflict_script( 'gp-limit-dates-admin' );
		$this->register_noconflict_script( 'gaddon_repeater' );
		$this->register_noconflict_script( 'jquery-ui-datepicker' );

		wp_register_style( 'gp-limit-dates-admin', $this->get_base_url() . "/css/gp-limit-dates-admin{$min}.css", array(), $this->version );
		$this->register_noconflict_styles( 'gp-limit-dates-admin' );

	}

	public function register_tooltips() {
		$this->add_tooltip( $this->key( 'min_date' ), sprintf( GravityPerks::$tooltip_template, __( 'Minimum Date', 'gp-limit-dates' ), __( 'Specify the minimum date that can be selected in this field.', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'max_date' ), sprintf( GravityPerks::$tooltip_template, __( 'Maximum Date', 'gp-limit-dates' ), __( 'Specify the maximum date that can be selected in this field.', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'date_modifier' ), sprintf( GravityPerks::$tooltip_template, __( 'Date Modifier', 'gp-limit-dates' ), __( 'Modify the selected date by adding or subracting days, weeks, months or years.<br><strong>Examples:</strong><code>+1 day</code><br><code>-2 weeks</code><br><code>+3 years</code>', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'days_week' ), sprintf( GravityPerks::$tooltip_template, __( 'Days of the Week', 'gp-limit-dates' ), __( 'Specify the days of the week for which dates should be selectable.', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'exceptions' ), sprintf( GravityPerks::$tooltip_template, __( 'Exceptions', 'gp-limit-dates' ), __( 'Specify any dates that should be excepted from the above rules.', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'exclude_before_today' ), sprintf( GravityPerks::$tooltip_template, __( 'Exclude Dates Before Current Date', 'gp-limit-dates' ), __( 'Prevent days that occur before the current date from being selected. Use this option to set a minimum date in the future and have the minimum date automatically adjust as time progresses.', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'exclude_before_today_modifier' ), sprintf( GravityPerks::$tooltip_template, __( 'Exclude Dates Before Current Date Modifier', 'gp-limit-dates' ), __( 'Modify the current date by adding or subracting days, weeks, months or years.<br><strong>Examples:</strong><code>+1 day</code><br><code>-2 weeks</code><br><code>+3 years</code>', 'gp-limit-dates' ) ) );
		$this->add_tooltip( $this->key( 'inline_datepicker' ), sprintf( GravityPerks::$tooltip_template, __( 'Inline Date Picker', 'gp-limit-dates' ), __( 'Display a full calendar in place of an input.', 'gp-limit-dates' ) ) );
	}

	public function enqueue_admin_scripts() {

		if ( GFForms::get_page() === 'form_editor' ) {
			wp_enqueue_script( 'gp-limit-dates-admin' );
			wp_enqueue_style( 'gp-limit-dates-admin' );
		}

	}

	public function field_settings_ui() {
		?>

		<li class="gp-limit-dates-field-settings field_setting gp-field-setting" style="display:none;">

			<div class="gp-row">

				<label for="gpld-minimum-date" class="section_label">
					<?php _e( 'Minimum Date', 'gp-limit-dates' ); ?>
					<?php gform_tooltip( $this->key( 'min_date' ) ); ?>
				</label>

				<div class="gp-group">

					<div id="gpld-minimum-date-container" class="gpld-date-select-container">

						<select id="gpld-minimum-date" class="gpld-date-select" onchange="SetFieldProperty( '<?php echo $this->key( 'minDate' ); ?>', this.value );">
							<!-- options added dynamically -->
						</select>

					</div>

					<div id="gpld-min-date-input-container" class="gpld-date-input-container">
						<input id="gpld-min-date-input" class="gpld-date-input" type="text" value="" onchange="SetFieldProperty( '<?php echo $this->key( 'minDateValue' ); ?>', this.value );"/>
						<span class="gpld-date-input-reset"><i class="fa fa-undo"></i></span>
					</div>

					<div id="gpld-min-date-modifier-container" class="gpld-date-modifier-container">
						<input id="gpld-min-date-modifier" type="text" value="" placeholder="i.e. +2 days (optional)" onkeyup="SetFieldProperty( '<?php echo $this->key( 'minDateMod' ); ?>', this.value );"/>
						<?php gform_tooltip( $this->key( 'date_modifier' ), 'gp-tooltip gp-tooltip-right tooltip' ); ?>
					</div>

				</div>

			</div>

			<div class="gp-row">

				<div id="gpld-min-date-exclude-before-today-container" class="gpld-date-modifier-today-container">
					<input type="checkbox" id="gpld-min-date-exclude-before-today" onclick="SetFieldProperty( '<?php echo $this->key( 'minDateExcludeBeforeToday' ); ?>', this.checked );">
					<label for="gpld-min-date-exclude-before-today" class="inline">
						<?php _e( 'Exclude Dates Before Current Date', 'gp-limit-dates' ); ?>
						<?php gform_tooltip( $this->key( 'exclude_before_today' ) ); ?>
					</label>
				</div>

				<div id="gpld-min-date-exclude-before-today-modifier-container" class="gpld-date-modifier-today-modifier-container gp-child-settings">
					<label for="gpld-min-date-exclude-before-today-modifier" class="section_label">
						<?php _e( 'Current Date Modifier', 'gp-limit-dates' ); ?>
						<?php gform_tooltip( $this->key( 'exclude_before_today_modifier' ) ); ?>
					</label>
					<input id="gpld-min-date-exclude-before-today-modifier" type="text" value="" placeholder="i.e. +2 days" onkeyup="SetFieldProperty( '<?php echo $this->key( 'minDateExcludeBeforeTodayMod' ); ?>', this.value );"/>
				</div>

			</div>

			<div class="gp-row">

				<label for="gpld-maximum-date" class="section_label">
					<?php _e( 'Maximum Date', 'gp-limit-dates' ); ?>
					<?php gform_tooltip( $this->key( 'max_date' ) ); ?>
				</label>

				<div class="gp-group">

					<div id="gpld-maximum-date-container" class="gpld-date-select-container">

						<select id="gpld-maximum-date" class="gpld-date-select" onchange="SetFieldProperty( '<?php echo $this->key( 'maxDate' ); ?>', this.value );">
							<!-- options added dynamically -->
						</select>

					</div>

					<div id="gpld-max-date-input-container" class="gpld-date-input-container">
						<input id="gpld-max-date-input" class="gpld-date-input" type="text" value="" onchange="SetFieldProperty( '<?php echo $this->key( 'maxDateValue' ); ?>', this.value );" />
						<span class="gpld-date-input-reset"><i class="fa fa-undo"></i></span>
					</div>

					<div id="gpld-max-date-modifier-container" class="gpld-date-modifier-container">
						<input id="gpld-max-date-modifier" type="text" value="" placeholder="i.e. +2 days" onkeyup="SetFieldProperty( '<?php echo $this->key( 'maxDateMod' ); ?>', this.value );"/>
						<span><?php gform_tooltip( $this->key( 'date_modifier' ), 'gp-tooltip gp-tooltip-right tooltip' ); ?></span>
					</div>

				</div>

			</div>

			<div class="gp-row">

				<label for="gpld-days-of-week" class="section_label">
					<?php _e( 'Days of the Week', 'gp-limit-dates' ); ?>
					<?php gform_tooltip( $this->key( 'days_week' ) ); ?>
				</label>

				<div class="gpld-all-days-container">

					<span id="gpld-all-days"><?php _e( 'All Days', 'gp-limit-dates' ); ?></span>

				</div>

				<div class="gpld-days-of-week-container">

					<?php
					foreach ( $this->get_days_of_the_week() as $day ) {
						printf(
							'<div class="gpld-day-container">
								<input type="checkbox" value="%1$d" name="gpld-days" id="gpld-day-%1$d" />
								<label for="gpld-day-%1$d" class="inline">%2$s</label>
							</div>',
							$day['value'],
							$day['label_abbr']
						);
					}
					?>

				</div>

			</div>

			<div class="gp-row">

				<label for="gpld-exceptions" class="section_label">
					<?php _e( 'Exceptions', 'gp-limit-dates' ); ?>
					<?php gform_tooltip( $this->key( 'exceptions' ) ); ?>
				</label>

				<div class="gpld-exceptions-repeater">
					<!-- Template Start -->
					<div class="row" data-gpld-date-value="{date}">
						<input class="date date_{i}" value="{date}" readonly="readonly" />
						{buttons}
					</div>
					<!-- / Template Ends -->
				</div>

				<div id="gpld-add-exception-container">
					<span id="gpld-add-exception" class="button secondary"><?php _e( 'Add Exception', 'gp-limit-dates' ); ?></span>
				</div>

				<input type="text" id="gpld-add-exception-input" />

			</div>

			<div class="gp-row">

				<label for="gpld-inline-datepicker" class="section_label">
					<?php _e( 'Inline Date Picker', 'gp-limit-dates' ); ?>
					<?php gform_tooltip( $this->key( 'inline_datepicker' ) ); ?>
				</label>

				<input type="checkbox" id="gpld-inline-datepicker" value="1" onclick="SetFieldProperty( '<?php echo $this->key( 'inlineDatepicker' ); ?>', this.checked );" />
				<label for="gpld-inline-datepicker" class="inline"><?php _e( 'Enable Inline Date Picker' ); ?></label>

			</div>

		</li>

		<?php
	}

	public function get_days_of_the_week() {
		return array(
			array(
				'label'      => __( 'Monday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Mon', 'gp-limit-dates' ),
				'value'      => 1,
			),
			array(
				'label'      => __( 'Tuesday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Tues', 'gp-limit-dates' ),
				'value'      => 2,
			),
			array(
				'label'      => __( 'Wednesday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Wed', 'gp-limit-dates' ),
				'value'      => 3,
			),
			array(
				'label'      => __( 'Thursday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Thur', 'gp-limit-dates' ),
				'value'      => 4,
			),
			array(
				'label'      => __( 'Friday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Fri', 'gp-limit-dates' ),
				'value'      => 5,
			),
			array(
				'label'      => __( 'Saturday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Sat', 'gp-limit-dates' ),
				'value'      => 6,
			),
			array(
				'label'      => __( 'Sunday', 'gp-limit-dates' ),
				'label_abbr' => __( 'Sun', 'gp-limit-dates' ),
				'value'      => 0,
			),
		);
	}

	public function enqueue_form_scripts( $form ) {

		if ( $this->has_limit_dates_enabled( $form ) ) {
			wp_enqueue_script( 'gp-limit-dates' );
			wp_localize_script( 'gp-limit-dates', 'GPLimitDatesData' . $form['id'], $this->get_limit_dates_options( $form ) );
			// Stylesheet is only currently needed for overriding GF 2.5 styles for inline datepicker.
			if ( GravityPerks::is_gf_version_gte( '2.5-beta-1' ) ) {
				wp_enqueue_style( 'gp-limit-dates' );
			}
		}

	}

	public function get_limit_dates_options( $form_or_field, &$options = array() ) {

		if ( isset( $form_or_field['fields'] ) && is_array( $form_or_field['fields'] ) ) {

			$form = $form_or_field;

			foreach ( $form['fields'] as $field ) {
				$field_options = $this->get_limit_dates_options( $field, $options );
				if ( $field_options ) {
					$options[ $field->id ] = ! isset( $options[ $field->id ] ) ? $field_options : array_merge( $options[ $field->id ], $field_options );
				}
			}

			/**
			 * Filter the date limiting options for each field.
			 *
			 * @since 1.0
			 *
			 * @param array $options An array of arrays containing the date limit options for each applicable field on the current form. See the 'gpld_limit_dates_options_%s_%s' filter below for more details.
			 * @param array $form The current Form object.
			 * @param array $field The current Field object.
			 */
			return gf_apply_filters( array( 'gpld_limit_dates_options', $form['id'] ), $options, $form, null );

		} else {

			$field = $form_or_field;

			if ( $field->get_input_type() != 'date' || $field->dateType != 'datepicker' ) {
				return false;
			}

			$min_date                 = $field->{$this->key( 'minDate' )};
			$min_date_mod             = $field->{$this->key( 'minDateMod' )};
			$exclude_before_today     = $field->{$this->key( 'minDateExcludeBeforeToday' )};
			$exclude_before_today_mod = $field->{$this->key( 'minDateExcludeBeforeTodayMod' )};

			if ( intval( $min_date ) > 0 ) {
				if ( ! isset( $options[ $min_date ] ) ) {
					$options[ $min_date ] = $this->get_default_limit_dates_options();
				}
				$options[ $min_date ]['setsMinDateFor'][] = $field->id;
			} elseif ( $min_date === '_custom_' ) {

				$min_date = $field->{$this->key( 'minDateValue' )};

				if ( $exclude_before_today ) {

					$today = strtotime( 'midnight', current_time( 'timestamp' ) );
					if ( $exclude_before_today_mod ) {
						$today = strtotime( $exclude_before_today_mod, $today );
					}

					$min_date_compare = strtotime( $min_date . ' 00:00:00' );
					if ( $min_date_compare < $today ) {
						$min_date = date( 'm/d/Y', $today );
					}
				}
			}

			$max_date     = $field->{$this->key( 'maxDate' )};
			$max_date_mod = $field->{$this->key( 'maxDateMod' )};
			if ( intval( $max_date ) > 0 ) {
				if ( ! isset( $options[ $max_date ] ) ) {
					$options[ $max_date ] = $this->get_default_limit_dates_options();
				}
				$options[ $max_date ]['setsMaxDateFor'][] = $field->id;
			} elseif ( $max_date == '_custom_' ) {
				$max_date = $field->{$this->key( 'maxDateValue' )};
			}

			$days_of_week = $field->{$this->key( 'daysOfWeek' )} ? $field->{$this->key( 'daysOfWeek' )} : false;

			$field_options = array(
				'minDate'                      => $min_date,
				'minDateMod'                   => $min_date_mod,
				'minDateExcludeBeforeToday'    => $exclude_before_today,
				'minDateExcludeBeforeTodayMod' => $exclude_before_today_mod,
				'maxDate'                      => $max_date,
				'maxDateMod'                   => $max_date_mod,
				'daysOfWeek'                   => $days_of_week,
				'exceptions'                   => $field->{$this->key( 'exceptions' )},
				'dateFormat'                   => $field->dateFormat ? $field->dateFormat : 'mdy',
				'disableAll'                   => false,
				'inlineDatepicker'             => $field->{$this->key( 'inlineDatepicker' )} == true,
			);

			/**
			 * Filter the date limiting options for a specific field.
			 *
			 * @since 1.0.0
			 *
			 * @param array $field_options {
			 *     An array of date limitation options.
			 *
			 *     @type string $minDate       The minimum date that can be selected. Can be either a field ID, a 'mm/dd/yyyy' string, or '{today}'.
			 *     @type string $minDateMod    A string representing the addition or subtraction of time to be applied to the $minDate (i.e. '+2 days').
			 *     @type bool   $minDateExcludeBeforeToday    If true, $minDate will be adjusted to current date if it is before the current date. Only available when "Set Specific Date" is selected for $minDate.
			 *     @type string $minDateExcludeBeforeTodayMod A string representing the addition or subtraction of time to be applied to the current date when $minDateExcludeBeforeToday is enabled (i.e. '+2 days').
			 *     @type string $maxDate       The maximum date that can be selected. Can be either a field ID, a 'mm/dd/yyyy' string, or '{today}'.
			 *     @type string $maxDateMod    A string representing the addition or subtraction of time to be applied to the $maxDate (i.e. '+2 days').
			 *     @type array  $daysOfWeek    An array of integers representing the days of the week that should be available (e.g. 0 = Sunday, 1 = Monday, 6 = Saturday). Return a false value to make all days of the week available.
			 *     @type array  $exceptions    An array of 'mm/dd/yyyy' date strings that should be excepted from all other rules. If a date is unavailable, adding an exceoption will make it available and vice versa.
			 *     @type string $dateFormat    The GF date format for the current field. Defaults to 'mdy'.
			 *     @type string $exceptionMode Specify the mode in which exceptions should be handled; 'default' = Blocked dates are available, Available dates are blocked; 'disable' = Excepted dates are all blocked; 'enable' = Excepted dates are all enabled.
			 * }
			 * @param array $form The current Form object.
			 * @param array $field The current Field object.
			 */
			return apply_filters( sprintf( 'gpld_limit_dates_options_%s_%s', $field->formId, $field->id ), $field_options, GFAPI::get_form( $field->formId ), $field );
		}

	}

	public function get_limit_dates_field_options( $form, $field_id = false ) {

		if ( is_a( $form, 'GF_Field' ) ) {

			$field    = $form;
			$field_id = $field->id;
			$form     = GFAPI::get_form( $form->formId );

			for ( $i = 0; $i < count( $form['fields'] ); $i++ ) {
				if ( $form['fields'][ $i ]->id == $field->id ) {
					$form['fields'][ $i ] = $field;
					break;
				}
			}
		}

		$options = array();
		$options = $this->get_limit_dates_options( $form, $options );

		return rgar( $options, $field_id );
	}

	public function get_default_limit_dates_options() {
		return array(
			'minDate'                      => false,
			'minDateMod'                   => false,
			'minDateExcludeBeforeToday'    => false,
			'minDateExcludeBeforeTodayMod' => false,
			'maxDate'                      => false,
			'maxDateMod'                   => false,
			'daysOfWeek'                   => false,
			'exceptions'                   => array(),
			'exceptionMode'                => 'default', // default: bad dates are now good, good dates are now bad; disable: all exceptions are blocked; enable: all exceptions are available.
			'disableAll'                   => false, // disable all dates,
			'inlineDatepicker'             => false,
		);
	}

	public function has_limit_dates_enabled( $form_or_field ) {

		if ( is_a( $form_or_field, 'GF_Field' ) ) {

			$field = $form_or_field;

			if ( $field->get_input_type() != 'date' || $field->dateType != 'datepicker' ) {
				$result = false;
			} else {

				$limit_date_options = $this->get_limit_dates_field_options( $field );

				// filter out properties that don't actually indicate that limit dates is enabled
				$limit_date_options = array_diff_key( $limit_date_options, array_flip( array( 'dateFormat' ) ) );

				// filter out all empty properties so we know if any limit date options are configured
				$limit_date_options = is_array( $limit_date_options ) ? array_filter( $limit_date_options ) : array();

				$result = ! empty( $limit_date_options );

			}

			$result = apply_filters( 'gpld_has_limit_dates_enabled', $result, $field );
			$result = apply_filters( "gpld_has_limit_dates_enabled_{$field->formId}", $result, $field );
			$result = apply_filters( "gpld_has_limit_dates_enabled_{$field->formId}_{$field->id}", $result, $field );

			return $result;

		} else {

			$form = $form_or_field;
			if ( ! is_array( $form ) ) {
				return false;
			}

			foreach ( $form['fields'] as $field ) {
				if ( $this->has_limit_dates_enabled( $field ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function validate( $result, $value, $form, $field ) {

		if ( ! $this->has_limit_dates_enabled( $field ) ) {
			return $result;
		}

		if ( ! rgblank( $value ) && ! $this->is_valid_date( $value, $field ) ) {
			$result['is_valid'] = false;
			$result['message']  = __( 'Please enter a valid date.', 'gp-limit-dates' );
		}

		return $result;
	}

	public function is_valid_date( $date, $field ) {

		$date = $this->parse_timestamp( $date, $field );

		$is_date_in_range = $this->is_date_in_range( $date, $this->get_date_value( $field, 'minDate' ), $this->get_date_value( $field, 'maxDate' ) );
		$is_valid_day     = $this->is_valid_day_of_week( $date, $field );
		$disable_all      = rgar( $this->get_limit_dates_field_options( $field ), 'disableAll', false );
		$is_valid         = $is_date_in_range && $is_valid_day && ! $disable_all;

		if ( $this->is_excepted( $date, $field ) ) {
			switch ( $this->get_exception_mode( $field ) ) {
				case 'enable':
					$is_valid = true;
					break;
				case 'disable':
					$is_valid = false;
					break;
				default:
					$is_valid = ! $is_valid;
					break;
			}
		}

		return $is_valid;
	}

	public function is_date_in_range( $date, $min, $max ) {

		if ( $min && $max ) {
			return $date >= $min && $date <= $max;
		} elseif ( $min ) {
			return $date >= $min;
		} elseif ( $max ) {
			return $date <= $max;
		}

		return true;
	}

	public function is_valid_day_of_week( $date, $field ) {

		$days_of_week = rgar( $this->get_limit_dates_field_options( $field ), 'daysOfWeek' );
		if ( ! $days_of_week ) {
			return true;
		}

		$day = date( 'N', $date );
		if ( $day == 7 ) {
			// JS Sunday is 0, PHP is 7
			$day = 0;
		}

		if ( in_array( $day, $days_of_week ) ) {
			return true;
		}

		return false;
	}

	public function is_excepted( $date, $field ) {

		$exceptions = rgar( $this->get_limit_dates_field_options( $field ), 'exceptions' );
		if ( empty( $exceptions ) ) {
			return false;
		}

		$formatted_date = date( 'm/d/Y', $date );
		if ( ! in_array( $formatted_date, $exceptions ) ) {
			return false;
		}

		return true;
	}

	public function get_exception_mode( $field ) {
		return rgar( $this->get_limit_dates_field_options( $field ), 'exceptionMode', 'default' );
	}

	public function get_date_value( $field, $key ) {

		$options     = $this->get_limit_dates_field_options( $field );
		$value       = rgar( $options, $key );
		$is_field_id = is_numeric( $value ) && $value > 0;

		if ( $value == '{today}' ) {
			$date = strtotime( 'midnight', current_time( 'timestamp' ) );
		} elseif ( $is_field_id ) {

			$mod_field_id = $value;
			$form         = GFAPI::get_form( $field->formId );
			$mod_field    = GFFormsModel::get_field( $form, $mod_field_id );

			$date = $this->parse_timestamp( rgpost( sprintf( 'input_%s', $mod_field->id ) ), $mod_field );

		} else {
			$date = strtotime( $value );
		}

		$modifier = $key == 'minDate' ? $options['minDateMod'] : $options['maxDateMod'];
		if ( $modifier ) {
			$date = strtotime( $modifier, $date );
		}

		return apply_filters( 'gpld_date_value', $date, $field, $key, $options );
	}

	public function parse_timestamp( $date, $field ) {

		if ( $this->is_valid_timestamp( $date ) ) {
			return $date;
		}

		$parsed_date = GFCommon::parse_date( $date, $field->dateFormat ? $field->dateFormat : 'mdy' );
		if ( empty( $parsed_date ) ) {
			return false;
		}

		$clean_date = sprintf( '%s/%s/%s', $parsed_date['year'], $parsed_date['month'], $parsed_date['day'] );
		$timestamp  = strtotime( $clean_date );

		return $timestamp;
	}

	public function get_date_select_options() {
		return array(
			array(
				'label' => __( 'Select Date', 'gp-limit-dates' ),
				'value' => '',
			),
			array(
				'label'   => __( 'Date Fields', 'gp-limit-dates' ),
				'value'   => '_datefields_',
				'options' => array(),
			),
			array(
				'label'   => __( 'Other', 'gp-limit-dates' ),
				'value'   => '',
				'options' => array(
					array(
						'label' => __( 'Set Specific Date', 'gp-limit-dates' ),
						'value' => '_custom_',
					),
					array(
						'label' => __( 'Current Date', 'gp-limit-dates' ),
						'value' => '{today}',
					),
				),
			),
		);
	}

	public function is_valid_timestamp( $timestamp ) {
		return ( (string) (int) $timestamp === (string) $timestamp )
			   && ( $timestamp <= PHP_INT_MAX )
			   && ( $timestamp >= ~PHP_INT_MAX );
	}

	public function handle_inline_datepicker( $content, $field ) {

		if ( $field->is_form_editor() || $field->is_entry_detail() || $field->get_input_type() !== 'date' ) {
			return $content;
		}

		$options = $this->get_limit_dates_field_options( $field );
		if ( ! rgar( $options, 'inlineDatepicker' ) ) {
			return $content;
		}

		preg_match( "/<input name='input_{$field->id}' (?:.+)\\/>/", $content, $match );
		if ( empty( $match ) ) {
			return $content;
		}

		$input = $match[0];

		// hide the default input
		$replace = sprintf( '<div style="display:none;">%s</div>', $input );

		// remove 'datepicker' class
		$replace = str_replace( 'datepicker', 'has-inline-datepicker', $replace );

		// add <div> for inline datepicker
		$date_format = $field->dateFormat ? $field->dateFormat : 'mdy';
		$replace    .= sprintf( '<div id="datepicker_%d_%d" class="datepicker %s"></div>', $field->formId, $field->id, $date_format );

		// add style block to assiet with styling inline datepicker; when we add more styles for datepicker move this to external file
		$replace .= sprintf( '<style type="text/css">#datepicker_%d_%d .ui-datepicker-inline { margin: 0 0 20px; }</style>', $field->formId, $field->id );

		$content = str_replace( $input, $replace, $content );

		return $content;
	}

}

function gp_limit_dates() {
	return GP_Limit_Dates::get_instance( null );
}
