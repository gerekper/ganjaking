<?php
/**
 * Builder Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Date picker Element
 *
 * @package Extra Product Options/Classes/Builder
 * @version 6.0
 */
class THEMECOMPLETE_EPO_BUILDER_ELEMENT_DATE extends THEMECOMPLETE_EPO_BUILDER_ELEMENT {

	/**
	 * Class Constructor
	 *
	 * @param string $name The element name.
	 * @since 6.0
	 */
	public function __construct( $name = '' ) {
		$this->element_name     = $name;
		$this->is_addon         = false;
		$this->namespace        = $this->elements_namespace;
		$this->name             = esc_html__( 'Date', 'woocommerce-tm-extra-product-options' );
		$this->description      = '';
		$this->width            = 'w100';
		$this->width_display    = '100%';
		$this->icon             = 'tcfa-calendar-alt';
		$this->is_post          = 'post';
		$this->type             = 'single';
		$this->post_name_prefix = 'date';
		$this->fee_type         = 'single';
		$this->tags             = 'price content';
		$this->show_on_backend  = true;
	}

	/**
	 * Get weekdays
	 *
	 * @since 6.0
	 * @access public
	 */
	public function get_weekdays() {

		echo '<div class="tm-weekdays-picker-wrap">';
		// load wp translations.
		if ( function_exists( 'wp_load_translations_early' ) ) {
			wp_load_translations_early();
			global $wp_locale;
			for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
				echo '<span class="tm-weekdays-picker"><label><input class="tm-weekday-picker" type="checkbox" value="' . esc_attr( $day_index ) . '"><span>' . esc_html( $wp_locale->get_weekday( $day_index ) ) . '</span></label></span>';
			}
			// in case something goes wrong.
		} else {
			$weekday[0] = /* translators: weekday Sunday */
				esc_html__( 'Sunday', 'default' );
			$weekday[1] = /* translators: weekday Monday */
				esc_html__( 'Monday', 'default' );
			$weekday[2] = /* translators: weekday Tuesday */
				esc_html__( 'Tuesday', 'default' );
			$weekday[3] = /* translators: weekday Wednesday */
				esc_html__( 'Wednesday', 'default' );
			$weekday[4] = /* translators: weekday Thursday */
				esc_html__( 'Thursday', 'default' );
			$weekday[5] = /* translators: weekday Friday */
				esc_html__( 'Friday', 'default' );
			$weekday[6] = /* translators: weekday Saturday */
				esc_html__( 'Saturday', 'default' );
			for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
				echo '<span class="tm-weekdays-picker"><label><input class="tm-weekday-picker" type="checkbox" value="' . esc_attr( $day_index ) . '"><span>' . esc_html( $weekday[ $day_index ] ) . '</span></label></span>';
			}
		}
		echo '</div>';

	}

	/**
	 * Get weekdays
	 *
	 * @since 6.0
	 * @access public
	 */
	public function get_months() {

		echo '<div class="tm-months-picker-wrap">';
		// load wp translations.
		if ( function_exists( 'wp_load_translations_early' ) ) {
			wp_load_translations_early();
			global $wp_locale;
			for ( $month_index = 1; $month_index <= 12; $month_index ++ ) {
				echo '<span class="tm-months-picker"><label><input class="tm-month-picker" type="checkbox" value="' . esc_attr( $month_index ) . '"><span>' . esc_html( $wp_locale->get_month( $month_index ) ) . '</span></label></span>';
			}
			// in case something goes wrong.
		} else {
			$month[0]  = /* translators: month January */
				esc_html__( 'January ', 'default' );
			$month[1]  = /* translators: month February */
				esc_html__( 'February', 'default' );
			$month[2]  = /* translators: month March */
				esc_html__( 'March', 'default' );
			$month[3]  = /* translators: month April */
				esc_html__( 'April', 'default' );
			$month[4]  = /* translators: month May */
				esc_html__( 'May', 'default' );
			$month[5]  = /* translators: month June */
				esc_html__( 'June', 'default' );
			$month[6]  = /* translators: month July */
				esc_html__( 'July', 'default' );
			$month[7]  = /* translators: month August */
				esc_html__( 'August', 'default' );
			$month[8]  = /* translators: month September */
				esc_html__( 'September', 'default' );
			$month[9]  = /* translators: month October */
				esc_html__( 'October', 'default' );
			$month[10] = /* translators: month November */
				esc_html__( 'November', 'default' );
			$month[11] = /* translators: month December */
				esc_html__( 'December', 'default' );
			for ( $month_index = 1; $month_index <= 12; $month_index ++ ) {
				echo '<span class="tm-months-picker"><label><input class="tm-month-picker" type="checkbox" value="' . esc_attr( $month_index ) . '"><span>' . esc_html( $month[ $month_index ] ) . '</span></label></span>';
			}
		}
		echo '</div>';

	}

	/**
	 * Initialize element properties
	 *
	 * @since 6.0
	 */
	public function set_properties() {
		$this->properties = $this->add_element(
			$this->element_name,
			[
				'enabled',
				'required',
				'price_type6',
				'lookuptable',
				'price',
				'sale_price',
				'fee',
				'hide_amount',
				'text_before_price',
				'text_after_price',
				'quantity',
				'button_type2',
				'date_format',
				'start_year',
				'end_year',
				[
					'id'      => 'date_default_value',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't',
						'id'    => 'builder_date_default_value',
						'name'  => 'tm_meta[tmfbuilder][date_default_value][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Enter a value to be applied to the field automatically according to your selected date format. (Two digits for day, two digits for month and four digits for year).', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_min_date',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 't',
						'id'    => 'builder_date_min_date',
						'name'  => 'tm_meta[tmfbuilder][date_min_date][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Minimum selectable date', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'A number of days from today.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_max_date',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'text',
					'tags'        => [
						'class' => 't',
						'id'    => 'builder_date_max_date',
						'name'  => 'tm_meta[tmfbuilder][date_max_date][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Maximum selectable date', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'A number of days from today.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'      => 'date_disabled_dates',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't',
						'id'    => 'builder_date_disabled_dates',
						'name'  => 'tm_meta[tmfbuilder][date_disabled_dates][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Disabled dates', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Comma separated dates according to your selected date format. (Two digits for day, two digits for month and four digits for year)', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'      => 'date_enabled_only_dates',
					'default' => '',
					'type'    => 'text',
					'tags'    => [
						'class' => 't',
						'id'    => 'builder_date_enabled_only_dates',
						'name'  => 'tm_meta[tmfbuilder][date_enabled_only_dates][]',
						'value' => '',
					],
					'label'   => esc_html__( 'Enabled dates', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Comma separated dates according to your selected date format. (Two digits for day, two digits for month and four digits for year). Please note that this will override any other setting!', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_theme',
					'wpmldisable' => 1,
					'default'     => 'epo',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_date_theme',
						'name' => 'tm_meta[tmfbuilder][date_theme][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Epo White', 'woocommerce-tm-extra-product-options' ),
							'value' => 'epo',
						],
						[
							'text'  => esc_html__( 'Epo Black', 'woocommerce-tm-extra-product-options' ),
							'value' => 'epo-black',
						],
					],
					'label'       => esc_html__( 'Theme', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the theme for the datepicker.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_theme_size',
					'wpmldisable' => 1,
					'default'     => 'medium',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_date_theme_size',
						'name' => 'tm_meta[tmfbuilder][date_theme_size][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Small', 'woocommerce-tm-extra-product-options' ),
							'value' => 'small',
						],
						[
							'text'  => esc_html__( 'Medium', 'woocommerce-tm-extra-product-options' ),
							'value' => 'medium',
						],
						[
							'text'  => esc_html__( 'Large', 'woocommerce-tm-extra-product-options' ),
							'value' => 'large',
						],
					],
					'label'       => esc_html__( 'Size', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the size of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_theme_position',
					'wpmldisable' => 1,
					'default'     => 'normal',
					'type'        => 'select',
					'tags'        => [
						'id'   => 'builder_date_theme_position',
						'name' => 'tm_meta[tmfbuilder][date_theme_position][]',
					],
					'options'     => [
						[
							'text'  => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
							'value' => 'normal',
						],
						[
							'text'  => esc_html__( 'Top of screen', 'woocommerce-tm-extra-product-options' ),
							'value' => 'top',
						],
						[
							'text'  => esc_html__( 'Bottom of screen', 'woocommerce-tm-extra-product-options' ),
							'value' => 'bottom',
						],
					],
					'label'       => esc_html__( 'Position', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Select the position of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_exlude_disabled',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'checkbox',
					'tags'        => [
						'value' => '1',
						'id'    => 'builder_date_exlude_disabled',
						'name'  => 'tm_meta[tmfbuilder][date_exlude_disabled][]',
					],
					'label'       => esc_html__( 'Exclude disabled', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'Enabling this will make it so that the Minimum and Maximum selectable date will not count the selected disabled weekdays.', 'woocommerce-tm-extra-product-options' ),
				],
				[
					'id'          => 'date_disabled_weekdays',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'hidden',
					'tags'        => [
						'class' => 'tm-weekdays',
						'id'    => 'builder_date_disabled_weekdays',
						'name'  => 'tm_meta[tmfbuilder][date_disabled_weekdays][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Disable weekdays', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'This allows you to disable all selected weekdays.', 'woocommerce-tm-extra-product-options' ),
					'extra'       => [ [ $this, 'get_weekdays' ], [] ],
				],
				[
					'id'          => 'date_disabled_months',
					'wpmldisable' => 1,
					'default'     => '',
					'type'        => 'hidden',
					'tags'        => [
						'class' => 'tm-months',
						'id'    => 'builder_date_disabled_months',
						'name'  => 'tm_meta[tmfbuilder][date_disabled_months][]',
						'value' => '',
					],
					'label'       => esc_html__( 'Disable months', 'woocommerce-tm-extra-product-options' ),
					'desc'        => esc_html__( 'This allows you to disable all selected months.', 'woocommerce-tm-extra-product-options' ),
					'extra'       => [ [ $this, 'get_months' ], [] ],
				],
				[
					'id'               => 'date_tranlation_custom',
					'type'             => 'custom',
					'label'            => esc_html__( 'Translations', 'woocommerce-tm-extra-product-options' ),
					'desc'             => '',
					'nowrap_end'       => 1,
					'noclear'          => 1,
					'message0x0_class' => 'justify-content-flex-end',
				],
				[
					'id'                   => 'date_tranlation_day',
					'default'              => '',
					'type'                 => 'text',
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_date_tranlation_day',
						'name'  => 'tm_meta[tmfbuilder][date_tranlation_day][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Day', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					'nowrap_start'         => 1,
					'nowrap_end'           => 1,
				],
				[
					'id'                   => 'date_tranlation_month',
					'default'              => '',
					'type'                 => 'text',
					'nowrap_start'         => 1,
					'nowrap_end'           => 1,
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_date_tranlation_month',
						'name'  => 'tm_meta[tmfbuilder][date_tranlation_month][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Month', 'woocommerce-tm-extra-product-options' ) . '</span> ',
				],
				[
					'id'                   => 'date_tranlation_year',
					'default'              => '',
					'type'                 => 'text',
					'tags'                 => [
						'class' => 't',
						'id'    => 'builder_date_tranlation_year',
						'name'  => 'tm_meta[tmfbuilder][date_tranlation_year][]',
						'value' => '',
					],
					'label'                => '',
					'desc'                 => '',
					'prepend_element_html' => '<span class="prepend_span">' . esc_html__( 'Year', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					'nowrap_start'         => 1,
				],
			]
		);
	}
}
