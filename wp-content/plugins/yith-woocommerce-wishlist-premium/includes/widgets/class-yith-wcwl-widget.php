<?php
/**
 * Widget that shows all user lists
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes\Widgets
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit; } // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Widget' ) ) {
	/**
	 * WooCommerce Wishlist Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Widget extends WP_Widget {

		/**
		 * Sets up the widgets
		 */
		public function __construct() {
			parent::__construct(
				'yith-wcwl-lists',
				__( 'YITH Wishlist Lists', 'yith-woocommerce-wishlist' ),
				array( 'description' => __( 'A list of all the user\'s wishlists', 'yith-woocommerce-wishlist' ) )
			);
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args General widgets argumetns.
		 * @param array $instance Widget specific instance.
		 */
		public function widget( $args, $instance ) {
			$create_page_title      = get_option( 'yith_wcwl_wishlist_create_title' );
			$manage_page_title      = get_option( 'yith_wcwl_wishlist_manage_title' );
			$search_page_title      = get_option( 'yith_wcwl_wishlist_search_title' );
			$default_wishlist_title = get_option( 'yith_wcwl_wishlist_title' );

			$current_wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
			$active           = YITH_WCWL()->get_current_endpoint();

			$instance['item']         = __CLASS__;
			$instance['unique_id']    = isset( $instance['unique_id'] ) ? $instance['unique_id'] : uniqid();
			$instance['ajax_loading'] = isset( $instance['ajax_loading'] ) ? $instance['ajax_loading'] : 'yes' === get_option( 'yith_wcwl_ajax_enable', 'no' );

			$fragment_options = YITH_WCWL_Frontend()->format_fragment_options( $instance );

			$additional_info = array(
				'wishlist_url'           => YITH_WCWL()->get_wishlist_url(),
				'instance'               => $instance,
				'fragment_options'       => $fragment_options,
				'users_wishlists'        => YITH_WCWL()->get_current_user_wishlists(),
				'multi_wishlist_enabled' => YITH_WCWL()->is_multi_wishlist_enabled(),
				'default_wishlist_title' => $default_wishlist_title,
				'create_page_title'      => $create_page_title,
				'manage_page_title'      => $manage_page_title,
				'search_page_title'      => $search_page_title,
				'current_wishlist'       => $current_wishlist,
				'active'                 => $active,
			);

			$args = array_merge( $args, $additional_info );

			yith_wcwl_get_template( 'wishlist-widget-lists.php', $args );

			// enqueue scripts.
			YITH_WCWL_Frontend()->enqueue_scripts();
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {
			$show_create_link = ( isset( $instance['show_create_link'] ) && 'yes' === $instance['show_create_link'] );
			$show_search_link = ( isset( $instance['show_search_link'] ) && 'yes' === $instance['show_search_link'] );
			$show_manage_link = ( isset( $instance['show_manage_link'] ) && 'yes' === $instance['show_manage_link'] );
			$title            = isset( $instance['title'] ) ? $instance['title'] : '';
			$wishlist_link    = isset( $instance['wishlist_link'] ) ? $instance['wishlist_link'] : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'yith-woocommerce-wishlist' ); ?></label>
				<input class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'wishlist_link' ) ); ?>"><?php esc_html_e( '"Your wishlist" link:', 'yith-woocommerce-wishlist' ); ?></label>
				<input class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'wishlist_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'wishlist_link' ) ); ?>" type="text" value="<?php echo esc_attr( $wishlist_link ); ?>"/>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_create_link' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_create_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_create_link' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_create_link ); ?> />
					<?php esc_html_e( 'Show create link', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_search_link' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_search_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_search_link' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_search_link ); ?> />
					<?php esc_html_e( 'Show search link', 'yith-woocommerce-wishlist' ); ?>
				</label><br/>
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_manage_link' ) ); ?>">
					<input id="<?php echo esc_attr( $this->get_field_id( 'show_manage_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_manage_link' ) ); ?>" type="checkbox" value="yes" <?php checked( $show_manage_link ); ?> />
					<?php esc_html_e( 'Show manage link', 'yith-woocommerce-wishlist' ); ?>
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
			$instance                     = array();
			$instance['title']            = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
			$instance['wishlist_link']    = ( ! empty( $new_instance['wishlist_link'] ) ) ? wp_strip_all_tags( $new_instance['wishlist_link'] ) : '';
			$instance['show_create_link'] = ( isset( $new_instance['show_create_link'] ) && yith_plugin_fw_is_true( $new_instance['show_create_link'] ) ) ? 'yes' : 'no';
			$instance['show_search_link'] = ( isset( $new_instance['show_search_link'] ) && yith_plugin_fw_is_true( $new_instance['show_search_link'] ) ) ? 'yes' : 'no';
			$instance['show_manage_link'] = ( isset( $new_instance['show_manage_link'] ) && yith_plugin_fw_is_true( $new_instance['show_manage_link'] ) ) ? 'yes' : 'no';

			return $instance;
		}
	}
}
