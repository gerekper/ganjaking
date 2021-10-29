<?php
/**
 * class-woocommerce-product-search-widget.php
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
 * @since 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product search widget.
 */
class WooCommerce_Product_Search_Widget extends WP_Widget {

	/**
	 * UI widget name.
	 *
	 * @var string
	 */
	private static $the_name = '';

	/**
	 * Identifies widget instances in cache (one cache entry is used for all instances, indexed by widget_id).
	 *
	 * @var string cache id
	 */
	private static $cache_id = 'woocommerce_product_search_widget';

	/**
	 * The cache group used.
	 *
	 * @var string cache group
	 */
	private static $cache_group = 'widget';

	/**
	 * Initialize.
	 */
	public static function init() {
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
		self::$the_name = __( 'Product Search Field', 'woocommerce-product-search' );
	}

	/**
	 * Registers the widget.
	 */
	public static function widgets_init() {
		register_widget( 'WooCommerce_Product_Search_Widget' );
	}

	/**
	 * Creates the widget.
	 */
	public function __construct( $id_base = null ) {
		parent::__construct(
			$id_base !== null ? $id_base : self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'WooCommerce Product Search &mdash; Your shop\'s instant product search field, it provides live results while you type.', 'woocommerce-product-search' )
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
	 * Widget output
	 *
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 *
	 * @see WP_Widget::widget()
	 * @link http://codex.wordpress.org/Class_Reference/WP_Object_Cache
	 */
	public function widget( $args, $instance ) {

		WooCommerce_Product_Search_Field::load_resources();

		$cache = wps_cache_get( self::$cache_id, self::$cache_group );
		if ( ! is_array( $cache ) ) {
			$cache = array();
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
		$instance['title'] = isset( $instance['query_title'] ) ? $instance['query_title'] : 'yes';
		$output .= WooCommerce_Product_Search_Field::woocommerce_product_search( $instance );
		$output .= $after_widget;

		echo $output;

		$cache[$args['widget_id']] = $output;
		wps_cache_set( self::$cache_id, $cache, self::$cache_group );

	}

	/**
	 * Save widget options
	 *
	 * @param array $new_instance requested widget instance settings
	 * @param array $old_instance current widget instance settings
	 *
	 * @return array validated widget instance settings
	 *
	 * @see WP_Widget::update()
	 */
	public function update( $new_instance, $old_instance ) {

		global $wpdb;

		$settings = $old_instance;

		if ( $this->id_base !== 'wps-auto-instance' ) {

			$settings['title'] = trim( strip_tags( $new_instance['title'] ) );
		}

		$settings['query_title'] = !empty( $new_instance['query_title'] ) ? 'yes' : 'no';
		$settings['excerpt']     = !empty( $new_instance['excerpt'] ) ? 'yes' : 'no';
		$settings['content']     = !empty( $new_instance['content'] ) ? 'yes' : 'no';
		$settings['categories']  = !empty( $new_instance['categories'] ) ? 'yes' : 'no';
		$settings['attributes']  = !empty( $new_instance['attributes'] ) ? 'yes' : 'no';
		$settings['tags']        = !empty( $new_instance['tags'] ) ? 'yes' : 'no';
		$settings['sku']         = !empty( $new_instance['sku'] ) ? 'yes' : 'no';

		$settings['order']    = !empty( $new_instance['order'] ) ? $new_instance['order'] : 'DESC';
		$settings['order_by'] = isset( $new_instance['order_by'] ) ? $new_instance['order_by'] : 'date';

		$limit = !empty( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_LIMIT;
		if ( $limit < 0 ) {
			$limit = WooCommerce_Product_Search_Service::DEFAULT_LIMIT;
		}
		$settings['limit'] = $limit;
		$settings['show_more'] = !empty( $new_instance['show_more'] ) ? 'yes' : 'no';

		$settings['category_results'] = !empty( $new_instance['category_results'] ) ? 'yes' : 'no';
		$category_limit = !empty( $new_instance['category_limit'] ) ? intval( $new_instance['category_limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT;
		if ( $category_limit < 0 ) {
			$category_limit = WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT;
		}
		$settings['category_limit'] = $category_limit;

		$settings['product_thumbnails'] = !empty( $new_instance['product_thumbnails'] ) ? 'yes' : 'no';

		$settings['show_description'] = !empty( $new_instance['show_description'] ) ? 'yes' : 'no';

		$settings['show_price'] = !empty( $new_instance['show_price'] ) ? 'yes' : 'no';

		$settings['show_add_to_cart'] = !empty( $new_instance['show_add_to_cart'] ) ? 'yes' : 'no';

		$delay = !empty( $new_instance['delay'] ) ? intval( $new_instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		if ( $delay < WooCommerce_Product_Search::MIN_DELAY ) {
			$delay = WooCommerce_Product_Search::MIN_DELAY;
		}
		$settings['delay'] = $delay;

		$characters = !empty( $new_instance['characters'] ) ? intval( $new_instance['characters'] ) : WooCommerce_Product_Search::DEFAULT_CHARACTERS;
		if ( $characters < WooCommerce_Product_Search::MIN_CHARACTERS ) {
			$characters = WooCommerce_Product_Search::MIN_CHARACTERS;
		}
		$settings['characters'] = $characters;

		$settings['show_clear'] = !empty( $new_instance['show_clear'] ) ? 'yes' : 'no';

		$placeholder = !empty( $new_instance['placeholder'] ) ? trim( strip_tags( $new_instance['placeholder'] ) ) : '';
		$settings['placeholder'] = $placeholder;

		$settings['dynamic_focus'] = !empty( $new_instance['dynamic_focus'] ) ? 'yes' : 'no';
		$settings['floating']      = !empty( $new_instance['floating'] ) ? 'yes' : 'no';
		$settings['inhibit_enter'] = !empty( $new_instance['inhibit_enter'] ) ? 'yes' : 'no';
		$settings['submit_button'] = !empty( $new_instance['submit_button'] ) ? 'yes' : 'no';
		$submit_button_label       = !empty( $new_instance['submit_button_label'] ) ? strip_tags( $new_instance['submit_button_label'] ) : '';
		$settings['submit_button_label'] = $submit_button_label;
		$settings['navigable']     = !empty( $new_instance['navigable'] ) ? 'yes' : 'no';
		$no_results                = !empty( $new_instance['no_results'] ) ? trim( strip_tags( $new_instance['no_results'] ) ) : '';
		$settings['no_results']    = $no_results;
		$settings['height']        = !empty( $new_instance['height'] ) ? WooCommerce_Product_Search_Utility::get_css_unit( $new_instance['height'] ) : '';

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$settings['wpml']   = !empty( $new_instance['wpml'] ) ? 'yes' : 'no';
		}

		$this->cache_delete();

		return $settings;
	}

	/**
	 * Output admin widget options form
	 *
	 * @param array $instance widget instance settings
	 *
	 * @see WP_Widget::form()
	 */
	public function form( $instance ) {

		if ( $this->id_base !== 'wps-auto-instance' ) {

			$widget_title = isset( $instance['title'] ) ? $instance['title'] : '';
			echo '<p>';
			echo sprintf( '<label title="%s">', esc_attr( __( 'The widget title.', 'woocommerce-product-search' ) ) );
			echo esc_html( __( 'Title', 'woocommerce-product-search' ) );
			echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $widget_title ) . '" />';
			echo '</label>';
			echo '</p>';
		}

		if ( $this->id_base === 'wps-auto-instance' ) {
			echo '<table>';
			echo '<tr>';
			echo '<td style="width:50%;vertical-align:top;">';
		}

		if ( $this->id_base !== 'wps-auto-instance' ) {
			$heading = '<h4>';
			$heading_close = '</h4>';
		} else {
			$heading = '<h5>';
			$heading_close = '</h5>';
		}

		echo $heading;
		esc_html_e( 'Search Results', 'woocommerce-product-search' );
		echo $heading_close;

		$title = isset( $instance['query_title'] ) ? $instance['query_title'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include matching titles.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'query_title' ) ),
			esc_attr( $this->get_field_name( 'query_title' ) ),
			$title == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in titles', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$excerpt = isset( $instance['excerpt'] ) ? $instance['excerpt'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include matches in excerpts.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'excerpt' ) ),
			esc_attr( $this->get_field_name( 'excerpt' ) ),
			$excerpt == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in excerpts', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$content = isset( $instance['content'] ) ? $instance['content'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include matches in contents.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'content' ) ),
			esc_attr( $this->get_field_name( 'content' ) ),
			$content == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in contents', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$categories = isset( $instance['categories'] ) ? $instance['categories'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include entries with matching categories.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'categories' ) ),
			esc_attr( $this->get_field_name( 'categories' ) ),
			$categories == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in categories', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$tags = isset( $instance['tags'] ) ? $instance['tags'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include entries with matching tags.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'tags' ) ),
			esc_attr( $this->get_field_name( 'tags' ) ),
			$tags == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in tags', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$attributes = isset( $instance['attributes'] ) ? $instance['attributes'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include entries with matching product attributes.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'attributes' ) ),
			esc_attr( $this->get_field_name( 'attributes' ) ),
			$attributes == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in attributes', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$sku = isset( $instance['sku'] ) ? $instance['sku'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include matches in SKU.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'sku' ) ),
			esc_attr( $this->get_field_name( 'sku' ) ),
			$sku == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Search in SKU', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$order_by = isset( $instance['order_by'] ) ? $instance['order_by'] : 'date';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Order the results by the chosen property.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Order by ...', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<select id="%s" name="%s">',
			esc_attr( $this->get_field_id( 'order_by' ) ),
			esc_attr( $this->get_field_name( 'order_by' ) )
		);
		$options = array(
			'date'       => esc_html__( 'Date', 'woocommerce-product-search' ),
			'title'      => esc_html__( 'Title', 'woocommerce-product-search' ),
			'ID'         => esc_html__( 'ID', 'woocommerce-product-search' ),
			'sku'        => esc_html__( 'SKU', 'woocommerce-product-search' ),
			'popularity' => esc_html__( 'Popularity', 'woocommerce-product-search' ),
			'rating'     => esc_html__( 'Rating', 'woocommerce-product-search' ),
			''           => esc_html_x( '&mdash;', 'Product Search Field Order by ... option : indicator for no specific ordering', 'woocommerce-product-search' )
		);
		foreach ( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $order_by == $key ? ' selected="selected" ' : '', esc_html( $value ) );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		$order = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		echo '<p>';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="ASC" %s />', esc_attr( $this->get_field_name( 'order' ) ), $order == 'ASC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Ascending', 'woocommerce-product-search' ) );
		echo '</label>';
		echo ' ';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="DESC" %s />', esc_attr( $this->get_field_name( 'order' ) ), $order == 'DESC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Descending', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$limit = isset( $instance['limit'] ) ? intval( $instance['limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_LIMIT;
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Limit the maximum number of results shown.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Limit', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" style="width:5em;text-align:center"/>',
			esc_attr( $this->get_field_id( 'limit' ) ),
			esc_attr( $this->get_field_name( 'limit' ) ),
			esc_attr( $limit )
		);
		echo '</label>';
		echo '</p>';

		$show_more = isset( $instance['show_more'] ) ? $instance['show_more'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show a link that leads to search results when there are more results than the limit.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_more' ) ),
			esc_attr( $this->get_field_name( 'show_more' ) ),
			$show_more == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show more', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$category_results = isset( $instance['category_results'] ) ? $instance['category_results'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results should include categories with matching results.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'category_results' ) ),
			esc_attr( $this->get_field_name( 'category_results' ) ),
			$category_results == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show category matches', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$category_limit = isset( $instance['category_limit'] ) ? intval( $instance['category_limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT;
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Limit the maximum number of category results shown.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Category Limit', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" style="width:5em;text-align:center"/>',
			esc_attr( $this->get_field_id( 'category_limit' ) ),
			esc_attr( $this->get_field_name( 'category_limit' ) ),
			esc_attr( $category_limit )
		);
		echo '</label>';
		echo '</p>';

		$product_thumbnails = isset( $instance['product_thumbnails'] ) ? $instance['product_thumbnails'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show a product thumbnail for each result.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'product_thumbnails' ) ),
			esc_attr( $this->get_field_name( 'product_thumbnails' ) ),
			$product_thumbnails == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show product thumbnails', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show_description = isset( $instance['show_description'] ) ? $instance['show_description'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show short product descriptions.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_description' ) ),
			esc_attr( $this->get_field_name( 'show_description' ) ),
			$show_description == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show descriptions', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show_price = isset( $instance['show_price'] ) ? $instance['show_price'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show product prices.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_price' ) ),
			esc_attr( $this->get_field_name( 'show_price' ) ),
			$show_price == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show prices', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show_add_to_cart = isset( $instance['show_add_to_cart'] ) ? $instance['show_add_to_cart'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Allows to add a product to the cart, select options or read more directly.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_add_to_cart' ) ),
			esc_attr( $this->get_field_name( 'show_add_to_cart' ) ),
			$show_add_to_cart == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show Add to Cart', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		if ( $this->id_base === 'wps-auto-instance' ) {
			echo '</td>';
			echo '<td style="width:50%;vertical-align:top;">';
		}

		echo $heading;
		esc_html_e( 'User Interface', 'woocommerce-product-search' );
		echo $heading_close;

		$delay = isset( $instance['delay'] ) ? intval( $instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		echo '<p>';

		echo sprintf( '<label title="%s">', esc_attr( __( 'The delay until the search starts after the user stops typing (in milliseconds, minimum %d).', 'woocommerce-product-search' ), WooCommerce_Product_Search::MIN_DELAY ) );
		echo esc_html( __( 'Delay', 'woocommerce-product-search' ) );
		echo '&nbsp;';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" style="width:5em;text-align:center"/>',
			esc_attr( $this->get_field_id( 'delay' ) ),
			esc_attr( $this->get_field_name( 'delay' ) ),
			esc_attr( $delay )
		);
		echo '&nbsp;';
		echo esc_html( __( 'ms', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$characters = isset( $instance['characters'] ) ? intval( $instance['characters'] ) : WooCommerce_Product_Search::DEFAULT_CHARACTERS;
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The minimum number of characters required to start a search.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Characters', 'woocommerce-product-search' ) );
		echo '&nbsp;';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" style="width:3em;text-align:center"/>',
			esc_attr( $this->get_field_id( 'characters' ) ),
			esc_attr( $this->get_field_name( 'characters' ) ),
			esc_attr( $characters )
		);
		echo '</label>';
		echo '</p>';

		$inhibit_enter = isset( $instance['inhibit_enter'] ) ? $instance['inhibit_enter'] : 'no';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr(
				__( 'Inhibit form submission via the Enter key', 'woocommerce-product-search' ) .
				' ' .
				__( 'If the Enter key is not inhibited, a normal product search is requested when the visitor presses the Enter key in the search field.', 'woocommerce-product-search' )
			)
		);
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'inhibit_enter' ) ),
			esc_attr( $this->get_field_name( 'inhibit_enter' ) ),
			$inhibit_enter == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo wp_kses( __( 'Inhibit form submission', 'woocommerce-product-search' ), array( 'em' => array( ) ) );
		echo '</label>';
		echo '</p>';

		$navigable = isset( $instance['navigable'] ) ? $instance['navigable'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'If enabled, the visitor can use the cursor keys to navigate through the search results and visit a search result link by pressing the Enter key.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'navigable' ) ),
			esc_attr( $this->get_field_name( 'navigable' ) ),
			$navigable == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Navigable results', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$placeholder = isset( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Search', 'woocommerce-product-search' );
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The placeholder text for the search field.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Placeholder', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'placeholder' ) ),
			esc_attr( $this->get_field_name( 'placeholder' ) ),
			esc_attr( $placeholder )
		);
		echo '</label>';
		echo '</p>';

		$show_clear = isset( $instance['show_clear'] ) ? $instance['show_clear'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display a mark to clear the search.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'show_clear' ) ),
			esc_attr( $this->get_field_name( 'show_clear' ) ),
			$show_clear == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Clear mark', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$submit_button = isset( $instance['submit_button'] ) ? $instance['submit_button'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show a submit button along with the search field.', 'woocommerce-product-search' ) ) );
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

		$submit_button_label = isset( $instance['submit_button_label'] ) ? $instance['submit_button_label'] : __( 'Search', 'woocommerce-product-search' );
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

		$dynamic_focus = isset( $instance['dynamic_focus'] ) ? $instance['dynamic_focus'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show/hide search results when the search input field gains/loses focus.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'dynamic_focus' ) ),
			esc_attr( $this->get_field_name( 'dynamic_focus' ) ),
			$dynamic_focus == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Dynamic focus', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$floating = isset( $instance['floating'] ) ? $instance['floating'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Search results are shown floating below the search field.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'floating' ) ),
			esc_attr( $this->get_field_name( 'floating' ) ),
			$floating == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Floating results', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$no_results = isset( $instance['no_results'] ) ? $instance['no_results'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The text shown when no search results are obtained.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'No results', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'no_results' ) ),
			esc_attr( $this->get_field_name( 'no_results' ) ),
			esc_attr( $no_results )
		);
		echo '</label>';
		echo '</p>';

		$height = !empty( $instance['height'] ) ? WooCommerce_Product_Search_Utility::get_css_unit( $instance['height'] ) : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'Determines the height of the results container.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Height', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'height' ) ),
			esc_attr( $this->get_field_name( 'height' ) ),
			esc_attr( $height ),
			__( 'Full content height', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$wpml = isset( $instance['wpml'] ) ? $instance['wpml'] : 'no';
			echo '<p>';
			echo sprintf( '<label title="%s">', esc_attr( __( 'Filter search results based on the current language.', 'woocommerce-product-search' ) ) );
			printf(
				'<input type="checkbox" id="%s" name="%s" %s />',
				esc_attr( $this->get_field_id( 'wpml' ) ),
				esc_attr( $this->get_field_name( 'wpml' ) ),
				$wpml == 'yes' ? ' checked="checked" ' : ''
			);
			echo ' ';
			echo esc_html( __( 'WMPL Language Filter', 'woocommerce-product-search' ) );
			echo '</label>';
			echo '</p>';
		}

		if ( $this->id_base === 'wps-auto-instance' ) {
			echo '</td>';
			echo '</tr>';
			echo '</table>';
		}
	}

	public static function get_auto_instance_default() {
		return array(
			'query_title'         => 'yes',
			'excerpt'             => 'yes',
			'content'             => 'yes',
			'categories'          => 'yes',
			'attributes'          => 'yes',
			'tags'                => 'yes',
			'sku'                 => 'yes',
			'order'               => 'DESC',
			'order_by'            => 'date',
			'limit'               => WooCommerce_Product_Search_Service::DEFAULT_LIMIT,
			'height'              => '',
			'show_more'           => 'yes',
			'category_results'    => 'yes',
			'category_limit'      => WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT,
			'product_thumbnails'  => 'yes',
			'show_description'    => 'yes',
			'show_price'          => 'yes',
			'show_add_to_cart'    => 'yes',
			'show_clear'          => 'yes',
			'delay'               => WooCommerce_Product_Search::DEFAULT_DELAY,
			'characters'          => WooCommerce_Product_Search::DEFAULT_CHARACTERS,
			'placeholder'         => __( 'Search products &hellip;', 'woocommerce-product-search' ),
			'dynamic_focus'       => 'yes',
			'floating'            => 'yes',
			'inhibit_enter'       => 'no',
			'submit_button'       => 'no',
			'submit_button_label' => __( 'Search', 'woocommerce-product-search' ),
			'navigable'           => 'yes',
			'no_results'          => '',
			'wpml'                => 'no'
		);
	}
}

WooCommerce_Product_Search_Widget::init();
