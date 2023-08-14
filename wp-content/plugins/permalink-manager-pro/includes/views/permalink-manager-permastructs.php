<?php

/**
 * Display the page where the slugs could be regenerated or replaced
 */
class Permalink_Manager_Permastructs {

	public function __construct() {
		add_filter( 'permalink_manager_sections', array( $this, 'add_admin_section' ), 2 );
	}

	/**
	 * Add a new section to the Permalink Manager UI
	 *
	 * @param array $admin_sections
	 *
	 * @return array
	 */
	public function add_admin_section( $admin_sections ) {
		$admin_sections['permastructs'] = array(
			'name'     => __( 'Permastructures', 'permalink-manager' ),
			'function' => array( 'class' => 'Permalink_Manager_Permastructs', 'method' => 'output' )
		);

		return $admin_sections;
	}

	/**
	 * Return an array of fields that will be used to adjust the permastructure settings
	 *
	 * @return array
	 */
	public function get_fields() {
		$post_types = Permalink_Manager_Helper_Functions::get_post_types_array( 'full' );
		$taxonomies = Permalink_Manager_Helper_Functions::get_taxonomies_array( 'full' );

		// Display additional information in Permalink Manager Lite
		if ( ! Permalink_Manager_Admin_Functions::is_pro_active() && ! class_exists( 'Permalink_Manager_URI_Functions_Tax' ) ) {
			$pro_text = sprintf( __( 'To edit taxonomy permalinks, <a href="%s" target="_blank">Permalink Manager Pro</a> is required.', 'permalink-manager' ), PERMALINK_MANAGER_WEBSITE );
			$pro_text = sprintf( '<div class="alert info">%s</div>', $pro_text );
		}

		// 1. Get fields
		$fields = array(
			'post_types' => array(
				'section_name' => __( 'Post types', 'permalink-manager' ),
				'container'    => 'row',
				'fields'       => array()
			),
			'taxonomies' => array(
				'section_name'   => __( 'Taxonomies', 'permalink-manager' ),
				'container'      => 'row',
				'append_content' => ( ! empty( $pro_text ) ) ? $pro_text : '',
				'fields'         => array()
			)
		);

		// 2. Add a separate section for WooCommerce content types
		if ( class_exists( 'WooCommerce' ) ) {
			$fields['woocommerce'] = array(
				'section_name'   => "<i class=\"woocommerce-icon woocommerce-cart\"></i> " . __( 'WooCommerce', 'permalink-manager' ),
				'container'      => 'row',
				'append_content' => ( ! empty( $pro_text ) ) ? $pro_text : '',
				'fields'         => array()
			);
		}

		// 3A. Add permastructure fields for post types
		foreach ( $post_types as $post_type ) {
			if ( $post_type['name'] == 'shop_coupon' ) {
				continue;
			}

			$fields["post_types"]["fields"][ $post_type['name'] ] = array(
				'label'       => $post_type['label'],
				'container'   => 'row',
				'input_class' => 'permastruct-field',
				'post_type'   => $post_type,
				'type'        => 'permastruct'
			);
		}

		// 3B. Add permastructure fields for taxonomies
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_name = $taxonomy['name'];

			// Check if taxonomy exists
			if ( ! taxonomy_exists( $taxonomy_name ) ) {
				continue;
			}

			$fields["taxonomies"]["fields"][ $taxonomy_name ] = array(
				'label'       => $taxonomy['label'],
				'container'   => 'row',
				'input_class' => 'permastruct-field',
				'taxonomy'    => $taxonomy,
				'type'        => 'permastruct'
			);
		}

		// 4. Separate WooCommerce CPT & custom taxonomies
		if ( class_exists( 'WooCommerce' ) ) {
			$woocommerce_fields     = array( 'product' => 'post_types', 'product_tag' => 'taxonomies', 'product_cat' => 'taxonomies' );
			$woocommerce_attributes = wc_get_attribute_taxonomies();

			foreach ( $woocommerce_attributes as $woocommerce_attribute ) {
				$woocommerce_fields["pa_{$woocommerce_attribute->attribute_name}"] = 'taxonomies';
			}

			foreach ( $woocommerce_fields as $field => $field_type ) {
				if ( empty( $fields[ $field_type ]["fields"][ $field ] ) ) {
					continue;
				}

				$fields["woocommerce"]["fields"][ $field ]         = $fields[ $field_type ]["fields"][ $field ];
				$fields["woocommerce"]["fields"][ $field ]["name"] = "{$field_type}[{$field}]";
				unset( $fields[ $field_type ]["fields"][ $field ] );
			}
		}

		return apply_filters( 'permalink_manager_permastructs_fields', $fields );
	}

	/**
	 * Get the array with settings and render the HTML output
	 */
	public function output() {
		$sidebar = sprintf( '<h3>%s</h3>', __( 'Instructions', 'permalink-manager' ) );
		$sidebar .= "<div class=\"notice notice-warning\"><p>";
		$sidebar .= __( 'The current permastructures settings will be automatically applied <strong>only to the new posts & terms</strong>.' );
		$sidebar .= '<br />';
		$sidebar .= sprintf( __( 'To apply the <strong>new format to existing posts and terms</strong>, please use "<a href="%s">Regenerate/reset</a>" tool after you update the permastructure settings below.', 'permalink-manager' ), admin_url( 'tools.php?page=permalink-manager&section=tools&subsection=regenerate_slugs' ) );
		$sidebar .= "</p></div>";

		$sidebar .= sprintf( '<h4>%s</h4>', __( 'Permastructure tags', 'permalink-manager' ) );
		$sidebar .= wpautop( sprintf( __( 'All allowed <a href="%s" target="_blank">permastructure tags</a> are listed below. Please note that some of them can be used only for particular post types or taxonomies.', 'permalink-manager' ), "https://codex.wordpress.org/Using_Permalinks#Structure_Tags" ) );
		$sidebar .= Permalink_Manager_Helper_Functions::get_all_structure_tags();

		return Permalink_Manager_UI_Elements::get_the_form( self::get_fields(), '', array( 'text' => __( 'Save permastructures', 'permalink-manager' ), 'class' => 'primary margin-top' ), $sidebar, array( 'action' => 'permalink-manager', 'name' => 'permalink_manager_permastructs' ) );
	}

}
