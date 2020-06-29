<?php
/**
 * WC_Widget_Composite class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since   3.0.0
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite Product Config Summary Widget.
 *
 * Displays configuration summary of the currently displayed composite product.
 * By default applicable to Multi-page Composites only.
 *
 * @version  4.1.2
 * @extends  WC_Widget
 */
class WC_Widget_Composite extends WC_Widget {

	const BASE_ID = 'woocommerce_widget_composite_summary';

	/**
	 * Constructor
	 */
	public function __construct() {

		$display_options = self::get_display_options();

		$this->widget_cssclass    = 'woocommerce widget_composite_summary cp-no-js summary_widget_inactive summary_widget_hidden';
		$this->widget_description = __( 'Dynamic configuration summary for Composite Products.', 'woocommerce-composite-products' );
		$this->widget_id          = self::BASE_ID;
		$this->widget_name        = __( 'Composite Products Summary', 'woocommerce-composite-products' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Your Selections', 'woocommerce-composite-products' ),
				'label' => __( 'Title', 'woocommerce' )
			),
			'display' => array(
				'type'    => 'select',
				'std'     => 'default',
				'label'   => __( 'Display', 'woocommerce' ),
				'options' => array_combine( array_keys( $display_options ), wp_list_pluck( $display_options, 'title' ) )
			)
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param  array  $args
	 * @param  array  $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		global $product;

		// Normally, this should never happen since 'sidebars_widgets' are filtered by 'wc_cp_remove_composite_summary_widget' to remove non-visible composite summary widgets.
		if ( ! self::is_visible() ) {
			return;
		}

		$components = $product->get_components();

		if ( empty( $components ) ) {
			return;
		}

		$product_id      = $product->get_id();
		$display_options = self::get_display_options();
		$display         = isset( $instance[ 'display' ] ) && in_array( $instance[ 'display' ], array_keys( $display_options ) ) ? $instance[ 'display' ] : 'default';

		/**
		 * Filter the display mode.
		 *
		 * @param  string                $display
		 * @param  WC_Product_Composite  $product
		 */
		$display = apply_filters( 'woocommerce_composite_component_summary_widget_display', $display, $product );
		$options = array(
			'columns' => 1,
			'display' => $display
		);

		echo str_replace( 'widget_composite_summary ', 'widget_composite_summary widget_position_' . $display . ' ', $args[ 'before_widget' ] );

		$default = isset( $this->settings[ 'title' ][ 'std' ] ) ? $this->settings[ 'title' ][ 'std' ] : '';

		if ( ! empty( $instance[ 'title' ] ) ) {
			/** Documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $instance[ 'title' ], $instance, $this->id_base );
			echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}

		$classes   = array();
		$classes[] = 'widget_composite_summary_content_' . $product_id;

		if ( 'fixed' === $display ) {

			$columns = count( $components );

			/**
			 * Filter the max number columns displayed in the summary.
			 *
			 * @param  int                   $max_columns
			 * @param  WC_Product_Composite  $product
			 */
			$max_columns = apply_filters( 'woocommerce_composite_component_summary_widget_max_columns', 3, $product );
			$columns     = min( $max_columns, $columns, 8 );

			if ( $columns > 1 ) {
				$classes[]            = 'columns-' . $columns;
				$options[ 'columns' ] = $columns;
			}
		}

		ob_start();

		?><div class="widget_composite_summary_content <?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-container_id="<?php echo $product_id; ?>"><?php

			/**
			 * 'woocommerce_composite_summary_widget_content' hook:
			 * @since  3.6.0
			 *
			 * @hooked wc_cp_summary_widget_content       - 10
			 * @hooked wc_cp_summary_widget_price         - 20
			 * @hooked wc_cp_summary_widget_message       - 30
			 * @hooked wc_cp_summary_widget_availability  - 40
			 * @hooked wc_cp_summary_widget_button        - 50
			 */
			do_action( 'woocommerce_composite_summary_widget_content', $components, $product, $options );

		?></div><?php

		echo ob_get_clean();

		echo $args[ 'after_widget' ];
	}

	/**
	 * True if the widget can be viewed.
	 *
	 * @return boolean
	 */
	public static function is_visible() {

		global $post, $product;

		$show_widget = false;

		if ( function_exists( 'is_product' ) && is_product() ) {

			if ( false === ( $product instanceof WC_Product ) ) {
				$product = wc_get_product( $post->ID );
			}

			if ( 'composite' === $product->get_type() ) {
				$layout_style           = $product->get_composite_layout_style();
				$layout_style_variation = $product->get_composite_layout_style_variation();
				$show_widget            = apply_filters( 'woocommerce_composite_summary_widget_display', true, $layout_style, $layout_style_variation, $product );
			}
		}

		return $show_widget;
	}

	/**
	 * True if the widget is visible.
	 *
	 * @return boolean
	 */
	public static function is_active() {

		return is_active_widget( false, false, self::BASE_ID, true );
	}

	/**
	 * Display options.
	 *
	 * @return array
	 */
	public static function get_display_options() {
		return array(
			'default' => array(
				'title' => __( 'Default', 'woocommerce' )
			),
			'fixed'   => array(
				'title' => __( 'Fixed', 'woocommerce' )
			)
		);
	}
}
