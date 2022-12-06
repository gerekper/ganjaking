<?php

namespace ACA\WC;

use AC;
use AC\Registerable;
use ACA\WC\Asset\Script\Table;
use ACA\WC\ListScreen;
use ACA\WC\Settings\HideOnScreen\FilterOrderCustomer;
use ACA\WC\Settings\HideOnScreen\FilterProductCategory;
use ACA\WC\Settings\HideOnScreen\FilterProductStockStatus;
use ACA\WC\Settings\HideOnScreen\FilterProductType;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionCustomer;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionPayment;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionProduct;
use ACA\WC\TableScreen\HideProductFilter;
use ACA\WC\TableScreen\HideSubscriptionsFilter;
use WC_Admin_List_Table_Orders;
use WP_Post;

final class TableScreen implements Registerable {

	/**
	 * @var AC\ListScreen
	 */
	private $current_list_screen;

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @var bool
	 */
	private $use_product_variations;

	public function __construct( AC\Asset\Location\Absolute $location, $use_product_variations ) {
		$this->location = $location;
		$this->use_product_variations = (bool) $use_product_variations;
	}

	public function register() {
		add_action( 'ac/table/list_screen', [ $this, 'set_current_list_screen' ] );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ] );
		add_action( 'ac/table_scripts/editing', [ $this, 'table_scripts_editing' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'product_scripts' ], 100 );
		add_action( 'admin_head', [ $this, 'display_width_styles' ], 10, 1 );
		add_filter( 'ac/editing/role_group', [ $this, 'set_editing_role_group' ], 10, 2 );

		// Add quick action to product overview
		if ( $this->use_product_variations ) {
			add_filter( 'post_row_actions', [ $this, 'add_quick_action_variation' ], 10, 2 );
			add_action( 'manage_product_posts_custom_column', [ $this, 'add_quick_link_variation' ], 11, 2 );
		}

		add_action( 'ac/table_scripts', [ $this, 'hide_filters' ] );
	}

	public function hide_filters( AC\ListScreen $list_screen ) {
		global $wc_list_table;

		if ( $wc_list_table instanceof WC_Admin_List_Table_Orders && ( new FilterOrderCustomer() )->is_hidden( $list_screen ) ) {
			remove_action( 'restrict_manage_posts', [ $wc_list_table, 'restrict_manage_posts' ] );
		}

		$services = [
			new HideProductFilter( $list_screen, new FilterProductType(), 'product_type' ),
			new HideProductFilter( $list_screen, new FilterProductCategory(), 'product_category' ),
			new HideProductFilter( $list_screen, new FilterProductStockStatus(), 'stock_status' ),
		];

		if ( $list_screen instanceof ListScreen\Subscriptions ) {
			$services[] = new HideSubscriptionsFilter( $list_screen, new FilterSubscriptionProduct() );
			$services[] = new HideSubscriptionsFilter( $list_screen, new FilterSubscriptionPayment() );
			$services[] = new HideSubscriptionsFilter( $list_screen, new FilterSubscriptionCustomer() );
		}

		foreach ( $services as $service ) {
			$service->register();
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function set_current_list_screen( $list_screen ) {
		if ( ! $list_screen ) {
			return;
		}

		$this->current_list_screen = $list_screen;
	}

	public function get_current_list_screen() {
		return $this->current_list_screen;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_wc_list_screen( $list_screen ) {
		return $list_screen instanceof ListScreen\ShopOrder ||
		       $list_screen instanceof ListScreen\ShopCoupon ||
		       $list_screen instanceof ListScreen\Product ||
		       $list_screen instanceof ListScreen\ProductVariation ||
		       $list_screen instanceof ListScreen\Subscriptions ||
		       $list_screen instanceof AC\ListScreen\User;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function table_scripts_editing( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		wp_localize_script( 'acp-editing-table', 'acp_woocommerce_vars', [
			'stock_status_options' => wc_get_product_stock_status_options(),
		] );

		// Translations
		wp_localize_script( 'acp-editing-table', 'acp_woocommerce_i18n', [
			'woocommerce' => [
				'add_note'                  => __( 'Add note', 'woocommerce' ),
				'added_by'                  => __( 'by', 'woocommerce' ),
				'custom_note_alert'         => __( 'This note will be send to the customer by email', 'codepress-admin-columns' ),
				'backorder'                 => __( 'On backorder', 'woocommerce' ),
				'clear_sale_price'          => __( 'Clear Sale Price', 'codepress-admin-columns' ),
				'decimals'                  => __( 'Decimals', 'codepress-admin-columns' ),
				'decrease_by'               => __( 'Decrease by', 'codepress-admin-columns' ),
				'downloadable'              => __( 'Downloadable', 'woocommerce' ),
				'height'                    => __( 'Height', 'woocommerce' ),
				'in_stock'                  => __( 'In stock', 'woocommerce' ),
				'increase_by'               => __( 'Increase by', 'codepress-admin-columns' ),
				'length'                    => __( 'Length', 'woocommerce' ),
				'manage_stock'              => __( 'Manage stock', 'woocommerce' ),
				'no_notes'                  => __( 'No notes', 'woocommerce' ),
				'out_of_stock'              => __( 'Out of stock', 'woocommerce' ),
				'price'                     => __( 'Price', 'woocommerce' ),
				'regular'                   => __( 'Regular', 'codepress-admin-columns' ),
				'regular_price'             => __( 'Regular Price', 'codepress-admin-columns' ),
				'replace'                   => __( 'Replace', 'codepress-admin-columns' ),
				'sale'                      => __( 'Sale', 'woocommerce' ),
				'sale_price'                => __( 'Sale price', 'woocommerce' ),
				'sale_from'                 => __( 'Sale from', 'codepress-admin-columns' ),
				'sale_to'                   => __( 'Sale To', 'codepress-admin-columns' ),
				'set_new'                   => __( 'Set New', 'codepress-admin-columns' ),
				'set_sale_based_on_regular' => __( 'Set the sale price based on the regular price', 'codepress-admin-columns' ),
				'schedule'                  => __( 'Schedule', 'woocommerce' ),
				'schedule_from'             => _x( 'From', 'schedule', 'codepress-admin-columns' ),
				'schedule_to'               => _x( 'To', 'schedule', 'codepress-admin-columns' ),
				'scheduled'                 => __( 'Scheduled', 'codepress-admin-columns' ),
				'stock_status'              => __( 'Stock status', 'woocommerce' ),
				'stock_qty'                 => __( 'Stock Qty', 'woocommerce' ),
				'usage_limit_per_coupon'    => __( 'Usage limit per coupon', 'woocommerce' ),
				'usage_limit_per_user'      => __( 'Usage limit per user', 'woocommerce' ),
				'usage_limit_products'      => __( 'Usage limit products', 'woocommerce' ),
				'width'                     => __( 'Width', 'woocommerce' ),
				'rounding_none'             => __( 'No Rounding', 'codepress-admin-columns' ),
				'rounding_up'               => __( 'Round up', 'codepress-admin-columns' ),
				'rounding_down'             => __( 'Round down', 'codepress-admin-columns' ),
				'rounding_example'          => __( 'Example:', 'codepress-admin-columns' ),
				'virtual'                   => __( 'Virtual', 'woocommerce' ),
				'delete_note'               => __( 'Delete note', 'codepress-admin-columns' ),
				'delete_note_confirm'       => __( 'Are you sure you want to delete this note?', 'codepress-admin-columns' ),
			],
		] );

		if ( $list_screen instanceof ListScreen\ProductVariation ) {

			wp_localize_script( 'acp-editing-table', 'woocommerce_admin_meta_boxes', [
				'calendar_image'         => WC()->plugin_url() . '/assets/images/calendar.png',
				'currency_format_symbol' => get_woocommerce_currency_symbol(),
			] );
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @since 1.3
	 */
	public function table_scripts( $list_screen ) {
		if ( ! $this->is_wc_list_screen( $list_screen ) ) {
			return;
		}

		$style = new AC\Asset\Style( 'aca-wc-column', $this->location->with_suffix( 'assets/css/table.css' ) );
		$style->enqueue();

		$script = new Table( 'aca-wc-table', $this->location );
		$script->enqueue();
	}

	/**
	 * Single product scripts
	 *
	 * @param string $hook
	 */
	public function product_scripts( $hook ) {
		global $post;

		if ( in_array( $hook, [ 'post-new.php', 'post.php' ] ) && $post && 'product' === $post->post_type ) {
			$script = new AC\Asset\Script(
				'aca-wc-product',
				$this->location->with_suffix( 'assets/js/product.js' ),
				[ 'jquery' ]
			);
			$script->enqueue();
		}
	}

	/**
	 * @param string $group
	 * @param string $role
	 *
	 * @return string
	 */
	public function set_editing_role_group( $group, $role ) {
		if ( in_array( $role, [ 'customer', 'shop_manager' ] ) ) {
			$group = __( 'WooCommerce', 'codepress-admin-columns' );
		}

		return $group;
	}

	/**
	 * @param int $product_id
	 *
	 * @return string
	 */
	private function get_list_table_link( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || 'variable' !== $product->get_type() ) {
			return false;
		}

		return add_query_arg( [ 'post_type' => 'product_variation', 'post_parent' => $product_id ], admin_url( 'edit.php' ) );
	}

	/**
	 * Add a quick action on the product overview which links to the product variations page.
	 *
	 * @param array   $actions
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function add_quick_action_variation( $actions, $post ) {
		if ( 'product' !== $post->post_type ) {
			return $actions;
		}

		$link = $this->get_list_table_link( $post->ID );

		if ( $link ) {
			$actions['variation'] = ac_helper()->html->link( $link, __( 'View Variations', 'codepress-admin-columns' ) );
		}

		return $actions;
	}

	/**
	 * Display an icon on the product name column which links to the product variations page.
	 *
	 * @param string $column
	 * @param int    $post_id
	 *
	 * @see \WP_Posts_List_Table::column_default
	 */
	public function add_quick_link_variation( $column, $post_id ) {
		if ( 'name' !== $column ) {
			return;
		}

		$link = $this->get_list_table_link( $post_id );

		if ( ! $link ) {
			return;
		}

		$label = ac_helper()->html->tooltip( '<span class="ac-wc-view"></span>', __( 'View Variations', 'codepress-admin-columns' ) );

		echo ac_helper()->html->link( $link, $label, [ 'class' => 'view-variations' ] );
	}

	/**
	 * Applies the width setting to the table headers
	 */
	public function display_width_styles() {
		if ( ! $this->get_current_list_screen() instanceof ListScreen\ShopOrder ) {
			return;
		}

		$css_column_width = '';

		foreach ( $this->current_list_screen->get_columns() as $column ) {
			$setting = $column->get_setting( 'width' );

			if ( ! $setting instanceof AC\Settings\Column\Width ) {
				continue;
			}

			$width = $setting->get_column_width();

			if ( $width ) {
				$css_width = $width->get_value() . $width->get_unit();
			} else {
				$css_width = 'auto';
			}

			$css_column_width .= sprintf(
				'.ac-%s .wrap table th.column-%s { width: %s !important; }',
				esc_attr( $this->current_list_screen->get_key() ),
				esc_attr( $column->get_name() ),
				$css_width
			);
			$css_column_width .= sprintf(
				'body.acp-overflow-table.ac-%s .wrap th.column-%s { min-width: %s !important; }',
				esc_attr( $this->current_list_screen->get_key() ),
				esc_attr( $column->get_name() ),
				$css_width
			);
		}

		if ( ! $css_column_width ) {
			return;
		}

		?>
		<style>
			@media screen and (min-width: 783px) {
			<?= $css_column_width; ?>
			}
		</style>

		<?php
	}

}