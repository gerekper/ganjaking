<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Theme Migrate file.
 *
 * @package crane
 */
class GM_migrate__v1_4_4_400_2 extends GM_Migration {

	/**
	 * Migrate
	 *
	 * @return bool
	 */
	public function migrate() {

		$this->db_version = '1.4.4.400.2';

		GroovyMenuStyleStorage::getInstance()->set_disable_storage();

		$preset_change_id = get_option( 'gm_migration_data_1_4_4_400_1' );

		if ( empty( $preset_change_id ) || ! is_array( $preset_change_id ) ) {
			$preset_change_id = GroovyMenuUtils::get_old_preset_ids();
		}

		// Update used_in.
		$used_in_storage = get_option( 'groovy_menu_preset_used_in_storage' );
		if ( empty( $used_in_storage ) ) {
			$used_in_storage = array();
		}


		// STAGE 1. Change default preset option.
		$this->add_migrate_debug_log( 'STAGE 1. Change default preset option.' );
		$default_preset_option_name = GroovyMenuPreset::DEFAULT_PRESET_OPTION;
		$default_preset             = get_option( $default_preset_option_name );

		if ( ! empty( $default_preset ) ) {
			$default_preset = intval( $default_preset );
			if ( ! empty( $preset_change_id[ $default_preset ] ) ) {
				update_option( $default_preset_option_name, strval( $preset_change_id[ $default_preset ] ) );
				$used_in_storage['default'] = intval( $preset_change_id[ $default_preset ] );

				$style           = new GroovyMenuStyle();
				$global_settings = get_option( GroovyMenuStyle::OPTION_NAME );
				$global_settings['taxonomies']['default_master_preset'] = strval( $preset_change_id[ $default_preset ] );
				$style->updateGlobal( $global_settings );

			}
		}


		// STAGE 2. Change taxonomy custom meta fields.
		$this->add_migrate_debug_log( 'STAGE 2. Change taxonomy custom meta fields.' );
		$taxonomies_meta = array(
			'category'             => array(),
			'post_tag'             => array(),
			'product_cat'          => array(),
			'crane_portfolio_cats' => array(),
		);

		$gm_term_custom_meta_name = 'gm_custom_preset_id';

		foreach ( $taxonomies_meta as $tax_name => $item ) {
			$taxonomies_meta[ $tax_name ] = $this->get_taxonomy_meta( $tax_name, $gm_term_custom_meta_name );

			foreach ( $taxonomies_meta[ $tax_name ] as $term_id => $gm_old_id ) {
				$old_id = intval( $gm_old_id );

				if ( ! empty( $preset_change_id[ $old_id ] ) ) {
					// update meta field for term.
					update_term_meta( $term_id, $gm_term_custom_meta_name, strval( $preset_change_id[ $old_id ] ) );
					$used_in_storage['taxonomy'][ $term_id ] = intval( $preset_change_id[ $old_id ] );
				}
			}
		}


		// STAGE 3. Change presets for Crane Theme Options.
		$this->add_migrate_debug_log( 'STAGE 3. Change presets for Crane Theme Options.' );
		$_redux_otions = maybe_unserialize( get_option( 'crane_options' ) );

		if ( ! empty( $_redux_otions ) && is_array( $_redux_otions ) && class_exists( 'Redux' ) ) {
			$options = array(
				'regular-page-menu'     => 'page',
				'portfolio-menu'        => 'crane_portfolio',
				'portfolio-single-menu' => 'crane_portfolio--single',
				'blog-menu'             => 'post',
				'blog-single-menu'      => 'post--single',
				'shop-menu'             => 'product',
				'shop-single-menu'      => 'product--single',
				'search-menu'           => 'page--is_search',
				'404-menu'              => 'page--is_404',
			);

			foreach ( $options as $redux_opt => $gm_opt ) {

				$one_option = isset( $_redux_otions[ $redux_opt ] ) ? intval( $_redux_otions[ $redux_opt ] ) : null;
				if ( empty( $one_option ) ) {
					continue;
				}

				$new_groovy_preset_id = empty( $preset_change_id[ $one_option ] ) ? '' : strval( $preset_change_id[ $one_option ] );

				if ( ! empty( $one_option ) && ! empty( $new_groovy_preset_id ) ) {
					Redux::setOption( 'crane_options', $redux_opt, $new_groovy_preset_id );
				}
			}
		}


		// STAGE 4. Change posts custom meta fields.
		$this->add_migrate_debug_log( 'STAGE 4. Change posts custom meta fields.' );
		foreach ( [ 'page', 'post', 'crane_portfolio', 'product' ] as $post_type ) {

			$posts_has_custom_preset = $this->get_meta_values( 'gm_custom_preset_id', $post_type );

			foreach ( $posts_has_custom_preset as $meta_item ) {
				if ( empty( $meta_item['meta_value'] ) ) {
					continue;
				}

				$old_id = $meta_item['meta_value'];
				if ( 'default' === $old_id ) {
					continue;
				}
				$old_id = intval( $old_id );

				if ( ! empty( $preset_change_id[ $old_id ] ) ) {
					$meta_id = intval( $meta_item['meta_id'] );

					// update meta field.
					update_metadata_by_mid( 'post', $meta_id, strval( $preset_change_id[ $old_id ] ), 'gm_custom_preset_id' );
					$used_in_storage['post'][ $post_type ][ intval( $meta_item['post_id'] ) ] = intval( $preset_change_id[ $old_id ] );
				}
			}


			$posts_has_custom_preset = $this->get_meta_values( 'grooni_meta', $post_type );

			foreach ( $posts_has_custom_preset as $meta_item ) {

				if ( empty( $meta_item['meta_value'] ) ) {
					continue;
				}

				$grooni_meta = json_decode( $meta_item['meta_value'], true );

				if ( empty( $grooni_meta ) ) {
					continue;
				}

				if ( ! isset( $grooni_meta['groovy_preset'] ) || 'default' === $grooni_meta['groovy_preset'] ) {
					continue;
				}

				$old_id               = intval( $grooni_meta['groovy_preset'] );
				$new_groovy_preset_id = empty( $preset_change_id[ $old_id ] ) ? '' : strval( $preset_change_id[ $old_id ] );

				if ( $new_groovy_preset_id ) {
					$grooni_meta['groovy_preset'] = $new_groovy_preset_id;
					$grooni_meta                  = json_encode( $grooni_meta, JSON_UNESCAPED_UNICODE );
					$meta_id                      = intval( $meta_item['meta_id'] );

					update_metadata_by_mid( 'post', $meta_id, $grooni_meta, 'grooni_meta' );
				}
			}
		}

		// Update used_in.
		if ( ! empty( $used_in_storage ) ) {
			update_option( 'groovy_menu_preset_used_in_storage', $used_in_storage, false );
		}


		$this->success();

		return true;

	}


	private function get_taxonomy_meta( $taxonomy_slug, $field_name = 'gm_custom_preset_id' ) {
		$return_term_meta = array();

		$args = array(
			'get'                    => 'all',
			'hide_empty'             => false,
			'taxonomy'               => $taxonomy_slug,
			'update_term_meta_cache' => true,
			'fields'                 => 'id=>slug',
		);

		$taxonomies = get_terms( $args );

		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy_id => $taxonomy_name ) {

				$data = maybe_unserialize( get_term_meta( $taxonomy_id, $field_name, true ) );

				if ( $data ) {
					$return_term_meta[ $taxonomy_id ] = $data;
				}
			}
		}

		return $return_term_meta;
	}


	private function get_meta_values( $key = '', $type = 'post' ) {

		global $wpdb;

		if ( empty( $key ) ) {
			return;
		}

		$request = $wpdb->get_results(
			$wpdb->prepare( "
                  SELECT pm.* FROM {$wpdb->postmeta} pm
                  LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                  WHERE pm.meta_key = '%s' 
                  AND p.post_type = '%s';", $key, $type ),
			ARRAY_A
		);

		return $request;
	}

}
