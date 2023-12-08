<?php
/**
 * Extra Product Options Helper class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Helper class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_HELPER_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_HELPER_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Cache for sum_array_values
	 *
	 * @var array<mixed>
	 * @since 6.4
	 */
	private $sum_array_values_cache = [];

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_HELPER_Base
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
	}

	/**
	 * Serialize an array
	 *
	 * @param mixed $a Input array.
	 * @return mixed
	 * @since 1.0
	 */
	public function array_serialize( $a ) {
		if ( is_array( $a ) ) {
			$r = [];
			foreach ( $a as $key => $value ) {
				if ( is_array( $value ) ) {
					$r[] = serialize( $value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
				} else {
					$r[] = $value;
				}
			}
			$r = implode( '|', $r );

			return $r;
		}

		return $a;
	}

	/**
	 * Unserialize an array
	 * Used in export csv
	 *
	 * @param mixed $a Input array.
	 * @return mixed
	 * @since 1.0
	 */
	public function array_unserialize( $a ) {
		$a = explode( '|', $a );
		$r = [];
		foreach ( $a as $key => $value ) {
			$r[] = themecomplete_maybe_unserialize( $value );
		}

		return $r;
	}

	/**
	 * Gets image size from attachment id and url
	 *
	 * @param integer $attachment_id Attachment post ID.
	 * @param string  $attachment_url Attachment URL.
	 * @return mixed
	 * @since 5.1
	 */
	public function get_attachment_sizes( $attachment_id = 0, $attachment_url = '' ) {
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( $meta && isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) { // @phpstan-ignore-line
			foreach ( $meta['sizes'] as $key => $value ) {
				if ( false !== strpos( $attachment_url, $value['file'] ) ) {
					return [ $value['width'], $value['height'] ];
				}
			}
			return [ $meta['width'], $meta['height'] ];
		}
		return false;
	}

	/**
	 * Gets attachement id from attachement url
	 *
	 * @param string $attachment_url Attachment URL.
	 * @return mixed
	 * @since 1.0
	 */
	public function get_attachment_id( $attachment_url = '' ) {

		$attachment_id = false;

		if ( '' === $attachment_url ) {
			return $attachment_id;
		}

		$original_attachment_url = $attachment_url;

		$attachment_id = get_transient( 'get_attachment_id_' . $original_attachment_url );
		if ( false !== $attachment_id ) {
			return $attachment_id;
		}

		$domain = $this->get_site_domain();

		if ( false === strpos( $attachment_url, $domain ) ) {
			$attachment_url = $domain . $attachment_url;
		}

		if ( function_exists( 'attachment_url_to_postid' ) ) {

			$attachment_id = attachment_url_to_postid( $attachment_url );

			if ( ! $attachment_id ) {

				// Get the upload directory paths.
				$upload_dir_paths = wp_upload_dir();
				$upload_url       = $upload_dir_paths['baseurl'];
				if ( is_ssl() ) {
					$upload_url = set_url_scheme( $upload_url, 'https' );
				}

				if ( false !== strpos( $attachment_url, $upload_url ) ) {

					// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
					$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

					$attachment_id = attachment_url_to_postid( $url );

				}

				if ( ! $attachment_id ) {

					$path = $attachment_url;
					if ( 0 === strpos( $path, $upload_dir_paths['baseurl'] . '/' ) ) {
						$path = substr( $path, strlen( $upload_dir_paths['baseurl'] . '/' ) );
					}

					if ( preg_match( '/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches ) ) {
						$url           = $upload_dir_paths['baseurl'] . '/' . $matches[1] . $matches[3];
						$attachment_id = attachment_url_to_postid( $url );
					}
				}
			}
		}

		set_transient( 'get_attachment_id_' . $original_attachment_url, $attachment_id, DAY_IN_SECONDS );

		return $attachment_id;
	}

	/**
	 * Generate image array
	 *
	 * @param array<mixed> $image_variations The image array.
	 * @param string       $image_link The image link.
	 * @param string       $image_type the image type of the array.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function generate_image_array( $image_variations = [], $image_link = '', $image_type = '' ) {
		if ( THEMECOMPLETE_EPO()->tm_epo_global_retrieve_image_sizes === 'yes' ) {
			$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
			$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
			$attachment_object = get_post( $attachment_id );
			if ( ! $attachment_object && get_transient( 'get_attachment_id_' . $image_link ) ) {
				delete_transient( 'get_attachment_id_' . $image_link );
				$attachment_id     = THEMECOMPLETE_EPO_HELPER()->get_attachment_id( $image_link );
				$attachment_id     = ( $attachment_id ) ? $attachment_id : 0;
				$attachment_object = get_post( $attachment_id );
			}
			$full_src      = wp_get_attachment_image_src( $attachment_id, 'large' );
			$image_title   = get_the_title( $attachment_id );
			$image_alt     = wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
			$image_srcset  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : false;
			$image_sizes   = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : false;
			$image_caption = ( $attachment_object ) ? $attachment_object->post_excerpt : '';

			if ( false === $full_src || ! is_array( $full_src ) ) {
				$full_src = [ '', '', '' ];
			}
		} else {
			$image_title   = '';
			$image_alt     = '';
			$image_srcset  = '';
			$image_sizes   = '';
			$image_caption = '';
			$attachment_id = '';
			$full_src      = [ '', '', '' ];
		}

		$image_variations[ $image_type ] = [
			'image_link'    => $image_link,
			'image_title'   => $image_title,
			'image_alt'     => $image_alt,
			'image_srcset'  => $image_srcset,
			'image_sizes'   => $image_sizes,
			'image_caption' => $image_caption,
			'image_id'      => $attachment_id,
			'full_src'      => $full_src[0],
			'full_src_w'    => $full_src[1],
			'full_src_h'    => $full_src[2],
		];

		return $image_variations;
	}

	/**
	 * Convert an array to a key/pair value array
	 *
	 * This is used for the select box creation
	 *
	 * @param mixed $a Input array.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function convert_to_select_options( $a = [] ) {
		$r = [];
		foreach ( $a as $key => $value ) {
			$r[] = [
				'text'  => $value,
				'value' => $key,
			];
		}

		return $r;
	}


	/**
	 * Generates a new set of IDs for use in recreate_element_ids
	 *
	 * @param mixed $meta Meta data.
	 * @return mixed
	 * @since 4.8.5
	 */
	public function generate_recreate_element_ids( $meta = [] ) {
		$meta          = themecomplete_maybe_unserialize( $meta );
		$builder       = $meta;
		$original_meta = false;
		$parsed_meta   = false;
		$invalid       = false;
		if ( isset( $meta['tmfbuilder'] ) ) {
			$original_meta = true;
			$builder       = $meta['tmfbuilder'];
		} elseif ( isset( $meta['element_type'] ) ) {
			$parsed_meta = true;
		} else {
			$invalid = true;
		}

		if ( $invalid ) {
			return $meta;
		}

		if ( isset( $builder ) ) {
			$ids = $this->array_contains_key( $builder, '_uniqid' );

			$new_ids = [];
			foreach ( $ids as $idx => $idelement ) {
				foreach ( $idelement as $idy => $id ) {
					$new_ids[ $id ] = THEMECOMPLETE_EPO_HELPER()->tm_uniqid();
				}
			}

			return $new_ids;

		}

		return false;
	}

	/**
	 * Recreate the element IDs of the options meta data
	 *
	 * @param mixed              $meta Meta data.
	 * @param array<mixed>|false $new_ids Array with new ids.
	 * @return mixed
	 * @since 1.0
	 */
	public function recreate_element_ids( $meta = [], $new_ids = false ) {
		$meta          = themecomplete_maybe_unserialize( $meta );
		$builder       = $meta;
		$original_meta = false;
		$parsed_meta   = false;
		$invalid       = false;
		if ( isset( $meta['tmfbuilder'] ) ) {
			$original_meta = true;
			$builder       = $meta['tmfbuilder'];
		} elseif ( isset( $meta['element_type'] ) ) {
			$parsed_meta = true;
		} else {
			$invalid = true;
		}

		if ( $invalid ) {
			return $meta;
		}

		if ( isset( $builder ) ) {
			$ids             = $this->array_contains_key( $builder, '_uniqid' );
			$logics          = $this->array_contains_key( $builder, '_clogic' );
			$logicrules      = $this->array_contains_key( $builder, '_logicrules' );
			$math_price      = $this->array_keys_end_with( $builder, '_price', [ '_before_price', '_after_price', '_sale_price' ] );
			$math_sale_price = $this->array_keys_end_with( $builder, '_sale_price', [ '_before_price', '_after_price' ] );
			$lookuptable_x   = $this->array_contains_key( $builder, '_lookuptable_x' );
			$lookuptable_y   = $this->array_contains_key( $builder, '_lookuptable_y' );

			if ( false === $new_ids ) {
				$new_ids = [];
				foreach ( $ids as $idx => $idelement ) {
					foreach ( $idelement as $idy => $id ) {
						$new_ids[ $id ] = THEMECOMPLETE_EPO_HELPER()->tm_uniqid();
					}
				}
			}
			foreach ( $ids as $idx => $idelement ) {
				foreach ( $idelement as $idy => $id ) {
					$ids[ $idx ][ $idy ] = $new_ids[ $id ];
				}
			}

			foreach ( $logics as $lx => $logicelement ) {
				foreach ( $logicelement as $ly => $logic ) {
					if ( 'string' !== gettype( $logic ) ) {
						$logic = wp_json_encode( $logic );
					}
					$logic                = str_replace( array_keys( $new_ids ), array_values( $new_ids ), $logic );
					$logics[ $lx ][ $ly ] = $logic;
				}
			}

			foreach ( $logicrules as $lx => $logicelement ) {
				foreach ( $logicelement as $ly => $logic ) {
					if ( 'string' !== gettype( $logic ) ) {
						$logic = wp_json_encode( $logic );
					}
					$logic                    = str_replace( array_keys( $new_ids ), array_values( $new_ids ), $logic );
					$logicrules[ $lx ][ $ly ] = $logic;
				}
			}

			foreach ( $lookuptable_x as $lxidx => $lxidelement ) {
				foreach ( $lxidelement as $lxidy => $lxid ) {
					if ( isset( $new_ids[ $lxid ] ) ) {
						$lookuptable_x[ $lxidx ][ $lxidy ] = $new_ids[ $lxid ];
					}
				}
			}
			foreach ( $lookuptable_y as $lyidx => $lyidelement ) {
				foreach ( $lyidelement as $lyidy => $lyid ) {
					if ( isset( $new_ids[ $lyid ] ) ) {
						$lookuptable_y[ $lyidx ][ $lyidy ] = $new_ids[ $lyid ];
					}
				}
			}

			foreach ( $math_price as $lx => $priceelement ) {
				foreach ( $priceelement as $ly => $price ) {
					$dojson = false;
					if ( 'string' !== gettype( $price ) ) {
						$dojson = true;
						$price  = wp_json_encode( $price );
					}
					$price = str_replace( array_keys( $new_ids ), array_values( $new_ids ), $price );
					if ( $dojson ) {
						$price = json_decode( $price );
					}
					$math_price[ $lx ][ $ly ] = $price;
				}
			}

			foreach ( $math_sale_price as $lx => $priceelement ) {
				foreach ( $priceelement as $ly => $price ) {
					$dojson = false;
					if ( 'string' !== gettype( $price ) ) {
						$dojson = true;
						$price  = wp_json_encode( $price );
					}
					$price = str_replace( array_keys( $new_ids ), array_values( $new_ids ), $price );
					if ( $dojson ) {
						$price = json_decode( $price );
					}
					$math_sale_price[ $lx ][ $ly ] = $price;
				}
			}

			$builder = array_merge( $builder, $ids );
			$builder = array_merge( $builder, $logics );
			$builder = array_merge( $builder, $logicrules );
			$builder = array_merge( $builder, $math_price );
			$builder = array_merge( $builder, $math_sale_price );
			$builder = array_merge( $builder, $lookuptable_x );
			$builder = array_merge( $builder, $lookuptable_y );

			if ( $original_meta ) {
				$meta['tmfbuilder'] = $builder;
			} else {
				$meta = $builder;
			}
		}

		return $meta;
	}

	/**
	 * Check if current request is made via AJAX
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function is_ajax_request() {
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( filter_var( stripslashes_deep( $_SERVER['HTTP_X_REQUESTED_WITH'] ), FILTER_SANITIZE_STRING ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Create min/max values for the provided options
	 *
	 * @param integer $product_id The product id.
	 * @param boolean $include_variation_prices If variation prices are included.
	 * @param string  $minkey The key 'min' or 'minall'. The minall does not include the field required status.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function sum_array_values( $product_id = 0, $include_variation_prices = false, $minkey = 'min' ) {

		if ( isset( $this->sum_array_values_cache[ $product_id ] ) ) {
			return $this->sum_array_values_cache[ $product_id ];
		}
		$sum = [];

		$fields = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->generate_fields( $product_id );

		if ( false === $fields ) {
			return $sum;
		}

		$is_real_max = THEMECOMPLETE_EPO()->tm_epo_global_max_real;

		$sum[ $minkey ] = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->calculate_minimum_price( $fields, $include_variation_prices ? $product_id : false, $minkey );
		if ( 'yes' === $is_real_max ) {
			$sum['max'] = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->calculate_real_maximum_price( $fields, $include_variation_prices ? $product_id : false );
		} else {
			$sum['max'] = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->calculate_maximum_price( $fields );
		}

		$this->sum_array_values_cache[ $product_id ] = $sum;

		return $sum;
	}

	/**
	 * Walker for adding values
	 *
	 * @param array<mixed> $value Input array.
	 * @param array<mixed> $key Array key.
	 * @param array<mixed> $num Value to add.
	 * @return void
	 * @since 1.0
	 */
	public function add_values_walker( $value, $key, $num ) {
		$value = floatval( $value ) + floatval( $num );
	}

	/**
	 * Add array values
	 *
	 * @param mixed $input Input array.
	 * @param mixed $add Input array.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function add_array_values( $input = [], $add = [] ) {
		$r = [];

		if ( is_array( $input ) && is_array( $add ) ) {
			foreach ( $input as $key => $value ) {
				$a = 0;
				if ( isset( $add[ $key ] ) ) {
					$a = floatval( $add[ $key ] );
				}

				$r[ $key ] = floatval( $value ) + $a;
			}
		}

		return $r;
	}

	/**
	 * Merge array values
	 *
	 * @param mixed $a Input array.
	 * @param mixed $b Input array.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function merge_price_array( $a = [], $b = [] ) {
		if ( ! is_array( $a ) || ! is_array( $b ) ) {
			return $a;
		}

		$r = [];

		foreach ( $b as $key => $value ) {
			if ( '' === $value && isset( $a[ $key ] ) ) {
				$r[ $key ] = $a[ $key ];
			} else {
				$r[ $key ] = $value;
			}
		}

		return $r;
	}

	/**
	 * Builds an array
	 *
	 * Uses array $b as base and $a for value override.
	 *
	 * @param mixed $a Override array.
	 * @param mixed $b Base Input array.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function build_array( $a = [], $b = [] ) {
		if ( ! is_array( $a ) || ! is_array( $b ) ) {
			return $a;
		}

		$result = [];

		foreach ( $b as $key => $value ) {
			if ( is_array( $value ) ) {
				$result[ $key ] = isset( $a[ $key ] ) ? $value : $this->build_array( $a[ $key ], $value );
			} else {
				$result[ $key ] = isset( $a[ $key ] ) ? $a[ $key ] : $value;
			}
		}

		return $result;
	}

	/**
	 * Filters an $input array by key.
	 *
	 * @param mixed  $input Input array.
	 * @param string $what String to search.
	 * @param string $where Placement where to search 'start' or 'end'.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function array_filter_key( $input, $what = 'tmcp_', $where = 'start' ) {
		if ( ! is_array( $input ) || empty( $input ) ) {
			return [];
		}

		$filtered_result = [];

		if ( 'end' === $where ) {
			$what = strrev( $what );
		}

		foreach ( $input as $key => $value ) {
			$k = $key;
			if ( 'end' === $where ) {
				$k = strrev( $key );
			}
			if ( strpos( $k, $what ) === 0 ) {
				$filtered_result[ $key ] = $value;
			}
		}

		return $filtered_result;
	}

	/**
	 * Array_map_deep functionality
	 *
	 * @param mixed    $input_array Input array.
	 * @param mixed    $array2 Input array.
	 * @param callable $callback Callback function.
	 * @return mixed
	 * @since 1.0
	 */
	public function array_map_deep( $input_array, $array2, $callback ) {
		$new = [];
		if ( is_array( $input_array ) && is_array( $array2 ) ) {
			foreach ( $input_array as $key => $val ) {
				if ( is_array( $val ) && is_array( $array2[ $key ] ) ) {
					$new[ $key ] = $this->array_map_deep( $val, $array2[ $key ], $callback );
				} else {
					$new[ $key ] = call_user_func( $callback, $val, $array2[ $key ] );
				}
			}
		} else {
			$new = call_user_func( $callback, $input_array, $array2 );
		}

		return $new;
	}

	/**
	 * Applies the callback to the elements of the given arrays, recursively
	 *
	 * @param  callable $callback Callback function to run for each element in each array.
	 * @param  mixed    $input_array    An array to run through the callback function.
	 * @return mixed Applies the callback to the elements of the given array.
	 */
	public function array_map_recursive( $callback, $input_array ) {
		if ( is_array( $input_array ) ) {
			return array_map(
				function ( $input_array ) use ( $callback ) {
					return $this->array_map_recursive( $callback, $input_array );
				},
				$input_array
			);
		}
		return $callback( $input_array );
	}

	/**
	 * Gets the site domain
	 *
	 * @return string
	 * @since 5.1
	 */
	public function get_site_domain() {
		$return   = site_url();
		$urlparts = wp_parse_url( $return );
		if ( $urlparts ) {
			$scheme = $urlparts['scheme'];
			$port   = isset( $urlparts['port'] ) ? $urlparts['port'] : '';
			$host   = $urlparts['host'];
			$port   = '80' === (string) $port ? '' : (string) $port;
			$port   = 'https' === $scheme && '443' === (string) $port ? '' : $port;
			$s_port = ! empty( $port ) ? ":$port" : '';
			$return = $scheme . '://' . $host . $s_port;
		}

		return $return;
	}

	/**
	 * Get ID from URL
	 *
	 * @param string $url Permalink to check.
	 * @return integer
	 * @since 1.0
	 */
	public function get_url_to_postid( $url ) {
		if ( function_exists( 'url_to_postid' ) ) {
			return url_to_postid( $url );
		}

		return 0;
	}

	/**
	 * Count number of words
	 *
	 * @param string $str Input string.
	 * @return integer
	 * @since 6.0
	 */
	public function count_words( $str ) {
		return preg_match_all( '/[\pL\d!@#$%^\&*()_+=\{[\}\]|\\"\':;?\/>.<,-]+/u', $str );
	}

	/**
	 * Build custom meta query
	 *
	 * @param string  $relation The query relation.
	 * @param string  $meta_key The meta key.
	 * @param mixed   $meta_value The meta value.
	 * @param string  $compare Compare operator.
	 * @param string  $exists Exists operator.
	 * @param boolean $use_double_check Check the value with quotes and without quotes.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function build_meta_query( $relation = 'OR', $meta_key = '', $meta_value = '', $compare = '!=', $exists = 'NOT EXISTS', $use_double_check = false ) {
		$meta_array = [
			'relation' => $relation,
		];
		if ( $use_double_check ) {
			$meta_array[] = [
				'relation' => 'AND',
				[
					'key'     => $meta_key, // get only enabled global extra options.
					'value'   => ':' . $meta_value . ';',
					'compare' => $compare,
				],
				[
					'key'     => $meta_key, // get only enabled global extra options.
					'value'   => ':"' . $meta_value . '";',
					'compare' => $compare,
				],
			];
		} else {
			$meta_array[] = [
				'key'     => $meta_key, // get only enabled global extra options.
				'value'   => $meta_value,
				'compare' => $compare,
			];
		}
		$meta_array[] = [
			'key'     => $meta_key, // backwards compatibility.
			'compare' => $exists,
		];

		return $meta_array;
	}

	/**
	 * Create a uniqe ID
	 *
	 * @param string $prefix Specifies a prefix to the unique ID.
	 * @return string
	 * @since 1.0
	 */
	public function tm_uniqid( $prefix = '' ) {
		return uniqid( $prefix, true );
	}

	/**
	 * Create uniqe IDs for provided array length
	 *
	 * @param integer $s Array length.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function tm_temp_uniqid( $s ) {
		$a = [];
		for ( $m = 0; $m < $s; $m++ ) {
			$a[] = $this->tm_uniqid();
		}

		return $a;
	}

	/**
	 * EncodeURIComponent functioanlity
	 *
	 * @param string $str Input string.
	 * @return string
	 * @since 1.0
	 */
	public function encode_uri_component( $str = '' ) {
		$revert = [
			'%21' => '!',
			'%2A' => '*',
			'%27' => "'",
			'%28' => '(',
			'%29' => ')',
		];

		return strtr( rawurlencode( $str ), $revert );
	}

	/**
	 * Return everything up to last instance of needle
	 *
	 * Use $trail to include needle chars including and past last needle
	 * http://php.net/manual/en/function.strrchr.php#64157
	 *
	 * @param string  $haystack The string to search in.
	 * @param string  $needle The needle.
	 * @param integer $trail Trailing character to include.
	 * @return string
	 * @since 1.0
	 */
	public function reverse_strrchr( $haystack, $needle, $trail = 0 ) {
		return strrpos( $haystack, $needle ) !== false ? substr( $haystack, 0, strrpos( $haystack, $needle ) + $trail ) : false;
	}

	/**
	 * Return the cache key for global forms based on the passed arguments
	 * Used in wp_count_posts below
	 *
	 * @param string $type Post type to retrieve count. Default 'post'.
	 * @param string $perm 'readable' or empty. Default empty.
	 * @return string
	 * @since 1.0
	 */
	private function count_posts_cache_key( $type = 'post', $perm = '' ) {
		$cache_key = 'tm-posts-' . $type;
		if ( 'readable' === $perm && is_user_logged_in() ) {
			$post_type_object = get_post_type_object( $type );
			if ( $post_type_object && ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$cache_key .= '_' . $perm . '_' . get_current_user_id();
			}
		}

		return $cache_key;
	}

	/**
	 * Find the amount of post's type
	 * Used in class-tm-epo-admin-global-list-table.php
	 *
	 * @param string $type Post type to retrieve count. Default 'post'.
	 * @param string $perm 'readable' or empty. Default empty.
	 * @return mixed
	 * @since 1.0
	 */
	public function wp_count_posts( $type = 'post', $perm = '' ) {
		global $wpdb;

		if ( ! post_type_exists( $type ) ) {
			return new stdClass();
		}

		$cache_key = $this->count_posts_cache_key( $type, $perm );
		$counts    = wp_cache_get( $cache_key, 'counts' );
		if ( false !== $counts ) {
			return apply_filters( 'wc_epo_wp_count_posts', $counts, $type, $perm );
		}

		// WPML.
		$_lang = THEMECOMPLETE_EPO_WPML()->get_lang();

		$args = [
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'post_type'      => $type,
			'post_status'    => get_post_stati(),
		];

		if ( THEMECOMPLETE_EPO_WPML()->is_active() && 'all' !== THEMECOMPLETE_EPO_WPML()->get_lang() ) {
			$args['meta_query'] = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $_lang, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}

		$posts_query = new WP_Query( $args );
		$the_count   = $posts_query->post_count;

		$counts = array_fill_keys( get_post_stati(), 0 );
		$counts = array_merge( $counts, array_count_values( wp_list_pluck( $posts_query->posts, 'post_status' ) ) );

		$counts = (object) $counts;

		wp_cache_set( $cache_key, $counts, 'counts' );

		return apply_filters( 'wc_epo_wp_count_posts', $counts, $type, $perm );
	}

	/**
	 * Get base currecny
	 *
	 * @return string
	 * @since 1.0
	 */
	public function wc_base_currency() {
		$from_currency = get_option( 'woocommerce_currency' );
		return $from_currency;
	}

	/**
	 * Get enabled currencies
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_currencies() {
		return apply_filters( 'wc_epo_enabled_currencies', [ $this->wc_base_currency() ] );
	}

	/**
	 * Get additional currencies
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_additional_currencies() {
		$enabled_currencies = $this->get_currencies();
		$from_currency      = $this->wc_base_currency();
		foreach ( $enabled_currencies as $key => $value ) {
			if ( $value === $from_currency ) {
				unset( $enabled_currencies[ $key ] );
				break;
			}
		}

		return $enabled_currencies;
	}

	/**
	 * Get additional currencies count
	 *
	 * @return integer
	 * @since 1.0
	 */
	public function wc_num_enabled_currencies() {
		$enabled_currencies = $this->get_additional_currencies();
		if ( is_array( $enabled_currencies ) ) {
			return count( $enabled_currencies );
		}

		return 0;
	}

	/**
	 * Get currency prefix
	 *
	 * @param mixed  $currency Input currency.
	 * @param string $prefix The prefix to add.
	 * @return mixed
	 * @since 1.0
	 */
	public function get_currency_price_prefix( $currency = null, $prefix = '_' ) {
		if ( true === $currency || null === $currency ) {
			if ( true === $currency || $this->wc_num_enabled_currencies() > 0 ) {
				$to_currency = themecomplete_get_woocommerce_currency();

				return $prefix . $to_currency;
			} else {
				return '';
			}
		} else {
			return ( empty( $currency ) || $currency === $this->wc_base_currency() ) ? '' : $prefix . $currency;
		}
	}

	/**
	 * Format bytes
	 *
	 * @param mixed   $bytes The number of bytes.
	 * @param integer $precision The format precision.
	 * @return string
	 * @since 1.0
	 */
	public function format_bytes( $bytes, $precision = 2 ) {
		$units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];

		$bytes = max( $bytes, 0 );
		$pow   = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );

		$bytes /= pow( 1024, $pow );

		return round( $bytes, $precision ) . ' ' . $units[ $pow ];
	}

	/**
	 * Add a right icon to label
	 *
	 * @param string $label The label.
	 * @param string $icon The icon id.
	 * @return string
	 * @since 1.0
	 */
	private function convert_to_right_icon( $label = '', $icon = 'tcfa-angle-right' ) {
		$label  = str_replace( '/', '', $label );
		$label .= '<i class="tm-icon tmfa tcfa ' . $icon . '"></i>';

		return $label;
	}

	/**
	 * Convert a url to an html link
	 *
	 * @param string $url The url.
	 * @param string $main_path The main path.
	 * @param string $main_path_label The main path label.
	 * @return string
	 * @since 1.0
	 */
	public function url_to_links( $url = '', $main_path = '', $main_path_label = '' ) {

		$param = str_replace( $main_path, '', $url );
		$param = explode( '/', $param );

		$html = '';

		$a     = '<a class="tm-mn-movetodir" data-tm-dir="" href="' . esc_url( $main_path ) . '">' . $this->convert_to_right_icon( esc_html( $main_path_label ) ) . '</a>';
		$html .= $a;
		$todir = '';
		foreach ( $param as $key => $value ) {
			if ( (int) ( count( $param ) - 1 ) === (int) $key ) {
				$a = '<span class="tm-mn-currentdir">' . esc_html( $value ) . '</span>';
			} else {
				$data_tm_dir = ( empty( $todir ) ) ? $value : $todir . '/' . $value;
				$a           = '<a class="tm-mn-movetodir" data-tm-dir="' . esc_attr( $data_tm_dir ) . '" href="' . esc_url( $main_path . $data_tm_dir ) . '">' . $this->convert_to_right_icon( esc_html( $value . '/' ) ) . '</a>';
				$todir       = $data_tm_dir;
			}
			$html .= $a;
		}

		return $html;
	}

	/**
	 * Inits WordPress filesystem
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function init_filesystem() {
		if ( function_exists( 'get_filesystem_method' ) ) {
			$access_type = get_filesystem_method();
			if ( 'direct' === $access_type ) {
				// you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL.
				$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, '', [] );

				// initialize the API.
				if ( ! WP_Filesystem( $creds ) ) {
					// any problems and we exit.
					return false;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Remove directory
	 *
	 * @param string $file The file path.
	 * @return boolean
	 * @since 1.0
	 */
	public function file_rmdir( $file = '' ) {
		if ( $this->init_filesystem() ) {
			global $wp_filesystem;
			$mn = $wp_filesystem->rmdir( $file, true );
			clearstatcache();

			return $mn;
		}

		return false;
	}

	/**
	 * Delete a file
	 *
	 * @param string $file The file path.
	 * @return boolean
	 * @since 1.0
	 */
	public function file_delete( $file = '' ) {
		if ( $this->init_filesystem() ) {
			global $wp_filesystem;
			$mn = $wp_filesystem->delete( $file );
			clearstatcache();

			return $mn;
		}

		return false;
	}

	/**
	 * File managet display
	 *
	 * @param string $main_path The main path.
	 * @param string $todir The directory.
	 * @return string
	 * @since 1.0
	 */
	public function file_manager( $main_path = '', $todir = '' ) {

		$html = '';

		if ( is_admin() && '//' !== $main_path && $this->init_filesystem() ) {

			global $wp_filesystem;

			$subdir = $main_path . $todir;
			$param  = wp_upload_dir();
			if ( empty( $param['subdir'] ) ) {
				$base_url        = $param['url'] . $main_path;
				$param['path']   = $param['path'] . $subdir;
				$param['url']    = $param['url'] . $subdir;
				$param['subdir'] = $subdir;
			} else {
				$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
				$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
				$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
				$base_url        = str_replace( $param['subdir'], $main_path, $param['url'] );
			}

			clearstatcache();
			$mn = $wp_filesystem->dirlist( $param['path'], true, false );

			$files       = [];
			$directories = [];
			if ( $mn ) {
				foreach ( $mn as $key => $value ) {
					if ( isset( $value['type'] ) && isset( $value['name'] ) && isset( $value['size'] ) ) {
						switch ( strtolower( $value['type'] ) ) {
							case 'd':
								$directories[] = [
									'name' => $value['name'],
									'size' => 0,
								];
								break;

							case 'f':
								$files[] = [
									'name' => $value['name'],
									'size' => $value['size'],
								];
								break;
						}
					}
				}
			}

			$html .= '<div class="tm-mn-header"><div class="tm-mn-path">' . $this->url_to_links( $param['url'], $base_url, $main_path ) . '</div></div>';
			$html .= '<div class="tm-mn-wrap-heading tc-row nopadding nomargin">';
			$html .= '<div class="tm-mn-name tc-cell tc-col-6">' . esc_html__( 'Filename', 'woocommerce-tm-extra-product-options' ) . '</div>';
			$html .= '<div class="tm-mn-size tc-cell tc-col-3">' . esc_html__( 'Size', 'woocommerce-tm-extra-product-options' ) . '</div>';
			$html .= '<div class="tm-mn-op tc-cell tc-col-3">&nbsp;</div>';
			$html .= '</div>';
			foreach ( $directories as $key => $value ) {
				$filetype    = wp_check_filetype( $value['name'] );
				$img         = '<img class="tm-mime" src="' . esc_attr( (string) wp_mime_type_icon( $filetype['type'] ) ) . '" /> ';
				$html       .= '<div class="tm-mn-wrap-dir tc-row nopadding nomargin">';
				$data_tm_dir = ( empty( $todir ) ) ? $value['name'] : $todir . '/' . $value['name'];
				$html       .= '<div class="tm-mn-name tc-cell tc-col-6">' . $img . '<a class="tm-mn-movetodir" data-tm-dir="' . esc_attr( $data_tm_dir ) . '" href="' . esc_url( $param['url'] . $value['name'] ) . '">' . esc_html( $value['name'] ) . '</a></div>';
				$html       .= '<div class="tm-mn-size tc-cell tc-col-3">&nbsp;</div>';
				$html       .= '<div class="tm-mn-op tc-cell tc-col-3">'
								. '<a title="' . esc_html__( 'Delete', 'woocommerce-tm-extra-product-options' ) . '" href="#" data-tm-dir="' . esc_attr( $todir ) . '" data-tm-deldir="' . esc_attr( $data_tm_dir ) . '" class="tm-mn-deldir"><i class="tm-icon tmfa tcfa tcfa-times"></i></a>'
								. '</div>';
				$html       .= '</div>';
			}
			foreach ( $files as $key => $value ) {
				$filetype = wp_check_filetype( $value['name'] );
				$img      = '<img class="tm-mime" src="' . esc_attr( (string) wp_mime_type_icon( $filetype['type'] ) ) . '" /> ';

				$html       .= '<div class="tm-mn-wrap-file tc-row nopadding nomargin">';
				$data_tm_dir = $todir;
				$html       .= '<div class="tm-mn-name tc-cell tc-col-6">' . $img . '<a class="tm-download-file" download href="' . esc_url( $base_url . $todir . '/' . $value['name'] ) . '">' . esc_html( $value['name'] ) . '</a></div>';
				$html       .= '<div class="tm-mn-size tc-cell tc-col-3">' . $this->format_bytes( $value['size'], 2 ) . '</div>';
				$html       .= '<div class="tm-mn-op tc-cell tc-col-3">'
								. '<a title="' . esc_html__( 'Delete', 'woocommerce-tm-extra-product-options' ) . '" href="#" data-tm-dir="' . esc_attr( $todir ) . '" data-tm-deldir="' . esc_attr( $data_tm_dir ) . '" data-tm-delfile="' . esc_attr( $value['name'] ) . '" class="tm-mn-delfile"><i class="tm-icon tmfa tcfa tcfa-times"></i></a>'
								. '</div>';
				$html       .= '</div>';
			}
		}

		return $html;
	}

	/**
	 * Get saved unique ids for elements in the order
	 *
	 * @param integer $product_id The product id.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function get_saved_order_multiple_keys( $product_id = 0 ) {
		$this_land_epos            = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id );
		$saved_order_multiple_keys = [];
		if ( isset( $this_land_epos['global'] ) && is_array( $this_land_epos['global'] ) ) {
			foreach ( $this_land_epos['global'] as $priority => $priorities ) {
				if ( is_array( $priorities ) ) {
					foreach ( $priorities as $pid => $field ) {
						if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
							foreach ( $field['sections'] as $section_id => $section ) {
								if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
									foreach ( $section['elements'] as $element ) {
										$saved_order_multiple_keys[ $element['uniqid'] ]              = $element['label'];
										$saved_order_multiple_keys[ 'options_' . $element['uniqid'] ] = $element['options'];
									}
								}
							}
						}
					}
				}
			}
		}

		return $saved_order_multiple_keys;
	}

	/**
	 * Get the WooCommerce order object
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function tm_get_order_object() {
		global $thepostid, $theorder;

		if ( ! is_object( $theorder ) ) {
			$theorder = wc_get_order( $thepostid );
		}
		if ( ! $theorder && isset( $_REQUEST['order_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_id = absint( $_REQUEST['order_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order    = wc_get_order( $order_id );

			return $order;
		} elseif ( ! $theorder && isset( $_REQUEST['post_ID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_id = absint( $_REQUEST['post_ID'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order    = wc_get_order( $order_id );

			return $order;
		}
		if ( ! $theorder ) {
			global $post;
			if ( $post ) {
				$theorder = wc_get_order( $post->ID );
			}
		}

		return $theorder;
	}

	/**
	 * Convert the uploaded image to png
	 *
	 * @param string $source The source file name.
	 * @param string $target The target file name.
	 * @return boolean
	 * @since 1.0
	 */
	public function upload_to_png( $source, $target ) {

		if ( function_exists( 'exif_imagetype' ) && exif_imagetype( $source ) === false ) {
			return false; // Invalid image.
		}

		if ( ! function_exists( 'imagecreatefromstring' ) ) {
			return false; // GD library not installed.
		}

		include_once ABSPATH . 'wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();

		$source_img = imagecreatefromstring( $wp_filesystem->get_contents( $source ) );

		if ( false === $source_img ) {
			return false; // Invalid image.
		}

		$width      = imagesx( $source_img );
		$height     = imagesy( $source_img );
		$target_img = imagecreatetruecolor( $width, $height );
		imagecolortransparent( $target_img, imagecolorallocatealpha( $target_img, 0, 0, 0, 127 ) );
		imagealphablending( $target_img, false );

		imagesavealpha( $target_img, true );

		imagecopy( $target_img, $source_img, 0, 0, 0, 0, $width, $height );

		imagedestroy( $source_img );
		imagepng( $target_img, $target );
		imagedestroy( $target_img );

		return true;
	}

	/**
	 * String starts with functionality
	 *
	 * @param string $source The string to search in.
	 * @param string $prefix The string search for.
	 * @return boolean
	 * @since 1.0
	 */
	public function str_startswith( $source, $prefix ) {
		return 0 === strncmp( $source, $prefix, strlen( $prefix ) );
	}

	/**
	 * String ends with functionality
	 *
	 * @param string $source The string to search in.
	 * @param string $suffix The string search for.
	 * @return boolean
	 * @since 1.0
	 */
	public function str_endsswith( $source, $suffix ) {
		return '' === $suffix || ( strlen( $suffix ) <= strlen( $source ) && substr_compare( $source, $suffix, - strlen( $suffix ) ) === 0 );
	}

	/**
	 * Search through an array for a matching key.
	 *
	 * See https://gist.github.com/steve-todorov/3671626
	 *
	 * @param array<mixed> $input_array The input array.
	 * @param string       $search_value The value to search.
	 * @param boolean      $case_sensitive if search is case sensitive.
	 *
	 * @return array<mixed>
	 */
	public function array_contains_key( $input_array, $search_value, $case_sensitive = true ) {
		if ( $case_sensitive ) {
			$preg_match = '/' . $search_value . '/';
		} else {
			$preg_match = '/' . $search_value . '/i';
		}
		$return_array = [];
		$keys         = array_keys( $input_array );
		foreach ( $keys as $k ) {
			if ( preg_match( $preg_match, $k ) ) {
				$return_array[ $k ] = $input_array[ $k ];
			}
		}

		return $return_array;
	}

	/**
	 * Search through an array for a matching key that ends with a string.
	 *
	 * @param array<mixed> $input_array The input array.
	 * @param string       $search_value The value to search.
	 * @param array<mixed> $excludes The values to exclude.
	 *
	 * @return array<mixed>
	 */
	public function array_keys_end_with( $input_array = [], $search_value = '', $excludes = [] ) {
		$return_array = [];
		$keys         = array_keys( $input_array );
		foreach ( $keys as $k ) {
			if ( $this->str_endsswith( $k, $search_value ) ) {
				$canbeadded = true;
				foreach ( $excludes as $exclude ) {
					if ( $this->str_endsswith( $k, $exclude ) ) {
						$canbeadded = false;
					}
				}
				if ( $canbeadded ) {
					$return_array[ $k ] = $input_array[ $k ];
				}
			}
		}

		return $return_array;
	}

	/**
	 * Sanitize array key
	 *
	 * @param string $source The input string.
	 * @return string
	 * @since 1.0
	 */
	public function sanitize_key( $source ) {
		return str_replace( [ '[', ']' ], '', $source );
	}

	/**
	 * Recursively implodes an array with optional key inclusion
	 *
	 * Adapted from https://gist.github.com/jimmygle/2564610
	 *
	 * @access public
	 *
	 * @param array<mixed>   $input_array    multi-dimensional array to recursively implode.
	 * @param string         $glue     value that glues elements together.
	 * @param string|boolean $key_glue include keys before their values.
	 * @param boolean        $trim_all trim ALL whitespace from string.
	 *
	 * @return string imploded array
	 */
	public function recursive_implode( $input_array = [], $glue = ',', $key_glue = false, $trim_all = false ) {
		if ( ! is_array( $input_array ) ) {
			return $input_array;
		}

		if ( true === $key_glue ) {
			$key_glue = $glue;
		}

		$glued_string = '';
		// Recursively iterates array and adds key/value to glued string.
		array_walk_recursive(
			$input_array,
			function ( $value, $key ) use ( $glue, $key_glue, &$glued_string ) {
				if ( false !== $key_glue ) {
					$glued_string .= $key . $key_glue;
				}
				$glued_string .= $value . $glue;
			}
		);
		// Removes last $glue from string.
		if ( strlen( $glue ) > 0 ) {
			$glued_string = substr( $glued_string, 0, - strlen( $glue ) );
		}
		// Trim ALL whitespace.
		if ( $trim_all ) {
			$glued_string = preg_replace( '/(\s)/ixsm', '', $glued_string );
		}

		return (string) $glued_string;
	}

	/**
	 * Computes the intersection of arrays using keys for comparison
	 * with wildcart support
	 *
	 * @param array<mixed> $arr The array with master keys to check.
	 * @param array<mixed> $arr2 Array to compare keys against.
	 * @return array<mixed>
	 */
	public function array_intersect_key_wildcard( $arr, $arr2 ) {
		$ret = [];

		foreach ( $arr2 as $key => $value ) {
			$nee   = str_replace( '\\*', '[0-9]+?', preg_quote( $key, '/' ) );
			$nee   = preg_grep( '/^' . $nee . '$/i', array_keys( $arr ) );
			$ret[] = array_intersect_key( $arr, array_flip( $nee ) );
		}
		$array = [];
		foreach ( $ret as $key => $value ) {
			$array = array_merge( $array, $value );
		}

		// keep the original order or $arr.
		$ordered_array = [];
		foreach ( $arr as $key => $value ) {
			if ( array_key_exists( $key, $array ) ) {
				$ordered_array[ $key ] = $array[ $key ];
			}
		}

		return $ordered_array;
	}

	/**
	 * Normalize variable
	 *
	 * @param mixed   $data The data to normalize.
	 * @param boolean $normalize_key If the array keys should be normalized.
	 * @param boolean $implode_value If the array values should be joined.
	 * @return mixed
	 * @since 6.0
	 */
	public function normalize_data( $data = [], $normalize_key = false, $implode_value = true ) {
		if ( class_exists( 'Normalizer' ) ) {
			if ( is_array( $data ) ) {
				foreach ( $data as $post_data_key => $post_data_value ) {
					if ( is_array( $post_data_key ) ) { // @phpstan-ignore-line
						$post_data_key = $this->recursive_implode( $post_data_key, '' );
					}
					if ( is_array( $post_data_value ) ) {
						if ( $implode_value ) {
							$post_data_value = $this->recursive_implode( $post_data_value, '' );
							$post_data_value = Normalizer::normalize( (string) $post_data_value );
						} else {
							foreach ( $post_data_value as $key => $value ) {
								if ( is_array( $value ) || is_object( $value ) ) {
									$post_data_value[ $key ] = $value;
								} else {
									$post_data_value[ $key ] = Normalizer::normalize( (string) $value );
								}
							}
						}
					} else {
						$post_data_value = Normalizer::normalize( (string) $post_data_value );
					}
					if ( $normalize_key ) {
						$post_data_key = Normalizer::normalize( (string) $post_data_key );
					}
					$data[ Normalizer::normalize( (string) $post_data_key ) ] = $post_data_value;
				}
			} elseif ( is_object( $data ) || is_bool( $data ) ) {
				return $data;
			} else {
				$data = Normalizer::normalize( (string) $data );
			}
		}
		return $data;
	}

	/**
	 * Array map for keys
	 *
	 * @param callable     $callback The function callback.
	 * @param array<mixed> $input_array The input array.
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 6.0
	 */
	public function array_map_key( $callback, $input_array, $args = [] ) {
		$out = [];

		foreach ( $input_array as $key => $value ) {
			$mapkey         = call_user_func_array( $callback, array_merge( [ $key ], $args ) );
			$out[ $mapkey ] = $value;
		}

		return $out;
	}

	/**
	 * Safe html_entity_decode
	 *
	 * @param string $str The value to decode.
	 * @return string
	 * @since 1.0
	 */
	public function html_entity_decode( $str = '' ) {
		return html_entity_decode( $str, version_compare( phpversion(), '5.4', '<' ) ? ENT_COMPAT : ( ENT_COMPAT | ENT_HTML401 ), 'UTF-8' );
	}

	/**
	 * Array map with html_entity_decode
	 *
	 * @param mixed $value The value to decode.
	 * @return mixed
	 * @since 6.0
	 */
	public function entity_decode( $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( [ $this, 'html_entity_decode' ], $value );
		} else {
			$value = $this->html_entity_decode( $value );
		}

		return $value;
	}

	/**
	 * Gets cached posts for a query. Results are stored against a hash of the
	 * parameter array. If there's nothing in the cache, a fresh query is made.
	 * https://wordpress.stackexchange.com/questions/162703/cache-get-posts
	 *
	 * @param array<mixed> $args The parameters to pass to get_posts().
	 *
	 * @return array<mixed> List of posts matching $args.
	 */
	public static function get_cached_posts( $args = [] ) {
		$post_list_name = 'tm_get_posts' . md5( wp_json_encode( $args ) );

		$post_list = wp_cache_get( $post_list_name );

		if ( false === $post_list ) {
			$post_list = get_posts( $args );

			wp_cache_set( $post_list_name, $post_list );
		}

		return $post_list;
	}

	/**
	 * Gets a cached post for a query. Results are stored against a hash of the
	 * parameter array. If there's nothing in the cache, a fresh query is made.
	 * https://wordpress.stackexchange.com/questions/162703/cache-get-posts
	 *
	 * @param integer $post_id The post id to pass to get_post().
	 *
	 * @return WP_Post|null The returned post or null.
	 */
	public static function get_cached_post( $post_id = 0 ) {
		$post_list_name = 'tm_get_post' . md5( wp_json_encode( $post_id ) );

		$post_list = wp_cache_get( $post_list_name );

		if ( false === $post_list ) {
			$post_list = get_post( $post_id );

			wp_cache_set( $post_list_name, $post_list );
		}

		return $post_list;
	}

	/**
	 * Takes a string/array of strings, removes all formatting/cruft
	 * and returns the raw float value.
	 *
	 * @param mixed $value the value to unformat.
	 * @param mixed $decimal the decimal point.
	 *
	 * @return mixed Unformatted value or 0.
	 */
	public function unformat( $value = '', $decimal = false ) {
		$unformatted = '';

		// Recursively unformat arrays.
		if ( is_array( $value ) ) {
			return array_map(
				function ( $item ) use ( $decimal ) {
					return $this->unformat( $item, $decimal );
				},
				$value
			);
		}

		// Return the value as-is if it's already a number.
		if ( 'integer' === gettype( $value ) || 'double' === gettype( $value ) ) {
			return $value;
		}

		if ( 'string' !== gettype( $value ) ) {
			return 0;
		}

		// Get local decimal point.
		if ( false === $decimal ) {
			$tm_epo_global_input_decimal_separator = THEMECOMPLETE_EPO()->tm_epo_global_input_decimal_separator;
			if ( '' === $tm_epo_global_input_decimal_separator ) {
				// currency_format_decimal_sep.
				$decimal = stripslashes_deep( get_option( 'woocommerce_price_decimal_sep' ) );
			} elseif ( class_exists( 'NumberFormatter' ) ) {
				$locale = get_locale();
				if ( isset( $_SERVER ) && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) { // @phpstan-ignore-line
					$locale = explode( ',', wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$locale = $locale[0];
					$locale = str_replace( '-', '_', $locale );
				}
				$fmt     = new NumberFormatter( $locale, NumberFormatter::DECIMAL );
				$n       = $fmt->format( 1.1 );
				$matches = [];
				preg_match( '/^1(.+)1$/', $n, $matches );
				if ( isset( $matches[1] ) ) {
					$decimal = $matches[1];
				}
			}
		}
		if ( false === $decimal || true === $decimal ) {
			$decimal = '.';
		}

		// Strip out everything except digits, decimal point and minus sign and the dot.
		// The dot is added in case the system posts a number while not using dot as a
		// decimal point like in the case of number fields.
		$unformatted = preg_replace( '/[^0-9-.' . $decimal . ']/', '', $value );
		// Make sure decimal point is standard.
		$unformatted = str_replace( $decimal, '.', $unformatted );

		$unformatted = (float) $unformatted;

		return $unformatted;
	}

	/**
	 * Convert url to ssl if it applies
	 *
	 * @param mixed $url The url.
	 * @return string
	 * @since 6.1
	 */
	public function to_ssl( $url = '' ) {

		if ( is_ssl() ) {
			if ( is_array( $url ) ) {
				foreach ( $url as $url_key => $url_value ) {
					if ( ! is_array( $url_value ) ) {
						$url[ $url_key ] = $this->to_ssl( $url_value );
					}
				}
			} else {
				$url = preg_replace( '/^http:/i', 'https:', $url );
			}
		}

		return $url;
	}

	/**
	 * Array some functionality
	 *
	 * @param array<mixed> $input_array The array.
	 * @param mixed        $func The callable function.
	 * @return boolean
	 * @since 6.1
	 */
	public function array_some( $input_array, $func ) {
		if ( is_array( $input_array ) && $func ) {
			foreach ( $input_array as $value ) {
				if ( $func( $value ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Convert user input to number
	 *
	 * @param mixed   $val The value to convert.
	 * @param boolean $noformat If a number should be returned for PHP calculations.
	 * @return mixed
	 * @since 6.3
	 */
	public function convert_to_number( $val = '', $noformat = false ) {
		if ( function_exists( 'numfmt_create' ) ) {
			$locale = get_locale();
			if ( isset( $_SERVER ) && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) { // @phpstan-ignore-line
				$locale = explode( ',', wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$locale = $locale[0];
				$locale = str_replace( '-', '_', $locale );
			}
			$fmt = numfmt_create( $locale, NumberFormatter::DECIMAL );
			// Set the maximum number of decimal places.
			$fmt->setAttribute( NumberFormatter::MAX_FRACTION_DIGITS, 50 );
			$val = numfmt_format( $fmt, $val );

			if ( $noformat ) {
				// format Arabic numbers to English numbers.
				$arabic_numbers = [
					'٠' => '0',
					'١' => '1',
					'٢' => '2',
					'٣' => '3',
					'٤' => '4',
					'٥' => '5',
					'٦' => '6',
					'٧' => '7',
					'٨' => '8',
					'٩' => '9',
				];

				$val = str_replace( array_keys( $arabic_numbers ), array_values( $arabic_numbers ), $val );

				// format Persian numbers to English numbers.
				$persian = [
					'۰' => '0',
					'۱' => '1',
					'۲' => '2',
					'۳' => '3',
					'۴' => '4',
					'۵' => '5',
					'۶' => '6',
					'۷' => '7',
					'۸' => '8',
					'۹' => '9',
				];

				$val = str_replace( array_keys( $persian ), array_values( $persian ), $val );

				// format Chinese numbers to English numbers.
				$chinese_numbers = [
					'零' => '0',
					'一' => '1',
					'二' => '2',
					'三' => '3',
					'四' => '4',
					'五' => '5',
					'六' => '6',
					'七' => '7',
					'八' => '8',
					'九' => '9',
				];

				$val = str_replace( array_keys( $chinese_numbers ), array_values( $chinese_numbers ), $val );

				// format Japanese numbers to English numbers.
				$japanese_numbers = [
					'零' => '0',
					'一' => '1',
					'二' => '2',
					'三' => '3',
					'四' => '4',
					'五' => '5',
					'六' => '6',
					'七' => '7',
					'八' => '8',
					'九' => '9',
				];

				$val = str_replace( array_keys( $japanese_numbers ), array_values( $japanese_numbers ), $val );

				// format Korean numbers to English numbers.
				$korean_numbers = [
					'영' => '0',
					'일' => '1',
					'이' => '2',
					'삼' => '3',
					'사' => '4',
					'오' => '5',
					'육' => '6',
					'칠' => '7',
					'팔' => '8',
					'구' => '9',
				];

				$val = str_replace( array_keys( $korean_numbers ), array_values( $korean_numbers ), $val );

				// format Hindi numbers to English numbers.
				$hindi_numbers = [
					'०' => '0',
					'१' => '1',
					'२' => '2',
					'३' => '3',
					'४' => '4',
					'५' => '5',
					'६' => '6',
					'७' => '7',
					'८' => '8',
					'९' => '9',
				];

				$val = str_replace( array_keys( $hindi_numbers ), array_values( $hindi_numbers ), $val );

				// format Thai numbers to English numbers.
				$thai_numbers = [
					'๐' => '0',
					'๑' => '1',
					'๒' => '2',
					'๓' => '3',
					'๔' => '4',
					'๕' => '5',
					'๖' => '6',
					'๗' => '7',
					'๘' => '8',
					'๙' => '9',
				];

				$val = str_replace( array_keys( $thai_numbers ), array_values( $thai_numbers ), $val );

				// format Mayan numbers to English numbers.
				$mayan_numbers = [
					'๐' => '0',
					'๑' => '1',
					'๒' => '2',
					'๓' => '3',
					'๔' => '4',
					'๕' => '5',
					'๖' => '6',
					'๗' => '7',
					'๘' => '8',
					'๙' => '9',
				];

				$val = str_replace( array_keys( $mayan_numbers ), array_values( $mayan_numbers ), $val );

				// format Babylonian numbers to English numbers.
				$babylonian_numbers = [
					'٠' => '0',
					'١' => '1',
					'٢' => '2',
					'٣' => '3',
					'٤' => '4',
					'٥' => '5',
					'٦' => '6',
					'٧' => '7',
					'٨' => '8',
					'٩' => '9',
				];

				$val = str_replace( array_keys( $babylonian_numbers ), array_values( $babylonian_numbers ), $val );

				// format Greek numbers to English numbers.
				$greek_numbers = [
					'⁰' => '0',
					'¹' => '1',
					'²' => '2',
					'³' => '3',
					'⁴' => '4',
					'⁵' => '5',
					'⁶' => '6',
					'⁷' => '7',
					'⁸' => '8',
					'⁹' => '9',
				];

				$val = str_replace( array_keys( $greek_numbers ), array_values( $greek_numbers ), $val );

				$thousand_separator_symbol = numfmt_get_symbol( $fmt, NumberFormatter::GROUPING_SEPARATOR_SYMBOL );
				$decimal_separator_symbol  = numfmt_get_symbol( $fmt, NumberFormatter::DECIMAL_SEPARATOR_SYMBOL );

				$val = str_replace( $thousand_separator_symbol, '', $val );
				$val = str_replace( $decimal_separator_symbol, '.', $val );
			}
		}
		return $val;
	}

	/**
	 * Converts a string from UTF-8 to ISO-8859-1, replacing invalid or unrepresentable characters
	 *
	 * @param string $str A UTF-8 encoded string.
	 * @return string
	 * @since 6.4
	 */
	public function utf8_decode( $str = '' ) {
		if ( function_exists( 'mb_convert_encoding' ) && function_exists( 'mb_detect_encoding' ) ) {
			$source_encoding = mb_detect_encoding( $str, 'UTF-8, ISO-8859-1, ISO-8859-15, Windows-1252' );
			if ( $source_encoding ) {
				// Convert to ISO-8859-1 (Latin-1).
				return mb_convert_encoding( $str, 'ISO-8859-1', $source_encoding );
			}
		}

		return $str;
	}

	/**
	 * Converts a string from ISO-8859-1 to UTF-8
	 *
	 * @param string $str An ISO-8859-1 string.
	 * @return string
	 * @since 6.4
	 */
	public function utf8_encode( $str = '' ) {
		if ( function_exists( 'mb_convert_encoding' ) && function_exists( 'mb_list_encodings' ) ) {
			return mb_convert_encoding( $str, 'UTF-8', mb_list_encodings() );
		}

		return $str;
	}
}
