<?php
/**
 * Widget to clear filters.
 *
 * @package ajax-layered-nav
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * SOD_Widget_Ajax_Layered_Nav_Clear class
 *
 * phpcs:disable Squiz.Commenting.FunctionComment.Missing, WordPress.Security.NonceVerification.Recommended
 */
class SOD_Widget_Ajax_Layered_Nav_Clear extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->widget_cssclass    = 'woocommerce widget_ajax_layered_nav_clear widget_layered_nav_clear widget_layered_nav_filters';
		$this->widget_description = __( 'Displays a "Clear All" Link. Should be used with the Ajax Layered Nav.', 'woocommerce-ajax-layered-nav' );
		$this->widget_id          = 'sod_ajax_layered_nav_clear';
		$this->widget_name        = __( 'WooCommerce Ajax Layered Nav Clear All', 'woocommerce-ajax-layered-nav' );
		parent::__construct();
	}

	public function init_settings() {
		$this->settings = array(
			'title'     => array(
				'type'  => 'text',
				'std'   => __( 'Clear All Filters', 'woocommerce-ajax-layered-nav' ),
				'label' => __( 'Title', 'woocommerce-ajax-layered-nav' ),
			),
			'link_text' => array(
				'type'  => 'text',
				'std'   => __( 'Clear All', 'woocommerce-ajax-layered-nav' ),
				'label' => __( 'Link Text', 'woocommerce-ajax-layered-nav' ),
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

		$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
		$current_term       = is_tax() ? get_queried_object()->term_id : '';
		$current_tax        = is_tax() ? get_queried_object()->taxonomy : '';

		$title     = ( ! isset( $instance['title'] ) ) ? __( 'Clear All Filters', 'woocommerce-ajax-layered-nav' ) : $instance['title'];
		$title     = apply_filters( 'widget_title', ! empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$link_text = ( ! isset( $instance['link_text'] ) ) ? __( 'Clear All', 'woocommerce-ajax-layered-nav' ) : $instance['link_text'];

		$post_min = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : 0;
		$post_max = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : 0;

		if ( count( $_chosen_attributes ) > 0 || $post_min > 0 || $post_max > 0 ) {

			$this->widget_start( $args, $instance );
			$link = false;
			echo '<ul>';

			foreach ( $_chosen_attributes as $taxonomy => $data ) {

				foreach ( $data['terms'] as $term_id ) {
					$term = get_term( $term_id, $taxonomy );

					$taxonomy_filter = str_replace( 'pa_', '', $taxonomy );
					$current_filter  = ! empty( $_GET[ 'filter_' . $taxonomy_filter ] ) ? wc_clean( wp_unslash( $_GET[ 'filter_' . $taxonomy_filter ] ) ) : '';
					if ( ! $link ) {
						$link = remove_query_arg( 'filter_' . $taxonomy_filter );
					} else {
						$link = remove_query_arg( 'filter_' . $taxonomy_filter, $link );
					}
					if ( 'or' === $data['query_type'] ) {
						$link = esc_url( remove_query_arg( 'query_type_' . $taxonomy_filter, $link ) );
					}
				}
			}

			if ( $post_min ) {
				$link = esc_url( remove_query_arg( 'min_price', $link ) );
			}

			if ( $post_max ) {
				$link = esc_url( remove_query_arg( 'max_price', $link ) );
			}

			if ( isset( $_GET['filter_product_brand'] ) ) {
				$link = esc_url( remove_query_arg( 'filter_product_brand', $link ) );
			}
			echo '<li><a href="#" data-filter="' . esc_url( urldecode( $link ) ) . '" data-link="' . esc_url( urldecode( $link ) ) . '" >' . wp_kses_post( $link_text ) . '</a></li>';
			echo '</ul>';

			$this->widget_end( $args );
		} else {
			echo $before_widget . $after_widget; // phpcs:ignore
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title']     = wp_strip_all_tags( wp_unslash( $new_instance['title'] ) );
		$instance['link_text'] = wp_strip_all_tags( wp_unslash( $new_instance['link_text'] ) );

		return $instance;
	}

	public function form( $instance ) {
		$this->init_settings();
		parent::form( $instance );
	}
}
