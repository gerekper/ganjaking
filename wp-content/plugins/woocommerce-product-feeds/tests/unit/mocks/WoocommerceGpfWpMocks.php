<?php

class WoocommerceGpfWpMocks {

	public static function setupMocks() {

		global $table_prefix;

		$table_prefix = 'wp_';

		/**
		 * Mocks plugins_url( $path, $plugin );
		 */
		\WP_Mock::userFunction(
			'plugins_url',
			array(
				'return' => function ( $path, $plugin = '' ) {
					if ( !empty( $plugin ) ) {
					    $plugin = basename( dirname( $plugin ) );
                    }
					return 'http://www.example.com/wp-content/plugins/' . urlencode( $plugin ) . '/' . $path;
				},
			)
		);
		/**
		 * Mocks is_wp_error().
		 */
		\WP_Mock::userFunction(
			'is_wp_error',
			array(
				'return' => function ( $val ) {
					return $val instanceof WP_Error || $val instanceof MockWpError;
				},
			)
		);
		/**
		 * Mocks get_post_thumbnail_id;
		 */
		\WP_Mock::userFunction(
			'get_post_thumbnail_id',
			array(
				'return' => function( $id_or_post ) {
					if ( is_int( $id_or_post ) ) {
						return $id_or_post + 100;
					} elseif ( isset( $id_or_post->id ) ) {
						return $id_or_post->id + 100;
					}
					throw new Exception( 'Unknown arg passed to ' . __FUNCTION__ );
				}
			)
		);
		/**
		 * Mocks wp_get_attachment_image_src.
		 */
		\WP_Mock::userFunction(
			'wp_get_attachment_image_src',
			array(
				'args' => [ Mockery::any(), Mockery::any(), Mockery::any() ],
				'return' => function ( $id, $size ) {
					return [
						'http://placehold.it/' . $id . '/' . $id . '?text=' . urlencode( $size ),
						$id,
						$id,
						false,
					];
				},
			)
		);
		/**
		 * Implements wp_list_pluck() based on WP 4.7.1.
		 */
		\WP_Mock::userFunction(
			'wp_list_pluck',
			array(
				'return' => function ( $list, $field, $index_key = null ) {
					if ( ! $index_key ) {
						/*
						* This is simple. Could at some point wrap array_column()
						* if we knew we had an array of arrays.
						*/
						foreach ( $list as $key => $value ) {
							if ( is_object( $value ) ) {
								$list[ $key ] = $value->$field;
							} else {
								$list[ $key ] = $value[ $field ];
							}
						}
						return $list;
					}
					$newlist = array();
					foreach ( $list as $value ) {
						if ( is_object( $value ) ) {
							if ( isset( $value->$index_key ) ) {
								$newlist[ $value->$index_key ] = $value->$field;
							} else {
								$newlist[] = $value->$field;
							}
						} else {
							if ( isset( $value[ $index_key ] ) ) {
								$newlist[ $value[ $index_key ] ] = $value[ $field ];
							} else {
								$newlist[] = $value[ $field ];
							}
						}
					}

					$list = $newlist;

					return $list;
				}
			)
		);
		/**
		 * Mocks trailingslashit
		 */
		\WP_Mock::userFunction(
			'trailingslashit',
			array(
				'return' => function ( $string ) {
					return rtrim( $string, '/\\' ) . '/';
				}
			)
		);
		\WP_Mock::userFunction(
			'get_template_directory',
			array(
				'return' => dirname( __FILE__ ),
			)
		);
		\WP_Mock::userFunction(
			'is_child_theme',
			array(
				'return' => false,
			)
		);
		$path = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/templates/frontend-gpf-element.php';
		\WP_Mock::userFunction(
			'load_template',
			array(
				'args' => [ Mockery::any(), false ],
				'return' => function( $file ) {
					require $file;
				},
			)
		);
		/**
		 * Mocks remove_filter for defined values.
		 */
		\WP_Mock::userFunction(
			'remove_filter',
			array(
				'args' => [ 'terms_clauses', 'to_terms_clauses', 99, 3 ],
				'return' => false,
			)
		);
		\WP_Mock::userFunction(
			'remove_filter',
			array(
				'args' => [ 'woocommerce_get_tax_location', Mockery::any() ],
				'return' => false,
			)
		);
	}
}
