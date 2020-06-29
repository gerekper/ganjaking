<?php
/**
 * class-woocommerce-product-search-filter-price-widget.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product filter widget.
 */
class WooCommerce_Product_Search_Filter_Price_Widget extends WP_Widget {

	/**
	 * This widget's name (shown on the admin UI).
	 *
	 * @var string
	 */
	private static $the_name = '';

	/**
	 * Cache identifier for all instances of this widget.
	 * There is one cache entry for all instances of this widget, an array
	 * indexed by the instances' widget_id.
	 *
	 * @var string cache id
	 */
	private static $cache_id = 'woocommerce_product_search_filter_price_widget';

	/**
	 * Cache group identifier for all instances of this widget.
	 *
	 * @var string cache flag
	 */
	private static $cache_group = 'widget';

	/**
	 * Initialize.
	 */
	public static function init() {
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		self::$the_name = __( 'Product Filter &ndash; Price', 'woocommerce-product-search' );
	}

	/**
	 * Registers the widget.
	 */
	public static function widgets_init() {
		register_widget( 'WooCommerce_Product_Search_Filter_Price_Widget' );
	}

	/**
	 * Creates the widget.
	 */
	public function __construct() {
		parent::__construct(
			self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'WooCommerce Product Search &mdash; This is the live price filter for your shop. It updates the products displayed instantly, according to the desired price range.', 'woocommerce-product-search' )
			)
		);
	}

	/**
	 * Clears cached widget.
	 */
	public static function cache_delete() {
		wp_cache_delete( self::$cache_id, self::$cache_group );
	}

	/**
	 * Renders the widget for display.
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @see WP_Widget::widget()
	 * @link http://codex.wordpress.org/Class_Reference/WP_Object_Cache
	 */
	public function widget( $args, $instance ) {

		$value = isset( $instance['shop_only'] ) ? strtolower( $instance['shop_only'] ) : 'yes';
		$shop_only = $value == 'true' || $value == 'yes' || $value == '1';
		if ( $shop_only && !is_shop() && !is_product_taxonomy() ) {
			return;
		}

		WooCommerce_Product_Search_Filter_Price::load_resources();

		$cache = wp_cache_get( self::$cache_id, self::$cache_group );
		if ( ! is_array( $cache ) ) {
			$cache = array();
		}
		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']]; 
			return;
		}

		$before_title  = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title   = isset( $args['after_title'] ) ? $args['after_title'] : '';
		$before_widget = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? $args['after_widget'] : '';

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );

		$output = '';

		$output .= $before_widget;
		if ( !empty( $title ) ) {
			$output .= $before_title . $title . $after_title;
		}
		$output .= WooCommerce_Product_Search_Filter_Price::render( $instance );
		$output .= $after_widget;

		echo $output; 

		$cache[$args['widget_id']] = $output;
		wp_cache_set( self::$cache_id, $cache, self::$cache_group );
	}

	/**
	 * Save widget options
	 *
	 * @param array $new_instance holds requested settings
	 * @param array $old_instance holds current settings for the widget instance
	 *
	 * @see WP_Widget::update()
	 */
	public function update( $new_instance, $old_instance ) {

		global $wpdb;

		$settings = $old_instance;

		$settings['title']         = trim( strip_tags( $new_instance['title'] ) );

		$settings['heading']            = !empty( $new_instance['heading'] ) ? trim( strip_tags( $new_instance['heading'] ) ) : null;
		$settings['heading_class']      = !empty( $new_instance['heading_class'] ) ? trim( $new_instance['heading_class'] ) : '';
		$settings['heading_id']         = !empty( $new_instance['heading_id'] ) ? trim( $new_instance['heading_id'] ) : '';
		$settings['heading_element']    = !empty( $new_instance['heading_element'] ) ? trim( $new_instance['heading_element'] ) : '';
		$settings['show_heading']       = !empty( $new_instance['show_heading'] ) ? 'yes' : 'no';

		$settings['filter']        = !empty( $new_instance['filter'] ) ? 'yes' : 'no';
		$settings['use_shop_url']  = !empty( $new_instance['use_shop_url'] ) ? 'yes' : 'no';
		$settings['submit_button'] = !empty( $new_instance['submit_button'] ) ? 'yes' : 'no';

		$settings['shop_only'] = !empty( $new_instance['shop_only'] ) ? 'yes' : 'no';

		$delay = !empty( $new_instance['delay'] ) ? intval( $new_instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		if ( $delay < WooCommerce_Product_Search::MIN_DELAY ) {
			$delay = WooCommerce_Product_Search::MIN_DELAY;
		}
		$settings['delay'] = $delay;

		$settings['slider']               = !empty( $new_instance['slider'] ) ? 'yes' : 'no';
		$settings['fields']               = !empty( $new_instance['fields'] ) ? 'yes' : 'no';
		$settings['submit_button_label']  = isset( $new_instance['submit_button_label'] ) ? strip_tags( $new_instance['submit_button_label'] ) : __( 'Go', 'woocommerce-product-search' );
		$settings['min_placeholder']      = isset( $new_instance['min_placeholder'] ) ? strip_tags( $new_instance['min_placeholder'] ) : __( 'Min', 'woocommerce-product-search' );
		$settings['max_placeholder']      = isset( $new_instance['max_placeholder'] ) ? strip_tags( $new_instance['max_placeholder'] ) : __( 'Max', 'woocommerce-product-search' );
		$settings['show_currency_symbol'] = !empty( $new_instance['show_currency_symbol'] ) ? 'yes' : 'no';
		$settings['show_clear']           = !empty( $new_instance['show_clear'] ) ? 'yes' : 'no';

		$container_id = !empty( $new_instance['container_id'] ) ? trim( $new_instance['container_id'] ) : '';
		if ( strlen( $container_id ) > 0 ) {
			$settings['container_id'] = $container_id;
		} else {
			unset( $settings['container_id'] );
		}

		$container_class = !empty( $new_instance['container_class'] ) ? trim( $new_instance['container_class'] ) : '';
		if ( strlen( $container_class ) > 0 ) {
			$settings['container_class'] = $container_class;
		} else {
			unset( $settings['container_class'] );
		}

		$this->cache_delete();

		return $settings;
	}

	/**
	 * Output admin widget options form
	 *
	 * @param array $instance current widget settings
	 *
	 * @see WP_Widget::form()
	 */
	public function form( $instance ) {

		$widget_title = isset( $instance['title'] ) ? $instance['title'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The widget title.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Title', 'woocommerce-product-search' ) );
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $widget_title ) . '" />';
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		esc_html_e( 'Heading', 'woocommerce-product-search' );
		echo '</h4>';

		$show_heading = isset( $instance['show_heading'] ) ? $instance['show_heading'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the heading before the fields.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'show_heading' ),
			$this->get_field_name( 'show_heading' ),
			$show_heading == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show heading', 'woocommerce-product-search' ) );
		echo '</label>';

		$heading = isset( $instance['heading'] ) ? $instance['heading'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'The text displayed in the heading.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Heading', 'woocommerce-product-search' );
		printf(
			'<input class="widefat" id="%s" name="%s" type="text" value="%s" placeholder="%s"/>',
			esc_attr( $this->get_field_id( 'heading' ) ),
			esc_attr( $this->get_field_name( 'heading' ) ),
			esc_attr( $heading ),
			esc_attr__( 'Automatic', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		esc_html_e( 'Display', 'woocommerce-product-search' );
		echo '</h4>';

		$shop_only = isset( $instance['shop_only'] ) ? $instance['shop_only'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the widget on relevant shop pages only, including the shop, tag, category pages etc&hellip;', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'shop_only' ),
			$this->get_field_name( 'shop_only' ),
			$shop_only == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show on shop pages only', 'woocommerce-product-search' ) );
		echo '</label>';

		$slider = isset( $instance['slider'] ) ? $instance['slider'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the price slider.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'slider' ),
			$this->get_field_name( 'slider' ),
			$slider == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show slider', 'woocommerce-product-search' ) );
		echo '</label>';

		$fields = isset( $instance['fields'] ) ? $instance['fields'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the minimum and maximum input fields.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'fields' ),
			$this->get_field_name( 'fields' ),
			$fields == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show input fields', 'woocommerce-product-search' ) );
		echo '</label>';

		$delay = isset( $instance['delay'] ) ? intval( $instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		echo '<p>';

		printf(
			'<label title="%s">',
			esc_attr( sprintf(
				__( 'The delay until the search starts after the user stops typing (in milliseconds, minimum %d).', 'woocommerce-product-search' ),
				WooCommerce_Product_Search::MIN_DELAY
			) )
		);
		echo esc_html( __( 'Delay', 'woocommerce-product-search' ) );
		echo '&nbsp;';
		printf(
			'<input id="%d" name="%s" type="text" value="%s" style="width:5em;text-align:center"/>',
			esc_attr( $this->get_field_id( 'delay' ) ),
			esc_attr( $this->get_field_name( 'delay' ) ),
			esc_attr( $delay )
		);
		echo '&nbsp;';
		echo esc_html( __( 'ms', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$min_placeholder = isset( $instance['min_placeholder'] ) ? $instance['min_placeholder'] : __( 'Min', 'woocommerce-product-search' );
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The placeholder text for the minimum amount field.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Minimum placeholder', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'min_placeholder' ) ),
			esc_attr( $this->get_field_name( 'min_placeholder' ) ),
			esc_attr( $min_placeholder )
		);
		echo '</label>';
		echo '</p>';

		$max_placeholder = isset( $instance['max_placeholder'] ) ? $instance['max_placeholder'] : __( 'Max', 'woocommerce-product-search' );
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The placeholder text for the maximum amount field.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Maximum placeholder', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'max_placeholder' ) ),
			esc_attr( $this->get_field_name( 'max_placeholder' ) ),
			esc_attr( $max_placeholder )
		);
		echo '</label>';
		echo '</p>';

		$show_currency_symbol = isset( $instance['show_currency_symbol'] ) ? $instance['show_currency_symbol'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the currency symbol with the price fields.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_currency_symbol' ) ),
			esc_attr( $this->get_field_name( 'show_currency_symbol' ) ),
			$show_currency_symbol == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Currency Symbol', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show_clear = isset( $instance['show_clear'] ) ? $instance['show_clear'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display a shortcut to clear the price fields when they contain text.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_clear' ) ),
			esc_attr( $this->get_field_name( 'show_clear' ) ),
			$show_clear == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Clear shortcut', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$submit_button = isset( $instance['submit_button'] ) ? $instance['submit_button'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show a submit button.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'submit_button' ) ),
			esc_attr( $this->get_field_name( 'submit_button' ) ),
			$submit_button == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Submit button', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$submit_button_label = isset( $instance['submit_button_label'] ) ? $instance['submit_button_label'] : __( 'Go', 'woocommerce-product-search' );
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The text shown on the submit button.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Submit button label', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'submit_button_label' ) ),
			esc_attr( $this->get_field_name( 'submit_button_label' ) ),
			esc_attr( $submit_button_label )
		);
		echo '</label>';
		echo '</p>';

		$use_shop_url = isset( $instance['use_shop_url'] ) ? $instance['use_shop_url'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Link to the shop page instead of the same page.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'use_shop_url' ) ),
			esc_attr( $this->get_field_name( 'use_shop_url' ) ),
			$use_shop_url== 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Use the Shop URL', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		esc_html_e( 'Advanced', 'woocommerce-product-search' );
		echo '</h4>';

		$heading_id = isset( $instance['heading_id'] ) ? $instance['heading_id'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'ID of the heading element.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Heading ID', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'heading_id' ) ),
			esc_attr( $this->get_field_name( 'heading_id' ) ),
			esc_attr( $heading_id ),
			esc_attr__( 'Automatic', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		$heading_class = isset( $instance['heading_class'] ) ? $instance['heading_class'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'CSS class of the heading element.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Heading Class', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'heading_class' ) ),
			esc_attr( $this->get_field_name( 'heading_class' ) ),
			esc_attr( $heading_class ),
			esc_attr__( 'Automatic', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		$heading_element = isset( $instance['heading_element'] ) ? $instance['heading_element'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The HTML element used to contain the heading text.', 'woocommerce-product-search' ) ) );
		echo esc_html__( 'Heading Element', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<select id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'heading_element' ) ),
			esc_attr( $this->get_field_name( 'heading_element' ) )
		);
		$options = WooCommerce_Product_Search_Filter::get_allowed_filter_heading_elements();
		printf( '<option value="" %s>%s</option>', $heading_element == '' ? ' selected="selected" ' : '', esc_html( __( 'Default', 'woocommerce-product-search' ) ) );
		foreach ( $options as $key ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $heading_element == $key ? ' selected="selected" ' : '', esc_html( $key ) );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';
	}

	public function get_default_instance() {
		return array(
			'title' => '',
			'show_heading' => true,
			'heading' => '',
			'shop_only' => true,
			'delay'=> WooCommerce_Product_Search::DEFAULT_DELAY,
			'min_placeholder' => __( 'Min', 'woocommerce-product-search' ),
			'max_placeholder' => __( 'Max', 'woocommerce-product-search' ),
			'show_currency_symbol' => true,
			'show_clear' => true,
			'submit_button' => false,
			'submit_button_label' => __( 'Go', 'woocommerce-product-search' ),
			'use_shop_url' => false,
			'heading_id' => '',
			'heading_class' => '',
			'heading_element' => '',
			'slider' => true,
			'fields' => true
		);
	}
}

WooCommerce_Product_Search_Filter_Price_Widget::init();
