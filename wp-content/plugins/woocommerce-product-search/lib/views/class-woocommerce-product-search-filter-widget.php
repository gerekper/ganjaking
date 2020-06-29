<?php
/**
 * class-woocommerce-product-search-filter-widget.php
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
class WooCommerce_Product_Search_Filter_Widget extends WP_Widget {

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
	private static $cache_id = 'woocommerce_product_search_filter_widget';

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
		self::$the_name = __( 'Product Filter &ndash; Search', 'woocommerce-product-search' );
	}

	/**
	 * Registers the widget.
	 */
	public static function widgets_init() {
		register_widget( 'WooCommerce_Product_Search_Filter_Widget' );
	}

	/**
	 * Creates the widget.
	 */
	public function __construct() {
		parent::__construct(
			self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'WooCommerce Product Search &mdash; The live product search filter for your shop. It updates the products displayed instantly, to show those that match the indicated search terms.', 'woocommerce-product-search' )
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

		WooCommerce_Product_Search_Filter::load_resources();

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
		$instance['title'] = isset( $instance['query_title'] ) ? $instance['query_title'] : 'yes';
		$instance['style'] = isset( $instance['main_container_style'] ) ? $instance['main_container_style'] : '';
		$output .= WooCommerce_Product_Search_Filter::render( $instance );
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

		$settings['title'] = !empty( $new_instance['title'] ) ? trim( strip_tags( $new_instance['title'] ) ) : '';

		$settings['heading']            = !empty( $new_instance['heading'] ) ? trim( strip_tags( $new_instance['heading'] ) ) : null;
		$settings['heading_class']      = !empty( $new_instance['heading_class'] ) ? trim( $new_instance['heading_class'] ) : '';
		$settings['heading_id']         = !empty( $new_instance['heading_id'] ) ? trim( $new_instance['heading_id'] ) : '';
		$settings['heading_element']    = !empty( $new_instance['heading_element'] ) ? trim( $new_instance['heading_element'] ) : '';
		$settings['show_heading']       = !empty( $new_instance['show_heading'] ) ? 'yes' : 'no';

		$settings['query_title'] = !empty( $new_instance['query_title'] ) ? 'yes' : 'no';
		$settings['excerpt']     = !empty( $new_instance['excerpt'] ) ? 'yes' : 'no';
		$settings['content']     = !empty( $new_instance['content'] ) ? 'yes' : 'no';
		$settings['categories']  = !empty( $new_instance['categories'] ) ? 'yes' : 'no';
		$settings['attributes']  = !empty( $new_instance['attributes'] ) ? 'yes' : 'no';
		$settings['tags']        = !empty( $new_instance['tags'] ) ? 'yes' : 'no';
		$settings['sku']         = !empty( $new_instance['sku'] ) ? 'yes' : 'no';

		$settings['shop_only'] = !empty( $new_instance['shop_only'] ) ? 'yes' : 'no';

		$settings['order']    = !empty( $new_instance['order'] ) ? $new_instance['order'] : '';
		if ( empty( $settings['order'] ) ) {
			unset( $settings['order'] );
		}
		$settings['order_by'] = !empty( $new_instance['order_by'] ) ? $new_instance['order_by'] : '';
		if ( empty( $settings['order_by'] ) ) {
			unset( $settings['order_by'] );
		}

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

		$settings['placeholder']         = isset( $new_instance['placeholder'] ) ? trim( strip_tags( $new_instance['placeholder'] ) ) : __( 'Search', 'woocommerce-product-search' );
		$settings['show_clear']          = !empty( $new_instance['show_clear'] ) ? 'yes' : 'no';
		$settings['submit_button']       = !empty( $new_instance['submit_button'] ) ? 'yes' : 'no';
		$settings['submit_button_label'] = isset( $new_instance['submit_button_label'] ) ? strip_tags( $new_instance['submit_button_label'] ) : __( 'Search', 'woocommerce-product-search' );

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$settings['wpml']   = !empty( $new_instance['wpml'] ) ? 'yes' : 'no';
		}

		$settings['update_address_bar']    = !empty( $new_instance['update_address_bar'] ) ? 'yes' : 'no';
		$settings['update_document_title'] = !empty( $new_instance['update_document_title'] ) ? 'yes' : 'no';
		$settings['use_shop_url']          = !empty( $new_instance['use_shop_url'] ) ? 'yes' : 'no';

		$containers = array(
			'breadcrumb_container'      => '.woocommerce-breadcrumb',
			'products_header_container' => '.woocommerce-products-header',
			'products_container'        => '.products',
			'product_container'         => '.product',
			'info_container'            => '.woocommerce-info',
			'ordering_container'        => '.woocommerce-ordering',
			'pagination_container'      => '.woocommerce-pagination',
			'result_count_container'    => '.woocommerce-result-count'
		);
		foreach( $containers as $key => $selector ) {
			$value = !empty( $new_instance[$key] ) ? $new_instance[$key] : $selector;
			$value = preg_replace( '/[^a-zA-Z0-9 _.#-]/', '', $value );
			$value = trim( $value );
			if ( empty( $value ) ) {
				$value = $selector;
			}
			$settings[$key] = $value;
		}

		$settings['main_container_style'] = !empty( $new_instance['main_container_style'] ) ? trim( $new_instance['main_container_style'] ) : '';

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
		esc_html_e( 'Search Results', 'woocommerce-product-search' );
		echo '</h4>';

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
			$this->get_field_id( 'excerpt' ),
			$this->get_field_name( 'excerpt' ),
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

		echo '<h4>';
		esc_html_e( 'User Interface', 'woocommerce-product-search' );
		echo '</h4>';

		$delay = isset( $instance['delay'] ) ? intval( $instance['delay'] ) : WooCommerce_Product_Search::DEFAULT_DELAY;
		echo '<p>';

		echo sprintf( '<label title="%s">', esc_attr( __( 'The delay until the search starts after the user stops typing (in milliseconds, minimum %d).', 'woocommerce-product-search' ), WooCommerce_Product_Search::MIN_DELAY ) );
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
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display a shortcut to clear the search field when it contains text.', 'woocommerce-product-search' ) ) );
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

		$update_address_bar = isset( $instance['update_address_bar'] ) ? $instance['update_address_bar'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Update the address bar of the browser based on the active filters.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'update_address_bar' ) ),
			esc_attr( $this->get_field_name( 'update_address_bar' ) ),
			$update_address_bar== 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Update the Address Bar', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$update_document_title = isset( $instance['update_document_title'] ) ? $instance['update_document_title'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Update the document title for the browser based on the active filters.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			esc_attr( $this->get_field_id( 'update_document_title' ) ),
			esc_attr( $this->get_field_name( 'update_document_title' ) ),
			$update_document_title== 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Update the Document Title', 'woocommerce-product-search' ) );
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

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			echo '<h4>';
			esc_html_e( 'WPML', 'woocommerce-product-search' );
			echo'</h4>';
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

		echo '<h4>';
		esc_html_e( 'Heading', 'woocommerce-product-search' );
		echo '</h4>';

		$show_heading = isset( $instance['show_heading'] ) ? $instance['show_heading'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the heading before the terms.', 'woocommerce-product-search' ) ) );
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

		$n = md5( time() . rand() );
		$trigger_id = 'containers-trigger-'. $n;
		$target_id = 'containers-target-' . $n;

		echo '<h4>';
		esc_html_e( 'Advanced', 'woocommerce-product-search' );
		printf( '<span id="%s" style="cursor:pointer">&emsp;&hellip;</span>', $trigger_id );
		echo '</h4>';

		printf( '<div id="%s">', $target_id );

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

		$containers = array(
			'breadcrumb_container'      => '.woocommerce-breadcrumb',
			'products_header_container' => '.woocommerce-products-header',
			'products_container'        => '.products',
			'product_container'         => '.product',
			'info_container'            => '.woocommerce-info',
			'ordering_container'        => '.woocommerce-ordering',
			'pagination_container'      => '.woocommerce-pagination',
			'result_count_container'    => '.woocommerce-result-count'
		);
		foreach( $containers as $key => $selector ) {
			$value = isset( $instance[$key] ) ? $instance[$key] : $selector;
			echo '<p>';
			echo sprintf( '<label title="%s">', esc_attr( $key ) );
			echo esc_html__( ucwords( str_replace( '_', ' ', $key ) ), 'woocommerce-product-search' );
			echo ' ';
			printf(
				'<input id="%s" name="%s" placeholder="%s" value="%s" type="text" class="widefat"/>',
				esc_attr( $this->get_field_id( $key ) ),
				esc_attr( $this->get_field_name( $key ) ),
				esc_attr( $selector ),
				esc_attr( $value )
			);
			echo '</label>';
			echo '</p>';
		}
		$style = isset( $instance['main_container_style'] ) ? $instance['main_container_style'] : '';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr__( 'Inline style of the surrounding container.', 'woocommerce-product-search' ) );
		echo esc_html__( 'Container Inline Style', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<textarea id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'main_container_style' ) ),
			esc_attr( $this->get_field_name( 'main_container_style' ) )
		);
		echo esc_attr( $style );
		echo  '</textarea>';
		echo '</label>';
		echo '</p>';
		echo '</div>'; 

		echo '<script type="text/javascript">';
		echo 'document.addEventListener( "DOMContentLoaded", function() {';
		echo 'if ( typeof jQuery !== "undefined" ) {';
		printf( 'jQuery("#%s").css("display","none");', esc_attr( $target_id ) );
		printf( 'jQuery(document).on( "click", "#%s", function() {', esc_attr( $trigger_id ) );
		printf( 'jQuery("#%s").toggle();', esc_attr( $target_id ) );
		echo '});'; 
		echo '}'; 
		echo '} );'; 
		echo '</script>';
	}

	public function get_default_instance() {
		return array(
			'title' => '',
			'query_title' => true,
			'excerpt' => true,
			'content' => true,
			'categories' => true,
			'tags' => true,
			'attributes' => true,
			'sku' => true,
			'shop_only' => true,
			'delay'=> WooCommerce_Product_Search::DEFAULT_DELAY,
			'characters' => WooCommerce_Product_Search::DEFAULT_CHARACTERS,
			'placeholder' => __( 'Search', 'woocommerce-product-search' ),
			'show_clear' => true,
			'submit_button' => false,
			'submit_button_label' => __( 'Search', 'woocommerce-product-search' ),
			'update_address_bar' => true,
			'update_document_title' => false,
			'use_shop_url' => false,
			'wpml' => false,
			'show_heading' => false,
			'heading' => '',
			'heading_id' => '',
			'heading_class' => '',
			'heading_element' => '',
			'breadcrumb_container'      => '.woocommerce-breadcrumb',
			'products_header_container' => '.woocommerce-products-header',
			'products_container'        => '.products',
			'product_container'         => '.product',
			'info_container'            => '.woocommerce-info',
			'ordering_container'        => '.woocommerce-ordering',
			'pagination_container'      => '.woocommerce-pagination',
			'result_count_container'    => '.woocommerce-result-count',
			'main_container_style' => '',
		);
	}
}

WooCommerce_Product_Search_Filter_Widget::init();
