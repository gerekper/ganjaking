<?php
/**
 * Utility class.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Engine;
use SearchWP\Option;
use SearchWP\Source;
use SearchWP\Tokens;
use SearchWP\Settings;

/**
 * Class Utils provides project-wide utility functions.
 *
 * @since 4.0
 */
class Utils {

	/**
	 * SearchWP's slug.
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $slug = 'searchwp';

	/**
	 * Word match regex pattern.
	 *
	 * @since 4.0
	 * @var string
	 */
	public static $word_match_pattern = '/(?!<.*?)(%s)(?![^<>]*?>)/usi';

	/**
	 * Retrieves all registered post types.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_post_types() {
		$post_types = array_unique(
			array_merge( [
				'post'       => 'post',
				'page'       => 'page',
				'attachment' => 'attachment',
			],
			get_post_types( [
				'public'              => true,
				'exclude_from_search' => false,
				'_builtin'            => false,
			] ),
			get_post_types( [
				'public'              => true,
				'exclude_from_search' => true,
				'_builtin'            => false,
			] )
		) );

		return array_values( $post_types );
	}

	/**
	 * Retrieves all searchable post types.
	 *
	 * @since 4.0
	 * @param string $post_type The post type name.
	 * @param string $engine    The engine name.
	 * @return array
	 */
	public static function get_post_type_stati( string $post_type = 'post', $engine = 'default', $skip_cache = false ) {
		$cache_key = SEARCHWP_PREFIX . 'post_type_stati' . $post_type . $engine;
		$cache     = wp_cache_get( $cache_key, '' );

		if ( ! empty( $cache ) && ! $skip_cache ) {
			return $cache;
		}

		if ( 'attachment' === $post_type ) {
			$post_stati = ['inherit'];
		} else {
			$post_stati = array_values( get_post_stati( [
				'exclude_from_search' => false,
				'public'              => true,
			] ) );
		}

		$post_stati = apply_filters( 'searchwp\post_stati', $post_stati, [ 'engine' => $engine ] );
		$post_stati = apply_filters( 'searchwp\post_stati\\' . $post_type, $post_stati, [ 'engine' => $engine ] );
		$post_stati = array_unique( $post_stati );

		wp_cache_set( $cache_key, $post_stati, '', 1 );

		return $post_stati;
	}

	/**
	 * Returns the Source name for a WP_Post type.
	 *
	 * @since 4.0
	 * @param string $post_type The Post Type name.
	 * @return string|WP_Error
	 */
	public static function get_post_type_source_name( string $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			return new \WP_Error( 'source_name', __( 'Invalid post type', 'searchwp' ), $post_type );
		}

		$source_name = 'post' . SEARCHWP_SEPARATOR . $post_type;
		$source      = \SearchWP::$index->get_source_by_name( $source_name );

