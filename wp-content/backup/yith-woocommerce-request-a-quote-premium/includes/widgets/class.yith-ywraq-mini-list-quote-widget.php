<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * YITH_YWRAQ_Mini_List_Quote_Widget add widget to show the request quote list.
 *
 * @class    YITH_YWRAQ_Mini_List_Quote_Widget
 * @package  YITH
 * @since    1.0.0
 * @author   YITH
 */

if ( ! class_exists( 'YITH_YWRAQ_Mini_List_Quote_Widget' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation Widget
	 *
	 * @since 1.0.0
	 */
	class YITH_YWRAQ_Mini_List_Quote_Widget extends WP_Widget {

		/**
		 * constructor
		 *
		 * @access public
		 */
		function __construct() {

			/* Widget variable settings. */
			$widget_cssclass    = 'woocommerce widget_ywraq_mini_list_quote';
			$widget_description = __( 'Show products added to your list', 'yith-woocommerce-request-a-quote' );
			$widget_idbase      = 'yith_ywraq_request_quote_list_mini';
			$widget_name        = __( 'YITH WooCommerce Mini Request a Quote List', 'yith-woocommerce-request-a-quote' );


			/* Widget settings. */
			$widget_ops = array( 'classname' => $widget_cssclass, 'description' => $widget_description );

			/* Create the widget. */
			parent::__construct( $widget_idbase, $widget_name, $widget_ops );

		}

		/**
		 * widget function.
		 *
		 * @param array $args
		 * @param array $instance
		 *
		 * @return void
		 * @see    WP_Widget
		 * @access public
		 *
		 */
		function widget( $args, $instance ) {
			extract( $args );

			$title                   = isset( $instance['title'] ) ? $instance['title'] : __( 'Quote List', 'yith-woocommerce-request-a-quote' );
			$item_name               = isset( $instance['item_name'] ) ? $instance['item_name'] : __( 'item', 'yith-woocommerce-request-a-quote' );
			$item_plural_name        = isset( $instance['item_plural_name'] ) ? $instance['item_plural_name'] : __( 'items', 'yith-woocommerce-request-a-quote' );
			$button_label            = isset( $instance['button_label'] ) ? $instance['button_label'] : __( 'View list', 'yith-woocommerce-request-a-quote' );
			$title                   = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$show_title_inside       = isset( $instance['show_title_inside'] ) && 'true' == $instance['show_title_inside'];
			$show_thumbnail          = isset( $instance['show_thumbnail'] ) && 'true' == $instance['show_thumbnail'];
			$show_price              = isset( $instance['show_price'] ) && 'true' == $instance['show_price'];
			$show_quantity           = isset( $instance['show_quantity'] ) && 'true' == $instance['show_quantity'];
			$show_variations         = isset( $instance['show_variations'] ) && 'true' == $instance['show_variations'];
			$instance['widget_type'] = 'mini';
			$instance['title']       = $title;
			$instance['show_title_inside']       = $show_title_inside;

			if ( ! apply_filters( 'yith_ywraq_before_print_widget', true ) ) {
				return;
			}

			echo $before_widget;

			$raq_content = YITH_Request_Quote()->get_raq_return();
			$args        = array(
				'raq_content'       => $raq_content,
				'title'             => $title,
				'item_name'         => $item_name,
				'item_plural_name'  => $item_plural_name,
				'button_label'      => $button_label,
				'template_part'     => 'view',
				'show_title_inside' => $show_title_inside,
				'show_thumbnail'    => $show_thumbnail,
				'show_price'        => $show_price,
				'show_quantity'     => $show_quantity,
				'show_variations'   => $show_variations,
				'widget_type'       => $instance['widget_type'],

			);

			echo '<div class="yith-ywraq-list-widget-wrapper" data-instance="' . http_build_query( $instance ) . '">';
			wc_get_template( 'widgets/mini-quote-list.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
			echo '</div>';

			echo $after_widget;
		}

		/**
		 * update function.
		 *
		 * @param array $new_instance
		 * @param array $old_instance
		 *
		 * @return array
		 * @see    WP_Widget->update
		 * @access public
		 *
		 */
		function update( $new_instance, $old_instance ) {
			$check_value                   = array(
				'true',
				true,
			);

			var_dump( $new_instance['show_title_inside'] );
			$instance['title']             = strip_tags( stripslashes( $new_instance['title'] ) );
			$instance['item_name']         = strip_tags( stripslashes( $new_instance['item_name'] ) );
			$instance['item_plural_name']  = strip_tags( stripslashes( $new_instance['item_plural_name'] ) );
			$instance['button_label']      = strip_tags( stripslashes( $new_instance['button_label'] ) );
			$instance['show_title_inside'] = isset( $new_instance['show_title_inside'] ) && in_array( $new_instance['show_title_inside'], $check_value );
			$instance['show_thumbnail']    = isset( $new_instance['show_thumbnail'] ) && in_array( $new_instance['show_thumbnail'], $check_value );
			$instance['show_price']        = isset( $new_instance['show_price'] ) && in_array( $new_instance['show_price'], $check_value );
			$instance['show_quantity']     = isset( $new_instance['show_quantity'] ) && in_array( $new_instance['show_quantity'], $check_value );
			$instance['show_variations']   = isset( $new_instance['show_variations'] ) && in_array( $new_instance['show_variations'], $check_value );

			$this->instance = $instance;

			return $instance;
		}

		/**
		 * form function.
		 *
		 * @param array $instance
		 *
		 * @return void
		 * @see    WP_Widget->form
		 * @access public
		 *
		 */
		function form( $instance ) {
			$defaults = array(
				'title'             => __( 'Quote List', 'yith-woocommerce-request-a-quote' ),
				'item_name'         => __( 'item', 'yith-woocommerce-request-a-quote' ),
				'item_plural_name'  => __( 'items', 'yith-woocommerce-request-a-quote' ),
				'button_label'      => __( 'View list', 'yith-woocommerce-request-a-quote' ),
				'show_title_inside' => 0,
				'show_thumbnail'    => 1,
				'show_price'        => 1,
				'show_quantity'     => 1,
				'show_variations'   => 1,
			);

			$instance = wp_parse_args( (array)$instance, $defaults ); ?>
			<!--suppress ALL -->


			<p>
				<label
					for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'yith-woocommerce-request-a-quote' ) ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
					value="<?php if ( isset ( $instance['title'] ) ) {
						echo esc_attr( $instance['title'] );
					} ?>"/>
			</p>

			<p>
				<label
					for="<?php echo $this->get_field_id( 'item_name' ); ?>"><?php esc_html_e( 'Item Name:', 'yith-woocommerce-request-a-quote' ) ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'item_name' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'item_name' ) ); ?>"
					value="<?php if ( isset ( $instance['item_name'] ) ) {
						echo esc_attr( $instance['item_name'] );
					} ?>"/>
			</p>

			<p>
				<label
					for="<?php echo $this->get_field_id( 'item_plural_name' ); ?>"><?php esc_html_e( 'Item Plural Name:', 'yith-woocommerce-request-a-quote' ) ?></label>
				<input type="text" class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'item_plural_name' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'item_plural_name' ) ); ?>"
					value="<?php if ( isset ( $instance['item_plural_name'] ) ) {
						echo esc_attr( $instance['item_plural_name'] );
					} ?>"/>
			</p>

			<p>
				<label
					for="<?php echo $this->get_field_id( 'button_label' ); ?>"><?php esc_html_e( 'Button Label:', 'yith-woocommerce-request-a-quote' ) ?></label>
				<input type="text" class="widefat"
					id="<?php echo esc_attr( $this->get_field_id( 'button_label' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'button_label' ) ); ?>"
					value="<?php if ( isset ( $instance['button_label'] ) ) {
						echo esc_attr( $instance['button_label'] );
					} ?>"/>
			</p>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_title_inside' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'show_title_inside' ) ); ?>"
						value="1" <?php checked( $instance['show_title_inside'], 1 ) ?> />
					<?php esc_html_e( 'Show widget title inside', 'yith-woocommerce-request-a-quote' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>"
						value="1" <?php checked( $instance['show_thumbnail'], 1 ) ?> />
					<?php esc_html_e( 'Show Thumbnail', 'yith-woocommerce-request-a-quote' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'show_price' ) ); ?>"
						value="1" <?php checked( $instance['show_price'], 1 ) ?> />
					<?php esc_html_e( 'Show Price', 'yith-woocommerce-request-a-quote' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_quantity' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'show_quantity' ) ); ?>"
						value="1" <?php checked( $instance['show_quantity'], 1 ) ?> />
					<?php esc_html_e( 'Show Quantity', 'yith-woocommerce-request-a-quote' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_variations' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'show_variations' ) ); ?>"
						value="1" <?php checked( $instance['show_variations'], 1 ) ?> />
					<?php esc_html_e( 'Show Variations', 'yith-woocommerce-request-a-quote' ); ?>
				</label>
			</p>

			<?php
		}

	}
}
