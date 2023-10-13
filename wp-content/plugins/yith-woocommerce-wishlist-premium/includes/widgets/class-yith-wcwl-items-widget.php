<?php
/**
 * Widget that shows all items in user wishlists
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Widgets
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Items_Widget' ) ) {
	/**
	 * Wishlist Items Widget
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Items_Widget extends WP_Widget {

		/**
		 * Sets up the widgets
		 */
		public function __construct() {
			parent::__construct(
				'yith-wcwl-items',
				__( 'YITH Wishlist Items', 'yith-woocommerce-wishlist' ),
				array( 'description' => __( 'A list of products in the user\'s wishlists', 'yith-woocommerce-wishlist' ) )
			);
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args     General arguments for widgets.
		 * @param array $instance Instance of current widget.
		 */
		public function widget( $args, $instance ) {
			$items    = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
				array(
					'orderby'     => 'dateadded',
					'order'       => 'ASC',
					'is_default'  => isset( $instance['show_default_only'] ) && 'yes' === $instance['show_default_only'],
					'wishlist_id' => 'all',
				)
			);
			$products = array();

			$instance['item']                 = __CLASS__;
			$instance['unique_id']            = isset( $instance['unique_id'] ) ? $instance['unique_id'] : uniqid();
			$instance['style']                = isset( $instance['style'] ) ? $instance['style'] : 'extended';
			$instance['current_url']          = isset( $instance['current_url'] ) ? $instance['current_url'] : add_query_arg( array() );
			$instance['ajax_loading']         = isset( $instance['ajax_loading'] ) ? $instance['ajax_loading'] : 'yes' === get_option( 'yith_wcwl_ajax_enable', 'no' );
			$instance['show_default_only']    = isset( $instance['show_default_only'] ) ? $instance['show_default_only'] : 'no';
			$instance['show_view_link']       = isset( $instance['show_view_link'] ) ? $instance['show_view_link'] : 'no';
			$instance['show_add_all_to_cart'] = isset( $instance['show_add_all_to_cart'] ) ? $instance['show_add_all_to_cart'] : 'no';

			$fragments_options = YITH_WCWL_Frontend()->format_fragment_options( $instance, 'YITH_WCWL_Items_Widget' );

			if ( ! empty( $items ) ) {
				foreach ( $items as $item ) {
					$product_id = $item->get_product_id();

					if ( ! isset( $products[ $product_id ] ) ) {
						$products[ $product_id ] = array(
							'product'  => $item->get_product(),
							'items'    => array(),
							'in_list'  => array(),
							'quantity' => 0,
						);
					}

					$products[ $product_id ]['items'][]                                    = $item;
					$products[ $product_id ]['quantity']                                  += $item->get_quantity() ? $item->get_quantity() : 1;
					$products[ $product_id ]['in_list'][ $item->get_wishlist()->get_id() ] = sprintf( '<a href="%s">%s</a>', $item->get_wishlist()->get_url(), $item->get_wishlist()->get_formatted_name() );
				}
			}

			$default_wishlist       = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
			$default_wishlist_url   = $default_wishlist ? $default_wishlist->get_url() : YITH_WCWL()->get_wishlist_url();
			$multi_wishlist_enabled = YITH_WCWL()->is_multi_wishlist_enabled();
			$wishlists              = YITH_WCWL()->get_current_user_wishlists();
			$wishlist_url           = ( count( $wishlists ) > 1 && $multi_wishlist_enabled ) ? YITH_WCWL()->get_wishlist_url( 'manage' ) : $default_wishlist_url;

			$icon = get_option( 'yith_wcwl_add_to_wishlist_icon' );

			if ( 'custom' === $icon ) {
				$custom_icon = get_option( 'yith_wcwl_add_to_wishlist_custom_icon' );
				/**
				 * APPLY_FILTERS: yith_wcwl_custom_icon_alt
				 *
				 * Filter the alternative text for the heading icon in the widget.
				 *
				 * @param string $text Alternative text
				 *
				 * @return string
				 */
				$custom_icon_alt = apply_filters( 'yith_wcwl_custom_icon_alt', '' );

				/**
				 * APPLY_FILTERS: yith_wcwl_custom_width
				 *
				 * Filter the width for the heading icon in the widget.
				 *
				 * @param string $width Icon width
				 *
				 * @return string
				 */
				$custom_icon_width = apply_filters( 'yith_wcwl_custom_width', '32' );

				$heading_icon = '<img src="' . esc_url( $custom_icon ) . '" alt="' . esc_attr( $custom_icon_alt ) . '" width="' . esc_attr( $custom_icon_width ) . '" />';
			} else {
				$heading_icon = ! empty( $icon ) ? '<i class="fa ' . $icon . '"></i>' : '';
			}

			$additional_info = array(
				'instance'               => $instance,
				'fragments_options'      => $fragments_options,
				'products'               => $products,
				'items'                  => $items,
				'wishlist_url'           => 'yes' === $instance['show_default_only'] ? $default_wishlist_url : $wishlist_url,
				'multi_wishlist_enabled' => $multi_wishlist_enabled,
				'add_all_to_cart_url'    => wp_nonce_url( add_query_arg( 'add_all_to_cart', 1, $instance['current_url'] ), 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ),
				'default_wishlist'       => $default_wishlist,
				'heading_icon'           => $heading_icon,
			);

			$args = array_merge( $args, $additional_info );

			yith_wcwl_get_template( 'wishlist-widget-items.php', $args );

			// enqueue scripts.
			YITH_WCWL_Frontend()->enqueue_scripts();
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			$title                = isset( $instance['title'] ) ? $instance['title'] : '';
			$style                = isset( $instance['style'] ) && in_array( $instance['style'], array( 'mini', 'extended' ), true ) ? $instance['style'] : 'extended';
			$show_count           = ( isset( $instance['show_count'] ) && 'yes' === $instance['show_count'] );
			$show_default_only    = ( isset( $instance['show_default_only'] ) && 'yes' === $instance['show_default_only'] );
			$show_add_all_to_cart = ( isset( $instance['show_add_all_to_cart'] ) && 'yes' === $instance['show_add_all_to_cart'] );
			$show_view_link       = ( isset( $instance['show_view_link'] ) && 'yes' === $instance['show_view_link'] );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'yith-woocommerce-wishlist' ); ?></label>
				<input class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
			</p>
			<p>
				<label>
					<input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" <?php checked( $style, 'extended' ); ?> value="extended"/>
					<?php esc_html_e( 'Show extended widget', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label>
					<input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" <?php checked( $style, 'mini' ); ?> value="mini"/>
					<?php esc_html_e( 'Show mini widget', 'yith-woocommerce-wishlist' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_count' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_count ); ?> />
					<?php esc_html_e( 'Show items count', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_default_only' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_default_only' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_default_only' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_default_only ); ?> />
					<?php esc_html_e( 'Show items from default wishlist only', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_add_all_to_cart' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_add_all_to_cart' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_add_all_to_cart' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_add_all_to_cart ); ?> />
					<?php esc_html_e( 'Show Add all to Cart button', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_view_link' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_view_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_view_link' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_view_link ); ?> />
					<?php esc_html_e( 'Show View Wishlists link', 'yith-woocommerce-wishlist' ); ?>
				</label>
			</p>
			<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']                = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
			$instance['style']                = ( isset( $new_instance['style'] ) && in_array( $new_instance['style'], array( 'mini', 'extended' ), true ) ) ? $new_instance['style'] : 'extended';
			$instance['show_count']           = ( isset( $new_instance['show_count'] ) && yith_plugin_fw_is_true( $new_instance['show_count'] ) ) ? 'yes' : 'no';
			$instance['show_default_only']    = ( isset( $new_instance['show_default_only'] ) && yith_plugin_fw_is_true( $new_instance['show_default_only'] ) ) ? 'yes' : 'no';
			$instance['show_add_all_to_cart'] = ( isset( $new_instance['show_add_all_to_cart'] ) && yith_plugin_fw_is_true( $new_instance['show_add_all_to_cart'] ) ) ? 'yes' : 'no';
			$instance['show_view_link']       = ( isset( $new_instance['show_view_link'] ) && yith_plugin_fw_is_true( $new_instance['show_view_link'] ) ) ? 'yes' : 'no';

			return $instance;
		}
	}
}
