<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SOD_Widget_Ajax_Layered_Nav_Clear extends WC_Widget {


	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass 		= 'woocommerce widget_ajax_layered_nav_clear widget_layered_nav_clear widget_layered_nav_filters';
		$this->widget_description	= __( 'Displays a "Clear All" Link. Should be used with the Ajax Layered Nav.', 'woocommerce-ajax-layered-nav' );
		$this->widget_id 		= 'sod_ajax_layered_nav_clear';
		$this->widget_name 			= __( 'WooCommerce Ajax Layered Nav Clear All', 'woocommerce-ajax-layered-nav' );

		/* Widget settings. */
		parent::__construct();
	}
    function init_settings(){
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Clear All Filters', 'woocommerce-ajax-layered-nav' ),
                'label' => __( 'Title', 'woocommerce-ajax-layered-nav' )
            ),
            'link_text'  => array(
                'type'  => 'text',
                'std'   => __( 'Clear All', 'woocommerce-ajax-layered-nav' ),
                'label' => __( 'Link Text', 'woocommerce-ajax-layered-nav' )
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
        $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
        $current_term   = is_tax() ? get_queried_object()->term_id : '';
		$current_tax   = is_tax() ? get_queried_object()->taxonomy : '';

		$title = ( ! isset( $instance['title'] ) ) ? __( 'Clear All Filters', 'woocommerce-ajax-layered-nav' ) : $instance['title'];
		$title    = apply_filters( 'widget_title', ! empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
        $link_text  = ( ! isset( $instance['link_text'] ) ) ? __( 'Clear All', 'woocommerce-ajax-layered-nav' ) : $instance['link_text'];
		// Price
		$post_min = isset( $_GET['min_price'] ) ? $_GET['min_price'] : 0;
		$post_max = isset( $_GET['max_price'] ) ? $_GET['max_price'] : 0;
        if ( count( $_chosen_attributes ) > 0 || $post_min > 0 || $post_max > 0 ) {

			$this->widget_start( $args, $instance );
            $link = false;
			echo '<ul>';

			// Attributes
			foreach ( $_chosen_attributes as $taxonomy => $data ) {

				foreach ( $data['terms'] as $term_id ) {
					$term 				= get_term( $term_id, $taxonomy );

					$taxonomy_filter = str_replace( 'pa_', '', $taxonomy );
					$current_filter  = ! empty( $_GET[ 'filter_' . $taxonomy_filter ] ) ? $_GET[ 'filter_' . $taxonomy_filter ] : '';
					if ( !$link ){
					   $link =  remove_query_arg( 'filter_' . $taxonomy_filter ) ;
					 }else{
					   $link = remove_query_arg( 'filter_' . $taxonomy_filter, $link  );
					 }
					if($data['query_type'] == 'or'){
						$link = esc_url( remove_query_arg( 'query_type_' . $taxonomy_filter, $link ) );
                    }
                	//echo '<li class="chosen"><a title="' . __( 'Remove filter', 'woocommerce' ) . '" href="#"  href="#" data-filter="'.esc_url($link).'" data-link="'.esc_url($link).'">' . $term->name . '</a></li>';
				}
			}

			if ( $post_min ) {
				$link = esc_url( remove_query_arg( 'min_price', $link ) );
		  }

			if ( $post_max ) {
				$link = esc_url( remove_query_arg( 'max_price', $link ) );
			}

			// Brands
			if ( isset( $_GET['filter_product_brand'] ) ) {
				$link = esc_url(remove_query_arg('filter_product_brand', $link));
			}
            echo '<li><a href="#" data-filter="'. esc_url(urldecode($link)) . '" data-link="'. esc_url( urldecode($link) ) . '" >'. $link_text .'</a></li>';
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
		$instance['link_text'] = strip_tags(stripslashes($new_instance['link_text']));

		return $instance;
	}

	/** @see WP_Widget->form */
     public function form( $instance ) {
        $this->init_settings();
        parent::form( $instance );
    }
}
