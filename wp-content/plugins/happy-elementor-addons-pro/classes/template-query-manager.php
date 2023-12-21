<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Template_Query_Manager {

	/**
	 * Get Elementor template list as Select
	 *
	 * @param  string $type
	 * @return array
	 */
	public static function get_page_template_options( $type = '' ) {
		$page_templates = self::get_elementor_templates( $type );

		//$options[-1] = __( 'Select', 'happy-addons-pro' );

		if ( count( $page_templates ) ) {
			foreach ( $page_templates as $id => $name ) {
				$options[$id] = $name;
			}
		} else {
			$options['no_template'] = __( 'No saved templates found!', 'happy-addons-pro' );
		}

		return $options;
	}

	/**
	 * Get all WordPress registered widgets
	 *
	 * @return array
	 */
	public static function get_registered_sidebars() {
		global $wp_registered_sidebars;
		$options = [];

		if ( ! $wp_registered_sidebars ) {
			// $options[''] = __( 'No sidebars were found', 'happy-addons-pro' );
		} else {
			// $options['---'] = __( 'Choose Sidebar', 'happy-addons-pro' );

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$options[$sidebar_id] = $sidebar['name'];
			}
		}
		return $options;
	}

	/**
	 * Get all elementor page templates
	 *
	 * @param  null    $type
	 * @return array
	 */
	public static function get_elementor_templates( $type = null ) {
		$options = [];

		if ( $type ) {
			$args = [
				'post_type'      => 'elementor_library',
				'posts_per_page' => -1,
			];
			$args['tax_query'] = [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type,
				],
			];

			$page_templates = get_posts( $args );

			if ( ! empty( $page_templates ) && ! is_wp_error( $page_templates ) ) {
				foreach ( $page_templates as $post ) {
					$options[$post->ID] = $post->post_title;
				}
			}
		} else {
			$options = self::get_query_post_list( 'elementor_library' );
		}

		return $options;
	}


    /**
     * Query Posts
     *
     * @param string $post_type
     * @param integer $limit
     * @param string $search
     * @return array
     */
	public static function get_query_post_list( $post_type = 'any', $limit = -1, $search = '' ) {
		global $wpdb;
		$where = '';
		$data  = [];

		if ( -1 == $limit ) {
			$limit = '';
		} elseif ( 0 == $limit ) {
			$limit = "limit 0,1";
		} else {
			$limit = $wpdb->prepare( " limit 0,%d", esc_sql( $limit ) );
		}

		if ( 'any' === $post_type ) {
			$in_search_post_types = get_post_types( ['exclude_from_search' => false] );
			if ( empty( $in_search_post_types ) ) {
				$where .= ' AND 1=0 ';
			} else {
				$where .= " AND {$wpdb->posts}.post_type IN ('" . join( "', '",
					array_map( 'esc_sql', $in_search_post_types ) ) . "')";
			}
		} elseif ( ! empty( $post_type ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_type = %s", esc_sql( $post_type ) );
		}

		if ( ! empty( $search ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", '%' . esc_sql( $search ) . '%' );
		}

		$query   = "select post_title,ID  from $wpdb->posts where post_status = 'publish' $where $limit";
		$results = $wpdb->get_results( $query );
		if ( ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$data[$row->ID] = $row->post_title;
			}
		}
		return $data;
	}
}
