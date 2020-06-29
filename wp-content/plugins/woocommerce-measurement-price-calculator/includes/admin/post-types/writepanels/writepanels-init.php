<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Measurement Price Calculator Write Panels
 *
 * Sets up the write panels used by the measurement price calculator plugin
 */

include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data.php' );
include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data-calculator.php' );
include_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product-type-variable.php' );

/**
 * Gets the WooCommerce product settings, containing measurement units.
 *
 * @since 3.3
 *
 * @return array
 */
function wc_measurement_price_calculator_get_wc_settings() {

	$plugin_path = wc()->plugin_path();

	if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.3' ) ) {

		if (    ! class_exists( 'WC_Settings_Page', false )
		     || ! class_exists( 'WC_Settings_Products', false ) ) {

			if ( ! class_exists( 'WC_Admin_Settings', false ) ) {
				include_once( $plugin_path . '/includes/admin/class-wc-admin-settings.php' );
			}

			\WC_Admin_Settings::get_settings_pages();
		}

		$settings_products = new \WC_Settings_Products();

	} else {

		include_once( $plugin_path . '/includes/admin/settings/class-wc-settings-page.php' );

		$settings_products = include( $plugin_path . '/includes/admin/settings/class-wc-settings-products.php' );
	}

	return $settings_products->get_settings();
}


/**
 * Returns all available weight units
 *
 * @since 3.0
 * @return array of weight units
 */
function wc_measurement_price_calculator_get_weight_units() {

	$settings = wc_measurement_price_calculator_get_wc_settings();

	foreach ( $settings as $setting ) {
		if ( 'woocommerce_weight_unit' === $setting['id'] ) {
			return $setting['options'];
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'g', 'woocommerce-measurement-price-calculator' )   => __( 'g', 'woocommerce-measurement-price-calculator' ),
		__( 'kg', 'woocommerce-measurement-price-calculator' )  => __( 'kg', 'woocommerce-measurement-price-calculator' ),
		__( 't', 'woocommerce-measurement-price-calculator' )   => __( 't', 'woocommerce-measurement-price-calculator' ),
		__( 'oz', 'woocommerce-measurement-price-calculator' )  => __( 'oz', 'woocommerce-measurement-price-calculator' ),
		__( 'lbs', 'woocommerce-measurement-price-calculator' ) => __( 'lbs', 'woocommerce-measurement-price-calculator' ),
		__( 'tn', 'woocommerce-measurement-price-calculator' )  => __( 'tn', 'woocommerce-measurement-price-calculator' ),
	);
}


/**
 * Returns all available dimension units
 *
 * @since 3.0
 * @return array of dimension units
 */
