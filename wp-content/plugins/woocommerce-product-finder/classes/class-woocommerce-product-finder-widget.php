<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooThemes Product Finder Widget
 *
 * @package WordPress
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * var $woo_widget_cssclass
 * var $woo_widget_description
 * var $woo_widget_idbase
 * var $woo_widget_title
 *
 * - __construct()
 * - widget()
 * - update()
 * - form()
 */
class WC_Product_Finder_Widget extends WP_Widget {
	private $woo_widget_cssclass;
	private $woo_widget_description;
	private $woo_widget_idbase;
	private $woo_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->woo_widget_cssclass = 'widget_product_finder';
		$this->woo_widget_description = __( 'Customisable Product Finder widget.', 'woocommerce-product-finder' );
		$this->woo_widget_idbase = 'product_finder';
		$this->woo_widget_title = __( 'WooCommerce Product Finder', 'woocommerce-product-finder' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->woo_widget_cssclass, 'description' => $this->woo_widget_description );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => $this->woo_widget_idbase );

		/* Create the widget. */
		parent::__construct( $this->woo_widget_idbase, $this->woo_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_top' );

		// Get selected attributes
		$used_atts = array();
		foreach ( $instance as $k => $v ) {
			if ( ! in_array( $k , array( 'title', 'use_category' ) ) ) {
				if ( is_bool( $v ) && $v ) {
					$att_name = str_replace( 'use_att_pa_' , '' , $k );
					array_push( $used_atts, $att_name );
				}
			}
		}

		// Load search form.
		$html = woocommerce_product_finder(
			array(
				'use_category'      => $instance['use_category'],
				'search_attributes' => $used_atts,
			),
			false
		);

		echo $html;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->woo_widget_cssclass . '_bottom' );

		/* After widget (defined by themes). */
		echo $after_widget;
	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		global $woocommerce;

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['use_category'] = (bool) esc_attr( $new_instance['use_category'] );

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$att_list = wc_get_attribute_taxonomies();
		} else {
			$att_list = $woocommerce->get_attribute_taxonomies();
		}

		if ( $att_list && is_array( $att_list ) && count( $att_list ) > 0 ) {
			foreach ( $att_list as $att ) {
				if ( isset( $att->attribute_name ) && strlen( $att->attribute_name ) > 0 ) {
					if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
						$tax_name = wc_attribute_taxonomy_name( $att->attribute_name );
					} else {
						$tax_name = $woocommerce->attribute_taxonomy_name( $att->attribute_name );
					}
					$field_id = 'use_att_' . $tax_name;
					$instance[ $field_id ] = (bool) esc_attr( $new_instance[ $field_id ] );
				}
			}
		}

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
	public function form( $instance ) {
		global $woocommerce;

		if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
			$att_list = wc_get_attribute_taxonomies();
		} else {
			$att_list = $woocommerce->get_attribute_taxonomies();
		}

		/* Set up some default widget settings. */
		$defaults = array(
			'title'        => __( 'Product Finder', 'woocommerce-product-finder' ),
			'use_category' => 1,
		);

		if ( $att_list && is_array( $att_list ) && count( $att_list ) > 0 ) {
			foreach ( $att_list as $att ) {
				if ( isset( $att->attribute_name ) && strlen( $att->attribute_name ) > 0 ) {

					if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
						$tax_name = wc_attribute_taxonomy_name( $att->attribute_name );
					} else {
						$tax_name = $woocommerce->attribute_taxonomy_name( $att->attribute_name );
					}

					$field_id = 'use_att_' . $tax_name;
					$defaults[ $field_id ] = 1;
				}
			}
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woocommerce-product-finder' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<h4><?php _e( 'Select which criteria to include:' , 'woocommerce-product-finder' ); ?></h4>
		<p>
			<input id="<?php echo $this->get_field_id( 'use_category' ); ?>" name="<?php echo $this->get_field_name( 'use_category' ); ?>" type="checkbox"<?php checked( $instance['use_category'], 1 ); ?> />
			<label for="<?php echo $this->get_field_id( 'use_category' ); ?>"><?php _e( 'Product Category', 'woocommerce-product-finder' ); ?></label>
		</p>
		<?php
		if ( $att_list && is_array( $att_list ) && count( $att_list ) > 0 ) {

			foreach ( $att_list as $att ) {

				if ( isset( $att->attribute_name ) && strlen( $att->attribute_name ) > 0 ) {

					if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
						$tax_name = wc_attribute_taxonomy_name( $att->attribute_name );
					} else {
						$tax_name = $woocommerce->attribute_taxonomy_name( $att->attribute_name );
					}

					if ( function_exists( 'wc_attribute_label' ) ) {
						$tax_label = wc_attribute_label( $tax_name );
					} else {
						$tax_label = $woocommerce->attribute_label( $tax_name );
					}
					$field_id = 'use_att_' . $tax_name;
					?>
					<p>
						<input id="<?php echo $this->get_field_id( $field_id ); ?>" name="<?php echo $this->get_field_name( $field_id ); ?>" type="checkbox"<?php checked( $instance[ $field_id ], 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $field_id ); ?>"><?php _e( $tax_label , 'woocommerce' ); ?></label>
					</p>
					<?php
				}
			}
		}

	} // End form()

	/**
	 * Handle widget registration
	 */
	public static function register() {
		return register_widget( __CLASS__ );
	}
} // End Class

/* Register the widget. */
add_action( 'widgets_init', 'WC_Product_Finder_Widget::register', 1 );
