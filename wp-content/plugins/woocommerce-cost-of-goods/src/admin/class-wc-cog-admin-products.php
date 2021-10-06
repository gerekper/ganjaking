<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Admin Products Class
 *
 * Handles various modifications to the products list table and edit product screen
 *
 * @since 2.0.0
 */
class WC_COG_Admin_Products {


	/**
	 * Bootstrap class
	 */
	public function __construct() {

		$this->init_hooks();
	}


	/**
	 * Initialize hooks
	 *
	 * @since 2.0.0
	 */
	protected function init_hooks() {

		// add cost field to simple products under the 'General' tab
		add_action( 'woocommerce_product_options_pricing', array( $this, 'add_cost_field_to_simple_product' ) );

		// add cost field to variable products under the 'General' tab
		add_action( 'woocommerce_product_options_sku', array( $this, 'add_cost_field_to_variable_product' ) );

		// save the cost field for simple products
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_simple_product_cost' ), 10, 2 );

		// adds the product variation 'Cost' bulk edit action
		add_action( 'woocommerce_variable_product_bulk_edit_actions', array( $this, 'add_variable_product_bulk_edit_cost_action' ) );

		// save variation cost for bulk edit action
		add_action( 'woocommerce_bulk_edit_variations_default', array( $this, 'variation_bulk_action_variable_cost' ), 10, 4 );

		// add cost field to variable products under the 'Variations' tab after the shipping class select
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_cost_field_to_product_variation' ), 15, 3 );

		// save the cost field for variable products
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_product_cost' ) );

		// save the default cost, cost/min/max costs for variable products
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_variable_product_cost' ), 15 );
		add_action( 'woocommerce_ajax_save_product_variations',  array( $this, 'save_variable_product_cost' ), 15 );

		// add Products list cost bulk edit field
		add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'add_cost_field_bulk_edit' ) );

		// save Products List cost bulk edit field
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'save_cost_field_bulk_edit' ) );

		// add/save Products list quick edit cost field
		add_action( 'woocommerce_product_quick_edit_end',  array( $this, 'render_quick_edit_cost_field' ) );
		add_action( 'manage_product_posts_custom_column',  array( $this, 'add_quick_edit_inline_values' ) );
		add_action( 'woocommerce_product_quick_edit_save', array( $this, 'save_quick_edit_cost_field' ) );

		// add support for Bookings
		// TODO: Yes, the misspelling here for "woocommmerce" is on purpose ಠ_ಠ -- we can remove it around 2017-09-01 {BR 2016-11-14}
		if ( wc_cog()->is_plugin_active( 'woocommmerce-bookings.php' ) || wc_cog()->is_plugin_active( 'woocommerce-bookings.php' ) ) {

			// add cost field to booking products under the 'General' tab
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_cost_field_to_booking_product' ) );

			// save the cost field for booking products
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_booking_product_cost' ), 10, 2 );
		}

		// Product List Table Hooks

		// Adds a "Cost" column header next to "Price"
		add_filter( 'manage_edit-product_columns', array( $this, 'product_list_table_cost_column_header' ), 11 );

		// Renders the product cost in the product list table
		add_action( 'manage_product_posts_custom_column', array( $this, 'product_list_table_cost_column' ), 11 );

		// Make the "Cost" column display as sortable
		add_filter( 'manage_edit-product_sortable_columns', array( $this, 'product_list_table_cost_column_sortable' ), 11 );

		// Make the "Cost" column sortable
		add_filter( 'request', array( $this, 'product_list_table_cost_column_orderby' ), 11 );
	}


	/**
	 * Gets cost of goods field's description.
	 *
	 * @since 2.11.0
	 *
	 * @return string
	 */
	protected function get_cog_field_description() : string {

		return __( 'Enter the amount it costs you to make and sell this product. The amount will be deducted from sales for profit reporting.', 'woocommerce-cost-of-goods' );
	}
	

	/**
	 * Add cost field to simple products under the 'General' tab
	 *
	 * @since 1.0
	 */
	public function add_cost_field_to_simple_product() {

		woocommerce_wp_text_input( [
			'id'          => '_wc_cog_cost',
			'class'       => 'wc_input_price short',
			/* translators: Placeholder: %s - currency symbol */
			'label'       => sprintf( __( 'Cost of Good (%s)', 'woocommerce-cost-of-goods' ), '<span>' . get_woocommerce_currency_symbol() . '</span>' ),
			'data_type'   => 'price',
			'desc_tip'    => true,
			'description' => $this->get_cog_field_description(),
		] );
	}


	/**
	 * Add cost field to variable products under the 'General' tab
	 *
	 * @since 1.1
	 */
	public function add_cost_field_to_variable_product() {

		woocommerce_wp_text_input(
			array(
				'id'                => '_wc_cog_cost_variable',
				'class'             => 'wc_input_price short',
				'wrapper_class'     => 'show_if_variable',
				/* translators: Placeholder: %s - currency symbol */
				'label'             => sprintf( __( 'Cost of Good (%s)', 'woocommerce-cost-of-goods' ), '<span>' . get_woocommerce_currency_symbol()  . '</span>' ),
				'data_type'         => 'price',
				'desc_tip'          => true,
				'description'       => __( 'Default cost for product variations', 'woocommerce-cost-of-goods' ),
			)
		);
	}


	/**
	 * Save cost field for simple product
	 *
	 * @param int $post_id post id
	 * @since 1.0
	 */
	public function save_simple_product_cost( $post_id ) {

		$product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		// need this check because this action is called *after* the variable product action, meaning the variable product cost would be overridden
		if ( 'variable' !== $product_type ) {
			update_post_meta( $post_id, '_wc_cog_cost', stripslashes( wc_format_decimal( $_POST['_wc_cog_cost'] ) ) );
		}

	}


	/**
	 * Renders the 'Cost' bulk edit action on the product admin Variations tab
	 *
	 * @since 1.0
	 */
	public function add_variable_product_bulk_edit_cost_action() {

		$option_label = __( 'Set cost', 'woocommerce-cost-of-goods' );

		echo '<option value="variable_cost_of_good">' . esc_html( $option_label ) . '</option>';
	}


	/**
	 * Set variation cost for variations via bulk edit
	 *
	 * @since 1.8.0
	 * @param string $bulk_action
	 * @param array $data
	 * @param int $product_id
	 * @param array $variations
	 */
	public function variation_bulk_action_variable_cost( $bulk_action, $data, $product_id, $variations ) {

		if ( empty( $data['value'] ) ) {
			return;
		}

		if ( 'variable_cost_of_good' !== $bulk_action ) {
			return;
		}

		foreach ( $variations as $variation_id ) {
			$this->update_variation_product_cost( $variation_id, wc_clean( $data['value'] ) );
		}
	}


	/**
	 * Add cost field to variable products under the 'Variations' tab after the shipping class dropdown
	 *
	 * @internal
	 *
	 * @since 1.0
	 *
	 * @param int $loop loop counter
	 * @param array $variation_data array of variation data
	 * @param \WP_Post $variation product variation post
	 */
	public function add_cost_field_to_product_variation( $loop, $variation_data, $variation ) {

		$cost_data = $this->get_cost_data( $variation );

		?>
		<div class="_wc_cog_variation_cost">
			<p class="form-row form-row-first">
				<label><?php
					/* translators: Placeholder: %s - currency symbol */
					printf( __( 'Cost of Good (%s)', 'woocommerce-cost-of-goods' ), '<span>' . get_woocommerce_currency_symbol() . '</span>' );
				?></label>

				<?php echo wc_help_tip( $this->get_cog_field_description() ); ?>

				<input
					type="text"
					size="6"
					name="variable_cost_of_good[<?php echo esc_attr( $loop ); ?>]"
					value="<?php echo esc_attr( $cost_data['cost'] ); ?>"
					class="wc_input_price"
					placeholder="<?php echo esc_attr( $cost_data['default_cost'] ); ?>"
				/>
			</p>
		</div>
		<?php
	}


	/**
	 * Helper method to update cost meta for a variation
	 *
	 * @since 1.8.0
	 * @param $variation_id int The variation ID
	 * @param $cost string The cost
	 */
	public function update_variation_product_cost( $variation_id, $cost ) {

		$parent_id    = null;
		$default_cost = null;

		if ( '' !== $cost ) {
			// setting a non-default cost for this variation
			update_post_meta( $variation_id, '_wc_cog_cost',         wc_format_decimal( $cost ) );
			update_post_meta( $variation_id, '_wc_cog_default_cost', 'no' );
		} else {
			// get the default cost, if any
			if ( is_null( $default_cost ) ) {
				$parent_id    = wp_get_post_parent_id( $variation_id );
				$default_cost = get_post_meta( $parent_id, '_wc_cog_cost_variable', true );
			}

			// and set it if available
			if ( $default_cost ) {
				update_post_meta( $variation_id, '_wc_cog_cost',         wc_format_decimal( $default_cost ) );
				update_post_meta( $variation_id, '_wc_cog_default_cost', 'yes' );
			} else {
				update_post_meta( $variation_id, '_wc_cog_cost',         '' );
				update_post_meta( $variation_id, '_wc_cog_default_cost', 'no' );
			}
		}
	}


	/**
	 * Save cost field for the variation product
	 *
	 * @since 1.0
	 * @param $variation_id
	 */
	public function save_variation_product_cost( $variation_id ) {

		// find the index for the given variation ID and save the associated cost
		if ( false !== ( $i = array_search( $variation_id, $_POST['variable_post_id'] ) ) ) {

			$cost = $_POST['variable_cost_of_good'][ $i ];

			$this->update_variation_product_cost( $variation_id, $cost );
		}
	}


	/**
	 * Save the overall cost/min/max costs for variable products
	 *
	 * @since 1.1
	 * @param int $post_id
	 */
	public function save_variable_product_cost( $post_id ) {

		// default cost
		if ( isset( $_POST['_wc_cog_cost_variable'] ) ) {
			$cost = stripslashes( $_POST['_wc_cog_cost_variable'] );
		} else {
			$cost = get_post_meta( $post_id, '_wc_cog_cost_variable', true );
		}

		$this->update_variable_product_cost( $post_id, $cost );
	}


	/**
	 * Update the cost meta for a variable product and set its children's costs if needed.
	 *
	 * @since 2.1.1
	 * @param \WC_Product|int $product a variable product or its ID
	 * @param string $cost the new cost
	 */
	protected function update_variable_product_cost( $product, $cost ) {

		$product = wc_get_product( $product );

		if ( ! $product ) {
			return;
		}

		$product->update_meta_data( '_wc_cog_cost_variable', wc_format_decimal( $cost ) );

		foreach ( $product->get_children() as $child_id ) {

			if ( $child_product = wc_get_product( $child_id ) ) {

				$child_cost = $child_product->get_meta( '_wc_cog_cost', true, 'edit' );
				$is_default = 'yes' === $child_product->get_meta( '_wc_cog_default_cost', true, 'edit' );

				if ( '' === $child_cost || $is_default ) {

					$child_product->update_meta_data( '_wc_cog_cost', wc_format_decimal( $cost ) );
					$child_product->update_meta_data( '_wc_cog_default_cost', '' !== $cost ? 'yes' : 'no' );
					$child_product->save_meta_data();
				}
			}
		}

		// get the minimum and maximum costs associated with the product
		list( $min_variation_cost, $max_variation_cost ) = \WC_COG_Product::get_variable_product_min_max_costs( $product->get_id() );

		$product->update_meta_data( '_wc_cog_cost',               wc_format_decimal( $min_variation_cost ) );
		$product->update_meta_data( '_wc_cog_min_variation_cost', wc_format_decimal( $min_variation_cost ) );
		$product->update_meta_data( '_wc_cog_max_variation_cost', wc_format_decimal( $max_variation_cost ) );

		$product->save_meta_data();
	}


	/**
	 * Add a cost bulk edit field, this is displayed on the Products list page
	 * when one or more products is selected, and the Edit Bulk Action is applied
	 *
	 * @since 1.0
	 */
	public function add_cost_field_bulk_edit() {
		?>
		<div class="inline-edit-group">
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Cost of Good', 'woocommerce-cost-of-goods' ); ?></span>
					<span class="input-text-wrap">
						<select class="change_cost_of_good change_to" name="change_cost_of_good">
							<?php
							$options = array(
								''  => __( '— No Change —', 'woocommerce-cost-of-goods' ),
								'1' => __( 'Change to:', 'woocommerce-cost-of-goods' ),
								'2' => __( 'Increase by (fixed amount or %):', 'woocommerce-cost-of-goods' ),
								'3' => __( 'Decrease by (fixed amount or %):', 'woocommerce-cost-of-goods' )
							);
							foreach ( $options as $key => $value ) {
								echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
							}
							?>
						</select>
					</span>
			</label>
			<label class="change-input">
				<input type="text" name="_cost_of_good" class="text cost_of_good" placeholder="<?php esc_attr_e( 'Enter Cost:', 'woocommerce-cost-of-goods' ); ?>" value="" />
			</label>
		</div>
		<?php
	}


	/**
	 * Save the cost bulk edit field
	 *
	 * @since 1.0
	 * @param \WC_Product $product A product object.
	 */
	public function save_cost_field_bulk_edit( $product ) {

		if ( ! empty( $_REQUEST['change_cost_of_good'] ) ) {

			$option_selected       = absint( $_REQUEST['change_cost_of_good'] );
			$requested_cost_change = stripslashes( $_REQUEST['_cost_of_good'] );
			$current_cost_value    = \WC_COG_Product::get_cost( $product->get_id() );

			switch ( $option_selected ) {

				// change cost to fixed amount
				case 1 :
					$new_cost = $requested_cost_change;
				break;

				// increase cost by fixed amount/percentage
				case 2 :

					if ( false !== strpos( $requested_cost_change, '%' ) ) {
						$percent = str_replace( '%', '', $requested_cost_change ) / 100;
						$new_cost = $current_cost_value + ( $current_cost_value * $percent );
					} else {
						$new_cost = $current_cost_value + $requested_cost_change;
					}

				break;

				// decrease cost by fixed amount/percentage
				case 3 :

					if ( false !== strpos( $requested_cost_change, '%' ) ) {
						$percent = str_replace( '%', '', $requested_cost_change ) / 100;
						$new_cost = $current_cost_value - ( $current_cost_value * $percent );
					} else {
						$new_cost = $current_cost_value - $requested_cost_change;
					}

				break;

			}

			// update to new cost if different than current cost
			if ( isset( $new_cost ) && $new_cost !== $current_cost_value ) {

				if ( $product->is_type( 'variable' ) ) {
					$this->update_variable_product_cost( $product, $new_cost );
				} else {
					$product->update_meta_data( '_wc_cog_cost', wc_format_decimal( $new_cost ) );
					$product->save_meta_data();
				}
			}
		}
	}


	/**
	 * Gets the cost and default cost for a variation.
	 *
	 * @since 2.9.9
	 *
	 * @param \WP_Post $variation product variation post
	 * @return string[] formatted cost and default_cost
	 */
	private function get_cost_data( $variation ) {

		$default_cost = '';
		$cost         = '';

		if ( $variation instanceof \WP_Post ) {

			$default_cost = $this->get_formatted_default_cost( $variation );
			$cost         = $this->get_formatted_cost( $variation );

			// if the variation cost is actually the default variable product cost
			if ( ! empty( $cost ) && 'yes' === get_post_meta( $variation->ID, '_wc_cog_default_cost', true ) ) {
				$cost = '';
			}
		}

		return [
			'cost'         => $cost,
			'default_cost' => $default_cost,
		];
	}


	/**
	 * Gets the formatted cost using the store decimal separator.
	 *
	 * @since 2.9.9
	 *
	 * @param \WP_Post $variation product variation post
	 * @return string the variation cost using the store decimal separator
	 */
	private function get_formatted_cost( \WP_Post $variation ) {

		$cost = get_post_meta( $variation->ID, '_wc_cog_cost', true );

		// formats the cost with the store decimal settings
		return $this->cost_format( $cost );
	}


	/**
	 * Gets the formatted default cost using the store decimal separator.
	 *
	 * @since 2.9.9
	 *
	 * @param \WP_Post $variation product variation post
	 * @return string the variation default cost using the store decimal separator
	 */
	private function get_formatted_default_cost( \WP_Post $variation ) {

		$default_cost = isset( $variation->post_parent ) ? get_post_meta( $variation->post_parent, '_wc_cog_cost_variable', true ) : '';

		// formats the default cost with the store decimal settings
		return $this->cost_format( $default_cost );
	}


	/**
	 * Gets the formatted cost using the store decimal separator.
	 *
	 * This method comes to fix an issue where the cost format using the store price decimal separator wasn't being applied properly.
	 *
	 * @since 2.9.9
	 *
	 * @param string|float $number
	 * @return string a proper formatted version of number
	 */
	private function cost_format( $number ) {

		return ! empty( $number ) ? number_format( $number, wc_get_price_decimals(), wc_get_price_decimal_separator(), '' ) : '';
	}


	/** Quick Edit support ****************************************************/


	/**
	 * Render the quick edit cost field. Note that the field value is intentionally
	 * empty.
	 *
	 * @since 2.1.0
	 */
	public function render_quick_edit_cost_field() {
		?>
			<br class="clear" />
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Cost', 'woocommerce-cost-of-goods' ); ?></span>
				<span class="input-text-wrap">
					<input type="text" name="_wc_cog_cost" class="text wc-cog-cost" value="">
				</span>
			</label>
		<?php
	}


	/**
	 * Add markup for the custom product meta values so Quick Edit can fill the inputs.
	 *
	 * @since 2.1.1
	 * @param string $column the current column slug
	 */
	public function add_quick_edit_inline_values( $column ) {
		/* @type \WC_Product $the_product */
		global $the_product;

		if ( $the_product instanceof \WC_Product && 'name' === $column ) {

			$meta_key   = $the_product->is_type( 'variable' ) ? '_wc_cog_cost_variable' : '_wc_cog_cost';
			$meta_value = $the_product->get_meta( $meta_key, true, 'edit' );

			echo '<div id="wc_cog_inline_' . esc_attr( $the_product->get_id() ) . '" class="hidden">';
				echo '<div class="cost">' . esc_html( $meta_value ) . '</div>';
			echo '</div>';
		}
	}


	/**
	 * Save the quick edit cost field, this occurs over Ajax
	 *
	 * @since 2.1.0
	 * @param \WC_Product $product
	 */
	public function save_quick_edit_cost_field( $product ) {

		$cost = isset( $_REQUEST['_wc_cog_cost'] ) ? $_REQUEST['_wc_cog_cost'] : '';

		if ( $product->is_type( 'variable' ) ) {
			$this->update_variable_product_cost( $product, $cost );
		} else {
			$product->update_meta_data( '_wc_cog_cost', wc_format_decimal( $cost ) );
			$product->save_meta_data();
		}
	}


	/** Bookings support ******************************************************/


	/**
	 * Add cost field to booking products under the general tab
	 *
	 * @since 1.7.0
	 */
	public function add_cost_field_to_booking_product() {
		global $thepostid;

		$cost = get_post_meta( $thepostid, '_wc_cog_cost', true );

		woocommerce_wp_text_input(
			array(
				'id'            => '_wc_cog_cost_booking',
				'name'          => '_wc_cog_cost_booking',
				'value'         => $cost,
				'class'         => 'wc_input_price short',
				'wrapper_class' => 'show_if_booking',
				/* translators: Placeholder: %s - currency symbol */
				'label'         => sprintf( __( 'Cost of Good (%s)', 'woocommerce-cost-of-goods' ), '<span>' . get_woocommerce_currency_symbol() . '</span>' ),
				'data_type'     => 'price',
			)
		);
	}


	/**
	 * Save cost field for bookable product
	 *
	 * @param int $post_id The post id
	 * @since 1.7.0
	 */
	public function save_booking_product_cost( $post_id ) {

		$product_type = empty( $_POST['product-type'] ) ? 'booking' : sanitize_title( stripslashes( $_POST['product-type'] ) );

		if ( 'booking' === $product_type ) {
			update_post_meta( $post_id, '_wc_cog_cost', stripslashes( wc_format_decimal( $_POST['_wc_cog_cost_booking'] ) ) );
		}
	}


	/** Product List table methods ********************************************/


	/**
	 * Adds a "Cost" column header after the core "Price" one, on the Products
	 * list table
	 *
	 * @since 1.1
	 * @param array $existing_columns associative array of column key to name
	 * @return array associative array of column key to name
	 */
	public function product_list_table_cost_column_header( $existing_columns ) {

		$columns = array();

		foreach ( $existing_columns as $key => $value ) {

			$columns[ $key ] = $value;

			// add our cost column after price
			if ( 'price' === $key ) {
				$columns['cost'] = __( 'Cost', 'woocommerce-cost-of-goods' );
			}
		}

		return $columns;
	}


	/**
	 * Renders the product cost value in the products list table
	 *
	 * @since 1.1
	 * @param string $column column id
	 */
	public function product_list_table_cost_column( $column ) {
		/* @type \WC_Product $the_product */
		global $post, $the_product;

		if ( ! $the_product instanceof \WC_Product || $the_product->get_id() !== $post->ID ) {
			$the_product = wc_get_product( $post );
		}

		if ( 'cost' === $column ) {

			if ( \WC_COG_Product::get_cost_html( $the_product ) ) {
				echo \WC_COG_Product::get_cost_html( $the_product );
			} else {
				echo '<span class="na">&ndash;</span>';
			}
		}
	}


	/**
	 * Add the "Cost" column to the list of sortable columns
	 *
	 * @since 1.1
	 * @param array $columns associative array of sortable columns, id to id
	 * @return array sortable columns
	 */
	public function product_list_table_cost_column_sortable( $columns ) {

		$columns['cost'] = 'cost';

		return $columns;
	}


	/**
	 * Add the "Cost" column to the orderby clause if sorting by cost
	 *
	 * @since 1.1
	 * @param array $vars query vars
	 * @return array query vars
	 */
	public function product_list_table_cost_column_orderby( $vars ) {

		if ( isset( $vars['orderby'] ) && 'cost' === $vars['orderby'] ) {

			$vars = array_merge( $vars, array(
				'meta_key' => '_wc_cog_cost',
				'orderby'  => 'meta_value_num',
			) );
		}

		return $vars;
	}


}