function wc_measurement_price_calculator_get_dimension_units() {

	$settings = wc_measurement_price_calculator_get_wc_settings();

	if ( $settings ) {
		foreach ( $settings as $setting ) {
			if ( 'woocommerce_dimension_unit' === $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'mm', 'woocommerce-measurement-price-calculator' ) => __( 'mm', 'woocommerce-measurement-price-calculator' ),
		__( 'cm', 'woocommerce-measurement-price-calculator' ) => __( 'cm', 'woocommerce-measurement-price-calculator' ),
		__( 'm',  'woocommerce-measurement-price-calculator' ) => __( 'm',  'woocommerce-measurement-price-calculator' ),
		__( 'km', 'woocommerce-measurement-price-calculator' ) => __( 'km', 'woocommerce-measurement-price-calculator' ),
		__( 'in', 'woocommerce-measurement-price-calculator' ) => __( 'in', 'woocommerce-measurement-price-calculator' ),
		__( 'ft', 'woocommerce-measurement-price-calculator' ) => __( 'ft', 'woocommerce-measurement-price-calculator' ),
		__( 'yd', 'woocommerce-measurement-price-calculator' ) => __( 'yd', 'woocommerce-measurement-price-calculator' ),
		__( 'mi', 'woocommerce-measurement-price-calculator' ) => __( 'mi', 'woocommerce-measurement-price-calculator' ),
	);
}


/**
 * Returns all available area units
 *
 * @since 3.0
 * @return array of area units
 */
function wc_measurement_price_calculator_get_area_units() {

	$settings = wc_measurement_price_calculator_get_wc_settings();

	if ( $settings ) {
		foreach ( $settings as $setting ) {
			if ( 'woocommerce_area_unit' === $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'sq mm',   'woocommerce-measurement-price-calculator' ) => __( 'sq mm',   'woocommerce-measurement-price-calculator' ),
		__( 'sq cm',   'woocommerce-measurement-price-calculator' ) => __( 'sq cm',   'woocommerce-measurement-price-calculator' ),
		__( 'sq m',    'woocommerce-measurement-price-calculator' ) => __( 'sq m',    'woocommerce-measurement-price-calculator' ),
		__( 'ha',      'woocommerce-measurement-price-calculator' ) => __( 'ha',      'woocommerce-measurement-price-calculator' ),
		__( 'sq km',   'woocommerce-measurement-price-calculator' ) => __( 'sq km',   'woocommerce-measurement-price-calculator' ),
		__( 'sq. in.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. in.', 'woocommerce-measurement-price-calculator' ),
		__( 'sq. ft.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. ft.', 'woocommerce-measurement-price-calculator' ),
		__( 'sq. yd.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. yd.', 'woocommerce-measurement-price-calculator' ),
		__( 'acs',     'woocommerce-measurement-price-calculator' ) => __( 'acs',     'woocommerce-measurement-price-calculator' ),
		__( 'sq. mi.', 'woocommerce-measurement-price-calculator' ) => __( 'sq. mi.', 'woocommerce-measurement-price-calculator' ),
	);
}


/**
 * Returns all available volume units
 *
 * @since 3.0
 * @return array of volume units
 */
function wc_measurement_price_calculator_get_volume_units() {

	$settings = wc_measurement_price_calculator_get_wc_settings();

	if ( $settings ) {
		foreach ( $settings as $setting ) {
			if ( 'woocommerce_volume_unit' === $setting['id'] ) {
				return $setting['options'];
			}
		}
	}

	// default in case the woocommerce settings are not available
	return array(
		__( 'ml',      'woocommerce-measurement-price-calculator' ) => __( 'ml',      'woocommerce-measurement-price-calculator' ),
		__( 'l',       'woocommerce-measurement-price-calculator' ) => __( 'l',       'woocommerce-measurement-price-calculator' ),
		__( 'cu m',    'woocommerce-measurement-price-calculator' ) => __( 'cu m',    'woocommerce-measurement-price-calculator' ),
		__( 'cup',     'woocommerce-measurement-price-calculator' ) => __( 'cup',     'woocommerce-measurement-price-calculator' ),
		__( 'pt',      'woocommerce-measurement-price-calculator' ) => __( 'pt',      'woocommerce-measurement-price-calculator' ),
		__( 'qt',      'woocommerce-measurement-price-calculator' ) => __( 'qt',      'woocommerce-measurement-price-calculator' ),
		__( 'gal',     'woocommerce-measurement-price-calculator' ) => __( 'gal',     'woocommerce-measurement-price-calculator' ),
		__( 'fl. oz.', 'woocommerce-measurement-price-calculator' ) => __( 'fl. oz.', 'woocommerce-measurement-price-calculator' ),
		__( 'cu. in.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. in.', 'woocommerce-measurement-price-calculator' ),
		__( 'cu. ft.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. ft.', 'woocommerce-measurement-price-calculator' ),
		__( 'cu. yd.', 'woocommerce-measurement-price-calculator' ) => __( 'cu. yd.', 'woocommerce-measurement-price-calculator' ),
	);
}


/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field with required fields 'id' and 'rbvalue'
 * @return void
 */
function wc_measurement_price_calculator_wp_radio( $field ) {
	global $thepostid, $post;

	if ( ! $thepostid ) {
		$thepostid = $post->ID;
	}
	if ( ! isset( $field['class'] ) ) {
		$field['class'] = 'radio';
	}
	if ( ! isset( $field['wrapper_class'] ) ) {
		$field['wrapper_class'] = '';
	}
	if ( ! isset( $field['name'] ) ) {
		$field['name'] = $field['id'];
	}
	if ( ! isset( $field['value'] ) ) {
		$product        = wc_get_product( $thepostid );
		$field['value'] = $product ? $product->get_meta( $field['name'] ) : '';
	}

	echo '<p class="form-field ' . $field['id'] . '_field ' . $field['wrapper_class'] . '"><label for="' . $field['id'].'">' . $field['label'] . '</label><input type="radio" class="' . $field['class'] . '" name="' . $field['name'] . '" id="' . $field['id'] . '" value="' . $field['rbvalue'] . '" ';

	checked( $field['value'], $field['rbvalue'] );

	echo ' /> ';

	if ( isset( $field['description'] ) && $field['description'] ) echo '<span class="description">' . $field['description'] . '</span>';

	echo '</p>';
}


/**
 * Render pricing overage input based on the measurement calculator option.
 *
 * @since 3.12.0
 *
 * @param string $measurement_type
 * @param array $settings
 * @return void
 */
function wc_measurement_price_calculator_overage_input( $measurement_type, $settings ) {

	$id    = "_measurement_{$measurement_type}_pricing_overage";
	$value = isset( $settings[ $measurement_type ]['pricing']['overage'] ) ? $settings[ $measurement_type ]['pricing']['overage'] : '';

	woocommerce_wp_text_input( array(
		'id'                => $id,
		'value'             => $value,
		'type'              => 'number',
		'decimal'           => 'decimal',
		'class'             => 'short small-text _measurement_pricing_overage',
		'wrapper_class'     => '_measurement_pricing_calculator_fields',
		'placeholder'       => '%',
		'label'             => __( 'Add Overage ', 'woocommerce-measurement-price-calculator' ),
		'description'       => __( 'If you need to add and charge for a cut or overage estimate in addition to the customer input, enter the percentage of the total measurement to use.', 'woocommerce-measurement-price-calculator' ),
		'desc_tip'          => true,
		'custom_attributes' => array(
			'min'  => '0',
			'max'  => '100',
			'step' => '1',
		),
	) );
}


/**
 * Render attributes inputs based on the measurement calculator option.
 *
 * @since 3.12.0
 *
 * @param array $args
 * @return void
 */
function wc_measurement_price_calculator_attributes_inputs( $args ) {

	$args = wp_parse_args( $args, array(
		'measurement'   => '',
		'input_name'    => '',
		'input_label'   => '',
		'settings'      => array(),
		'limited_field' => '',
	) );

	$settings    = $args['settings'];
	$measurement = $args['measurement'];
	$input_name  = $args['input_name'];

	if ( ! isset( $settings[ $measurement ] ) || ! isset( $settings[ $measurement ][ $input_name ] ) ) {
		return;
	}

	$inputs_id_prefix = $measurement === $input_name ? "_measurement_{$measurement}" : "_measurement_{$measurement}_{$input_name}";

	// for backwards compat to set an initial value; remove empty strings
	$original_options = array_filter( $settings[ $measurement ][ $input_name ]['options'] );

	woocommerce_wp_select( array(
		'id'                => "{$inputs_id_prefix}_accepted_input",
		'value'             => isset( $settings[$measurement][$input_name]['accepted_input'] ) ? $settings[$measurement][$input_name]['accepted_input'] : ( ! empty( $original_options ) ? 'limited' : 'free' ),
		'class'             => 'short small-text _measurement_accepted_input',
		'wrapper_class'     => '_measurement_pricing_calculator_fields',
		'label'             => sprintf( __( '%s Input', 'woocommerce-measurement-price-calculator' ), $args['input_label'] ),
		'options'           => array(
			'free'    => __( 'Accept free-form customer input', 'woocommerce-measurement-price-calculator' ),
			'limited' => __( 'Accept a limited set of customer inputs', 'woocommerce-measurement-price-calculator' ),
		),
		'custom_attributes' => array(
			'data-free'    => ".{$inputs_id_prefix}_input_attributes_field",
			'data-limited' => ".{$args['limited_field']}_field",
		),
	) );

	// these won't be set for stores upgrading to 3.12.0, have a sanity check
	$min_value  = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['min'] )  ? $settings[ $measurement ][ $input_name ]['input_attributes']['min']  : '';
	$max_value  = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['max'] )  ? $settings[ $measurement ][ $input_name ]['input_attributes']['max']  : '';
	$step_value = isset( $settings[ $measurement ][ $input_name ]['input_attributes']['step'] ) ? $settings[ $measurement ][ $input_name ]['input_attributes']['step'] : '';

	?>
	<p class="form-field <?php echo $inputs_id_prefix; ?>_input_attributes_field _measurement_pricing_calculator_fields _measurement_input_attributes dimensions_field">
		<label><?php printf( __( '%s Options', 'woocommerce-measurement-price-calculator' ), $args['input_label'] ); ?></label>
		<span class="wrap">
		<input placeholder="<?php esc_attr_e( 'Min value', 'woocommerce-measurement-price-calculator' ); ?>"
		       class="input-text wc_input_decimal" size="6" type="number" step="any"
		       name="<?php echo $inputs_id_prefix; ?>_input_attributes[min]"
		       value="<?php echo esc_attr( $min_value ); ?>"/>
		<input placeholder="<?php esc_attr_e( 'Max value', 'woocommerce-measurement-price-calculator' ); ?>"
		       class="input-text wc_input_decimal" size="6" type="number" step="any"
		       name="<?php echo $inputs_id_prefix; ?>_input_attributes[max]"
		       value="<?php echo esc_attr( $max_value ); ?>"/>
		<input placeholder="<?php esc_attr_e( 'Increment', 'woocommerce-measurement-price-calculator' ); ?>"
		       class="input-text wc_input_decimal last" size="6" type="number" step="any"
		       name="<?php echo $inputs_id_prefix; ?>_input_attributes[step]"
		       value="<?php echo esc_attr( $step_value ); ?>" />
		</span>
		<?php echo wc_help_tip( __( 'If applicable, enter limits to restrict customer input, such as an accepted increment and/or maximum value.', 'woocommerce-measurement-price-calculator' ) ); ?>
	</p>
	<?php
}
