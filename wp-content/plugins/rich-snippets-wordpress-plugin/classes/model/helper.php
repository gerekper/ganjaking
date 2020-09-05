<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Helper.
 *
 * Helps to fetch some data.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Helper_Model {


	/**
	 * Media metadata cache.
	 *
	 * @var array
	 */
	private $media_meta = array();


	/**
	 * Author meta cache.
	 *
	 * @var array
	 */
	private $author_meta = array();


	/**
	 * The instance.
	 *
	 * @var Helper_Model
	 *
	 * @since 2.0.0
	 */
	protected static $_instance = null;


	/**
	 * If this instance has been initialized already.
	 *
	 * @since 2.0.0
	 *
	 * @var bool
	 */
	protected $_initialized = false;

	/**
	 *
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return   Helper_Model
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Magic function for cloning.
	 *
	 * Disallow cloning as this is a singleton class.
	 *
	 * @since 2.0.0
	 */
	protected function __clone() {
	}


	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.0.0
	 */
	protected function __construct() {
	}


	/**
	 * Fetches the current post ID.
	 *
	 * @return int
	 * @since 2.0.0
	 *
	 */
	public function get_current_post_id(): int {

		/**
		 * @var \WP_Query $wp_the_query ;
		 */
		global $wp_the_query;

		if ( ! isset( $wp_the_query ) ) {
			return 0;
		}

		if ( ! is_a( $wp_the_query, '\WP_Query' ) ) {
			return 0;
		}

		if ( ! $wp_the_query->is_singular ) {
			return 0;
		}

		if ( isset( $wp_the_query->queried_object_id ) && ! empty( $wp_the_query->queried_object_id ) ) {
			return intval( $wp_the_query->queried_object_id );
		}

		if ( isset( $wp_the_query->post ) && $wp_the_query->post instanceof \WP_Post ) {
			return intval( $wp_the_query->post->ID );
		}

		if ( isset( $wp_the_query->posts ) && is_array( $wp_the_query->posts ) && 1 === count( $wp_the_query->posts ) ) {
			$post = array_values( $wp_the_query->posts )[0];
			if ( $post instanceof \WP_Post ) {
				return intval( $post->ID );
			}
		}

		return 0;
	}


	/**
	 * Returns meta information for the thumbnail.
	 *
	 * @param string $info
	 * @param int $post_id
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function get_thumbnail_meta( string $info = 'url', int $post_id ) {

		return $this->get_media_meta( $info, (int) get_post_thumbnail_id( $post_id ) );
	}


	/**
	 * Returns meta information for the media item.
	 *
	 * @param string $info
	 * @param int $media_id
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function get_media_meta( string $info = 'url', int $media_id ) {

		# fetch from cache
		if ( isset( $this->media_meta[ $media_id ] ) ) {
			$media = $this->media_meta[ $media_id ];
		} else {
			$media                         = wp_get_attachment_image_src( $media_id, 'full' );
			$this->media_meta[ $media_id ] = $media;
		}

		if ( ! is_array( $media ) ) {
			return null;
		}

		list( $url, $width, $height ) = $media;

		if ( ! isset( ${$info} ) ) {
			return null;
		}

		return ${$info};
	}


	/**
	 * Returns author meta if a post ID is given.
	 *
	 * @param string $meta
	 * @param int $post_id
	 *
	 * @return mixed
	 * @since 2.0.0
	 *
	 */
	public function get_author_meta_by_post_id( string $meta, int $post_id ) {

		$author_id = $this->get_author_id( $post_id );

		return get_the_author_meta( $meta, $author_id );
	}


	/**
	 * Returns the author ID when a post_id is given.
	 *
	 * @param int $post_id
	 *
	 * @return int The author user ID.
	 * @since 2.0.0
	 *
	 */
	public function get_author_id( int $post_id ): int {

		$post = get_post( $post_id );

		if ( ! is_a( $post, '\WP_Post' ) ) {
			return 0;
		}

		return $post->post_author;
	}


	/**
	 * Fetches the current post type on an admin screen (if any).
	 *
	 * @return string Post Type or empty string.
	 * @since 2.0.0
	 *
	 */
	public function get_current_admin_post_type(): string {

		$screen = get_current_screen();

		if ( is_a( $screen, '\WP_Screen' ) ) {
			if ( isset( $screen->post_type ) ) {
				return (string) $screen->post_type;
			}
		}

		$post_id   = (int) filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
		$post_type = get_post_type( $post_id );

		if ( false !== $post_type ) {
			return (string) $post_type;
		}

		return (string) filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
	}


	/**
	 * Returns the slug when a basename is given.
	 *
	 * @param string $basename
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_slug_from_basename( $basename ) {

		return str_replace( array( '/', '.php' ), '', strrchr( $basename, '/' ) );
	}


	/**
	 * Searches the database for a snippet ID.
	 *
	 * @param string $snippet_uid
	 *
	 * @return int
	 * @since 2.0.0
	 *
	 */
	public function get_post_id_by_snippet_uid( $snippet_uid ) {

		global $wpdb;
		$q     = $wpdb->esc_like( $snippet_uid );
		$regex = sprintf( "a:[0-9]+:\{s:%d:\"%s\"", strlen( $q ), $q );
		$regex = esc_sql( $regex );

		$sql = "SELECT post_ID FROM {$wpdb->postmeta} WHERE meta_key = '_wpb_rs_schema' AND meta_value REGEXP '{$regex }' LIMIT 1";

		$post_id = $wpdb->get_var( $sql );

		return absint( $post_id );

	}


	/**
	 * Removes schema.org from an URL.
	 *
	 * @param string $url
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function remove_schema_url( $url ) {

		return str_replace( array(
			'http://schema.org/',
			'https://schema.org/',
		), '', $url );
	}


	/**
	 * Transforms a string to a bool.
	 *
	 * @param mixed $v
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function string_to_bool( $v ) {

		if ( is_bool( $v ) ) {
			return $v;
		}

		return filter_var( $v, FILTER_CALLBACK, array(
			'options' => function ( $v ) {

				if ( 'y' === strtolower( $v ) ) {
					return true;
				}

				return boolval( filter_var( $v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) );
			},
		) );
	}


	/**
	 * Sanitizes single scalar elements in an array.
	 *
	 * @param $arr
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function sanitize_text_in_array( $arr ) {

		if ( ! is_array( $arr ) ) {
			return array();
		}

		foreach ( $arr as $k => $v ) {
			if ( ! is_scalar( $v ) ) {
				unset( $arr[ $k ] );
				continue;
			}

			$arr[ sanitize_text_field( $k ) ] = sanitize_text_field( $v );
		}

		return $arr;
	}


	/**
	 * Returns the users first name (if any).
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_current_user_firstname() {

		$user = wp_get_current_user();

		$first_name = get_user_meta( $user->ID, 'first_name', true );

		if ( ! empty( $first_name ) ) {
			return $first_name;
		}

		return $user->display_name;
	}


	/**
	 * Checks if Yoast SEO is active.
	 *
	 * @return string|bool 'premium' if Yoast SEO premium is active. Otherwise true or false.
	 * @since 2.2.0
	 *
	 */
	public function is_yoast_seo_active() {

		if ( defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {
			return 'premium';
		}
		if ( defined( 'WPSEO_FILE' ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Sanitizes a HTML ID.
	 *
	 * @param string $val
	 *
	 * @return string
	 * @since 2.2.0
	 *
	 */
	public function sanitize_html_id( $val ): string {

		return strtolower( sanitize_title( sanitize_text_field( $val ) ) );
	}


	/**
	 * Searches for the primary category ID. If Yoast SEO did not set one, it will return the first one in the list.
	 *
	 * @param int $post_id
	 *
	 * @return int
	 * @since 2.7.0 moved from YoastSEO_Model class
	 *
	 * @since 2.6.0
	 */
	public function get_primary_category( $post_id ) {

		if ( $this->is_yoast_seo_active() ) {
			$cat_id = absint( get_post_meta( $post_id, '_yoast_wpseo_primary_category', true ) );

			if ( $cat_id > 0 ) {
				return $cat_id;
			}
		}

		return $this->get_primary_term( 'category', $post_id );
	}


	/**
	 * Search or the first term in a set of terms and return it.
	 *
	 * @param string $taxonomy
	 * @param int $post_id
	 *
	 * @return int
	 * @since 2.8.0
	 *
	 */
	public function get_primary_term( $taxonomy, $post_id ) {
		/**
		 * @var \WP_Term[] $terms
		 */
		$terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'object_ids' => $post_id
		] );

		if ( ! is_array( $terms ) ) {
			return 0;
		}

		if ( count( $terms ) <= 0 ) {
			return 0;
		}

		foreach ( $terms as $k => $term ) {
			$terms[ $k ]->snip_parent_no = count( get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );
		}

		uasort( $terms, function ( $term_a, $term_b ) {
			if ( $term_a->snip_parent_no == $term_b->snip_parent_no ) {
				return 0;
			}

			return ( $term_a->snip_parent_no < $term_b->snip_parent_no ) ? - 1 : 1;
		} );

		$term = array_pop( $terms );

		return $term->term_id;
	}


	/**
	 * Translate a field type and returns the label.
	 *
	 * @param string $field_type
	 *
	 * @return string
	 * @since 2.7.0
	 *
	 */
	public function get_field_type_label( $field_type ) {
		if ( empty( $field_type ) ) {
			return __( 'not selected', 'rich-snippets-schema' );
		}

		$internal_values = Fields_Model::get_internal_values();

		foreach ( $internal_values as $type => $fields ) {
			foreach ( $fields as $field ) {
				if ( ! isset( $field['id'] ) ) {
					continue;
				}
				if ( ! isset( $field['label'] ) ) {
					continue;
				}
				if ( $field_type === $field['id'] ) {
					return $field['label'];
				}
			}
		}

		$reference_values = Fields_Model::get_reference_values();

		if ( isset( $reference_values[ $field_type ] ) ) {
			return $reference_values[ $field_type ];
		}

		return $field_type;
	}


	/**
	 * Returns the URL host part from the current WordPress site.
	 *
	 * @return string
	 * @since 2.7.0
	 *
	 */
	public function get_site_url_host() {
		return parse_url( site_url(), PHP_URL_HOST );
	}


	/**
	 * Adds campaign parameters to an URL.
	 *
	 * @param $url
	 *
	 * @return string
	 * @since 2.7.0
	 *
	 */
	public function get_campaignify( $url, $campaign ) {
		return add_query_arg( [
			'pk_campaign' => $campaign,
			'pk_source'   => $this->get_site_url_host()
		], $url );
	}


	/**
	 * Generates a short hash out of a string.
	 *
	 * @param $sth
	 *
	 * @return string
	 * @since 2.8.0
	 *
	 */
	public function get_short_hash( $sth ): string {


		if ( ! function_exists( '\hash_algos' ) ) {
			return $sth;
		}

		if ( ! function_exists( '\hash_hmac' ) ) {
			return $sth;
		}

		$possible_hash_algos = hash_algos();

		# Search for an algorithm that produces short output
		if ( version_compare( PHP_VERSION, '7.2.0', '<' ) ) {
			$hash_algos_to_use = array(
				'crc32',
				'adler32',
				'crc32b',
				'fnv132',
				'fnv1a32',
				'fnv164',
				'joaat',
				'fnv164',
				'fnv1a64',
				'md5',
			);
		} else {
			$hash_algos_to_use = array(
				'haval128,4',
				'md4',
				'tiger128,4',
				'tiger128,3',
				'haval128,3',
				'md2',
				'ripemd128',
				'haval128,5',
				'haval160,5',
				'sha1',
				'tiger160,3',
				'tiger160,4',
				'ripemd160',
				'haval160,3',
				'haval192,4',
				'tiger192,3',
				'haval192,5',
				'tiger192,4',
				'tiger192,3',
				'sha224',
				'haval224,5',
				'haval224,5',
				'haval224,3',
				'sha512/224',
				'sha3-224',
				'haval224,4',
				'haval254,4',
				'haval256,3',
				'snefru256',
				'gost-crypto',
				'gost',
				'snefru',
				'ripemd256',
				'sha3-256',
				'sha512/256',
				'sha256',
				'haval256,5',
			);
		}

		$algo = 'sha256';

		# search for the first algo available
		foreach ( $hash_algos_to_use as $hash_algo_to_use ) {
			if ( false !== $k = array_search( $hash_algo_to_use, $possible_hash_algos ) ) {
				$algo = $hash_algo_to_use;
				break;
			}
		}

		return (string) hash_hmac( $algo, $sth, wp_salt( 'wpb_rs' ) );
	}


	/**
	 * Filters array items by hierarchy.
	 *
	 * The $items array is filter so that only the items that are the parents of a certain sub-item
	 * are still in the returned array.
	 *
	 * @param object[] $items
	 * @param int $id
	 * @param string $object_param_name
	 * @param string $object_id_param_name
	 *
	 * @return object[] Filtered item list.
	 * @since 2.8.0
	 *
	 */
	public function filter_item_hierarchy( $items, $id, $object_parent_param_name, $object_id_param_name, $new_items = [] ) {

		foreach ( $items as $key => $item ) {
			if ( ! is_object( $item ) ) {
				continue;
			}

			if ( ! isset( $item->{$object_id_param_name} ) ) {
				continue;
			}


			if ( $id == $item->{$object_id_param_name} ) {
				$new_items[] = $item;

				if ( ! isset( $item->{$object_parent_param_name} ) ) {
					continue;
				}

				if ( empty( $item->{$object_parent_param_name} ) ) {
					continue;
				}

				$new_items = $this->filter_item_hierarchy(
					$items,
					$item->{$object_parent_param_name},
					$object_parent_param_name,
					$object_id_param_name,
					$new_items
				);
			}

		}

		return $new_items;

	}


	/**
	 * Integrates an array into another array on a certain place represented by $no.
	 *
	 * @param array $before
	 * @param int $no
	 * @param array $insert_arr
	 *
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public function integrate_into_array( $before, $no, $insert_arr ) {
		$after2 = array_slice( $before, $no, null, true );
		$after1 = array_diff_key( $before, $after2 );

		return array_merge( $after1, $insert_arr, $after2 );
	}


	/**
	 * Checks if a user should rate the plugin.
	 *
	 * @return bool
	 * @since 2.9.0
	 *
	 */
	public function should_user_rate() {

		$time = ( intval( get_option( 'wpb_rs/rating_dismissed_timestamp', 0 ) ) + 2 * WEEK_IN_SECONDS );

		return true !== wp_validate_boolean( get_option( 'wpb_rs/rated', false ) ) && $time < time() && $this->magic();
	}


	/**
	 * Returns the value from the string $name.
	 *
	 * @param array|object $var
	 * @param string $name
	 *
	 * @return mixed
	 * @since 2.10.0
	 *
	 */
	public function get_deep( $var, $name ) {
		$names       = explode( '->', $name );
		$names       = array_filter( $names ); # filter empty values
		$search_name = array_shift( $names );

		if ( is_array( $var ) && isset( $var[ $search_name ] ) ) {
			if ( count( $names ) > 0 ) {
				return $this->get_deep( $var[ $search_name ], implode( '->', $names ) );
			} else {
				return $var[ $search_name ];
			}
		}

		if ( is_object( $var ) && isset( $var->{$search_name} ) ) {
			if ( count( $names ) > 0 ) {
				return $this->get_deep( $var->{$search_name}, implode( '->', $names ) );
			} else {
				return $var->{$search_name};
			}
		}

		return $var;
	}


	/**
	 * Sanitizes all fields from a schema sent via React.
	 *
	 * @param array $properties
	 *
	 * @return array
	 * @since 2.14.0
	 */
	public function sanitize_schema_props( $properties ) {
		$props       = [];
		$sub_schemas = [];

		foreach ( $properties as $property ) {
			if ( ! isset(
				$property['name'],
				$property['valueObject'],
				$property['valueObject']['label'],
				$property['valueObject']['selection'],
				$property['valueObject']['textValue'],
				$property['valueObject']['typeObject'],
				$property['valueObject']['typeObject']['label'],
				$property['valueObject']['typeObject']['value']
			) ) {
				continue;
			}

			$prop = [
				'id'              => esc_url( $property['name'] ),
				'subfield_select' => sanitize_text_field( $property['valueObject']['selection'] ),
				'textfield'       => sanitize_text_field( $property['valueObject']['textValue'] )
			];

			if ( isset( $property['valueObject']['subSchemaProps'] ) && count( $property['valueObject']['subSchemaProps'] ) > 0 ) {
				$uid         = uniqid( 'snip-' );
				$prop['ref'] = $uid;

				$s = Helper_Model::instance()->sanitize_schema_props( $property['valueObject']['subSchemaProps'] );

				$sub_schemas[ $uid ] = [
					'id'         => $prop['subfield_select'],
					'properties' => $s[0] # All the props
				];

				if ( isset( $s[1] ) ) {
					$sub_schemas = array_merge( $sub_schemas, $s[1] );
				}
			}

			$props[] = $prop;
		}

		return [ $props, $sub_schemas ];
	}


	/**
	 * Fetches overwrite data for a main snippet.
	 *
	 * @param int $post_id
	 * @param string $main_snippet
	 * @param string $snippet_id
	 * @param string $parent_snippet_id
	 *
	 * @return array
	 *
	 * @since      2.14.0
	 * @since      2.14.1 Moved from Admin_Snippets_Overwrite_Controller class
	 *
	 * @deprecated 2.14.3
	 */
	public function get_properties_to_overwrite( $post_id, $main_snippet, $snippet_id, $parent_snippet_id ) {
		$overwrite_data = get_post_meta( $post_id, '_wpb_rs_overwrite_data', true );
		$overwrite_data = $this->back_compat_overwrite( $overwrite_data, $post_id );

		if ( ! is_array( $overwrite_data ) ) {
			return [];
		}

		if ( count( $overwrite_data ) <= 0 ) {
			return [];
		}

		if ( ! isset( $overwrite_data[ $main_snippet ] ) ) {
			return [];
		}

		$d = [];

		foreach ( $overwrite_data[ $main_snippet ] as $prop ) {
			if ( $prop['snippet_id'] === $snippet_id && $prop['parent_snippet_id'] == $parent_snippet_id ) {
				$d[] = $prop;
			}
		}

		return $d;
	}


	/**
	 * Back Compat for Overwrite data.
	 * In version 2.14.0 we changed the way how overwrite data is stored. We correct that here.
	 *
	 * @param array $data
	 *
	 * @return array
	 *
	 * @since      2.14.0
	 * @since      2.14.0 Moved from Admin_Snippets_Overwrite_Controller class
	 *
	 * @deprecated 2.14.3
	 */
	public function back_compat_overwrite( $data, $post_id ) {
		if ( ! is_array( $data ) ) {
			return [];
		}

		if ( count( $data ) <= 0 ) {
			return $data;
		}

		$new_data = [];

		foreach ( $data as $snip_id => $d ) {
			if ( ! isset( $d['id'] ) ) {
				$new_data[ $snip_id ] = $d;
				continue;
			}

			if ( ! isset( $d['properties'] ) ) {
				continue;
			}

			$post_id = Snippets_Model::get_post_id_by_snippet_id( $snip_id );

			if ( ! $post_id ) {
				continue;
			}

			$snippet = Snippets_Model::get_first_snippet( $post_id );

			if ( ! $snippet ) {
				continue;
			}

			if ( $snippet->id !== $snip_id ) {
				continue;
			}

			foreach ( $d['properties'] as $property_id => $property_value ) {
				if ( ! is_array( $property_value ) ) {
					$property_value = [ $property_value ];
				}

				foreach ( $property_value as $pv ) {
					$new_data[ $snippet->id ][ uniqid() ] = [
						"prop_id"           => $property_id,
						"snippet_id"        => $snippet->id,
						"parent_snippet_id" => $snippet->id,
						"value"             => $pv
					];
				}

				unset( $property_id, $property_value );
			}

			foreach ( $snippet->get_sub_snippet_ids_deep() as $property_name_and_id => $sub_snippet_id ) {
				if ( ! isset( $data[ $sub_snippet_id ] ) ) {
					continue;
				}

				$sub_snip = $data[ $sub_snippet_id ];

				if ( ! isset( $sub_snip['properties'] ) ) {
					continue;
				}

				foreach ( $sub_snip['properties'] as $property_id => $property_value ) {
					if ( ! is_array( $property_value ) ) {
						$property_value = [ $property_value ];
					}

					foreach ( $property_value as $pv ) {
						$new_data[ $snippet->id ][ uniqid() ] = [
							"prop_id"           => $property_id,
							"snippet_id"        => $sub_snippet_id,
							"parent_snippet_id" => $snippet->id,
							"value"             => $pv
						];
					}

					unset( $property_id, $property_value );
				}
			}

		}

		if ( md5( serialize( $new_data ) ) !== md5( serialize( $data ) ) ) {
			update_post_meta( $post_id, '_wpb_rs_overwrite_data', $new_data );
		}

		return $new_data;
	}


	/**
	 * Fetches extra properties for list elements.
	 *
	 * @param int $post_id
	 * @param string $main_snippet
	 * @param Rich_Snippet $snippet
	 * @param string $parent_snippet_id
	 *
	 * @return array
	 *
	 * @since      2.14.0
	 * @since      2.14.1 Moved from Admin_Snippets_Overwrite_Controller class
	 *
	 * @dperecated 2.14.3
	 */
	public function get_properties_to_list( $post_id, $main_snippet, $snippet, $parent_snippet_id ) {
		$overwrite_data = get_post_meta( $post_id, '_wpb_rs_overwrite_data', true );
		$overwrite_data = $this->back_compat_overwrite( $overwrite_data, $post_id );

		if ( ! is_array( $overwrite_data ) ) {
			return [];
		}

		if ( count( $overwrite_data ) <= 0 ) {
			return [];
		}

		if ( ! isset( $overwrite_data[ $main_snippet ] ) ) {
			return [];
		}

		$d = [];

		$snippet_ids = $snippet->get_sub_snippet_ids();

		$ends_with_fct = function ( $haystack, $needle ) {
			return substr_compare( $haystack, $needle, - strlen( $needle ) ) === 0;
		};

		foreach ( $snippet_ids as $prop_name => $snippet_id ) {
			foreach ( $overwrite_data[ $main_snippet ] as $prop ) {
				if ( false === stripos( $prop['snippet_id'], $snippet_id ) ) {
					continue;
				}
				if ( $ends_with_fct( $prop['snippet_id'], $snippet_id ) ) {
					continue;
				}

				if ( $prop['parent_snippet_id'] !== $parent_snippet_id ) {
					continue;
				}

				$d[ $prop['snippet_id'] ]['properties'][] = $prop;
				$d[ $prop['snippet_id'] ]['prop_name']    = $prop_name;
			}
		}

		return $d;
	}


	/**
	 * Returns plugin data.
	 *
	 * @param null|string $field
	 *
	 * @return array|string|null
	 *
	 * @since 2.0.0
	 */
	public function get_plugin_data( $field = null ) {
		static $plugin_data;

		if ( ! is_array( $plugin_data ) ) {
			$file = ABSPATH . '/wp-admin/includes/plugin.php';

			if ( ! function_exists( '\get_plugin_data' ) && is_file( $file ) ) {
				require_once $file;
			}

			$plugin_data = function_exists( '\get_plugin_data' )
				? get_plugin_data( rich_snippets()->get_plugin_file(), false, false )
				: null;
		}

		if ( is_array( $plugin_data ) && array_key_exists( $field, $plugin_data ) ) {
			return $plugin_data[ $field ];
		}

		return $plugin_data;
	}


	/**
	 * Checks if any of the caching plugins is active.
	 *
	 * @return false
	 * @since 2.19.2
	 */
	public function is_cache_plugin_active() {

		/**
		 * Bails early if a caching plugin is active.
		 *
		 * Allows to do more "if cache plugin active" checks.
		 *
		 * @hook  wpbuddy/rich_snippets/is_cache_plugin_active/bail
		 *
		 * @param {null} $is_active
		 * @returns {null|bool} True or false if to bail early.
		 *
		 * @since 2.19.2
		 */
		$bail_early = apply_filters( 'wpbuddy/rich_snippets/is_cache_plugin_active/bail', null );

		if ( ! is_null( $bail_early ) ) {
			return $bail_early;
		}

		/**
		 * W3Total Cache
		 */
		if ( defined( 'W3TC' ) ) {
			return true;
		}

		/**
		 * WPFastestCache
		 */
		if ( class_exists( 'WpFastestCache' ) ) {
			return true;
		}

		/**
		 * LiteSpeed Cache
		 */
		if ( function_exists( 'run_litespeed_cache' ) ) {
			return true;
		}

		/**
		 * WP Super Cache
		 */
		if ( function_exists( 'wpsc_init' ) ) {
			return true;
		}

		/**
		 * Hummmingbird
		 */
		if ( class_exists( 'Hummingbird\\WP_Hummingbird' ) ) {
			return true;
		}

		/**
		 * Breeze
		 */
		if ( defined( 'BREEZE_VERSION' ) ) {
			return true;
		}

		/**
		 * Simple Cache
		 */
		if ( defined( 'SC_PATH' ) ) {
			return true;
		}

		/**
		 * Nginx Cache
		 */
		if ( class_exists( 'NginxCache' ) ) {
			return true;
		}

		/**
		 * Powered Cache
		 */
		if ( function_exists( 'powered_cache' ) ) {
			return true;
		}

		if ( class_exists( 'HyperCache' ) ) {
			return true;
		}

		/**
		 * Cachify
		 */
		if ( defined( 'CACHIFY_FILE' ) ) {
			return true;
		}

		/**
		 * NitroPack
		 */
		if ( function_exists( 'nitropack_handle_request' ) ) {
			return true;
		}

		/**
		 * CometCache
		 */
		if ( class_exists( '\WebSharks\CometCache\Classes\Plugin' ) ) {
			return true;
		}

		/**
		 * Redis Page Cache
		 */
		if ( class_exists( 'Redis_Page_Cache' ) ) {
			return true;
		}

		/**
		 * EZCache
		 */
		if ( defined( 'EZCACHE_FILE' ) ) {
			return true;
		}

		/**
		 * WP Speed of Light
		 */
		if ( defined( 'WPSOL_PLUGIN_NAME' ) ) {
			return true;
		}

		/**
		 * WP Rocket
		 */
		if ( defined( 'WP_ROCKET_VERSION' ) ) {
			return true;
		}

		/**
		 * If caching plugin is detected.
		 *
		 * Allows do your own cache plugin checks.
		 *
		 * @hook  wpbuddy/rich_snippets/is_cache_plugin_active
		 *
		 * @param {null} $is_active
		 * @returns {bool} True or false.
		 *
		 * @since 2.19.2
		 */
		return apply_filters( 'wpbuddy/rich_snippets/is_cache_plugin_active', false );
	}
}