		if ( is_wp_error( $source ) ) {
			return new \WP_Error( 'source_name', __( 'Invalid SearchWP Source name', 'searchwp' ), $source_name );
		} else {
			return $source_name;
		}
	}

	/**
	 * Validates the submitted database table name to make sure it exists.
	 *
	 * @since 4.0
	 * @param string $table_name The database table name to check.
	 * @return bool Whether the database table exists
	 */
	public static function valid_db_table( string $table_name ) {
		global $wpdb;

		$cache = wp_cache_get( $table_name, '' );

		if ( ! empty( $cache ) ) {
			return $cache;
		}

		$valid = true;

		if ( $wpdb->get_var( $wpdb->prepare(
				"SHOW TABLES LIKE %s",
				$table_name
			) ) != $table_name ) {
			$valid = false;
		}

		wp_cache_set( $table_name, $valid, '', 1 );

		return $valid;
	}

	/**
	 * Validates the submitted database table column name to make sure it exists.
	 *
	 * @since 4.0
	 * @param string $table  The database table name to check.
	 * @param string $column The database column name of $table to check.
	 * @return bool Whether the column exists.
	 */
	public static function valid_db_column( string $table, string $column ) {
		global $wpdb;

		$cache = wp_cache_get( $table . '_' . $column, '' );

		if ( ! empty( $cache ) ) {
			return $cache;
		}

		$valid = true;

		$column_check = $wpdb->get_results(
			$wpdb->prepare(
				"SHOW COLUMNS FROM {$table} LIKE %s",
				$column
			)
		);

		$valid = ! empty( $column_check );

		wp_cache_set( $table . '_' . $column, $valid, '', 1 );

		return $valid;
	}

	/**
	 * Ensures that a compare argument is one that is supported.
	 *
	 * @since 4.0
	 * @param string $arg The compare argument to validate.
	 * @return string Validated argument.
	 */
	public static function validate_compare_arg( $arg ) {
		$valid_compare = [ '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN',
		                   'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS' ];
		$compare       = strtoupper( $arg );

		return in_array( $compare, $valid_compare, true ) ? $compare : '=';
	}

	/**
	 * Validates, sanitizes submitted clause arguments to ensure they're what we expect.
	 *
	 * @since 4.0
	 * @param array $args The clause arguments to validate.
	 * @return array The validated, sanitized arguments.
	 */
	public static function validate_clause_args( $args ) {
		$args = wp_parse_args( $args, [
			'compare' => '=',
			'type'    => 'CHAR',
			'column'  => '',
			'value'   => '',
		] );

		$column  = sanitize_text_field( $args['column'] );
		$value   = $args['value'];
		$compare = self::validate_compare_arg( $args['compare'] );

		$valid_type = [ 'CHAR', 'NUMERIC' ];
		$type       = strtoupper( $args['type'] );
		$type       = in_array( $type, $valid_type, true ) ? $type : 'CHAR';

		if ( 'CHAR' === $type ) {
			if ( is_array( $value ) ) {
				$value = array_filter( array_map( function( $array_value ) {
					return trim( sanitize_text_field( (string) $array_value ) );
				}, (array) $value ) );
			} else {
				$value = sanitize_text_field( (string) $value );
			}
		} elseif ( 'NUMERIC' === $type ) {
			if ( is_array( $value ) ) {
				$value = array_filter( array_map( function( $array_value ) {
					return is_float( $array_value ) ? (float) $array_value : (int) $array_value;
				}, (array) $value ) );
			} else {
				$value = is_float( $value ) ? (float) $value : (int) $value;
			}
		}

		// Some compares require an array value.
		if ( in_array( $compare, [ 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ], true ) ) {
			$value = (array) $value;
		}

		return [
			'column'  => $column,
			'value'   => $value,
			'compare' => $compare,
			'type'    => $type,
		];
	}

	/**
	 * Retrieves filtered weight definitions.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_weight_definitions() {
		$weights = array_filter( (array) apply_filters( 'searchwp\weights', [
			1   => __( 'Baseline Relevance', 'searchwp' ),
			150 => __( 'Increased Relevance', 'searchwp' ),
			300 => __( 'Highest Relevance', 'searchwp' ),
		] ), function( $weight ) {
			return is_numeric( $weight ) && $weight > 0;
		}, ARRAY_FILTER_USE_KEY );

		ksort($weights);

		return $weights;
	}

	/**
	 * Retrieves the maximum possible weight value.
	 *
	 * @since 4.0
	 * @return int
	 */
	public static function get_max_engine_weight() {
		$defs = array_keys( self::get_weight_definitions() );

		return count( $defs ) > 1 ? $defs[count( $defs ) - 1] : $defs[0];
	}

	/**
	 * Retrieves the minimum possible weight value.
	 *
	 * @since 4.0
	 * @return int
	 */
	public static function get_min_engine_weight() {
		$defs = array_keys( self::get_weight_definitions() );

		return $defs[0];
	}

	/**
	 * Retrieves all meta keys for the submitted post type.
	 *
	 * @since 4.0
	 * @param string $post_type The post type name.
	 * @param string $search    Search string.
	 * @return array
	 */
	public static function get_meta_keys_for_post_type( $post_type = 'post', $search = false ) {
		global $wpdb;

		if ( ! post_type_exists( $post_type ) ) {
			return [];
		}

		$cache_key = SEARCHWP_PREFIX . 'meta_keys_' . md5( serialize( [ $post_type, $search ] ) );
		$cache     = wp_cache_get( $cache_key, '' );

		if ( ! empty( $cache ) ) {
			return $cache;
		}

		$values      = [ $post_type ];
		$placeholder = self::get_placeholder();

		if ( $search && '*' !== $search ) {
			// Partial matching (using asterisks) is supported, so we're going to utilize that if applicable.
			if ( false === strpos( '*', $search ) ) {
				$search = '*' . $search . '*';
			}

			$values[]    = str_replace( '*', $placeholder, $wpdb->esc_like( $search ) );
			$search      = "AND {$wpdb->postmeta}.meta_key LIKE %s";
		} else {
			$search = '';
		}

		$ignored_meta_keys = (array) apply_filters( 'searchwp\source\post\attributes\meta\ignored', [
			'_edit_lock',
			'_edit_last',
			'_wp_page_template',
			'_wp_trash_meta_status',
			'_wp_trash_meta_time',
			'_wp_desired_post_slug',
			SEARCHWP_PREFIX . 'content', // This is useless unless Document Content proper is added.
			SEARCHWP_PREFIX . 'content_skipped', // Internal.
		], [
			'post_type' => $post_type,
		] );

		if ( ! empty( $ignored_meta_keys ) ) {
			$values  = array_merge( $values, $ignored_meta_keys );
			$ignored = "AND {$wpdb->postmeta}.meta_key NOT IN ("
				. implode( ',', array_fill( 0, count( $ignored_meta_keys ), '%s' ) ) . ')';
		} else {
			$ignored = '';
		}

		// MAYBE: Consider post stati? This adds overhead and doesn't feel worth it at this time.
		$post_type_meta_keys = $wpdb->get_col(
			$wpdb->prepare("
				SELECT DISTINCT({$wpdb->postmeta}.meta_key)
				FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->postmeta}
				ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE {$wpdb->posts}.post_type = %s
				AND {$wpdb->postmeta}.meta_key != ''
				AND {$wpdb->postmeta}.meta_key NOT LIKE '_oembed_%%'
				{$search} {$ignored}",
				array_map( function( $value ) use ( $placeholder ) {
					if ( ! is_string( $value ) ) {
						return $value;
					}

					return str_replace( $placeholder, '%', $value );
				}, $values )
			)
		);

		wp_cache_set( $cache_key, $post_type_meta_keys, '', 1 );

		return $post_type_meta_keys;
	}

	/**
	 * Retrieves all meta keys for the submitted post type.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_meta_keys_for_users( $search = false ) {
		global $wpdb;

		$cache_key = SEARCHWP_PREFIX . 'user_meta_keys_' . md5( $search );
		$cache     = wp_cache_get( $cache_key, '' );

		if ( ! empty( $cache ) ) {
			return $cache;
		}

		$values      = [];
		$placeholder = self::get_placeholder();

		if ( $search && '*' !== $search ) {
			// Partial matching (using asterisks) is supported, so we're going to utilize that if applicable.
			if ( false === strpos( '*', $search ) ) {
				$search = '*' . $search . '*';
			}

			$values[]    = str_replace( '*', $placeholder, $wpdb->esc_like( $search ) );
			$search      = "AND {$wpdb->usermeta}.meta_key LIKE %s";
		} else {
			$search = '';
		}

		$ignored_meta_keys = (array) apply_filters( 'searchwp\source\post\attributes\meta\ignored', [
			'rich_editing',
			'syntax_highlighting',
			'admin_color',
			'use_ssl',
			'show_admin_bar_front',
			'locale',
			'session_tokens',
			'wp_dashboard_quick_press_last_post_id',
			'community-events-location',
			'managenav-menuscolumnshidden',
			'metaboxhidden_nav-menus',
			'nav_menu_recently_edited',
			'closedpostboxes_nav-menus',
			SEARCHWP_PREFIX . 'searchwp_ignored_queries',
		] );

		if ( ! empty( $ignored_meta_keys ) ) {
			$values  = array_merge( $values, $ignored_meta_keys );
			$ignored = "AND {$wpdb->usermeta}.meta_key NOT IN ("
				. implode( ',', array_fill( 0, count( $ignored_meta_keys ), '%s' ) ) . ')';
		} else {
			$ignored = '';
		}

		// MAYBE: Consider post stati? This adds overhead and doesn't feel worth it at this time.
		$user_meta_keys = $wpdb->get_col(
			$wpdb->prepare("
				SELECT DISTINCT({$wpdb->usermeta}.meta_key)
				FROM {$wpdb->usermeta}
				WHERE {$wpdb->usermeta}.meta_key != ''
				{$search} {$ignored}",
				array_map( function( $value ) use ( $placeholder ) {
					if ( ! is_string( $value ) ) {
						return $value;
					}

					return str_replace( $placeholder, '%', $value );
				}, $values )
			)
		);

		wp_cache_set( $cache_key, $user_meta_keys, '', 1 );

		return $user_meta_keys;
	}

	/**
	 * Generates a unique placeholder.
	 *
	 * @since 4.0
	 * @return string
	 */
	public static function get_placeholder() {
		$algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$salt = (string) rand();

		return '{' . hash_hmac( $algo, uniqid( $salt, true ), $salt ) . '}';
	}

	/**
	 * Tokenizes data.
	 *
	 * @since 4.0
	 * @param mixed $data The data to tokenize.
	 * @return Tokens The tokenized data.
	 */
	public static function tokenize( $data ) {
		return new Tokens( $data );
	}

	/**
	 * Low level handling of a string to ensure it's UTF-8, emoji handled properly, unwanted characters removed.
	 *
	 * @since 4.0
	 * @param string $string The string to normalize.
	 * @return string The normalized string.
	 */
	public static function normalize_string( string $string ) {
		$string = apply_filters( 'searchwp\normalize_string', $string );

		// We prefer UTF-8.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$string = mb_convert_encoding( $string, 'UTF-8', 'UTF-8' );
		}

		// Emoji are fine, but if we can avoid them we will.
		if ( apply_filters( 'searchwp\allow_emoji', false ) ) {
			$string = self::replace_4_byte( $string );
		}

		// Handle strange entities that are better suited by not strange entities.
		$string = preg_replace( '~\x{00AD}~u', '-', $string ); // &shy; soft hyphen => hyphen.

		return $string;
	}

	/**
	 * Enforce 4-byte UTF-8 when utf8mb4 is not supported.
	 *
	 * @since 4.0
	 * @link http://stackoverflow.com/questions/16496554/can-php-detect-4-byte-encoded-utf8-chars
	 * @param string $string The source string.
	 * @return string
	 */
	public static function replace_4_byte( $string ) {
		return preg_replace( '%(?:
              \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )%xs', '', $string );
	}

	/**
	 * Parses any data type to retrieve a useful string.
	 *
	 * @since 4.0
	 * @param mixed $data The data to parse.
	 * @return string The stringified version of the data.
	 */
	public static function get_string_from( $data ) {
		// Strings could be stringified versions of JSON or serialized data.
		// We need to determine that before further processing.
		if ( is_string( $data ) ) {
			$data = self::maybe_decode_stringified( $data );
		}

		// Data is still mixed at this point.
		if ( is_string( $data ) ) {
			$data = self::decode_string( $data );
		} elseif ( is_array( $data ) || is_object( $data ) ) {
			$data = implode( ' ', array_map( function( $value ) {
				return self::get_string_from( $value );
			}, (array) $data ) );
		} elseif ( ! is_bool( $data ) ) {
			// Integers, Floats can be strings.
			$data = (string) $data;
		}

		return $data;
	}

	/**
	 * Cleans, sanitizes, removes all punctuation and normalizes a string.
	 *
	 * @since 4.0
	 * @param string $string The string to clean.
	 * @return string
	 */
	public static function clean_string( string $string ) {
		$string = str_replace( self::get_punctuation(), ' ', $string );
		$string = preg_replace( '/[[:punct:][:space:]]/uiU', ' ', $string );
		$string = function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
		$string = preg_replace( '/\s+/', ' ', $string );

		return trim( $string );
	}

	/**
	 * Removes the submitted array of strings from a string.
	 *
	 * @since 4.0
	 * @param array  $to_remove The strings to remove.
	 * @param string $string    The string from which to remove.
	 * @return string The string without the submitted array of strings.
	 */
	public static function remove_strings_from_string( array $to_remove, string $string ) {
		// Add the buffer the entire string so we can whole-word replace.
		$string = '  ' . $string . '  ';

		// Need to buffer the terms to prevent replacement overrun.
		$to_remove = array_map( function( $val ) {
			return ' ' . $val . ' ';
		}, array_unique( $to_remove ) );

		// Remove the matches.
		$string = str_ireplace( $to_remove, ' ', $string );

		// Remove the buffer and return.
		$string = trim( preg_replace( '/\s+/', ' ', $string ) );

		return $string;
	}

	/**
	 * Decodes a string into something we expect. Strips slashes and decodes.
	 *
	 * @since 4.0
	 * @param string $string The string to decode.
	 * @return string The stripslashed and decoded string.
	 */
	public static function decode_string( string $string ) {
		$string = ! seems_utf8( $string ) ? utf8_encode( $string ) : $string;
		$string = stripslashes( $string );
		$string = html_entity_decode( $string, ENT_QUOTES );
		$string = trim( $string );
		$string = str_replace( array( '”', '“' ), '"', $string );

		return $string;
	}

	/**
	 * Parses HTML to extract useful bits from the content itself and valid HTML tag attributes.
	 *
	 * @since 4.0
	 * @param string $html The incoming HTML.
	 * @return string A tag-less version of the HTML.
	 */
	public static function stringify_html( $html ) {
		$valid_html_tags = (array) apply_filters( 'searchwp\valid_html_tags', [
			'a'     => [ 'title' ],
			'img'   => [ 'alt', 'src', 'longdesc', 'title' ],
			'input' => [ 'placeholder', 'value' ],
		 ] );

		 $html = ! empty( $html ) ? html_entity_decode( $html, ENT_QUOTES ) : '';
		 $invalid_nodes = apply_filters( 'searchwp\invalid_html_nodes', [ 'script', 'style', 'iframe', 'link', ] );

		 if (
			empty( $valid_html_tags )
			|| empty( $html )
			|| ! class_exists( 'DOMDocument' )
			|| ! class_exists( 'DOMXPath' )
			|| ! function_exists( 'libxml_use_internal_errors' )
		) {
			// We can't properly parse this so do what we can: remove unwanted nodes and strip tags.
			if ( ! empty( $invalid_nodes ) ) {
				$html = preg_replace( '/(<(' . implode( '|', $invalid_nodes ) . ')\b[^>]*>).*?(<\/\2>)/is', ' ', $html );
			}

			return strip_tags( $html );
		}

		// Parse the HTML into something we can work with.
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( $html );
		$xpath = new \DOMXPath( $dom );

		// Remove unwanted nodes.
		if ( ! empty( $invalid_nodes ) ) {
			foreach ( $invalid_nodes as $tag ) {
				foreach ( $xpath->query( '//body//' . $tag ) as $item ) {
					$item->parentNode->removeChild( $item );
				}
			}

			// With unwanted nodes removed, reload the HTML.
			$dom->loadHTML( $dom->saveHTML( $dom->documentElement->lastChild ) );
		}

		// Extract desirable tokens from attributes before we remove all tags.
		$attribute_content = [];
		foreach( $valid_html_tags as $tag => $attributes ) {
			$node_list = $dom->getElementsByTagName( $tag );

			if ( empty( $node_list ) ) {
				continue;
			}

			foreach ( $node_list as $node_index => $node ) {
				$node = $node_list->item( $node_index );

				if ( ! $node->hasAttributes() ) {
					continue;
				}

				foreach( $node->attributes as $attribute ) {
					if ( isset( $attribute->name ) && in_array( $attribute->name, $attributes, true ) ) {
						$attribute_content[] = $attribute->nodeValue;
					}
				}
			}
		}

		return strip_tags( $html ) . ' ' . implode( ' ', $attribute_content );
	}

	/**
	 * Getter for token patterns.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_token_regex_patterns() {
		$patterns = apply_filters( 'searchwp\tokens\regex_patterns', [
			// Function names.
			"/\b(\\w+?)?\\(|[\\s\\n]\\(/is",

			// Date formats.
			'/\b([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\b/is',     // YYYY-MM-DD
			'/\b([0-9]{1,2}-[0-9]{1,2}-[0-9]{4})\b/is',     // MM-DD-YYYY
			'/\b([0-9]{4}\\/[0-9]{1,2}\\/[0-9]{1,2})\b/is', // YYYY/MM/DD
			'/\b([0-9]{1,2}\\/[0-9]{1,2}\\/[0-9]{4})\b/is', // MM/DD/YYYY

			// IP addresses.
			'/\b(\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3})\b/is', // IPv4.

			// Initials.
			"/\\b((?:[A-Za-z]\\.\\s{0,1})+)/isu",

			// Version numbers: 1.0 or 1.0.4 or 1.0.5b1.
			'/\b([a-z0-9]+(?:\\.[a-z0-9]+)+)\b/is',

			// Serial numbers.
			'/(?=\S*[\-\_])([[:alnum:]\-\_]+)/ius', // Hyphen/underscore separator.

			// Strings of digits.
			"/\\b(\\d{1,})\\b/is",

			// e.g. M&M, M & M.
			"/\\b([[:alnum:]]+\\s?(?:&\\s?[[:alnum:]]+)+)\b/isu",
		] );

		return array_unique( (array) $patterns );
	}

	/**
	 * Decodes stringified strings.
	 *
	 * @since 4.0
	 * @param string $string The string to decode.
	 * @return mixed
	 */
	public static function maybe_decode_stringified( string $data ) {
		$json_decoded_input = json_decode( $data, true );

		if ( is_null( $json_decoded_input ) ) {
			// It's not JSON, but it might be serialized.
			$data = maybe_unserialize( $data );
		} else {
			// It was JSON.
			if ( ! is_numeric( $data ) ) {
				$data = $json_decoded_input;
			}
		}

		return $data;
	}

	/**
	 * Getter for punctuation reference.
	 *
	 * @since 4.0
	 * @return array
	 */
	public static function get_punctuation() {
		return apply_filters( 'searchwp\utils\punctuation', [
			'(', ')', '·', "'", '"', '´', '’', '‘', '”', '“', '„', '—', '=', '–', '×', '©',
			'…', '€', '\n', '.', ',', '/', '\\', '|', '[', ']', '{', '}', '•', '`', '™',
			'>', '<', ':', ';', '_', '+', '$', '@', '%', '*', '#', '!', '&', '^', '®', ] );
	}

	/**
	 * Retrieves WP_Post IDs to be used as a limiter.
	 *
	 * @since 4.0
	 * @return array The WP_Post IDs.
	 */
	public static function get_filtered_post__in( array $args = [], $skip_cache = false ) {
		$ids = (array) apply_filters( 'searchwp\post__in', [], $args );
		$ids = array_map( 'absint', $ids );
		$ids = array_unique( $ids );

		return $ids;
	}

	/**
	 * Retrieves WP_Post IDs to be excluded.
	 *
	 * @since 4.0
	 * @return array The WP_Post IDs.
	 */
	public static function get_filtered_post__not_in( array $args = [], $skip_cache = false ) {
		$ids = (array) apply_filters( 'searchwp\post__not_in', [], $args );
		$ids = array_map( 'absint', $ids );
		$ids = array_unique( $ids );

		return $ids;
	}

	/**
	 * Generate a string of comma separated integers from an existing string of
	 * comma separated integers or an array of integers
	 *
	 * @since 2.5.6
	 * @param string|array $source Array of integers or string of (maybe comma separated) integers
	 * @return string Comma separated string of integers
	 */
	public static function get_integer_csv_string_from( $source = '' ) {
		if ( ! is_string( $source ) && ! is_array( $source ) || empty( $source ) ) {
			return '';
		}

		if ( is_array( $source ) ) {
			$source = implode( ',', $source );
		}

		if ( false !== strpos( $source, ',' ) ) {
			$source = explode( ',' , $source );
			$source = array_map( 'trim', $source );
			$source = array_map( 'absint', $source );
			$source = array_unique( $source );
			$source = implode( ',', $source );
		} else {
			$source = (string) absint( $source );
		}

		return $source;
	}

	/**
	 * Parses WHERE clauses into SQL clauses.
	 *
	 * @since 4.0
	 * @param string $db_table Database table name.
	 * @param array  $clauses  Clauses to parse.
	 * @return array Parsed WHERE clauses.
	 */
	public static function parse_where( string $db_table, $clauses ) {
		global $wpdb;

		$values       = [];
		$placeholders = [];

		if ( empty( $clauses ) ) {
			return false;
		}

		$relation = isset( $clauses['relation'] ) && 'OR' === $clauses['relation'] ? 'OR' : 'AND';
		unset( $clauses['relation'] );

		foreach ( $clauses as $clause ) {
			// In order to get here, the clause column has been validated.
			$validated_column = "`{$db_table}`.`{$clause['column']}`";
			$type_placeholder = isset( $clause['type'] ) && 'NUMERIC' === $clause['type'] ? '%d' : '%s';

			if ( empty( $clause['compare'] ) ) {
				$clause['compare'] = '=';
			}

			switch ( $clause['compare'] ) {
				case 'LIKE':
				case 'NOT LIKE':
					array_push( $values, '%' . $wpdb->esc_like( $clause['value'] ) . '%' );

					$placeholders[] = "( {$validated_column} {$clause['compare']} {$type_placeholder} )";
					break;
				case 'BETWEEN':
				case 'NOT BETWEEN':
					// Array of two values required.
					if ( ! is_array( $clause['value'] ) || 2 !== count( $clause['value'] ) ) {
						break;
					}

					$values = array_merge( $values, array_values( $clause['value'] ) );

					$placeholders[] = "( {$validated_column} {$clause['compare']} {$type_placeholder} AND {$type_placeholder} )";
					break;
				case 'IN':
				case 'NOT IN':
					// If the value is empty there's nothing to do with this clause.
					if ( empty( $clause['value'] ) ) {
						break;
					}

					$values = array_merge( $values, $clause['value'] );

					$placeholders[] = "( {$validated_column} {$clause['compare']} ( " .
						implode( ', ',
							array_fill( 0, count( $clause['value'] ), $type_placeholder )
						) . ' ) )';
					break;
				default: // ‘=’, ‘!=’, ‘>’, ‘>=’, ‘<‘, ‘<=’
					array_push( $values, $clause['value'] );

					if ( ! in_array( $clause['compare'], [ '=', '!=', '>', '>=', '<', '<=' ], true ) ) {
						$clause['compare'] = '=';
					}

					$placeholders[] = "( {$validated_column} {$clause['compare']} {$type_placeholder} )";
			}
		}

		return [
			'values'       => $values,
			'placeholders' => $placeholders,
			'relation'     => $relation,
			'clauses'      => $clauses,
		];
	}

	/**
	 * Extracts partial matches from submitted array.
	 *
	 * @since 4.0
	 * @param array $mixed Array of strings to work with
	 * @return array Separated partial and full matches.
	 */
	public static function separate_partial_matches( array $mixed ) {
		// Extract partial matches designated with *.
		$partial_matches = array_values( array_filter( $mixed, function( $single ) {
			return false !== strpos( $single, '*' );
		} ) );

		// Remove partial matches from incoming.
		$full_matches = array_filter( $mixed, function( $single ) use ( $partial_matches ) {
			return ! in_array( $single, $partial_matches );
		} );

		return [
			'partial' => $partial_matches,
			'full'    => $full_matches,
		];
	}

	/**
	 * Determines whether the submitted search string has phrase(s)
	 * and that logic has been enabled either by setting or hook.
	 *
	 * @since 4.0
	 * @param string          $search_string The search string to analyze.
	 * @param \SearchWP\Query $query         The query being run.
	 * @return bool|string[]
	 */
	public static function search_string_has_phrases( string $search_string, Query $query ) {
		$phrases = self::get_phrases_from_string( $search_string );

		if (
			! empty( $phrases )
			&& apply_filters( 'searchwp\query\logic\phrase',
					\SearchWP\Settings::get( 'quoted_search_support', 'boolean' ),
					$query )
		) {
			return $phrases;
		} else {
			return false;
		}
	}

	/**
	 * Extracts phrases (delimited by double quotes) from a string.
	 *
	 * @since 4.0
	 * @param string $string String to parse for phrases.
	 * @return array The phrases in the string (without quotes).
	 */
	public static function get_phrases_from_string( string $string ) {
		$phrases = [];

		preg_match_all( '/"([^"]*)"/miu', $string, $matches, PREG_SET_ORDER, 0 );

		if ( empty( $matches ) ) {
			return $phrases;
		}

		// Make sure there are no single word phrases.
		foreach ( $matches as $match ) {
			if ( false !== strpos( $match[1], ' ' ) ) {
				$phrases[] = $match[1];
			}
		}

		return $phrases;
	}

	/**
	 * Retrieves globally chosen Attribute options across all engines for a Source.
	 *
	 * @since 4.0
	 * @param Attribute $attribute Attribute to consider.
	 * @param Source    $source    Source to consider.
	 * @return array Option values.
	 */
	public static function get_global_attribute_options_settings( Attribute $attribute, Source $source ) {
		$source_name = $source->get_name();

		$per_engine = array_filter( array_map( function( Engine $engine ) use ( $attribute, $source_name ) {
			return array_filter( array_map( function( Source $source ) use ( $engine, $source_name, $attribute ) {
				if ( $source_name !== $source->get_name() || false === $attribute->get_options() ) {
					return false;
				} else {
					return $engine->get_source_attribute_options_settings( $source, $attribute->get_name() );
				}
			}, $engine->get_sources() ) );
		}, Settings::get_engines() ) );

		if ( empty( $per_engine ) ) {
			return [];
		}

		// Extract ony the unique Attribute Option names across all Engines for this Source.
		return array_filter( array_unique(
			call_user_func_array( 'array_merge',
				array_values( array_map( function( $engine_sources ) {
					return array_keys( call_user_func_array( 'array_merge', $engine_sources ) );
				}, $per_engine ) )
			)
		) );
	}

	/**
	 * Whether any Engine has a Source.
	 *
	 * @since 4.0.13
	 * @param Source    $source    Source to consider.
	 * @return boolean
	 */
	public static function any_engine_has_source( Source $source ) {
		$engines = array_filter( Settings::get_engines(), function( $engine ) use ( $source ) {
			return in_array( $source->get_name(), array_keys( $engine->get_sources() ) );
		} );

		return ! empty( $engines );
	}

	/**
	 * Retrieve database details.
	 *
	 * @since 4.0.13
	 * @return array
	 */
	public static function get_db_details() {
		global $wpdb;

		if ( $wpdb->use_mysqli ) {
			$mysql_server_type = mysqli_get_server_info( $wpdb->dbh );
		} else {
			$mysql_server_type = mysql_get_server_info( $wpdb->dbh );
		}

		return [
			'engine'  => stristr( $mysql_server_type, 'mariadb' ) ? 'MariaDB' : 'MySQL',
			'version' => $wpdb->get_var( 'SELECT VERSION()' ),
		];
	}

	/**
	 * Whether any Engine has a Source Attribute.
	 *
	 * @since 4.0
	 * @param Attribute $attribute Attribute to consider.
	 * @param Source    $source    Source to consider.
	 * @return boolean
	 */
	public static function any_engine_has_source_attribute( Attribute $attribute, Source $source ) {
		$values = self::get_global_attribute_settings_per_engine( $attribute, $source );

		return ! empty( $values );
	}

	/**
	 * Whether any Engine has a Source Attribute with the submitted option.
	 *
	 * @param Attribute $attribute The Attribute to check.
	 * @param Source    $source    The Source to check.
	 * @param string    $option    The option to check.
	 * @return bool
	 */
	public static function any_engine_has_source_attribute_option( Attribute $attribute, Source $source, string $option ) {
		$values = self::get_global_attribute_settings_per_engine( $attribute, $source );

		if ( empty( $values ) ) {
			return false;
		}

		// $values is a multidimensional array:
		// Engine -> Source -> key value pair [option] => weight
		$existing_options = [];
		foreach ( $values as $engine ) {
			foreach( $engine as $source ) {
				$existing_options = array_merge( $existing_options, array_keys( $source ) );
			}
		}

		$existing_options = array_unique( array_filter( $existing_options ) );

		$has_option = false;

		// If there is an 'any' we have a match right away.
		if ( in_array( '*', $existing_options ) ) {
			$has_option = true;
		}

		// Exact match?
		if ( ! $has_option ) {
			foreach ( $existing_options as $existing_option ) {
				if ( $existing_option === $option ) {
					$has_option = true;
					break;
				}
			}
		}

		// Partial match?
		if ( ! $has_option ) {
			foreach ( $existing_options as $existing_option ) {
				if ( false === strpos( $existing_option, '*' ) ) {
					continue;
				}

				$pattern = '/' . str_replace( '*', '.{1,}', $existing_option ) . '/iu';
				preg_match( $pattern, $option, $matches );

				if ( ! empty( $matches ) ) {
					$has_option = true;
					break;
				}
			}
		}

		return $has_option;
	}

	/**
	 * Retrieve settings for Source Attribute across all Engines.
	 *
	 * @since 4.0
	 * @param Attribute $attribute The Attribute to consider.
	 * @param Source    $source    The Source to consider.
	 * @return array
	 */
	public static function get_global_attribute_settings_per_engine( Attribute $attribute, Source $source ) {
		return array_filter( array_map( function( Engine $engine ) use ( $attribute ) {
			return array_filter( array_map( function( Source $source ) use ( $attribute ) {
				$source_attribute = $source->get_attribute( $attribute->get_name() );
				return $source_attribute ? $source_attribute->get_settings() : false;
			}, $engine->get_sources() ) );
		}, Settings::get_engines() ) );
	}

	/**
	 * Processor for engine source settings to ensure the data is normalized, specifically
	 * to ensure that Attribute Options are properly namespaced.
	 *
	 * @since 4.0
	 * @param Engine $engine Engine to work with.
	 * @return array Normalized source settings.
	 */
	public static function normalize_engine_source_settings( Engine $engine ) {
		// Namespace any Attribute Options.
		$sources = [];

		foreach ( $engine->get_sources() as $source ) {
			$attributes = $source->get_attributes();

			if ( empty( $attributes ) ) {
				continue;
			}

			$normalized = [];

			foreach ( $attributes as $attribute ) {
				$data = $attribute->get_settings();

				if ( ! is_array( $data ) ) {
					if ( ! empty( $data ) ) {
						$normalized[ $attribute->get_name() ] = $data;
					}
					continue;
				}

				// Namespace these Attribute Options
				foreach ( $data as $option => $weight ) {
					if ( ! empty( $weight ) ) {
						$normalized[ $attribute->get_name() . SEARCHWP_SEPARATOR . $option ] = $weight;
					}
				}
			}

			$sources[ $source->get_name() ]['attributes'] = $normalized;
		}

		return $sources;
	}

	/**
	 * Retrieves all Source (names) that are utilized across all engines.
	 *
	 * @since 4.0
	 * @return string[] Source names.
	 */
	public static function get_global_engine_source_names() {
		return array_unique(
			call_user_func_array( 'array_merge',
				array_values(
					array_map( function( $engine ) {
						return array_map( function( $source ) {
							return $source->get_name();
						}, $engine->get_sources() );
					}, Settings::get_engines() )
				)
			)
		);
	}

	/**
	 * Prepares Options collection for serialization.
	 *
	 * @since 4.0
	 * @param mixed $options Options
	 * @return mixed|array Options
	 */
	public static function normalize_options( $options ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		$options = array_filter( $options, function( $option ) {
			return $option instanceof Option;
		} );

		return array_values( array_map( function( Option $option ) {
			// We want to trigger jsonSerialize().
			return json_decode( json_encode( $option ), true );
		}, $options ) );
	}

	/**
	 * Normalizes an Engine config.
	 *
	 * @since 4.0
	 * @param array $config
	 * @return array
	 */
	public static function normalize_engine_config( array $config ) {
		return [
			'label'    => $config['label'],
			'settings' => $config['settings'],
			'sources'  => array_map( function( $source ) {
				$source_options = ! isset( $source['options'] )
										|| empty( $source['options'] )
										|| ! is_array( $source['options'] )
									? []
									: array_filter( $source['options'], function ( $option ) {
										return isset( $option['enabled'] ) &&
											( 'true' === $option['enabled'] || true === $option['enabled'] );
										} );

				return [
					'attributes' => array_filter( array_map( function( $attribute ) {
						$settings = ! empty( $attribute['settings'] ) ? $attribute['settings'] : false;

						if ( is_array( $settings ) ) {
							$settings = call_user_func_array( 'array_merge', array_map( function( $setting, $weight ) {
								return [ $setting => $weight ];
							}, array_keys( $settings ), array_values( $settings ) ) );
						}

						return $settings;
					}, $source['attributes'] ) ),
					'rules' => ! isset( $source['ruleGroups'] ) || empty( $source['ruleGroups'] ) ? [] :
						array_map( function( $rule_group ) {
							return [
								'type'  => $rule_group['type'],
								'rules' => array_map( function( $rule ) {
									return [
										'option'    => isset( $rule['option'] ) ? $rule['option'] : null,
										'condition' => $rule['condition'],
										'rule'      => $rule['rule'],
										'value'     => is_array( $rule['value'] )
														? array_map( function( $value ) {
															return is_array( $value ) ? $value['value'] : $value;
														}, $rule['value'] )
														: $rule['value'],
									];
								}, $rule_group['rules'] ),
							];
						}, $source['ruleGroups'] ),
					'options' => empty( $source_options ) ? [] :
						call_user_func_array( 'array_merge', array_map( function( $option ) {
							return [ $option['name'] => [
								'enabled' => true,
								'option'  => isset( $option['option'] ) ? $option['option'] : null,
								'value'   => isset( $option['value'] ) ? $option['value'] : null,
							] ];
						}, $source_options ) ),
				];
			}, $config['sources'] ),
		];
	}

	/**
	 * Localizes a script using a standard set of variables.
	 *
	 * @since 4.0
	 * @param string $handle The script handle to localize.
	 * @param array $settings Additional settings to localize.
	 * @return void
	 */
	public static function localize_script( string $handle, array $settings = [] ) {
		wp_localize_script( $handle, '_SEARCHWP', array_merge( [
			'nonce'      => wp_create_nonce( SEARCHWP_PREFIX . 'settings' ),
			'separator'  => SEARCHWP_SEPARATOR,
			'prefix'     => SEARCHWP_PREFIX,
			'i18n'       => \SearchWP\Admin\i18n::get(),
			'misc'       => [
				'colors' => Settings::get_colors(),
				'prefix' => SEARCHWP_PREFIX,
			],
		], $settings ) );
	}

	/**
	 * Applies regex to array of needles depending on whether we want partial matches.
	 *
	 * @since 4.0
	 * @param string[] $needles The needles to work with.
	 * @param bool     $partial Whether we want partial matches.
	 * @return array
	 */
	public static function map_needles_for_regex( array $needles, $partial = false ) {
		if ( ! $partial ) {
			// Restrict matches to only whole words.
			$needles = array_map( function( $word ) {
				return '\b' . preg_quote( $word, '/' ) . '\b';
			}, $needles );
		} else {
			// Highlight the whole word when a partial match is found.
			$needles = array_map( function( $word ) {
				return '\b([^\s]' . preg_quote( $word, '/' ) . '.*?|' . preg_quote( $word, '/' ) . '.*?)\b';
			}, $needles );
		}

		return $needles;
	}

	/**
	 * Determine whether a string contains at least one of the submitted Tokens.
	 *
	 * @since 4.0
	 * @param string $string     The string to check.
	 * @param array  $substrings The substrings to find.
	 * @return bool  Whether the string has at least one substring.
	 */
	public static function string_has_substring_from_string( string $string, string $substrings ) {
		$substrings = array_map( function( $substring ) {
			return preg_quote( $substring, '/' );
		}, explode( ' ', $substrings ) );

		$needles    = self::map_needles_for_regex( $substrings, Settings::get( 'partial_matches' ) );
		$pattern    = sprintf( self::$word_match_pattern . 'i', implode( '|', $needles ) );

		preg_match_all( $pattern, $string, $matches, PREG_SET_ORDER, 0 );

		return ! empty( $matches );
	}

	/**
	 * Strips Shortcodes from the submitted string.
	 *
	 * @since 4.0.14
	 * @param string $string The string to clean
	 * @param bool $aggressive Whether to remove all Shortcode-formatted content (default is only registered Shortcodes)
	 * @return string
	 */
	public static function strip_shortcodes( string $string, $aggressive = false ) {
		$aggressive = apply_filters( 'searchwp\utils\strip_shortcodes\aggressive', $aggressive, $string );
		$aggressive_pattern = '/\[.*?\]/miu';

		return $aggressive ? preg_replace( $aggressive_pattern, '', $string ) : strip_shortcodes( $string );
	}

	/**
	 * Builds an excerpt from a string that's centered on the location of the first search term it can find.
	 *
	 * @since 4.0
	 * @param string $string     The string to trim.
	 * @param string $substrings The substrings to use as the center.
	 * @param int    $length     How many words to include.
	 * @return string
	 */
	public static function trim_string_around_substring( string $string, string $substrings, $length = 55 ) {
		$text   = self::strip_shortcodes( $string, true );
		$text   = excerpt_remove_blocks( $text );
		$length = (int) apply_filters( 'excerpt_length', $length );
		$more   = apply_filters( 'searchwp\utils\excerpt_more', ' [&hellip;] ' );

		$flag = false;
		foreach ( explode( ' ', $substrings ) as $substing ) {
			$needles = self::map_needles_for_regex( [ $substing ], Settings::get( 'partial_matches' ) );
			$pattern = sprintf( self::$word_match_pattern . 'i', implode( '|', $needles ) );

			if ( 1 == preg_match( $pattern, $text, $matches ) ) {
				$flag = $matches[0];
				break;
			}
		}

		$words = explode( ' ', $text );

		// If there was no flag or there aren't enough words just start from the beginning.
		if ( empty( $flag ) || $words <= $length ) {
			return wp_trim_words( $text, $length, $more );
		}

		// There was a flag found, so we can work from that.
		$flag_index = ! Settings::get( 'partial_matches' )
						? array_search( $flag, $words )
						: array_filter( $words, function( $word ) use( $flag ) {
							return false !== mb_stripos( $word, $flag );
						} );

		// If no flag was found, fall back to the native excerpt.
		if ( empty( $flag_index ) ) {
			return wp_trim_words( $text, $length, $more );
		} else {
			// Depending on whether partial matching was performed we have either a filtered array or an array key.
			$flag_index = is_array( $flag_index ) ? key( $flag_index ) : $flag_index;
		}

		// This may cause an off by one word issue but that's ok.
		$buffer = (int) floor( $length / 2 );

		// There are a few conditions that could be met here:
		// 1) The flag has both start and end buffers to work with.
		// 2) The flag was too close to the beginning to fit the start buffer.
		// 3) The flag was too close to the end to fit the end buffer.

		$start     = $flag_index - $buffer;
		$end       = $buffer + $flag_index;
		$before_ok = $start >= 0;
		$after_ok  = $end <= count( $words ) - 1;

		if ( ! $before_ok && $after_ok ) {
			$start      = 0;
			$adjustment = absint( $flag_index - $buffer );
			$end        = $flag_index + $buffer + $adjustment;

			// If adding the adjustment went too far, scale it back.
			if ( $end > count( $words ) - 1 ) {
				$end = count( $words ) - 1;
			}
		} else if ( $before_ok && ! $after_ok ) {
			$end        = count( $words ) - 1;
			$adjustment = ( $buffer + $flag_index ) - ( count( $words ) - 1 );
			$start      = $flag_index - $buffer - $adjustment;

			// If subtracting the adjustment went too far, reset it.
			if ( $start < 0 ) {
				$start = 0;
			}
		}

		$excerpt = array_slice( $words, $start, $end - $start, false );
		$excerpt = implode( ' ', $excerpt );

		if ( $start > 0 ) {
			$excerpt = $more . $excerpt;
		}

		if ( $end < count( $words ) ) {
			$excerpt .= $more;
		}

		return $excerpt;
	}

	/**
	 * Human readable index status.
	 *
	 * @since 4.0
	 * @param string $source The name of the Source.
	 * @param string|int $id The ID of the Source entry.
	 * @return string
	 */
	public static function get_source_entry_index_status( string $source, $id ) {
		$status = \SearchWP::$index->get_source_id_status( $source, $id );

		if ( empty( $status ) || ! is_object( $status ) ) {
			$status = __( 'Not indexed', 'searchwp' );
		} elseif ( ! empty( $status->indexed ) ) {
			$status = sprintf(
				// Translators: 1st placeholder is how long ago an entry was indexed.
				__( 'Indexed %1$s ago', 'searchwp' ),
				human_time_diff( date( 'U', strtotime( $status->indexed ) ), current_time( 'timestamp' ) )
			);
		} elseif ( ! empty( $status->queued ) ) {
			$status = sprintf(
				// Translators: 1st placeholder is how long ago an entry was queued.
				__( 'Queued for indexing %1$s ago', 'searchwp' ),
				human_time_diff( date( 'U', strtotime( $status->queued ) ), current_time( 'timestamp' ) )
			);
		} elseif ( ! empty( $status->omitted ) ) {
			$status = sprintf(
				// Translators: 1st placeholder is how long ago an entry was omitted.
				__( 'Omitted from indexing %1$s ago', 'searchwp' ),
				human_time_diff( date( 'U', strtotime( $status->omitted ) ), current_time( 'timestamp' ) )
			);
		}

		return $status;
	}

	/**
	 * Applies do_shortcode deeply.
	 *
	 * @since 4.0
	 * @param string|array $content The content.
	 * @return string
	 */
	public static function do_shortcode_deep( $content ) {
		if ( is_array( $content ) ) {
			foreach ( $content as $key => $val ) {
				$content[ $key ] = self::do_shortcode_deep( $val );
			}
		} elseif ( is_string( $content ) ) {
			$content = do_shortcode( $content );
		}

		return $content;
	}
}
