<?php
/**
 * WooCommerce Checkout Add-Ons
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Checkout_Add_Ons\Admin;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_Factory;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Add_Ons\Add_On_With_Options;
use SkyVerge\WooCommerce\Checkout_Add_Ons\Admin\Meta_Boxes\Add_On_Data;

defined( 'ABSPATH' ) or exit;

/**
 * Add-Ons List Table class
 *
 * @since 2.0.0
 */
class Add_Ons_List_Table extends \WP_List_Table {


	/** @var Add_On[] memoization of all add-ons  */
	protected $add_ons = array();

	/** @var array memoization of add-on types */
	protected $add_on_types = array();

	/** @var array add-on attributes to filter by */
	protected $add_on_attributes = [];

	/** @var string add-on type to filter by */
	protected $add_on_type_filter = null;

	/** @var string add-on attribute to filter by */
	protected $add_on_attribute_filter = null;


	/**
	 * Sets up the table.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->add_ons      = Add_On_Factory::get_add_ons();
		$this->add_on_types = Add_On_Factory::get_add_on_types();

		// add options to filter via attributes
		$this->add_on_attributes = [
			'required'      => esc_html__( 'Required', 'woocommerce-checkout-add-ons' ),
			'not_required'  => esc_html__( 'Not Required', 'woocommerce-checkout-add-ons' )
		];

		parent::__construct( array(
			'plural'   => __( 'Add-Ons', 'woocommerce-checkout-add-ons' ),
			'singular' => __( 'Add-On', 'woocommerce-checkout-add-ons' ),
			'ajax'     => true,
		) );
	}


	/**
	 * Gets the list of add-ons and formats them into an array ready for the table.
	 *
	 * @since 2.0.0
	 *
	 * @return Add_On[]
	 */
	protected function get_add_ons() {

		$add_ons          = $this->add_ons;
		$type_filter      = $this->get_add_on_type_filter();
		$attribute_filter = $this->get_add_on_attribute_filter();

		if ( '' !== $type_filter ) {

			$add_ons = array_filter( $add_ons, function( Add_On $add_on ) use( $type_filter ) {

				return $type_filter === $add_on->get_type();
			} );
		}

		if ( '' !== $attribute_filter ) {

			$add_ons = array_filter( $add_ons, function( Add_On $add_on ) use( $attribute_filter ) {

				if ( 'required' === $attribute_filter ) {
					return $add_on->is_required();
				} elseif ( 'not_required' === $attribute_filter ) {
					return ! $add_on->is_required();
				}

				return true;
			} );
		}

		/**
		 * Filters the add-ons that are displayed in the add-on list.
		 *
		 * @since 2.0.0
		 *
		 * @param Add_On[] the add-ons to be displayed
		 * @param Add_Ons_List_Table instance of this list table class
		 */
		return apply_filters( 'wc_checkout_add_ons_add_on_list', $add_ons, $this );
	}


	/**
	 * Prepares items for display.
	 *
	 * @since 2.0.0
	 */
	public function prepare_items() {

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns()
		);

		$this->items = $this->get_add_ons();

