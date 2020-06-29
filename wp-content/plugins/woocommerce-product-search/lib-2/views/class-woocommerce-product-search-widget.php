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
		self::$the_name = __( 'WooCommerce Instant Product Search', 'woocommerce-product-search' );
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
	public function __construct() {
		parent::__construct(
			self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'A dynamic product search widget', 'woocommerce-product-search' )
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

		WooCommerce_Product_Search_Shortcodes::load_resources();

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
		$instance['title'] = $instance['query_title'];
		$output .= WooCommerce_Product_Search_Shortcodes::woocommerce_product_search( $instance );
		$output .= $after_widget;

		echo $output;


		$cache[$args['widget_id']] = $output;
		wp_cache_set( self::$cache_id, $cache, self::$cache_group );

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

		$settings['title'] = trim( strip_tags( $new_instance['title'] ) );

		$settings['query_title']   = !empty( $new_instance['query_title'] ) ? 'yes' : 'no';
		$settings['excerpt'] = !empty( $new_instance['excerpt'] ) ? 'yes' : 'no';
		$settings['content'] = !empty( $new_instance['content'] ) ? 'yes' : 'no';
		$settings['tags']    = !empty( $new_instance['tags'] ) ? 'yes' : 'no';
		$settings['sku']    = !empty( $new_instance['sku'] ) ? 'yes' : 'no';

		$settings['order']    = !empty( $new_instance['order'] ) ? $new_instance['order'] : 'DESC';
		$settings['order_by'] = !empty( $new_instance['order_by'] ) ? $new_instance['order_by'] : 'date';

		$limit = !empty( $new_instance['limit'] ) ? intval( $new_instance['limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_LIMIT;
		if ( $limit < 0 ) {
			$limit = WooCommerce_Product_Search_Service::DEFAULT_LIMIT;
		}
		$settings['limit'] = $limit;

		$settings['category_results'] = !empty( $new_instance['category_results'] ) ? 'yes' : 'no';
		$category_limit = !empty( $new_instance['category_limit'] ) ? intval( $new_instance['category_limit'] ) : WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT;
		if ( $category_limit < 0 ) {
			$category_limit = WooCommerce_Product_Search_Service::DEFAULT_CATEGORY_LIMIT;
		}
		$settings['category_limit'] = $category_limit;

		$settings['product_thumbnails'] = !empty( $new_instance['product_thumbnails'] ) ? 'yes' : 'no';

		$settings['show_description'] = !empty( $new_instance['show_description'] ) ? 'yes' : 'no';

		$settings['show_price'] = !empty( $new_instance['show_price'] ) ? 'yes' : 'no';

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

		$settings['placeholder'] = trim( strip_tags( $new_instance['placeholder'] ) );

		$settings['dynamic_focus'] = !empty( $new_instance['dynamic_focus'] ) ? 'yes' : 'no';
		$settings['floating']      = !empty( $new_instance['floating'] ) ? 'yes' : 'no';
		$settings['inhibit_enter'] = !empty( $new_instance['inhibit_enter'] ) ? 'yes' : 'no';
		$settings['submit_button'] = !empty( $new_instance['submit_button'] ) ? 'yes' : 'no';
		$settings['submit_button_label'] = strip_tags( $new_instance['submit_button_label'] );
		$settings['navigable']     = !empty( $new_instance['navigable'] ) ? 'yes' : 'no';
		$settings['no_results']    = trim( strip_tags( $new_instance['no_results'] ) );
		$settings['auto_adjust']   = !empty( $new_instance['auto_adjust'] ) ? 'yes' : 'no';

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

		$widget_title = isset( $instance['title'] ) ? $instance['title'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The widget title.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Title', 'woocommerce-product-search' ) );
		echo '<input class="widefat" id="' . esc_attr( $this->get_field_id( 'title' ) ) . '" name="' . esc_attr( $this->get_field_name( 'title' ) ) . '" type="text" value="' . esc_attr( $widget_title ) . '" />';
		echo '</label>';
		echo '</p>';

		echo '<h5>' . esc_html( __( 'Search Results', 'woocommerce-product-search' ) ) . '</h5>';

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

		$sku = isset( $instance['sku'] ) ? $instance['sku'] : 'false';
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
			'date'  => __( 'Date', 'woocommerce-product-search' ),
			'title' => __( 'Title', 'woocommerce-product-search' ),
			'ID'    => __( 'ID', 'woocommerce-product-search' ),
			'rand'  => __( 'Random', 'woocommerce-product-search' )
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
		echo '<input id="' . esc_attr( $this->get_field_id( 'limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'limit' ) ) . '" type="text" value="' . esc_attr( $limit ) . '" />';
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
		echo '<input id="' . esc_attr( $this->get_field_id( 'category_limit' ) ) . '" name="' . esc_attr( $this->get_field_name( 'category_limit' ) ) . '" type="text" value="' . esc_attr( $category_limit ) . '" />';
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

		$show_description = isset( $instance['show_description'] ) ? $instance['show_description'] : 'no';
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

		$show_price = isset( $instance['show_price'] ) ? $instance['show_price'] : 'no';
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

		echo '<h5>' . esc_html( __( 'Search Form and UI Interaction', 'woocommerce-product-search' ) ) . '</h5>';

		$delay = isset( $instance['delay'] ) ? intval( $instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		echo '<p>';

		echo sprintf( '<label title="%s">', esc_attr( __( 'The delay until the search starts after the user stops typing (in milliseconds, minimum %d).', 'woocommerce-product-search' ), WooCommerce_Product_Search::MIN_DELAY ) );
		echo esc_html( __( 'Delay', 'woocommerce-product-search' ) );
		echo ' ';
		echo '<input id="' . esc_attr( $this->get_field_id( 'delay' ) ) . '" name="' . esc_attr( $this->get_field_name( 'delay' ) ) . '" type="text" value="' . esc_attr( $delay ) . '" />';
		echo ' ';
		echo esc_html( __( 'ms', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$characters = isset( $instance['characters'] ) ? intval( $instance['characters'] ) : WooCommerce_Product_Search::DEFAULT_CHARACTERS;
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'The minimum number of characters required to start a search.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Characters', 'woocommerce-product-search' ) );
		echo ' ';
		echo '<input id="' . esc_attr( $this->get_field_id( 'characters' ) ) . '" name="' . esc_attr( $this->get_field_name( 'characters' ) ) . '" type="text" value="' . esc_attr( $characters ) . '" />';
		echo '</label>';
		echo '</p>';

		$inhibit_enter = isset( $instance['inhibit_enter'] ) ? $instance['inhibit_enter'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'If the Enter key is not inhibited, a normal product search is requested when the visitor presses the Enter key in the search field.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'inhibit_enter' ) ),
			esc_attr( $this->get_field_name( 'inhibit_enter' ) ),
			$inhibit_enter == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Inhibit form submission via the <em>Enter</em> key', 'woocommerce-product-search' ) );
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
		echo '<input id="' . esc_attr( $this->get_field_id( 'placeholder' ) ) . '" name="' . esc_attr( $this->get_field_name( 'placeholder' ) ) . '" type="text" value="' . esc_attr( $placeholder ) . '" />';
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
		echo '<input id="' . esc_attr( $this->get_field_id( 'submit_button_label' ) ) . '" name="' . esc_attr( $this->get_field_name( 'submit_button_label' ) ) . '" type="text" value="' . esc_attr( $submit_button_label ) . '" />';
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
		echo '<input id="' . esc_attr( $this->get_field_id( 'no_results' ) ) . '" name="' . esc_attr( $this->get_field_name( 'no_results' ) ) . '" type="text" value="' . esc_attr( $no_results ) . '" />';
		echo '</label>';
		echo '</p>';

		$auto_adjust = isset( $instance['auto_adjust'] ) ? $instance['auto_adjust'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Automatically adjust the width of the results to match that of the search field.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'auto_adjust' ) ),
			esc_attr( $this->get_field_name( 'auto_adjust' ) ),
			$auto_adjust == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Auto-adjust results width', 'woocommerce-product-search' ) );
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
	}

}

WooCommerce_Product_Search_Widget::init();
