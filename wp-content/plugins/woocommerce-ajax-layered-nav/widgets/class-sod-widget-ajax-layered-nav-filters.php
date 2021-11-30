<?php
/**
 * Widget for active filters.
 *
 * @package ajax-layered-nav
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SOD_Widget_Ajax_Layered_Nav_Filters class
 *
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing, WordPress.Security.NonceVerification.Recommended
 */
class SOD_Widget_Ajax_Layered_Nav_Filters extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->widget_cssclass    = 'woocommerce widget_ajax_layered_nav_filters widget_layered_nav_filters';
		$this->widget_description = __( 'Shows active layered nav filters so users can see and deactivate them. Should be used with the Ajax Layered Nav.', 'woocommerce-ajax-layered-nav' );
		$this->widget_id          = 'sod_ajax_layered_nav_filters';
		$this->widget_name        = __( 'WooCommerce Ajax Layered Nav Filters', 'woocommerce-ajax-layered-nav' );
		parent::__construct();
	}

	public function init_settings() {
		$this->settings = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Active Filters', 'woocommerce-ajax-layered-nav' ),
				'label' => __( 'Title', 'woocommerce-ajax-layered-nav' ),
			),
		);

	}

	/**
	 * Widget main function.
	 *
	 * @see WP_Widget
	 * @param array $args Widget args.
	 * @param array $instance Widget instance object.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		global $_chosen_attributes;

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];

		$current_term       = is_tax() ? get_queried_object()->term_id : '';
		$current_tax        = is_tax() ? get_queried_object()->taxonomy : '';
		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

		$title = ( ! isset( $instance['title'] ) ) ? __( 'Active filters', 'woocommerce-ajax-layered-nav' ) : $instance['title'];
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$post_min = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0;
		$post_max = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0;
		$brand    = isset( $_GET['filter_product_brand'] ) ? wc_clean( wp_unslash( $_GET['filter_product_brand'] ) ) : 0;

		if ( count( $_chosen_attributes ) > 0 || $post_min > 0 || $post_max > 0 || $brand > 0 ) {

			$this->widget_start( $args, $instance );

			echo '<ul>';

			foreach ( $_chosen_attributes as $taxonomy => $data ) {

				foreach ( $data['terms'] as $term_slug ) {
					$term = get_term_by( 'slug', $term_slug, $taxonomy );

					if ( ! $term ) {
							continue;
					}
					$filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) );
					$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array();
					$current_filter = array_map( 'sanitize_title', $current_filter );
					$new_filter     = array_diff( $current_filter, array( $term_slug ) );

					$link = remove_query_arg( array( 'add-to-cart', $filter_name ) );
					if ( count( $new_filter ) > 0 ) {
						$link = add_query_arg( $filter_name, implode( ',', $new_filter ), $link );
					} else {

						if ( 'or' === $data['query_type'] ) {
							$link = remove_query_arg( 'query_type_' . sanitize_title( str_replace( 'pa_', '', $taxonomy ) ), $link );
						}
					}

					$link = urldecode( $link );

					echo '<li class="chosen"><a title="' . esc_attr__( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '" href="#"  href="#" data-filter="' . esc_url( $link ) . '" data-link="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a></li>';
				}
			}

			if ( $post_min ) {
				$link = esc_url( remove_query_arg( 'min_price' ) );
				echo '<li class="chosen"><a title="' . esc_attr__( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '" href="#" data-filter="' . esc_attr( urldecode( esc_url( $link ) ) ) . '" data-link="' . esc_attr( urldecode( esc_url( $link ) ) ) . '">' . esc_html__( 'Min', 'woocommerce-ajax-layered-nav' ) . ' ' . wp_kses_post( wc_price( $post_min ) ) . '</a></li>';
			}

			if ( $post_max ) {
				$link = esc_url( remove_query_arg( 'max_price' ) );
				echo '<li class="chosen"><a title="' . esc_attr__( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '"  href="#" data-filter="' . esc_attr( urldecode( esc_url( $link ) ) ) . '" data-link="' . esc_attr( urldecode( esc_url( $link ) ) ) . '">' . esc_html__( 'Max', 'woocommerce-ajax-layered-nav' ) . ' ' . wp_kses_post( wc_price( $post_max ) ) . '</a></li>';
			}

			if ( $brand ) {
				$brand_term_name = get_term_field( 'name', intval( $_GET['filter_product_brand'] ), 'product_brand', 'display' );
				$link            = esc_url( remove_query_arg( 'filter_product_brand' ) );
				echo '<li class="chosen"><a title="' . esc_attr__( 'Remove filter', 'woocommerce-ajax-layered-nav' ) . '"  href="#" data-filter="' . esc_attr( urldecode( esc_url( $link ) ) ) . '" data-link="' . esc_attr( urldecode( esc_url( $link ) ) ) . '">' . esc_html( $brand_term_name ) . '</a></li>';
			}

			echo '</ul>';

			$this->widget_end( $args );

		} else {
			echo $before_widget . $after_widget; // phpcs:ignore
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = wp_strip_all_tags( wp_unslash( $new_instance['title'] ) );
		parent::flush_widget_cache();
		return $instance;
	}

	public function form( $instance ) {
		$this->init_settings();
		parent::form( $instance );
	}
}
