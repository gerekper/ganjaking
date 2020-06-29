<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SOD_Widget_Ajax_Layered_Nav_Filters extends WC_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */

		$this->widget_cssclass  		= 'woocommerce widget_ajax_layered_nav_filters widget_layered_nav_filters';
		$this->widget_description	= __( 'Shows active layered nav filters so users can see and deactivate them. Should be used with the Ajax Layered Nav.', 'woocommerce' );
		$this->widget_id  		= 'sod_ajax_layered_nav_filters';
		$this->widget_name 			= __( 'WooCommerce Ajax Layered Nav Filters', 'woocommerce' );
		/* Widget settings. */
		parent::__construct();
		/* Create the widget. */
	}
     public function init_settings() {
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Active Filters', 'woocommerce' ),
                'label' => __( 'Title', 'woocommerce' )
            )
        );

     }
	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		global $_chosen_attributes, $woocommerce;

		extract( $args );

		 if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) )
			return;

		$current_term   = is_tax() ? get_queried_object()->term_id : '';
		$current_tax   = is_tax() ? get_queried_object()->taxonomy : '';
        $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

		$title = ( ! isset( $instance['title'] ) ) ? __( 'Active filters', 'woocommerce-ajax-layered-nav' ) : $instance['title'];
		$title    = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// Price
		$post_min = isset( $_GET['min_price'] ) ? $_GET['min_price'] : 0;
		$post_max = isset( $_GET['max_price'] ) ? $_GET['max_price'] : 0;

		// Brands
		$brand = isset( $_GET['filter_product_brand'] ) ? $_GET['filter_product_brand'] : 0;

		if ( count( $_chosen_attributes ) > 0 || $post_min > 0 || $post_max > 0 || $brand > 0 ) {

			$this->widget_start( $args, $instance );

			echo "<ul>";

			// Attributes
			foreach ( $_chosen_attributes as $taxonomy => $data ) {

				foreach ( $data['terms'] as $term_slug ) {
					if ( ! $term = get_term_by( 'slug', $term_slug, $taxonomy ) ) {
                            continue;
                        }
                    $filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) );
                    $current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( $_GET[ $filter_name ] ) ) : array();
                    $current_filter = array_map( 'sanitize_title', $current_filter );
                    $new_filter      = array_diff( $current_filter, array( $term_slug ) );

					$link = remove_query_arg( array( 'add-to-cart', $filter_name ) );
                    if ( sizeof( $new_filter ) > 0 ) {
                         $link = add_query_arg( $filter_name, implode( ',', $new_filter ), $link );
                   }else{

						if($data['query_type'] == 'or'){
						  $link = remove_query_arg( 'query_type_' . sanitize_title( str_replace( 'pa_', '', $taxonomy )), $link );
						}
					}

					$link = urldecode($link);

					echo '<li class="chosen"><a title="' . __( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '" href="#"  href="#" data-filter="'.esc_url($link).'" data-link="'.esc_url($link).'">' . $term->name . '</a></li>';
				}
			}

			if ( $post_min ) {
				$link = esc_url( remove_query_arg( 'min_price' ) );
				echo '<li class="chosen"><a title="' . __( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '" href="#" data-filter="'.urldecode(esc_url($link)).'" data-link="'.urldecode(esc_url($link)).'">' . __( 'Min', 'woocommerce-ajax-layered-nav' ) . ' ' . wc_price( $post_min ) . '</a></li>';
			}

			if ( $post_max ) {
				$link = esc_url( remove_query_arg( 'max_price' ) );
				echo '<li class="chosen"><a title="' . __( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '"  href="#" data-filter="'.urldecode(esc_url($link)).'" data-link="'.urldecode(esc_url($link)).'">' . __( 'Max', 'woocommerce-ajax-layered-nav' ) . ' ' . wc_price( $post_max ) . '</a></li>';
			}

			// Brands
			if ( $brand ) {
				$brand_term_name = get_term_field( 'name', intval( $_GET['filter_product_brand'] ), 'product_brand', 'display' );
				$link = esc_url( remove_query_arg( 'filter_product_brand' ) );
				echo '<li class="chosen"><a title="' . __( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '"  href="#" data-filter="' . urldecode(esc_url($link)) . '" data-link="' . urldecode(esc_url($link)) . '">' . esc_html( $brand_term_name ) . '</a></li>';
			}

			echo "</ul>";

			         $this->widget_end( $args );

		}else{
			echo $before_widget;
			echo $after_widget;
		}
	}
	function update( $new_instance, $old_instance ) {
		global $woocommerce;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
         parent::flush_widget_cache();

		return $instance;
	}

	/** @see WP_Widget->form */
	 public function form( $instance ) {
        $this->init_settings();
        parent::form( $instance );
    }
}
