<?php
/**
 * Widget that shows all items in user wishlists
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( !defined( 'YITH_WCWL' ) ) { exit; } // Exit if accessed directly

if( ! class_exists( 'YITH_WCWL_Items_Widget' ) ) {
	/**
	 * Wishlist Items Widget
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Items_Widget extends WP_Widget {

		/**
		 * Sets up the widgets
		 */
		public function __construct(){
			parent::__construct(
				'yith-wcwl-items',
				__( 'YITH Wishlist Items', 'yith-woocommerce-wishlist' ),
				array( 'description' => __( 'A list of products in the user\'s wishlists', 'yith-woocommerce-wishlist' ) )
			);
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items( array(
				'orderby'     => 'dateadded',
				'order'       => 'ASC',
				'is_default'  => isset( $instance['show_default_only'] ) && $instance['show_default_only'] == 'yes',
				'wishlist_id' => 'all'
			) );
			$products = array();

			$instance['item'] = __CLASS__;
			$instance['unique_id'] = isset( $instance['unique_id'] ) ? $instance['unique_id'] : uniqid();
			$instance['style'] = isset( $instance['style'] ) ? $instance['style'] : 'extended';
			$instance['current_url'] = isset( $instance['current_url'] ) ? $instance['current_url'] : add_query_arg( array() );
			$instance['ajax_loading'] = isset( $instance['ajax_loading'] ) ? $instance['ajax_loading'] : 'yes' == get_option( 'yith_wcwl_ajax_enable', 'no' );
			$instance['show_default_only'] = isset( $instance['show_default_only'] ) ? $instance['show_default_only'] : 'no';
			$instance['show_view_link'] = isset( $instance['show_view_link'] ) ? $instance['show_view_link'] : 'no';
			$instance['show_add_all_to_cart'] = isset( $instance['show_add_all_to_cart'] ) ? $instance['show_add_all_to_cart'] : 'no';

			$fragments_options = YITH_WCWL_Frontend()->format_fragment_options( $instance, 'YITH_WCWL_Items_Widget' );

			if( ! empty( $items ) ){
				foreach( $items as $item ){
					$product_id = $item->get_product_id();

					if( ! isset( $products[ $product_id ] ) ){
						$products[ $product_id ] = array(
							'product' => $item->get_product(),
							'items' => array(),
							'in_list' => array(),
							'quantity' => 0
						);
					}

					$products[ $product_id ]['items'][] = $item;
					$products[ $product_id ]['quantity'] += $item->get_quantity() ? $item->get_quantity() : 1;
					$products[ $product_id ]['in_list'][ $item->get_wishlist()->get_id() ] = sprintf( '<a href="%s">%s</a>', $item->get_wishlist()->get_url(), $item->get_wishlist()->get_formatted_name() );
				}
			}

			$default_wishlist = YITH_WCWL_Wishlist_Factory::get_default_wishlist();
			$default_wishlist_url = $default_wishlist ? $default_wishlist->get_url() : YITH_WCWL()->get_wishlist_url();
			$multi_wishlist_enabled = YITH_WCWL()->is_multi_wishlist_enabled();
			$wishlists = YITH_WCWL()->get_current_user_wishlists();
			$wishlist_url = ( count( $wishlists ) > 1 && $multi_wishlist_enabled ) ? YITH_WCWL()->get_wishlist_url( 'manage' ) : $default_wishlist_url;

			$icon = get_option( 'yith_wcwl_add_to_wishlist_icon' );
			$custom_icon = get_option( 'yith_wcwl_add_to_wishlist_custom_icon' );

			if( 'custom' == $icon ){
				$heading_icon = '<img src="' . $custom_icon . '" width="32" />';
			}
			else{
				$heading_icon = ! empty( $icon ) ? '<i class="fa ' . $icon . '"></i>' : '';
			}

			$additional_info = array(
				'instance' => $instance,
				'fragments_options' => $fragments_options,
				'products' => $products,
				'items' => $items,
				'wishlist_url' => 'yes' == $instance['show_default_only'] ? $default_wishlist_url : $wishlist_url,
				'multi_wishlist_enabled' => $multi_wishlist_enabled,
				'add_all_to_cart_url' => wp_nonce_url( add_query_arg( 'add_all_to_cart', 1, $instance['current_url'] ), 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ),
				'default_wishlist' => $default_wishlist,
				'heading_icon' => $heading_icon
			);

			$args = array_merge( $args, $additional_info );

			yith_wcwl_get_template( 'wishlist-widget-items.php', $args );
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title = isset( $instance['title'] ) ?  $instance['title'] : '';
			$style = isset( $instance['style'] ) && in_array( $instance['style'], array( 'mini', 'extended' ) ) ? $instance['style'] : 'extended';
			$show_count = ( isset( $instance['show_count'] ) && $instance['show_count'] == 'yes' );
			$show_default_only = ( isset( $instance['show_default_only'] ) && $instance['show_default_only'] == 'yes' );
			$show_add_all_to_cart = ( isset( $instance['show_add_all_to_cart'] ) && $instance['show_add_all_to_cart'] == 'yes' );
			$show_view_link = ( isset( $instance['show_view_link'] ) && $instance['show_view_link'] == 'yes' );
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'yith-woocommerce-wishlist' )?></label>
				<input class="widefat"  id="<?php echo $this->get_field_id( 'title' )?>" name="<?php echo $this->get_field_name( 'title' ) ?>" type="text" value="<?php echo $title ?>"/>
			</p>
			<p>
				<label>
					<input type="radio" name="<?php echo $this->get_field_name( 'style' )?>" id="<?php echo $this->get_field_id( 'style' ) ?>" <?php checked( $style, 'extended' ) ?> value="extended"/>
					<?php _e( 'Show extended widget', 'yith-woocommerce-wishlist' ) ?>
				</label><br/>
				<label>
					<input type="radio" name="<?php echo $this->get_field_name( 'style' )?>" id="<?php echo $this->get_field_id( 'style' ) ?>" <?php checked( $style, 'mini' ) ?> value="mini"/>
					<?php _e( 'Show mini widget', 'yith-woocommerce-wishlist' ) ?>
				</label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'show_count' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_count' )?>" name="<?php echo $this->get_field_name( 'show_count' ) ?>" type="checkbox" value="yes" <?php checked( $show_count ) ?> />
					<?php _e( 'Show items count', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo $this->get_field_id( 'show_default_only' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_default_only' )?>" name="<?php echo $this->get_field_name( 'show_default_only' ) ?>" type="checkbox" value="yes" <?php checked( $show_default_only ) ?> />
					<?php _e( 'Show items from default wishlist only', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo $this->get_field_id( 'show_add_all_to_cart' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_add_all_to_cart' )?>" name="<?php echo $this->get_field_name( 'show_add_all_to_cart' ) ?>" type="checkbox" value="yes" <?php checked( $show_add_all_to_cart ) ?> />
					<?php _e( 'Show Add all to Cart button', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo $this->get_field_id( 'show_view_link' ); ?>">
					<input id="<?php echo $this->get_field_id( 'show_view_link' )?>" name="<?php echo $this->get_field_name( 'show_view_link' ) ?>" type="checkbox" value="yes" <?php checked( $show_view_link ) ?> />
					<?php _e( 'Show View Wishlists link', 'yith-woocommerce-wishlist' ); ?>
				</label>
			</p>
		<?php
		}

		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['style'] = ( isset( $new_instance['style'] ) && in_array( $new_instance['style'], array( 'mini', 'extended' ) ) ) ? $new_instance['style'] : 'extended';
			$instance['show_count'] = ( isset( $new_instance['show_count'] ) ) ? 'yes' : 'no';
			$instance['show_default_only'] = ( isset( $new_instance['show_default_only'] ) ) ? 'yes' : 'no';
			$instance['show_add_all_to_cart'] = ( isset( $new_instance['show_add_all_to_cart'] ) ) ? 'yes' : 'no';
			$instance['show_view_link'] = ( isset( $new_instance['show_view_link'] ) ) ? 'yes' : 'no';

			return $instance;
		}
	}
}