<?php
/**
 * class-woocommerce-product-search-filter-tag-widget.php
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
class WooCommerce_Product_Search_Filter_Tag_Widget extends WP_Widget {

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
	private static $cache_id = 'woocommerce_product_search_filter_tag_widget';

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
		self::$the_name = __( 'Product Filter &ndash; Tags', 'woocommerce-product-search' );
	}

	/**
	 * Registers the widget.
	 */
	public static function widgets_init() {
		register_widget( 'WooCommerce_Product_Search_Filter_Tag_Widget' );
	}

	/**
	 * Creates the widget.
	 */
	public function __construct() {
		parent::__construct(
			self::$cache_id,
			self::$the_name,
			array(
				'description' => __( 'WooCommerce Product Search &mdash; This is the live product tag filter for your shop. It updates the products shown instantly, to include those that are related to the selected tags.', 'woocommerce-product-search' )
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

		WooCommerce_Product_Search_Filter_Tag::load_resources();

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
		$output .= WooCommerce_Product_Search_Filter_Tag::render( $instance );
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

		$settings['title'] = trim( strip_tags( $new_instance['title'] ) );

		$settings['heading']            = !empty( $new_instance['heading'] ) ? trim( strip_tags( $new_instance['heading'] ) ) : null;
		$settings['heading_no_results'] = !empty( $new_instance['heading_no_results'] ) ? trim( strip_tags( $new_instance['heading_no_results'] ) ) : '';
		$settings['heading_class']      = !empty( $new_instance['heading_class'] ) ? trim( $new_instance['heading_class'] ) : '';
		$settings['heading_id']         = !empty( $new_instance['heading_id'] ) ? trim( $new_instance['heading_id'] ) : '';
		$settings['heading_element']    = !empty( $new_instance['heading_element'] ) ? trim( $new_instance['heading_element'] ) : '';
		$settings['show_heading']       = !empty( $new_instance['show_heading'] ) ? 'yes' : 'no';
		$settings['toggle']             = !empty( $new_instance['toggle'] ) ? 'yes' : 'no';
		$settings['toggle_widget']      = !empty( $new_instance['toggle_widget'] ) ? 'yes' : 'no';

		$settings['show_thumbnails'] = !empty( $new_instance['show_thumbnails'] ) ? 'yes' : 'no';
		$settings['show_names']      = !empty( $new_instance['show_names'] ) ? 'yes' : 'no';

		$settings['shop_only'] = !empty( $new_instance['shop_only'] ) ? 'yes' : 'no';

		$style = !empty( $new_instance['style'] ) ? $new_instance['style'] : 'inline';
		switch( $style ) {
			case 'list' :
			case 'inline' :
				break;
			default :
				$style = 'inline';
		}
		$settings['style'] = $style;

		$number = !empty( $new_instance['number'] ) ? intval( $new_instance['number'] ) : WooCommerce_Product_Search_Filter_Tag::DEFAULT_NUMBER;
		if ( $number < 1 ) {
			$number = 1;
		}
		$settings['number'] = $number;

		$settings['order']   = !empty( $new_instance['order'] ) ? $new_instance['order'] : 'ASC';
		$settings['orderby'] = !empty( $new_instance['orderby'] ) ? $new_instance['orderby'] : 'name';

		$settings['filter']       = !empty( $new_instance['filter'] ) ? 'yes' : 'no';
		$settings['hide_empty']   = !empty( $new_instance['hide_empty'] ) ? 'yes' : 'no';
		$settings['multiple']     = !empty( $new_instance['multiple'] ) ? 'yes' : 'no';
		$settings['show_count']   = !empty( $new_instance['show_count'] ) ? 'yes' : 'no';

		$show = !empty( $new_instance['show'] ) ? $new_instance['show'] : 'set';
		switch( $show ) {
			case 'all' :
			case 'set' :
				break;
			default :
				$show = 'set';
		}
		$settings['show'] = $show;

		$sizing = !empty( $new_instance['sizing'] ) ? $new_instance['sizing'] : 'none';
		switch( $sizing ) {
			case 'auto' :
			case 'none' :
				break;
			default :
				$sizing = 'none';
		}
		$settings['sizing'] = $sizing;

		$thumbnail_sizing_factor = !empty( $new_instance['thumbnail_sizing_factor'] ) ? floatval( $new_instance['thumbnail_sizing_factor'] ) : WooCommerce_Product_Search_Filter_Tag::DEFAULT_THUMBNAIL_SIZING_FACTOR;
		if ( $thumbnail_sizing_factor <= 0 ) {
			$thumbnail_sizing_factor = WooCommerce_Product_Search_Filter_Tag::DEFAULT_THUMBNAIL_SIZING_FACTOR;
		}
		$settings['thumbnail_sizing_factor'] = $thumbnail_sizing_factor;

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
		printf(
			'<label title="%s">',
			esc_attr__( 'The widget title.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Title', 'woocommerce-product-search' );
		printf(
			'<input class="widefat" id="%s" name="%s" type="text" value="%s" placeholder="%s"/>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $widget_title ),
			esc_attr__( 'None', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		esc_html_e( 'Heading', 'woocommerce-product-search' );
		echo '</h4>';

		$show_heading = isset( $instance['show_heading'] ) ? $instance['show_heading'] : 'yes';
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

		$heading_no_results = isset( $instance['heading_no_results'] ) ? $instance['heading_no_results'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'The alternative text displayed in the heading when there are no results.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Heading for no results', 'woocommerce-product-search' );
		printf(
			'<input class="widefat" id="%s" name="%s" type="text" value="%s"/>',
			esc_attr( $this->get_field_id( 'heading_no_results' ) ),
			esc_attr( $this->get_field_name( 'heading_no_results' ) ),
			esc_attr( $heading_no_results )
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

		$style = isset( $instance['style'] ) ? $instance['style'] : 'inline';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the tags as a list or inline.', 'woocommerce-product-search' ) ) );
		echo esc_html__( 'Style', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<select id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'style' ) ),
			esc_attr( $this->get_field_name( 'style' ) )
		);
		$options = array(
			'inline' => __( 'Inline', 'woocommerce-product-search' ),
			'list'   => __( 'List', 'woocommerce-product-search' )
		);
		foreach ( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $style == $key ? ' selected="selected" ' : '', esc_html( $value ) );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		$show_thumbnails = isset( $instance['show_thumbnails'] ) ? $instance['show_thumbnails'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show the thumbnails for attribute terms.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'show_thumbnails' ),
			$this->get_field_name( 'show_thumbnails' ),
			$show_thumbnails == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show Thumbnails', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$sizing = isset( $instance['sizing'] ) ? $instance['sizing'] : 'none';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'This option determines whether the size of entries is adjusted automatically or a uniform size is used.', 'woocommerce-product-search' ) ) );
		echo esc_html__( 'Sizing', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<select id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'sizing' ) ),
			esc_attr( $this->get_field_name( 'sizing' ) )
		);
		$options = array(
			'auto' => __( 'Automatic', 'woocommerce-product-search' ),
			'none' => __( 'Uniform', 'woocommerce-product-search' )
		);
		foreach ( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $sizing == $key ? ' selected="selected" ' : '', esc_html( $value ) );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		$thumbnail_sizing_factor = isset( $instance['thumbnail_sizing_factor'] ) ? floatval( $instance['thumbnail_sizing_factor'] ) : WooCommerce_Product_Search_Filter_Tag::DEFAULT_THUMBNAIL_SIZING_FACTOR;
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'This setting determines the relation between the size of thumbnails and the size of their terms.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Thumbnail sizing factor', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'thumbnail_sizing_factor' ) ),
			esc_attr( $this->get_field_name( 'thumbnail_sizing_factor' ) ),
			esc_attr( $thumbnail_sizing_factor )
		);
		echo '</label>';
		echo '</p>';

		$show_names = isset( $instance['show_names'] ) ? $instance['show_names'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Show the names of attribute terms.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'show_names' ),
			$this->get_field_name( 'show_names' ),
			$show_names == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show Names', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show_count= isset( $instance['show_count'] ) ? $instance['show_count'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Display the related number of products for each term.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'show_count' ),
			$this->get_field_name( 'show_count' ),
			$show_count== 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Show product counts', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Do not display terms that have no related products.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'hide_empty' ),
			$this->get_field_name( 'hide_empty' ),
			$hide_empty == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Hide terms without products', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$number = isset( $instance['number'] ) ? intval( $instance['number'] ) : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'Limits the number of terms shown.', 'woocommerce-product-search' )
		);
		echo esc_html( __( 'Number of terms', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'number' ) ),
			esc_attr( $this->get_field_name( 'number' ) ),
			esc_attr( $number ),
			__( 'Minimum one', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		$toggle = isset( $instance['toggle'] ) ? $instance['toggle'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Hide the filter component when it has no options.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'toggle' ),
			$this->get_field_name( 'toggle' ),
			$toggle == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Toggle the component', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$toggle_widget = isset( $instance['toggle_widget'] ) ? $instance['toggle_widget'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Hide the widget when the component has no options.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'toggle_widget' ),
			$this->get_field_name( 'toggle_widget' ),
			$toggle_widget == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Toggle the widget', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		esc_html_e( 'Order', 'woocommerce-product-search' );
		echo '</h4>';

		$orderby = isset( $instance['orderby'] ) ? $instance['orderby'] : 'term_order';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Order the results by the chosen property.', 'woocommerce-product-search' ) ) );
		echo esc_html( __( 'Order by ...', 'woocommerce-product-search' ) );
		echo ' ';
		printf(
			'<select id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'orderby' ) ),
			esc_attr( $this->get_field_name( 'orderby' ) )
		);
		$options = array(
			'name'       => __( 'Name', 'woocommerce-product-search' ),
			'count'      => __( 'Count', 'woocommerce-product-search' )
		);
		foreach ( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $orderby == $key ? ' selected="selected" ' : '', esc_html( $value ) );
		}
		echo '</select>';
		echo '</label>';
		echo '</p>';

		$order = isset( $instance['order'] ) ? $instance['order'] : 'ASC';
		echo '<p>';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="ASC" %s />', esc_attr( $this->get_field_name( 'order' ) ), $order == 'ASC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Ascending', 'woocommerce-product-search' ) );
		echo '</label>';
		echo ' ';
		echo '<label>';
		printf( '<input type="radio" name="%s" value="DESC" %s/>', esc_attr( $this->get_field_name( 'order' ) ), $order == 'DESC' ? ' checked="checked" ' : '' );
		echo ' ';
		echo esc_html( __( 'Descending', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		echo '<h4>';
		_e( 'Terms', 'woocommerce-product-search' );
		echo '</h4>';

		$filter = isset( $instance['filter'] ) ? $instance['filter'] : 'yes';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Activate live filtering.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'filter' ),
			$this->get_field_name( 'filter' ),
			$filter == 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Filter', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$multiple = isset( $instance['multiple'] ) ? $instance['multiple'] : 'no';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'Allow multiple choices.', 'woocommerce-product-search' ) ) );
		printf(
			'<input type="checkbox" id="%s" name="%s" %s />',
			$this->get_field_id( 'multiple' ),
			$this->get_field_name( 'multiple' ),
			$multiple== 'yes' ? ' checked="checked" ' : ''
		);
		echo ' ';
		echo esc_html( __( 'Multiple', 'woocommerce-product-search' ) );
		echo '</label>';
		echo '</p>';

		$show = isset( $instance['show'] ) ? $instance['show'] : 'set';
		echo '<p>';
		echo sprintf( '<label title="%s">', esc_attr( __( 'With \'Set\', where a single term restricts the selection to itself or its children, related terms are not included.', 'woocommerce-product-search' ) ) );
		echo esc_html__( 'Show', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<select id="%s" name="%s" class="widefat">',
			esc_attr( $this->get_field_id( 'show' ) ),
			esc_attr( $this->get_field_name( 'show' ) )
		);
		$options = array(
			'set' => __( 'Set', 'woocommerce-product-search' ),
			'all' => __( 'All', 'woocommerce-product-search' )
		);
		foreach ( $options as $key => $value ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), $show == $key ? ' selected="selected" ' : '', esc_html( $value ) );
		}
		echo '</select>';
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

		$container_id = isset( $instance['container_id'] ) ? $instance['container_id'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'ID of the filter\'s main div container.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Container ID', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'container_id' ) ),
			esc_attr( $this->get_field_name( 'container_id' ) ),
			esc_attr( $container_id ),
			esc_attr__( 'Automatic', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';

		$container_class = isset( $instance['container_class'] ) ? $instance['container_class'] : '';
		echo '<p>';
		printf(
			'<label title="%s">',
			esc_attr__( 'CSS class added to the filter\'s main div container.', 'woocommerce-product-search' )
		);
		echo esc_html__( 'Container Class', 'woocommerce-product-search' );
		echo ' ';
		printf(
			'<input id="%s" name="%s" type="text" value="%s" placeholder="%s" class="widefat"/>',
			esc_attr( $this->get_field_id( 'container_class' ) ),
			esc_attr( $this->get_field_name( 'container_class' ) ),
			esc_attr( $container_class ),
			esc_attr__( 'Automatic', 'woocommerce-product-search' )
		);
		echo '</label>';
		echo '</p>';
	}

	public function get_default_instance() {
		return array(
			'title' => '',
			'show_heading' => true,
			'heading' => '',
			'heading_no_results' => '',
			'shop_only' => true,
			'style'=> 'inline',
			'show_thumbnails' => false,
			'sizing' => 'none',
			'thumbnail_sizing_factor' => WooCommerce_Product_Search_Filter_Tag::DEFAULT_THUMBNAIL_SIZING_FACTOR,
			'show_names' => true,
			'show_count' => false,
			'hide_empty' => true,
			'number' => '',
			'orderby' => 'term_order',
			'order' => 'ASC',
			'filter' => true,
			'multiple' => false,
			'show' => 'set',
			'heading_id' => '',
			'heading_class' => '',
			'heading_element' => '',
			'container_id' => '',
			'container_class' => '',
			'toggle' => true,
			'toggle_widget' => true
		);
	}
}

WooCommerce_Product_Search_Filter_Tag_Widget::init();
