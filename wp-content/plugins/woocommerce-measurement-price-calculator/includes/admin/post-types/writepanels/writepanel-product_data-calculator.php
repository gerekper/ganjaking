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
 * Product Data Panel - Measurement Price Calculator Tab
 *
 * Functions for displaying the measurement price calculator product data panel tab
 */

add_action( 'woocommerce_product_write_panel_tabs', 'wc_price_calculator_product_rates_panel_tab', 99 );

/**
 * Adds the "Calculator" tab to the Product Data postbox in the admin product interface
 */
function wc_price_calculator_product_rates_panel_tab() {
	echo '<li class="measurement_tab hide_if_grouped"><a href="#measurement_product_data"><span>' . __( 'Measurement', 'woocommerce-measurement-price-calculator' ) . '</span></a></li>';
}


add_action( 'woocommerce_product_data_panels', 'wc_price_calculator_product_rates_panel_content' );


/**
 * Adds the Calculator tab panel to the Product Data postbox in the product interface
 */
function wc_price_calculator_product_rates_panel_content() {
	global $post;

	$measurement_units = array(
		'weight'    => wc_measurement_price_calculator_get_weight_units(),
		'dimension' => wc_measurement_price_calculator_get_dimension_units(),
		'area'      => wc_measurement_price_calculator_get_area_units(),
		'volume'    => wc_measurement_price_calculator_get_volume_units(),
	);

	?>
	<div id="measurement_product_data" class="panel woocommerce_options_panel">
		<style type="text/css">
			#measurement_product_data hr { height:2px; border-style:none; border-bottom:solid 1px white; color:#DFDFDF; background-color:#DFDFDF; }
			.measurement-subnav { margin:14px 12px; }
			.measurement-subnav a { text-decoration:none; }
			.measurement-subnav a.active { color:black; font-weight:bold; }
			.measurement-subnav a.disabled { color: #8A7F7F; cursor: default; }
			#measurement_product_data .wc-calculator-pricing-table td.wc-calculator-pricing-rule-range input { float:none; width:auto; }
			#measurement_product_data table.wc-calculator-pricing-table { margin: 12px; width: 95%; }
			#measurement_product_data table.wc-calculator-pricing-table td { padding: 10px 7px 10px; cursor: move; }
			#measurement_product_data table.wc-calculator-pricing-table button { font-family: sans-serif; }
			#measurement_product_data table.wc-calculator-pricing-table button.wc-calculator-pricing-table-delete-rules { float: right; }
			#measurement_product_data input._measurement_pricing_overage { width: 65px !important; }
		</style>
		<div class="measurement-subnav">
			<a class="active" href="#calculator-settings"><?php esc_html_e( 'Calculator Settings', 'woocommerce-measurement-price-calculator' ); ?></a> |
			<a class="wc-measurement-price-calculator-pricing-table" href="#calculator-pricing-table"><?php esc_html_e( 'Pricing Table', 'woocommerce-measurement-price-calculator' ); ?></a>
		</div>
		<hr/>
		<?php
		$settings = new \WC_Price_Calculator_Settings( $post->ID );

		$pricing_weight_wrapper_class = '';
		if ( 'no' === get_option( 'woocommerce_enable_weight', true ) ) {
			$pricing_weight_wrapper_class = 'hidden';
		}

		$settings = $settings->get_raw_settings();  // we want the underlying raw settings array

		$calculator_options = array(
			''                 => __( 'None',                         'woocommerce-measurement-price-calculator' ),
			'dimension'        => __( 'Dimensions',                   'woocommerce-measurement-price-calculator' ),
			'area'             => __( 'Area',                         'woocommerce-measurement-price-calculator' ),
			'area-dimension'   => __( 'Area (LxW)',                   'woocommerce-measurement-price-calculator' ),
			'area-linear'      => __( 'Perimeter (2L + 2W)',          'woocommerce-measurement-price-calculator' ),
			'area-surface'     => __( 'Surface Area 2(LW + LH + WH)', 'woocommerce-measurement-price-calculator' ),
			'volume'           => __( 'Volume',                       'woocommerce-measurement-price-calculator' ),
			'volume-dimension' => __( 'Volume (LxWxH)',               'woocommerce-measurement-price-calculator' ),
			'volume-area'      => __( 'Volume (AxH)',                 'woocommerce-measurement-price-calculator' ),
			'weight'           => __( 'Weight',                       'woocommerce-measurement-price-calculator' ),
			'wall-dimension'   => __( 'Room Walls',                   'woocommerce-measurement-price-calculator' ),
		);

		echo '<div id="calculator-settings" class="calculator-subpanel">';

		// Measurement select
		woocommerce_wp_select( array(
			'id'          => '_measurement_price_calculator',
			'value'       => $settings['calculator_type'],
			'label'       => __( 'Measurement', 'woocommerce-measurement-price-calculator' ),
			'options'     => $calculator_options,
			'description' => __( 'Select the product measurement to calculate quantity by or define pricing within.', 'woocommerce-measurement-price-calculator' ),
			'desc_tip'    => true,
		) );

		echo '<p id="area-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement to have the customer prompted for a length and width to calculate the area required.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the product area.", 'woocommerce-measurement-price-calculator' ) . '</p>';
		echo '<p id="area-linear_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for a length and width to calculate the linear distance (L * 2 + W * 2).", 'woocommerce-measurement-price-calculator' ) . '</p>';
		echo '<p id="area-surface_description" class="measurement_description" style="display:none;">' .     __( "Use this measurement to have the customer prompted for a length, width and height to calculate the surface area 2 * (L * W + W * H + L * H).", 'woocommerce-measurement-price-calculator' ) . '</p>';
		echo '<p id="volume-dimension_description" class="measurement_description" style="display:none;">' . __( "Use this measurement to have the customer prompted for a length, width and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", 'woocommerce-measurement-price-calculator' ) . '</p>';
		echo '<p id="volume-area_description" class="measurement_description" style="display:none;">' .      __( "Use this measurement to have the customer prompted for an area and height to calculate the volume required.  When pricing is disabled (no custom dimensions) this calculator uses the product volume attribute or otherwise the length, width and height attributes to determine the product volume.", 'woocommerce-measurement-price-calculator' ) . '</p>';
		echo '<p id="wall-dimension_description" class="measurement_description" style="display:none;">' .   __( "Use this measurement for applications such as wallpaper; the customer will be prompted for the wall height and distance around the room.  When pricing is disabled (no custom dimensions) this calculator uses the product area attribute or otherwise the length and width attributes to determine the wall surface area.", 'woocommerce-measurement-price-calculator' ) . '</p>';

		echo '<div id="dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_dimension_pricing',
				'value'         => $settings['dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_pricing_label',
					'value'       => $settings['dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_pricing_unit',
					'value'       => $settings['dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_weight_enabled',
					'value'         => $settings['dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product dimension', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_dimension_pricing_inventory_enabled',
					'value'         => $settings['dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product dimension', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'dimension', $settings );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Length
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_length',
				'rbvalue'     => 'length',
				'value'       => 'yes' == $settings['dimension']['length']['enabled'] ? 'length' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Select to display the product length in the price calculator', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_dimension_length_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_length_label',
					'value'       => $settings['dimension']['length']['label'],
					'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_length_unit',
					'value'       => $settings['dimension']['length']['unit'] ,
					'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_length_editable',
					'value'       => $settings['dimension']['length']['editable'],
					'label'       => __( 'Length Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed length to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'dimension',
					'input_name'    => 'length',
					'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_dimension_length_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_length_options',
					'value'         => wc_measurement_price_calculator_get_options_value( $settings['dimension']['length']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wc_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Width
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_width',
				'rbvalue'     => 'width',
				'value'       => 'yes' == $settings['dimension']['width']['enabled'] ? 'width' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Width', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Select to display the product width in the price calculator', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_dimension_width_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_width_label',
					'value'       => $settings['dimension']['width']['label'],
					'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_width_unit',
					'value'       => $settings['dimension']['width']['unit'],
					'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_width_editable',
					'value'       => $settings['dimension']['width']['editable'],
					'label'       => __( 'Width Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed width to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'dimension',
					'input_name'    => 'width',
					'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_dimension_width_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_width_options',
					'value'         => wc_measurement_price_calculator_get_options_value( $settings['dimension']['width']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wc_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
			echo '<hr/>';

			// Dimension - Height
			wc_measurement_price_calculator_wp_radio( array(
				'name'        => '_measurement_dimension',
				'id'          => '_measurement_dimension_height',
				'rbvalue'     => 'height',
				'value'       => 'yes' == $settings['dimension']['height']['enabled'] ? 'height' : '',
				'class'       => 'checkbox _measurement_dimension',
				'label'       => __( 'Height', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Select to display the product height in the price calculator', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_dimension_height_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_dimension_height_label',
					'value'       => $settings['dimension']['height']['label'],
					'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_dimension_height_unit',
					'value'       => $settings['dimension']['height']['unit'],
					'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'          => '_measurement_dimension_height_editable',
					'value'       => $settings['dimension']['height']['editable'],
					'label'       => __( 'Height Editable', 'woocommerce-measurement-price-calculator' ),
					'class'       => 'checkbox _measurement_editable',
					'description' => __( 'Check this box to allow the needed height to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_attributes_inputs( array(
					'measurement'   => 'dimension',
					'input_name'    => 'height',
					'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
					'settings'      => $settings,
					'limited_field' => '_measurement_dimension_height_options',
				) );
				woocommerce_wp_text_input( array(
					'id'            => '_measurement_dimension_height_options',
					'value'         => wc_measurement_price_calculator_get_options_value( $settings['dimension']['height']['options'] ),
					'wrapper_class' => '_measurement_pricing_calculator_fields',
					'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
					'description'   => wc_measurement_price_calculator_get_options_tooltip(),
					'desc_tip'      => true,
				) );
			echo '</div>';
		echo '</div>';

		// Area
		echo '<div id="area_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area_pricing',
				'value'         => $settings['area']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' )
			) );
			echo '<div id="_measurement_area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area_pricing_label',
					'value'       => $settings['area']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area_pricing_unit',
					'value'       => $settings['area']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_weight_enabled',
					'value'         => $settings['area']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area_pricing_inventory_enabled',
					'value'         => $settings['area']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'area', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_label',
				'value'       => $settings['area']['area']['label'],
				'label'       => __( 'Area Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Area input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_unit',
				'value'       => $settings['area']['area']['unit'],
				'label'       => __( 'Area Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['area'],
				'description' => __( 'The frontend area input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_area_editable',
				'value'       => $settings['area']['area']['editable'],
				'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area',
				'input_name'    => 'area',
				'input_label'   => __( 'Area', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area']['area']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Area Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Area (LxW)
		echo '<div id="area-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-dimension_pricing',
				'value'         => $settings['area-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_area-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-dimension_pricing_label',
					'value'       => $settings['area-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-dimension_pricing_unit',
					'value'       => $settings['area-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_weight_enabled',
					'value'         => $settings['area-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-dimension_pricing_inventory_enabled',
					'value'         => $settings['area-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'area-dimension', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_length_label',
				'value'       => $settings['area-dimension']['length']['label'],
				'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_length_unit',
				'value'       => $settings['area-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-dimension',
				'input_name'    => 'length',
				'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area_length_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_length_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area_width_label',
				'value'       => $settings['area-dimension']['width']['label'],
				'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area_width_unit',
				'value'       => $settings['area-dimension']['width']['unit'],
				'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-dimension',
				'input_name'    => 'width',
				'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area_width_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area_width_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Perimeter (2 * L + 2 * W)
		echo '<div id="area-linear_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-linear_pricing',
				'value'         => $settings['area-linear']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_area-linear_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-linear_pricing_label',
					'value'       => $settings['area-linear']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-linear_pricing_unit',
					'value'       => $settings['area-linear']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['dimension'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-linear']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_weight_enabled',
					'value'         => $settings['area-linear']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-linear_pricing_inventory_enabled',
					'value'         => $settings['area-linear']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'area-linear', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-linear_length_label',
				'value'       => $settings['area-linear']['length']['label'],
				'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-linear_length_unit',
				'value'       => $settings['area-linear']['length']['unit'],
				'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-linear',
				'input_name'    => 'length',
				'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area-linear_length_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-linear_length_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-linear']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-linear_width_label',
				'value'       => $settings['area-linear']['width']['label'],
				'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-linear_width_unit',
				'value'       => $settings['area-linear']['width']['unit'],
				'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-linear',
				'input_name'    => 'width',
				'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area-linear_width_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-linear_width_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-linear']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Surface Area 2 * (L * W + W * H + L * H)
		echo '<div id="area-surface_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_area-surface_pricing',
				'value'         => $settings['area-surface']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_area-surface_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_area-surface_pricing_label',
					'value'       => $settings['area-surface']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_area-surface_pricing_unit',
					'value'       => $settings['area-surface']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['area-surface']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_weight_enabled',
					'value'         => $settings['area-surface']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_area-surface_pricing_inventory_enabled',
					'value'         => $settings['area-surface']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'area-surface', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_length_label',
				'value'       => $settings['area-surface']['length']['label'],
				'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_length_unit',
				'value'       => $settings['area-surface']['length']['unit'],
				'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-surface',
				'input_name'    => 'length',
				'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area-surface_length_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_length_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-surface']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_width_label',
				'value'       => $settings['area-surface']['width']['label'],
				'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_width_unit',
				'value'       => $settings['area-surface']['width']['unit'],
				'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-surface',
				'input_name'    => 'width',
				'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area-surface_width_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_width_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-surface']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_area-surface_height_label',
				'value'       => $settings['area-surface']['height']['label'],
				'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_area-surface_height_unit',
				'value'       => $settings['area-surface']['height']['unit'],
				'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'area-surface',
				'input_name'    => 'height',
				'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_area-surface_height_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_area-surface_height_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['area-surface']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume
		echo '<div id="volume_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume_pricing',
				'value'         => $settings['volume']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_volume_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume_pricing_label',
					'value'       => $settings['volume']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume_pricing_unit',
					'value'       => $settings['volume']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_weight_enabled',
					'value'         => $settings['volume']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume_pricing_inventory_enabled',
					'value'         => $settings['volume']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'volume', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_label',
				'value'       => $settings['volume']['volume']['label'],
				'label'       => __( 'Volume Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Volume input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_unit',
				'value'       => $settings['volume']['volume']['unit'],
				'label'       => __( 'Volume Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['volume'],
				'description' => __( 'The frontend volume input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_volume_editable',
				'value'       => $settings['volume']['volume']['editable'],
				'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume',
				'input_name'    => 'volume',
				'input_label'   => __( 'Volume', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume']['volume']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Volume Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume (LxWxH)
		echo '<div id="volume-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume-dimension_pricing',
				'value'         => $settings['volume-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_volume-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume-dimension_pricing_label',
					'value'       => $settings['volume-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume-dimension_pricing_unit',
					'value'       => $settings['volume-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_weight_enabled',
					'value'         => $settings['volume-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-dimension_pricing_inventory_enabled',
					'value'         => $settings['volume-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'volume-dimension', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_length_label',
				'value'       => $settings['volume-dimension']['length']['label'],
				'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_length_unit',
				'value'       => $settings['volume-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend length input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume-dimension',
				'input_name'    => 'length',
				'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_length_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_length_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_width_label',
				'value'       => $settings['volume-dimension']['width']['label'],
				'label'       => __( 'Width Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Width input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_width_unit',
				'value'       => $settings['volume-dimension']['width']['unit'],
				'label'       => __( 'Width Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend width input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume-dimension',
				'input_name'    => 'width',
				'input_label'   => __( 'Width', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_width_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_width_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Width Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_height_label',
				'value'       => $settings['volume-dimension']['height']['label'],
				'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_height_unit',
				'value'       => $settings['volume-dimension']['height']['unit'],
				'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume-dimension',
				'input_name'    => 'height',
				'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_height_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_height_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume-dimension']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Volume (AxH)
		echo '<div id="volume-area_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_volume-area_pricing',
				'value'         => $settings['volume-area']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_volume-area_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_volume-area_pricing_label',
					'value'       => $settings['volume-area']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_volume-area_pricing_unit',
					'value'       => $settings['volume-area']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['volume'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['volume-area']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_weight_enabled',
					'value'         => $settings['volume-area']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_volume-area_pricing_inventory_enabled',
					'value'         => $settings['volume-area']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product volume', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'volume-area', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_area_label',
				'value'       => $settings['volume-area']['area']['label'],
				'label'       => __( 'Area Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Area input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_area_unit',
				'value'       => $settings['volume-area']['area']['unit'],
				'label'       => __( 'Area Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['area'],
				'description' => __( 'The frontend area input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume-area',
				'input_name'    => 'area',
				'input_label'   => __( 'Area', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_area_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_area_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume-area']['area']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Area Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_volume_area_height_label',
				'value'       => $settings['volume-area']['height']['label'],
				'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_volume_area_height_unit',
				'value'       => $settings['volume-area']['height']['unit'],
				'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend height input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'volume-area',
				'input_name'    => 'height',
				'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_volume_area_height_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_volume_area_height_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['volume-area']['height']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';

		// Weight
		echo '<div id="weight_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_weight_pricing',
				'value'         => $settings['weight']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_weight_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_weight_pricing_label',
					'value'       => $settings['weight']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_weight_pricing_unit',
					'value'       => $settings['weight']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['weight'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['weight']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_weight_enabled',
					'value'         => $settings['weight']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to use the customer-configured product weight as the item weight', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_weight_pricing_inventory_enabled',
					'value'         => $settings['weight']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product weight', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'weight', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_weight_label',
				'value'       => $settings['weight']['weight']['label'],
				'label'       => __( 'Weight Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Weight input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_weight_unit',
				'value'       => $settings['weight']['weight']['unit'],
				'label'       => __( 'Weight Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['weight'],
				'description' => __( 'The frontend weight input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_checkbox( array(
				'id'          => '_measurement_weight_editable',
				'value'       => $settings['weight']['weight']['editable'],
				'label'       => __( 'Editable', 'woocommerce-measurement-price-calculator' ),
				'class'       => 'checkbox _measurement_editable',
				'description' => __( 'Check this box to allow the needed measurement to be entered by the customer', 'woocommerce-measurement-price-calculator' ),
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'weight',
				'input_name'    => 'weight',
				'input_label'   => __( 'Weight', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_weight_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_weight_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['weight']['weight']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Weight Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';


		// wall dimension is just the area-dimension calculator with different labels
		echo '<div id="wall-dimension_measurements" class="measurement_fields">';
			woocommerce_wp_checkbox( array(
				'id'            => '_measurement_wall-dimension_pricing',
				'value'         => $settings['wall-dimension']['pricing']['enabled'],
				'class'         => 'checkbox _measurement_pricing',
				'label'         => __( 'Show Product Price Per Unit', 'woocommerce-measurement-price-calculator' ),
				'description'   => __( 'Check this box to display product pricing per unit on the frontend', 'woocommerce-measurement-price-calculator' ),
			) );
			echo '<div id="_measurement_wall-dimension_pricing_fields" class="_measurement_pricing_fields" style="display:none;">';
				woocommerce_wp_text_input( array(
					'id'          => '_measurement_wall-dimension_pricing_label',
					'value'       => $settings['wall-dimension']['pricing']['label'],
					'label'       => __( 'Pricing Label', 'woocommerce-measurement-price-calculator' ),
					'description' => __( 'Label to display next to the product price (defaults to pricing unit)', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_select( array(
					'id'          => '_measurement_wall-dimension_pricing_unit',
					'value'       => $settings['wall-dimension']['pricing']['unit'],
					'class'       => '_measurement_pricing_unit',
					'label'       => __( 'Pricing Unit', 'woocommerce-measurement-price-calculator' ),
					'options'     => $measurement_units['area'],
					'description' => __( 'Unit to define pricing in', 'woocommerce-measurement-price-calculator' ),
					'desc_tip'    => true,
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_calculator_enabled',
					'class'         => 'checkbox _measurement_pricing_calculator_enabled',
					'value'         => $settings['wall-dimension']['pricing']['calculator']['enabled'],
					'label'         => __( 'Calculated Price', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define product pricing per unit and allow customers to provide custom measurements', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_weight_enabled',
					'value'         => $settings['wall-dimension']['pricing']['weight']['enabled'],
					'class'         => 'checkbox _measurement_pricing_weight_enabled',
					'wrapper_class' => $pricing_weight_wrapper_class . ' _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Weight', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define the product weight per unit and calculate the item weight based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				woocommerce_wp_checkbox( array(
					'id'            => '_measurement_wall-dimension_pricing_inventory_enabled',
					'value'         => $settings['wall-dimension']['pricing']['inventory']['enabled'],
					'class'         => 'checkbox _measurement_pricing_inventory_enabled',
					'wrapper_class' => 'stock_fields _measurement_pricing_calculator_fields',
					'label'         => __( 'Calculated Inventory', 'woocommerce-measurement-price-calculator' ),
					'description'   => __( 'Check this box to define inventory per unit and calculate inventory based on the product area', 'woocommerce-measurement-price-calculator' ),
				) );
				wc_measurement_price_calculator_overage_input( 'wall-dimension', $settings );
			echo '</div>';
			echo '<hr/>';
			woocommerce_wp_text_input( array(
				'id'          => '_measurement_wall_length_label',
				'value'       => $settings['wall-dimension']['length']['label'],
				'label'       => __( 'Length Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Wall length input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_wall_length_unit',
				'value'       => $settings['wall-dimension']['length']['unit'],
				'label'       => __( 'Length Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend wall length input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'wall-dimension',
				'input_name'    => 'length',
				'input_label'   => __( 'Length', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_wall_length_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_wall_length_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['wall-dimension']['length']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Length Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
			echo '<hr/>';

			woocommerce_wp_text_input( array(
				'id'          => '_measurement_wall_width_label',
				'value'       => $settings['wall-dimension']['width']['label'],
				'label'       => __( 'Height Label', 'woocommerce-measurement-price-calculator' ),
				'description' => __( 'Room wall height input field label to display on the frontend', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			woocommerce_wp_select( array(
				'id'          => '_measurement_wall_width_unit',
				'value'       => $settings['wall-dimension']['width']['unit'],
				'label'       => __( 'Height Unit', 'woocommerce-measurement-price-calculator' ),
				'options'     => $measurement_units['dimension'],
				'description' => __( 'The frontend room wall height input field unit', 'woocommerce-measurement-price-calculator' ),
				'desc_tip'    => true,
			) );
			wc_measurement_price_calculator_attributes_inputs( array(
				'measurement'   => 'wall-dimension',
				'input_name'    => 'width',
				'input_label'   => __( 'Height', 'woocommerce-measurement-price-calculator' ),
				'settings'      => $settings,
				'limited_field' => '_measurement_wall_width_options',
			) );
			woocommerce_wp_text_input( array(
				'id'            => '_measurement_wall_width_options',
				'value'         => wc_measurement_price_calculator_get_options_value( $settings['wall-dimension']['width']['options'] ),
				'wrapper_class' => '_measurement_pricing_calculator_fields',
				'label'         => __( 'Height Options', 'woocommerce-measurement-price-calculator' ),
				'description'   => wc_measurement_price_calculator_get_options_tooltip(),
				'desc_tip'      => true,
			) );
		echo '</div>';
		echo '</div>'; // close the subpanel
		echo '<div id="calculator-pricing-table" class="calculator-subpanel">';
		require_once( wc_measurement_price_calculator()->get_plugin_path() . '/includes/admin/post-types/writepanels/writepanel-product_data-pricing_table.php' );
		echo '</div>';
		?>

	</div>
	<?php
}


// Hooked after the WC core handler
add_action( 'woocommerce_process_product_meta', 'wc_measurement_price_calculator_process_product_meta_measurement', 10, 2 );


/**
 * Save the measurement price calculator custom fields
 *
 * @param int $post_id post identifier
 * @param array $post the post object
 */
function wc_measurement_price_calculator_process_product_meta_measurement( $post_id, $post ) {

	$product = wc_get_product( $post );

	// skip saving the meta if this a calculator type is not set i.e. not a measurement product
	if ( isset( $_POST['_measurement_price_calculator'] ) && '' === $_POST['_measurement_price_calculator'] ) {

		$settings = get_post_meta( $product->get_id(), '_wc_price_calculator', true );

		// check if post meta is set already
		if ( ! empty( $settings ) && is_array( $settings ) ) {

			// only change the calculator type so none of the other fields are lost
			$settings['calculator_type'] = '';

			update_post_meta( $product->get_id(), '_wc_price_calculator', $settings );
		}

		return;
	}

	// get product type
	$is_virtual   = isset( $_POST['_virtual'] ) ? 'yes' : 'no';
	$product_type = sanitize_title( stripslashes( $_POST['product-type'] ) );

	// Dimensions: virtual and grouped products not allowed
	if ( 'no' === $is_virtual && 'grouped' !== $product_type ) {

		$settings = array();

		// the type of calculator enabled, one of 'dimension', 'area', etc or empty for disabled
		$settings['calculator_type'] = $_POST['_measurement_price_calculator'];

		$settings['dimension']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_dimension_pricing'] ) && $_POST['_measurement_dimension_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_dimension_pricing_label'],
			'unit'           => $_POST['_measurement_dimension_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_dimension_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'dimension' ),
		);
		$settings['dimension']['length'] = array(
			'enabled'          => isset( $_POST['_measurement_dimension'] ) && 'length' === $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'            => $_POST['_measurement_dimension_length_label'],
			'unit'             => $_POST['_measurement_dimension_length_unit'],
			'editable'         => isset( $_POST['_measurement_dimension_length_editable'] ) && $_POST['_measurement_dimension_length_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_dimension_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'dimension', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'dimension', 'length' ),
		);
		$settings['dimension']['width'] = array(
			'enabled'          => isset( $_POST['_measurement_dimension'] ) && 'width' === $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'            => $_POST['_measurement_dimension_width_label'],
			'unit'             => $_POST['_measurement_dimension_width_unit'],
			'editable'         => isset( $_POST['_measurement_dimension_width_editable'] ) && $_POST['_measurement_dimension_width_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_dimension_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'dimension', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'dimension', 'width' ),
		);
		$settings['dimension']['height'] = array(
			'enabled'          => isset( $_POST['_measurement_dimension'] ) && 'height' === $_POST['_measurement_dimension'] ? 'yes' : 'no',
			'label'            => $_POST['_measurement_dimension_height_label'],
			'unit'             => $_POST['_measurement_dimension_height_unit'],
			'editable'         => isset( $_POST['_measurement_dimension_height_editable'] ) && $_POST['_measurement_dimension_height_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_dimension_height_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'dimension', 'height' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'dimension', 'height' ),
		);

		// simple area calculator
		$settings['area']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_area_pricing'] ) && $_POST['_measurement_area_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_area_pricing_label'],
			'unit'           => $_POST['_measurement_area_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'area' ),
		);
		$settings['area']['area'] = array(
			'label'            => $_POST['_measurement_area_label'],
			'unit'             => $_POST['_measurement_area_unit'],
			'editable'         => isset( $_POST['_measurement_area_editable'] ) && $_POST['_measurement_area_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area', 'area' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area', 'area' ),
		);

		// area (LxW) calculator
		$settings['area-dimension']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_area-dimension_pricing'] ) && $_POST['_measurement_area-dimension_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_area-dimension_pricing_label'],
			'unit'           => $_POST['_measurement_area-dimension_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-dimension_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'area-dimension' ),
		);
		$settings['area-dimension']['length'] = array(
			'label'            => $_POST['_measurement_area_length_label'],
			'unit'             => $_POST['_measurement_area_length_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-dimension', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-dimension', 'length' ),
		);
		$settings['area-dimension']['width'] = array(
			'label'            => $_POST['_measurement_area_width_label'],
			'unit'             => $_POST['_measurement_area_width_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-dimension', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-dimension', 'width' ),
		);

		// Perimeter (2L + 2W) calculator
		$settings['area-linear']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_area-linear_pricing'] ) && $_POST['_measurement_area-linear_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_area-linear_pricing_label'],
			'unit'           => $_POST['_measurement_area-linear_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-linear_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'area-linear' ),
		);
		$settings['area-linear']['length'] = array(
			'label'            => $_POST['_measurement_area-linear_length_label'],
			'unit'             => $_POST['_measurement_area-linear_length_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area-linear_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-linear', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-linear', 'length' ),
		);
		$settings['area-linear']['width'] = array(
			'label'            => $_POST['_measurement_area-linear_width_label'],
			'unit'             => $_POST['_measurement_area-linear_width_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area-linear_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-linear', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-linear', 'width' ),
		);

		// Surface Area 2(LW + WH + LH) calculator
		$settings['area-surface']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_area-surface_pricing'] ) && $_POST['_measurement_area-surface_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_area-surface_pricing_label'],
			'unit'           => $_POST['_measurement_area-surface_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_area-surface_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'area-surface' ),
		);
		$settings['area-surface']['length'] = array(
			'label'            => $_POST['_measurement_area-surface_length_label'],
			'unit'             => $_POST['_measurement_area-surface_length_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area-surface_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-surface', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-surface', 'length' ),
		);
		$settings['area-surface']['width'] = array(
			'label'            => $_POST['_measurement_area-surface_width_label'],
			'unit'             => $_POST['_measurement_area-surface_width_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area-surface_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-surface', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-surface', 'width' ),
		);
		$settings['area-surface']['height'] = array(
			'label'            => $_POST['_measurement_area-surface_height_label'],
			'unit'             => $_POST['_measurement_area-surface_height_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_area-surface_height_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'area-surface', 'height' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'area-surface', 'height' ),
		);

		// Simple volume calculator
		$settings['volume']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_volume_pricing'] ) && $_POST['_measurement_volume_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_volume_pricing_label'],
			'unit'           => $_POST['_measurement_volume_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'volume' ),
		);
		$settings['volume']['volume'] = array(
			'label'            => $_POST['_measurement_volume_label'],
			'unit'             => $_POST['_measurement_volume_unit'],
			'editable'         => isset( $_POST['_measurement_volume_editable'] ) && $_POST['_measurement_volume_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume', 'volume' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume', 'volume' ),
		);

		// volume (L x W x H) calculator
		$settings['volume-dimension']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_volume-dimension_pricing'] ) && $_POST['_measurement_volume-dimension_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_volume-dimension_pricing_label'],
			'unit'           => $_POST['_measurement_volume-dimension_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-dimension_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'volume-dimension' ),
		);
		$settings['volume-dimension']['length'] = array(
			'label'            => $_POST['_measurement_volume_length_label'],
			'unit'             => $_POST['_measurement_volume_length_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume-dimension', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume-dimension', 'length' ),
		);
		$settings['volume-dimension']['width'] = array(
			'label'            => $_POST['_measurement_volume_width_label'],
			'unit'             => $_POST['_measurement_volume_width_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume-dimension', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume-dimension', 'width' ),
		);
		$settings['volume-dimension']['height'] = array(
			'label'            => $_POST['_measurement_volume_height_label'],
			'unit'             => $_POST['_measurement_volume_height_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_height_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume-dimension', 'height' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume-dimension', 'height' ),
		);

		// volume (A x H) calculator
		$settings['volume-area']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_volume-area_pricing'] ) && $_POST['_measurement_volume-area_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_volume-area_pricing_label'],
			'unit'           => $_POST['_measurement_volume-area_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_volume-area_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'volume-area' ),
		);
		$settings['volume-area']['area'] = array(
			'label'            => $_POST['_measurement_volume_area_label'],
			'unit'             => $_POST['_measurement_volume_area_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_area_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume-area', 'area' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume-area', 'area' ),
		);
		$settings['volume-area']['height'] = array(
			'label'            => $_POST['_measurement_volume_area_height_label'],
			'unit'             => $_POST['_measurement_volume_area_height_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_volume_area_height_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'volume-area', 'height' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'volume-area', 'height' ),
		);

		// simple weight calculator
		$settings['weight']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_weight_pricing'] ) && $_POST['_measurement_weight_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_weight_pricing_label'],
			'unit'           => $_POST['_measurement_weight_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_weight_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'weight' ),
		);
		$settings['weight']['weight'] = array(
			'label'            => $_POST['_measurement_weight_label'],
			'unit'             => $_POST['_measurement_weight_unit'],
			'editable'         => isset( $_POST['_measurement_weight_editable'] ) && $_POST['_measurement_weight_editable'] ? 'yes' : 'no',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_weight_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'weight', 'weight' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'weight', 'weight' ),
		);

		// the wall calculator is just a bit of syntactic sugar on top of the Area (LxW) calculator
		$settings['wall-dimension']['pricing'] = array(
			'enabled'        => isset( $_POST['_measurement_wall-dimension_pricing'] ) && $_POST['_measurement_wall-dimension_pricing'] ? 'yes' : 'no',
			'label'          => $_POST['_measurement_wall-dimension_pricing_label'],
			'unit'           => $_POST['_measurement_wall-dimension_pricing_unit'],
			'calculator'     => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_calculator_enabled' ),
			),
			'inventory'      => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_inventory_enabled' ),
			),
			'weight'         => array(
				'enabled' => wc_measurement_price_calculator_get_checkbox_post( '_measurement_wall-dimension_pricing_weight_enabled' ),
			),
			'overage'        => wc_measurement_price_calculator_get_overage_post( 'wall-dimension' ),
		);
		$settings['wall-dimension']['length'] = array(
			'label'            => $_POST['_measurement_wall_length_label'],
			'unit'             => $_POST['_measurement_wall_length_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_wall_length_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'wall-dimension', 'length' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'wall-dimension', 'length' ),
		);
		$settings['wall-dimension']['width'] = array(
			'label'            => $_POST['_measurement_wall_width_label'],
			'unit'             => $_POST['_measurement_wall_width_unit'],
			'editable'         => 'yes',
			'options'          => wc_measurement_price_calculator_get_options_post( '_measurement_wall_width_options' ),
			'accepted_input'   => wc_measurement_price_calculator_get_accepted_input_post( 'wall-dimension', 'width' ),
			'input_attributes' => wc_measurement_price_calculator_get_input_attributes_post( 'wall-dimension', 'width' ),
		);

		// save settings
		update_post_meta( $product->get_id(), '_wc_price_calculator', $settings );

		// persist any pricing rules
		$rules = array();

		// persist any rules assigned to this product, only if the current pricing calculator is enabled
		if ( isset( $_POST["_measurement_{$settings['calculator_type']}_pricing_calculator_enabled"] ) && ! empty( $_POST['_wc_measurement_pricing_rule_range_start'] ) && is_array( $_POST['_wc_measurement_pricing_rule_range_start'] ) ) {

			$regular_prices = $sale_prices = $prices = array();

			foreach ( $_POST['_wc_measurement_pricing_rule_range_start'] as $index => $pricing_rule_range_start ) {

				$pricing_rule_range_end     = $_POST['_wc_measurement_pricing_rule_range_end'][ $index ];
				$pricing_rule_regular_price = $_POST['_wc_measurement_pricing_rule_regular_price'][ $index ];
				$pricing_rule_sale_price    = $_POST['_wc_measurement_pricing_rule_sale_price'][ $index ];
				$pricing_rule_price         = '' !== $pricing_rule_sale_price ? $pricing_rule_sale_price : $pricing_rule_regular_price;

				if ( $pricing_rule_range_start || $pricing_rule_range_end || $pricing_rule_price ) {

					if ( is_numeric( $pricing_rule_sale_price ) ) {
						$sale_prices[]    = abs( $pricing_rule_sale_price );
					}

					if ( is_numeric( $pricing_rule_regular_price ) ) {
						$regular_prices[] = abs( $pricing_rule_regular_price );
					}

					if ( is_numeric( $pricing_rule_price ) ) {
						$prices[]         = abs( $pricing_rule_price );
					}

					$rules[] = array(
						'range_start'   => $pricing_rule_range_start,
						'range_end'     => $pricing_rule_range_end,
						'price'         => $pricing_rule_price,
						'regular_price' => $pricing_rule_regular_price,
						'sale_price'    => $pricing_rule_sale_price,
					);
				}
			}

			$meta_prices = array(
				'_price'         => ! empty( $prices )         ? min( $prices )         : '',
				'_regular_price' => ! empty( $regular_prices ) ? min( $regular_prices ) : '',
			    '_sale_price'    => ! empty( $sale_prices )    ? min( $sale_prices )    : '',
			);

			// this tricks WC core to show the product in sale product listings when using direct MySQL queries
			foreach ( $meta_prices as $meta_key => $value ) {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		// save settings
		update_post_meta( $product->get_id(), '_wc_price_calculator_pricing_rules', $rules );
	}
}


/**
 * Helper function to safely get a checkbox post value
 *
 * @access private
 * @since 3.0
 * @param string $name the checkbox name
 * @return string "yes" or "no" depending on whether the checkbox named $name
 *         was set
 */
function wc_measurement_price_calculator_get_checkbox_post( $name ) {
	return isset( $_POST[ $name ] ) && $_POST[ $name ] ? 'yes' : 'no';
}


/**
 * Helper function to safely get overage post value
 *
 * @since 3.12.0
 *
 * @param string $measurement_type
 * @return int positive number between 0 & 100
 */
function wc_measurement_price_calculator_get_overage_post( $measurement_type ) {

	$input_name  = "_measurement_{$measurement_type}_pricing_overage";
	$input_value = isset( $_POST[ $input_name ] ) ? absint( $_POST[ $input_name ] ) : 0;

	if ( $input_value > 100 ) {
		return 100;
	}

	if ( $input_value < 0 ) {
		return 0;
	}

	return $input_value;
}


/**
 * Helper function to safely get accepted input post value
 *
 * @since 3.12.0
 *
 * @param string $measurement_type
 * @param string $input_name
 * @return string
 */
function wc_measurement_price_calculator_get_accepted_input_post( $measurement_type, $input_name ) {

	$post_name      = $measurement_type === $input_name ? "_measurement_{$measurement_type}_accepted_input" : "_measurement_{$measurement_type}_{$input_name}_accepted_input";
	$accepted_input = isset( $_POST[ $post_name ] ) ? sanitize_key( $_POST[ $post_name ] ) : '';

	if ( ! in_array( $accepted_input, array( 'free', 'limited' ) ) ) {
		$accepted_input = 'free';
	}

	return $accepted_input;
}


/**
 * Helper function to safely get input attributes post values
 *
 * @since 3.12.0
 *
 * @param string $measurement_type
 * @param string $input_name
 * @return array
 */
function wc_measurement_price_calculator_get_input_attributes_post( $measurement_type, $input_name ) {

	$post_name        = $measurement_type === $input_name ? "_measurement_{$measurement_type}_input_attributes" : "_measurement_{$measurement_type}_{$input_name}_input_attributes";
	$input_attributes = isset( $_POST[ $post_name ] ) && is_array( $_POST[ $post_name ] ) ? array_map( 'abs', array_map( 'floatval', $_POST[ $post_name ] ) ) : array();

	return wp_parse_args( array_filter( $input_attributes ), array(
		'min'  => '',
		'max'  => '',
		'step' => '',
	) );
}


/**
 * Helper function to safely get measurement options post values
 *
 * @since 3.12.0
 *
 * @param string $input_name
 * @return array
 */
function wc_measurement_price_calculator_get_options_post( $input_name ) {

	$input_value = sanitize_text_field( isset( $_POST[ $input_name ] ) ? $_POST[ $input_name ] : '' );

	if ( empty( $input_value ) ) {
		 $values = array();

	// try to explode based on a semi-colon if a semi-colon exists in the input
	} elseif ( Framework\SV_WC_Helper::str_exists( $input_value, ';' ) ) {

		$values = array_map( 'trim', explode( ';', $input_value ) );

	} else {
		$values = array_map( 'trim', explode( ',', $input_value ) );
	}

	return $values;
}


/**
 * Helper function to output limited option set.
 *
 * @since 3.12.8
 *
 * @param string[] $options original options array
 * @return string delimited options
 */
function wc_measurement_price_calculator_get_options_value( $options ) {

	$value = null;

	if ( ',' === trim( wc_get_price_decimal_separator() ) ) {
		$value = implode( '; ', $options );
	}

	return $value ? $value : implode( ', ', $options );
}


/**
 * Helper to get the "options" input description.
 *
 * @since 3.12.8
 *
 * @return string description text
 */
function wc_measurement_price_calculator_get_options_tooltip() {

	// use semi-colons if commas are used as the decimal separator
	$delimiter = ',' === trim( wc_get_price_decimal_separator() ) ? 'semicolon' : 'comma';

	/* translators: Placeholder: %s - delimiter to use in the input */
	$description = sprintf( __( 'Use a single number to set a fixed value for this field on the frontend, or a %s-separated list of numbers to create a select box for the customer to choose between.', 'woocommerce-measurement-price-calculator' ), $delimiter );

	if ( 'semicolon' === $delimiter ) {
		$description .= ' ' . __( 'Example: 1/8; 0,5; 2', 'woocommerce-measurement-price-calculator' );
	} else {
		$description .= ' ' . __( 'Example: 1/8, 0.5, 2', 'woocommerce-measurement-price-calculator' );
	}

	return $description;
}