		$this->set_pagination_args( array(
			'total_items' => count( $this->items ),
			'per_page' => 0,
		) );
	}


	/**
	 * Gets the add-on type filter passed in the URL, if there is one.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_add_on_type_filter() {

		if ( null === $this->add_on_type_filter ) {

			$this->add_on_type_filter = isset( $_GET['add_on_type'], $this->add_on_types[ $_GET['add_on_type'] ] ) ? $_GET['add_on_type'] : '';
		}

		return $this->add_on_type_filter;
	}


	/**
	 * Gets the add-on attribute filter passed in the URL, if there is one.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_add_on_attribute_filter() {

		if ( null === $this->add_on_attribute_filter ) {

			$this->add_on_attribute_filter = isset( $_GET['add_on_attribute'], $this->add_on_attributes[ $_GET['add_on_attribute'] ] ) ? $_GET['add_on_attribute'] : '';
		}

		return $this->add_on_attribute_filter;
	}


	/**
	 * Gets the list of columns for this table.
	 *
	 * @see \WP_List_Table::get_columns()
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_columns() {

		return array(
			'cb'               => '<input type="checkbox" />',
			'name'             => __( 'Name', 'woocommerce-checkout-add-ons' ),
			'enabled'          => __( 'Enabled', 'woocommerce-checkout-add-ons' ),
			'type'             => __( 'Type', 'woocommerce-checkout-add-ons' ),
			'price_adjustment' => __( 'Price Adjustment', 'woocommerce-checkout-add-ons' ),
			'display_rules'    => __( 'Display When', 'woocommerce-checkout-add-ons' ),
			'sort_handle'      => '',
		);
	}


	/**
	 * Returns the checkbox column.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="bulk-action[]" value="%s" />', $item->get_id()
		);
	}


	/**
	 * Returns the link to duplicate add-on item.
	 *
	 * @since 2.1.0
	 *
	 * @param Add_On item $id
	 * @return string
	 */
	protected function duplicate_link( $id ) {
		return sprintf( '<a href="%1$s" rel="permalink" title="%2$s">%3$s</a>',
			esc_attr( wc_checkout_add_ons()->get_admin_instance()->get_duplicate_add_on_url( $id ) ),
			__( 'Make a duplicate from this add-on', 'woocommerce-checkout-add-ons' ),
			_x( 'Duplicate', 'Duplicate this Add-On', 'woocommerce-checkout-add-ons' )
		);
	}


	/**
	 * Returns the name column content.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_name( $item ) {

		return sprintf( '<a href="%1$s" class="row-title" aria-label="%2$s">%3$s</a> %4$s <div class="row-actions"><span class="add-on-id">ID: %5$s |</span> <span class="add-on-duplicate-action">%6$s</span></div>',
			wc_checkout_add_ons()->get_admin_instance()->get_edit_add_on_screen_url( $item->get_id() ),
			$item->get_name(),
			$item->get_name(),
			$this->get_attribute( $item->get_attributes() ),
			$item->get_id(),
			$this->duplicate_link( $item->get_id() )
		);
	}


	/**
	 * Returns the 'enabled' column content.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_enabled( $item ) {

		ob_start();
		?>

		<label class="wc-checkout-add-on-switch">
            <input type="checkbox" class="js-wc-checkout-add-on-enabled-switch" <?php checked( $item->get_enabled() ); ?> />
            <span class="checkout-add-on-slider"></span>
		</label>

		<?php
		return ob_get_clean();
	}


	/**
	 * Returns the 'type' column content.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_type( $item ) {

		return $item->get_type_name();
	}


	/**
	 * Returns the price adjustment column content.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_price_adjustment( $item ) {

		if ( $item instanceof Add_On_With_Options ) {

			$lowest  = null;
			$highest = null;

			foreach ( $item->get_options( 'edit' ) as $option ) {

				$lowest  = null === $lowest  || ( isset( $option['adjustment'], $lowest['adjustment'] )  && $option['adjustment'] < $lowest['adjustment']  ) ? $option : $lowest;
				$highest = null === $highest || ( isset( $option['adjustment'], $highest['adjustment'] ) && $option['adjustment'] > $highest['adjustment'] ) ? $option : $highest;
			}

			$adjustment_content = $this->format_adjustment( $lowest['adjustment'], $lowest['adjustment_type'] );

			if ( $lowest && $highest && ( $lowest['adjustment'] !== $highest['adjustment'] || $lowest['adjustment_type'] !== $highest['adjustment_type'] ) ) {

				$adjustment_content .= ' to ' . $this->format_adjustment( $highest['adjustment'], $highest['adjustment_type'] );
			}

		} else {

			$adjustment_content = $this->format_adjustment( $item->get_adjustment(), $item->get_adjustment_type() );
		}

		return $adjustment_content;
	}


	/**
	 * Formats the adjustment value of an add-on or option.
	 *
	 * @since 2.0.0
	 *
	 * @param float $value the adjustment value
	 * @param string $type the adjustment type - `fixed` or `percent`
	 * @return string
	 */
	public function format_adjustment( $value, $type ) {

		$adjustment = '&mdash;';
		$value      = (float) $value;
		$type       = 'percent' === $type ? 'percent' : 'fixed';

		if ( 0.0 !== $value ) {

			$adjustment = sprintf(
			// e.g. +$3.00, -15%, -$5.42, +9.75%
				'%1$s%2$s%3$s%4$s',
				$value < 0.0 ? '-' : '+',
				'fixed' === $type ? get_woocommerce_currency_symbol() : '',
				'fixed' === $type ? number_format( $value, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ) : abs( $value ),
				'percent' === $type ? '%' : ''
			);
		}

		return $adjustment;
	}


	/**
	 * Returns the display rules column content.
	 *
	 * @since 2.1.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_display_rules( $item ) {

		$ruleset = $item->get_ruleset();

		$human_readable_rules = [];

		foreach ( $ruleset as $rule ) {
			$human_readable_rules[] = $rule->get_description();
		}

		$column_value = implode( '<br>', array_filter( $human_readable_rules ) );

		if ( empty( $column_value ) ) {
			$column_value = '&mdash;';
		}

		return $column_value;
	}


	/**
	 * Returns the sort handle column content.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $item
	 * @return string
	 */
	protected function column_sort_handle( $item ) {

		$handle_url = wc_checkout_add_ons()->get_plugin_url() . '/assets/images/draggable-handle.png';

		return sprintf( '<img class="js-checkout-add-on-sort-handle" src="%s" alt="draggable sort handle" />', esc_url( $handle_url ) );
	}


	/**
	 * Gets the bulk actions for this table.
	 *
	 * @see \WP_List_Table::get_bulk_actions()
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {

		$actions = array(
			'bulk-edit'    => _x( 'Edit', 'bulk action', 'woocommerce-checkout-add-ons' ),
			'bulk-enable'  => _x( 'Enable', 'bulk action', 'woocommerce-checkout-add-ons' ),
			'bulk-disable' => _x( 'Disable', 'bulk action', 'woocommerce-checkout-add-ons' ),
			'bulk-delete'  => _x( 'Delete', 'bulk action', 'woocommerce-checkout-add-ons' ),
		);

		// currently the only bulk editing we allow is on tax settings, which
		// are unavailable if taxes are disabled on the shop - so in that case,
		// there are no bulk edits to be made, and we should remove the option
		if ( 'yes' !== get_option( 'woocommerce_calc_taxes' ) ) {
			unset( $actions['bulk-edit'] );
		}

		return $actions;
	}


	/**
	 * Outputs the filter dropdown and button.
	 *
	 * @since 2.0.0
	 *
	 * @param string $which the tablenav this is for -- 'top' or 'bottom'
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' === $which ) {

			$type_filter      = $this->get_add_on_type_filter();
			$attribute_filter = $this->get_add_on_attribute_filter();

			?>
			<div class="alignleft actions">
				<label class="screen-reader-text" for="filter-by-comment-type"><?php _e( 'Filter by add-on type', 'woocommerce-checkout-add-ons' ); ?></label>
				<select id="filter-by-comment-type" name="add_on_type">
					<option value=""><?php _e( 'Show all types', 'woocommerce-checkout-add-ons' ); ?></option>

					<?php foreach ( $this->add_on_types as $add_on_type_slug => $add_on_type_name ) : ?>

						<option value="<?php echo esc_attr( $add_on_type_slug ); ?>" <?php selected( $add_on_type_slug, $type_filter ); ?>>
							<?php echo esc_html( $add_on_type_name ); ?>
						</option>

					<?php endforeach; ?>

				</select>

				<select id="filter-by-attribute-type" name="add_on_attribute">
					<option value=""><?php esc_html_e( 'Show all attributes', 'woocommerce-checkout-add-ons' ); ?></option>

					<?php foreach ( $this->add_on_attributes as $add_on_attribute_slug => $add_on_attribute_name ) : ?>

						<option value="<?php echo esc_attr( $add_on_attribute_slug ); ?>" <?php selected( $add_on_attribute_slug, $attribute_filter ); ?>>
							<?php echo esc_html( $add_on_attribute_name ); ?>
						</option>

					<?php endforeach; ?>

				</select>
				<?php submit_button( _x( 'Filter', 'button text', 'woocommerce-checkout-add-ons' ), '', 'filter_action', false ); ?>
			</div>
			<?php

		}
	}


	/**
	 * Message to be displayed when there are no items
	 *
	 * @see \WP_List_Table::no_items()
	 *
	 * @since 2.0.0
	 */
	public function no_items() {

		if ( empty( $this->add_ons ) ) {

			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			$message = __( 'You haven\'t created any Checkout Add-Ons yet &mdash; %1$sClick Here%2$s to create your first one!', 'woocommerce-checkout-add-ons' );

		} else {

			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
			$message = __( 'No add-ons found &mdash; %1$sClick Here%2$s to create one!', 'woocommerce-checkout-add-ons' );
		}

		printf(
			esc_html( $message ),
			'<a href="' . esc_url( wc_checkout_add_ons()->get_admin_instance()->get_new_add_on_screen_url() ) . '">',
			'</a>'
		);
	}


	/**
	 * Generates content for a single row of the table.
	 *
	 * @since 2.0.0
	 *
	 * @param Add_On $add_on The current add-on
	 */
	public function single_row( $add_on ) {

		?>

		<tr data-id="<?php echo esc_attr( $add_on->get_id() ); ?>">
			<?php $this->single_row_columns( $add_on ); ?>
		</tr>

		<?php
	}


	/**
	 * Outputs the bulk edit fields.
	 *
	 * @since 2.0.0
	 */
	public function bulk_edit_fields() {

		$add_on_data = new Add_On_Data();

		?>

		<table style="display: none !important;" id="bulk-edit-hidden-table">
			<tr id="bulk-edit">
				<td colspan="6">
					<fieldset class="bulk-edit-col-left">
						<legend class="bulk-edit-legend"><?php esc_html_e( 'Bulk Edit', 'woocommerce-checkout-add-ons' ); ?></legend>
						<div class="bulk-edit-col">
							<?php

							$add_on_data->output_field( 'taxable' );
							$add_on_data->output_field( 'tax_class' );

							?>
						</div>
					</fieldset>

					<div class="submit inline-edit-save">
						<button type="button" class="button cancel alignleft">Cancel</button>
						<input type="submit" name="bulk_edit" id="bulk_edit" class="button button-primary alignright" value="Update">
					</div>
				</td>
			</tr>
		</table>

		<?php
	}


	/**
	 * Returns the required attribute of item.
	 *
	 * @since 2.1.3
	 *
	 * @param array $attributes
	 * @return string|false
	 */
	protected function get_attribute( $attributes ) {

		// check whether we have a term matching `required` in the attributes array
		if ( in_array( 'required', $attributes ) ) {

			return sprintf( '<abbr class="required" title="%1$s">*</abbr>',
				esc_attr__( 'Required', 'woocommerce-checkout-add-ons' )
			);
		}

		return false;
	}


}
