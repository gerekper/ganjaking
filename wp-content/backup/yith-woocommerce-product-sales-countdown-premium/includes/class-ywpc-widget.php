<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Main class
 *
 * @class   YWPC_Widget
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

if ( ! class_exists( 'YWPC_Widget' ) ) {

	class YWPC_Widget extends WP_Widget {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {
			parent::__construct(
				'ywpc_widget', esc_html__( 'YITH WooCommerce Product Countdown', 'yith-woocommerce-product-countdown' ), array( 'description' => esc_html__( 'Display a list of products with sale timer and/or sale bar', 'yith-woocommerce-product-countdown' ), )
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		}

		/**
		 * Enqueue admin script files
		 *
		 * @since   1.0.0
		 *
		 * @param   $hook
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts( $hook ) {

			wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . ywpc_get_minified() . '.js', array( 'jquery' ) );
			wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . ywpc_get_minified() . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );
			wp_register_script( 'ywpc-widget-panel', YWPC_ASSETS_URL . '/js/ywpc-widget-panel' . ywpc_get_minified() . '.js', array( 'jquery' ), YWPC_VERSION, true );

			if ( $hook == 'widgets.php' ) {

			    wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_script( 'wc-enhanced-select' );
				wp_enqueue_script( 'ywpc-widget-panel' );

			}

		}

		/**
		 * Outputs the content of the widget
		 *
		 * @since   1.0.0
		 *
		 * @param   $args
		 * @param   $instance
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function widget( $args, $instance ) {

			extract( $args );

			if (is_array($instance['product_ids']) && count( $instance['product_ids'] ) > 0 ) {

				echo '<div class="clearfix widget">';

				if ( $instance['title'] ) :

					?>

                    <h3>
						<?php echo $instance['title']; ?>
                    </h3>

				<?php

				endif;

				$ids = ( is_array( $instance['product_ids'] ) ) ? $instance['product_ids'] : explode( ',', $instance['product_ids'] );
				$ids = array_map( 'trim', $ids );

				$options = array(
					'show_title'     => get_option( 'ywpc_widget_title', 'yes' ),
					'show_rating'    => get_option( 'ywpc_widget_rating', 'yes' ),
					'show_price'     => get_option( 'ywpc_widget_price', 'yes' ),
					'show_image'     => get_option( 'ywpc_widget_image', 'yes' ),
					'show_addtocart' => get_option( 'ywpc_widget_addtocart', 'yes' ),
				);

				YITH_WPC()->get_ywpc_custom_loop( $ids, 'widget', $options );

				echo '</div>';

			}

		}

		/**
		 * Outputs the options form on admin
		 *
		 * @since   1.0.0
		 *
		 * @param   $instance
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function form( $instance ) {

			$defaults = array(
				'product_ids' => '',
				'title'       => esc_html__( 'Our Exclusive Sale', 'yith-woocommerce-product-countdown' ),
			);
			@$instance = wp_parse_args( (array) $instance, $defaults );

			wp_reset_postdata();

			$product_ids = array_filter( array_map( 'absint', ( is_array( $instance['product_ids'] ) ) ? $instance['product_ids'] : explode( ',', $instance['product_ids'] ) ) );
			$json_ids    = array();

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
				}
			}

			$select_args = array(
				'class'            => 'wc-product-search ywpc-wc-product-search',
				'name'             => $this->get_field_name( 'product_ids' ),
				'data-placeholder' => esc_html__( 'Search for a product&hellip;', 'yith-woocommerce-product-countdown' ),
				'data-allow_clear' => false,
				'data-selected'    => $json_ids,
				'data-multiple'    => true,
				'data-action'      => 'woocommerce_json_search_products',
				'value'            => implode( ',', array_keys( $json_ids ) ),
				'style'            => 'width: 100%'
			);

			?>
            <p>
                <label for="<?php echo $this->get_field_name( 'title' ); ?>">
					<?php esc_html_e( 'Widget Title', 'yith-woocommerce-product-countdown' ); ?>
                </label>
                <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_name( 'product_ids' ); ?>">
					<?php esc_html_e( 'Products to show', 'yith-woocommerce-product-countdown' ); ?>
                </label>
				<?php yit_add_select2_fields( $select_args ); ?>
            </p>

			<?php

		}

		/**
		 * Processing widget options on save
		 *
		 * @since   1.0.0
		 *
		 * @param   $new_instance
		 * @param   $old_instance
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                = $old_instance;
			$instance['product_ids'] = isset( $new_instance['product_ids'] ) ? esc_sql( $new_instance['product_ids'] ) : '';
			$instance['title']       = sanitize_text_field( $new_instance['title'] );

			return $instance;
		}

	}

}