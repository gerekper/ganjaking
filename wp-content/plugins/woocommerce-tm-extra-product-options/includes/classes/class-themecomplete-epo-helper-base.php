<?php
/**
 * Extra Product Options Helper class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Helper class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
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
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
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
	 * @since 5.1
	 */
	public function get_attachment_sizes( $attachment_id = 0, $attachment_url = '' ) {
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( $meta && isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
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
	 * @since 1.0
	 */
	public function get_attachment_id( $attachment_url = '' ) {

		$attachment_id = false;

		if ( '' === $attachment_url ) {
			return $attachment_id;
		}

		$attachment_id = get_transient( 'get_attachment_id_' . $attachment_url );
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

				if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

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

		set_transient( 'get_attachment_id_' . $attachment_url, $attachment_id, DAY_IN_SECONDS );

		return $attachment_id;
	}

	/**
	 * Generate image array
	 *
	 * @param array  $image_variations The image array.
	 * @param string $image_link The image link.
	 * @param string $image_type the image type of the array.
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
	 * @param array $meta Meta data.
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
		} else {
			if ( isset( $meta['element_type'] ) ) {
				$parsed_meta = true;
			} else {
				$invalid = true;
			}
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
	 * @param array       $meta Meta data.
	 * @param array|false $new_ids Array with new ids..
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
		} else {
			if ( isset( $meta['element_type'] ) ) {
				$parsed_meta = true;
			} else {
				$invalid = true;
			}
		}

		if ( $invalid ) {
			return $meta;
		}

		if ( isset( $builder ) ) {
			$ids             = $this->array_contains_key( $builder, '_uniqid' );
			$logics          = $this->array_contains_key( $builder, '_clogic' );
			$math_price      = $this->array_keys_end_with( $builder, '_price', [ '_before_price', '_after_price', '_sale_price' ] );
			$math_sale_price = $this->array_keys_end_with( $builder, '_sale_price', [ '_before_price', '_after_price' ] );

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
			$builder = array_merge( $builder, $math_price );
			$builder = array_merge( $builder, $math_sale_price );

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
	 * @since 1.0
	 */
	public function is_ajax_request() {
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( filter_var( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ), FILTER_SANITIZE_STRING ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Create min/max values for the provided options
	 *
	 * @param array   $epos Option data array.
	 * @param boolean $include_variation_prices If variation prices are included.
	 * @param string  $minkey The key 'min' or 'max'.
	 * @since 1.0
	 */
	public function sum_array_values( $epos = [], $include_variation_prices = false, $minkey = 'min' ) {
		$r = [];

		if ( is_array( $epos ) ) {
			$input                = $epos['price'];
			$variation_section_id = $epos['variation_section_id'];
			$variations_max       = [];
			$variations_min       = [];
			$variations_all       = 0;

			$logictree            = [];
			$logictree_helper     = [];
			$logictree_max        = [];
			$logictree_helper_max = [];
			$section_ids          = [];

			$add_logic_prices     = 0;
			$add_logic_prices_max = 0;

			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {

					$j         = json_decode( $value['clogic'] );
					$has_logic = json_decode( $value['logic'] );

					if ( '1' === (string) $value['required'] ) {
						if ( ! isset( $section_ids[ $value['section_uniqueid'] ] ) ) {
							$section_ids[ $value['section_uniqueid'] ] = [];
						}
						$section_ids[ $value['section_uniqueid'] ][ $value['element'] ] = $value['uniqueid'];
					}
					foreach ( $value as $key2 => $value2 ) {
						if ( ! ( $key2 === $minkey || 'max' === $key2 ) ) {
							continue;
						}
						$a = 0;
						if ( isset( $r[ $key2 ] ) ) {
							$a = $r[ $key2 ];
						}

						if ( $j ) {
							foreach ( $j->rules as $jkey => $rules ) {
								if ( $rules ) {
									$section = $rules->section;
									$element = $rules->element;
									if ( property_exists( $rules, 'value' ) ) {
										$lvalue = $rules->value;
									} else {
										$lvalue = '';
									}

									$operator = $rules->operator;

									if ( $section == $variation_section_id && 0 == $element ) { // phpcs:ignore WordPress.PHP.StrictComparisons
										if ( 'max' === $key2 ) {

											if ( ! $has_logic ) {
												$variations_all = $variations_all + floatval( $value2 );
											}
											if ( $has_logic && in_array( $operator, [ 'is', 'isnotempty' ], true ) ) {
												if ( ! isset( $variations_max[ $lvalue ] ) ) {
													$variations_max[ $lvalue ] = 0;
												}
												$variations_max[ $lvalue ] = $variations_max[ $lvalue ] + floatval( $value2 );
											}
										}
										if ( $key2 === $minkey ) {
											if ( ! isset( $variations_min[ $lvalue ] ) ) {
												$variations_min[ $lvalue ] = 0;
											}
											$variations_min[ $lvalue ] = $variations_min[ $lvalue ] + floatval( $value2 );
										}
									} else {

										if ( $has_logic ) {
											if ( $key2 === $minkey ) {
												if ( ! isset( $logictree[ $section ] ) ) {
													$logictree[ $section ] = [];
												}
												if ( ! isset( $logictree[ $section ][ $element ] ) ) {
													$logictree[ $section ][ $element ] = [];
												}
												if ( ! isset( $logictree[ $section ][ $element ][ $value['uniqueid'] ] ) ) {
													$logictree[ $section ][ $element ][ $value['uniqueid'] ] = 0;
												}
												if ( ! isset( $logictree_helper[ $section ] ) ) {
													$logictree_helper[ $section ] = [];
												}
												if ( ! isset( $logictree_helper[ $section ][ $element ] ) ) {
													$logictree_helper[ $section ][ $element ] = [];
												}
												if ( ! isset( $logictree_helper[ $section ][ $element ][ $value['uniqueid'] ] ) ) {
													$logictree_helper[ $section ][ $element ][ $value['uniqueid'] ] = $j;
												}
												$logictree[ $section ][ $element ][ $value['uniqueid'] ] = floatval( $value2 );

											}

											if ( 'max' === $key2 ) {
												if ( ! isset( $logictree_max[ $section ] ) ) {
													$logictree_max[ $section ] = [];
												}
												if ( ! isset( $logictree_max[ $section ][ $element ] ) ) {
													$logictree_max[ $section ][ $element ] = [];
												}
												if ( ! isset( $logictree_max[ $section ][ $element ][ $value['uniqueid'] ] ) ) {
													$logictree_max[ $section ][ $element ][ $value['uniqueid'] ] = 0;
												}
												if ( ! isset( $logictree_helper_max[ $section ] ) ) {
													$logictree_helper_max[ $section ] = [];
												}
												if ( ! isset( $logictree_helper_max[ $section ][ $element ] ) ) {
													$logictree_helper_max[ $section ][ $element ] = [];
												}
												if ( ! isset( $logictree_helper_max[ $section ][ $element ][ $value['uniqueid'] ] ) ) {
													$logictree_helper_max[ $section ][ $element ][ $value['uniqueid'] ] = $j;
												}
												$logictree_max[ $section ][ $element ][ $value['uniqueid'] ] = floatval( $value2 );

											}
										}
									}
								}
							}
						}

						if ( $key2 === $minkey ) {
							if ( ! $value['section_logic'] && ! $value['logic'] ) {
								$r[ $key2 ] = floatval( $value2 ) + $a;
							}
						}
						if ( 'max' === $key2 ) {
							$r[ $key2 ] = floatval( $value2 ) + $a;
						}
					}
				}
			}

			$logic_prices       = [];
			$required_not_found = [];

			$checkmultiple = [];

			foreach ( $logictree as $section_id => $section ) {

				foreach ( $section as $element => $element_ids ) {

					foreach ( $element_ids as $id => $price ) {

						if ( ! isset( $required_not_found[ $id ] ) && isset( $section_ids[ $section_id ] ) && isset( $section_ids[ $section_id ][ $element ] ) ) {

							if ( ! isset( $logic_prices[ $section_id ] ) ) {
								$logic_prices[ $section_id ] = [];
							}
							if ( ! isset( $logic_prices[ $section_id ][ $element ] ) ) {
								$logic_prices[ $section_id ][ $element ] = [];
							}

							$rules = $logictree_helper[ $section_id ][ $element ][ $id ];

							foreach ( $rules->rules as $jkey => $rule ) {
								if ( $rule ) {
									$isection  = $rule->section;
									$ielement  = $rule->element;
									$ilvalue   = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $rule->value ) ) );
									$ioperator = $rule->operator;

									foreach ( $epos['global'] as $priority => $pid ) {
										foreach ( $pid as $keydata => $data ) {
											foreach ( $data['sections'] as $epo_section ) {
												if ( $section_id === $epo_section['sections_uniqid'] ) {
													if ( isset( $epo_section['elements'][ $ielement ] ) ) {
														$el = $epo_section['elements'][ $ielement ];
														foreach ( $el['options'] as $xk => $xv ) {
															$el['options'][ $xk ] = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $xv ) ) );
														}
														switch ( $el['type'] ) {
															case 'radio':
															case 'select':
																if ( count( $el['options'] ) > 1 ) {
																	if ( ! isset( $checkmultiple[ $el['uniqid'] ] ) ) {
																		$checkmultiple[ $el['uniqid'] ] = [
																			'options' => array_flip( $el['options'] ),
																			'found'   => [],
																		];
																	}
																	$checkmultiple[ $el['uniqid'] ]['found'][] = [
																		'value' => $ilvalue,
																		'price' => $price,
																	];
																} else {
																	$logic_prices[ $section_id ][ $element ][] = $price;
																}

																break;

															default:
																// code...
																break;
														}
													}
												} else {
													continue;
												}
											}
										}
									}
								}
							}
						} else {
							if ( isset( $logictree_helper[ $section_id ] ) && isset( $logictree_helper[ $section_id ][ $element ] ) && isset( $logictree_helper[ $section_id ][ $element ][ $id ] ) ) {
								$rules = $logictree_helper[ $section_id ][ $element ][ $id ];
								if ( 'show' === $rules->toggle ) {
									if ( 'all' === $rules->what ) {
										$required_not_found[ $id ] = $id;
									}
								} elseif ( 'hide' === $rules->toggle ) {
									// not enough information so we just add it.
									$required_not_found[ $id ] = $id;
								}
							} else {
								$required_not_found[ $id ] = $id;
							}
						}
					}
				}
			}

			foreach ( $checkmultiple as $idata ) {

				$min_max = [];

				foreach ( $idata['found'] as $price_data ) {
					$ivalue = $price_data['value'];
					if ( isset( $idata['options'][ $ivalue ] ) ) {

						if ( ! isset( $min_max[ $ivalue ] ) || ! is_array( $min_max[ $ivalue ] ) ) {
							$min_max[ $ivalue ] = [];
						}
						$min_max[ $ivalue ][] = floatval( $price_data['price'] );

					}
				}
				$min_max_n = count( $min_max );
				$idata_n   = count( $idata['options'] );

				if ( $min_max_n >= $idata_n ) {
					$all_min_max = [];
					foreach ( $min_max as $key => $price_min_max ) {
						$all_min_max[ $key ] = array_sum( $price_min_max );
					}
					if ( ! empty( $all_min_max ) ) {
						$add_logic_prices = $add_logic_prices + min( $all_min_max );
					}
				}
			}

			foreach ( $logic_prices as $key => $section_id ) {
				foreach ( $section_id as $prices ) {
					foreach ( $prices as $price ) {
						$add_logic_prices = $add_logic_prices + $price;
					}
				}
			}

			// MAX.

			$logic_prices       = [];
			$required_not_found = [];
			$checkmultiple      = [];

			foreach ( $logictree_max as $section_id => $section ) {

				foreach ( $section as $element => $element_ids ) {

					foreach ( $element_ids as $id => $price ) {

						if ( ! isset( $required_not_found[ $id ] ) && isset( $section_ids[ $section_id ] ) && isset( $section_ids[ $section_id ][ $element ] ) ) {

							if ( ! isset( $logic_prices[ $section_id ] ) ) {
								$logic_prices[ $section_id ] = [];
							}
							if ( ! isset( $logic_prices[ $section_id ][ $element ] ) ) {
								$logic_prices[ $section_id ][ $element ] = [];
							}

							$rules = $logictree_helper_max[ $section_id ][ $element ][ $id ];

							foreach ( $rules->rules as $jkey => $rule ) {
								if ( $rule ) {
									$isection  = $rule->section;
									$ielement  = $rule->element;
									$ilvalue   = $rule->value;
									$ioperator = $rule->operator;

									foreach ( $epos['global'] as $priority => $pid ) {
										foreach ( $pid as $keydata => $data ) {
											foreach ( $data['sections'] as $epo_section ) {
												if ( $section_id === $epo_section['sections_uniqid'] ) {
													if ( isset( $epo_section['elements'][ $ielement ] ) ) {
														$el = $epo_section['elements'][ $ielement ];

														switch ( $el['type'] ) {
															case 'radio':
															case 'select':
																if ( count( $el['options'] ) > 1 ) {
																	if ( ! isset( $checkmultiple[ $el['uniqid'] ] ) ) {
																		$checkmultiple[ $el['uniqid'] ] = [
																			'options' => array_flip( $el['options'] ),
																			'found'   => [],
																		];
																	}
																	$checkmultiple[ $el['uniqid'] ]['found'][] = [
																		'value' => $ilvalue,
																		'price' => $price,
																	];
																} else {
																	$logic_prices[ $section_id ][ $element ][] = $price;
																}

																break;

															default:
																// code...
																break;
														}
													}
												} else {
													continue;
												}
											}
										}
									}
								}
							}
						} else {
							if ( isset( $logictree_helper_max[ $section_id ] ) && isset( $logictree_helper_max[ $section_id ][ $element ] ) && isset( $logictree_helper_max[ $section_id ][ $element ][ $id ] ) ) {
								$rules = $logictree_helper_max[ $section_id ][ $element ][ $id ];
								if ( 'show' === $rules->toggle ) {
									if ( 'all' === $rules->what ) {
										$required_not_found[ $id ] = $id;
									}
								} elseif ( 'hide' === $rules->toggle ) {
									// not enough information so we just add it.
									$required_not_found[ $id ] = $id;
								}
							} else {
								$required_not_found[ $id ] = $id;
							}
						}
					}
				}
			}

			foreach ( $checkmultiple as $idata ) {

				$min_max = [];

				foreach ( $idata['found'] as $price_data ) {
					$ivalue = $price_data['value'];
					if ( isset( $idata['options'][ $ivalue ] ) ) {

						if ( ! isset( $min_max[ $ivalue ] ) || ! is_array( $min_max[ $ivalue ] ) ) {
							$min_max[ $ivalue ] = [];
						}
						$min_max[ $ivalue ][] = floatval( $price_data['price'] );

					}
				}

				$all_min_max = [];
				foreach ( $min_max as $key => $price_min_max ) {
					$all_min_max[ $key ] = array_sum( $price_min_max );
				}
				if ( ! empty( $all_min_max ) ) {
					$add_logic_prices_max = $add_logic_prices_max + min( $all_min_max );
				}
			}

			foreach ( $logic_prices as $key => $section_id ) {
				foreach ( $section_id as $prices ) {
					foreach ( $prices as $price ) {
						$add_logic_prices_max = $add_logic_prices_max + $price;
					}
				}
			}
		}

		if ( ! empty( $variations_max ) ) {
			foreach ( $variations_max as $key => $value ) {
				$variations_max[ $key ] = $value + $variations_all;
			}
			$r['max'] = ( $include_variation_prices ) ? $variations_max : max( $variations_max );
		}
		if ( ! empty( $variations_min ) ) {
			$check = min( $variations_min );
			if ( ! empty( $check ) ) {
				$r[ $minkey ] = ( $include_variation_prices ) ? $variations_min : min( $variations_min );
			}
		}

		if ( isset( $r['max'] ) ) {

			if ( is_array( $r['max'] ) ) {
				array_walk( $r['max'], [ $this, 'add_values_walker' ], $add_logic_prices );
			} else {
				$r['max'] = $r['max'] + $add_logic_prices_max;
			}
		}
		if ( isset( $r[ $minkey ] ) ) {
			if ( $include_variation_prices && is_array( $r[ $minkey ] ) ) {
				array_walk( $r[ $minkey ], [ $this, 'add_values_walker' ], $add_logic_prices );
			} else {
				$r[ $minkey ] = $r[ $minkey ] + $add_logic_prices;
			}
		}

		return $r;
	}

	/**
	 * Walker for adding values
	 *
	 * @param array $value Input array.
	 * @param array $key Array.
	 * @param array $num Value to add.
	 * @since 1.0
	 */
	public function add_values_walker( $value, $key, $num ) {
		$value = floatval( $value ) + floatval( $num );
	}

	/**
	 * Add array values
	 *
	 * @param array $input Input array.
	 * @param array $add Input array.
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
	 * @since 1.0
	 */
	public function build_array( $a = [], $b = [] ) {
		if ( ! is_array( $a ) || ! is_array( $b ) ) {
			return $a;
		}

		$r = [];

		foreach ( $b as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( isset( $a[ $key ] ) ) {
					$r[ $key ] = $value;
				} else {
					$r[ $key ] = $this->build_array( $a[ $key ], $b[ $key ] );
				}
			} else {
				if ( isset( $a[ $key ] ) ) {
					$r[ $key ] = $a[ $key ];
				} else {
					$r[ $key ] = $value;
				}
			}
		}

		return $r;
	}

	/**
	 * Filters an $input array by key.
	 *
	 * @param mixed  $input Input array.
	 * @param string $what String to search.
	 * @param string $where Placement where to search 'start' or 'end'.
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
	 * @param mixed    $array Input array.
	 * @param mixed    $array2 Input array.
	 * @param callable $callback Callback function.
	 * @since 1.0
	 */
	public function array_map_deep( $array, $array2, $callback ) {
		$new = [];
		if ( is_array( $array ) && is_array( $array2 ) ) {
			foreach ( $array as $key => $val ) {
				if ( is_array( $val ) && is_array( $array2[ $key ] ) ) {
					$new[ $key ] = $this->array_map_deep( $val, $array2[ $key ], $callback );
				} else {
					$new[ $key ] = call_user_func( $callback, $val, $array2[ $key ] );
				}
			}
		} else {
			$new = call_user_func( $callback, $array, $array2 );
		}

		return $new;

	}

	/**
	 * Applies the callback to the elements of the given arrays, recursively
	 *
	 * @param  callable $callback Callback function to run for each element in each array.
	 * @param  array    $array    An array to run through the callback function.
	 * @return array Applies the callback to the elements of the given array.
	 */
	public function array_map_recursive( $callback, $array ) {
		if ( is_array( $array ) ) {
			return array_map(
				function ( $array ) use ( $callback ) {
					return $this->array_map_recursive( $callback, $array );
				},
				$array
			);
		}
		return $callback( $array );
	}

	/**
	 * Gets the site domain
	 *
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
	 * @since 1.0
	 */
	public function get_url_to_postid( $url ) {
		if ( function_exists( 'url_to_postid' ) ) {
			return url_to_postid( $url );
		}

		return 0;
	}

	/**
	 * Checks for WordPress meta mode
	 *
	 * This will be removed in a future version when support
	 * for WordPress version lower than 4 will be removed
	 *
	 * @since 1.0
	 */
	public function new_meta() {
		global $wp_version;

		return version_compare( $wp_version, '4.0.1', '>' );
	}

	/**
	 * Count number of words
	 *
	 * @param string $string Input string.
	 * @since 6.0
	 */
	public function count_words( $string ) {

		return preg_match_all( '/[\pL\d!@#$%^\&*()_+=\{[\}\]|\\"\':;?\/>.<,-]+/u', $string );

	}

	/**
	 * Build custom meta query
	 *
	 * @param string $relation The query relation.
	 * @param string $meta_key The meta key.
	 * @param string $meta_value The meta value.
	 * @param string $compare Compare operator.
	 * @param string $exists Exists operator.
	 * @since 1.0
	 */
	public function build_meta_query( $relation = 'OR', $meta_key = '', $meta_value = '', $compare = '!=', $exists = 'NOT EXISTS' ) {
		$meta_array = [
			'relation' => $relation,
			[
				'key'     => $meta_key, // get only enabled global extra options.
				'value'   => $meta_value,
				'compare' => $compare,
			],
			[
				'key'     => $meta_key, // backwards compatibility.
				'value'   => $meta_value,
				'compare' => $exists,
			],
		];
		if ( $this->new_meta() ) {
			$meta_array = [
				'relation' => $relation,
				[
					'key'     => $meta_key, // get only enabled global extra options.
					'value'   => $meta_value,
					'compare' => $compare,
				],
				[
					'key'     => $meta_key, // backwards compatibility.
					'compare' => $exists,
				],
			];

		}

		return $meta_array;
	}

	/**
	 * Create a uniqe ID
	 *
	 * @param string $prefix Specifies a prefix to the unique ID.
	 * @since 1.0
	 */
	public function tm_uniqid( $prefix = '' ) {
		return uniqid( $prefix, true );
	}

	/**
	 * Create uniqe IDs for provided array length
	 *
	 * @param integer $s Array length.
	 * @since 1.0
	 */
	public function tm_temp_uniqid( $s ) {
		$a = [];
		for ( $m = 0; $m < $s; $m ++ ) {
			$a[] = $this->tm_uniqid();
		}

		return $a;
	}

	/**
	 * EncodeURIComponent functioanlity
	 *
	 * @param string $str Input string.
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
	 * @since 1.0
	 */
	public function wc_base_currency() {

		$from_currency = get_option( 'woocommerce_currency' );

		return $from_currency;

	}

	/**
	 * Get enabled currencies
	 *
	 * @since 1.0
	 */
	public function get_currencies() {
		return apply_filters( 'wc_epo_enabled_currencies', [ $this->wc_base_currency() ] );
	}

	/**
	 * Get additional currencies
	 *
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
	 * @param string|null $currency Input currency.
	 * @param string      $prefix The prefix to add.
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
	 * @since 1.0
	 */
	public function init_filesystem() {
		if ( function_exists( 'get_filesystem_method' ) ) {
			$access_type = get_filesystem_method();
			if ( 'direct' === $access_type ) {
				// you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL.
				$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, [] );

				// initialize the API.
				if ( ! WP_Filesystem( $creds ) ) {
					// any problems and we exit.
					return '';
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
				$img         = '<img class="tm-mime" src="' . esc_attr( wp_mime_type_icon( $filetype['type'] ) ) . '" /> ';
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
				$img      = '<img class="tm-mime" src="' . esc_attr( wp_mime_type_icon( $filetype['type'] ) ) . '" /> ';

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
	 * @param array   $input_array The input array.
	 * @param string  $search_value The value to search.
	 * @param boolean $case_sensitive if search is case sensitive.
	 *
	 * @return array
	 */
	public function array_contains_key( array $input_array, $search_value, $case_sensitive = true ) {
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
	 * @param array  $input_array The input array.
	 * @param string $search_value The value to search.
	 * @param array  $excludes The values to exclude.
	 *
	 * @return array
	 */
	public function array_keys_end_with( array $input_array = [], $search_value = '', $excludes = [] ) {
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
	 * @since 1.0
	 */
	public function sanitize_key( $source ) {
		return str_replace( [ '[', ']' ], '', $source );
	}

	/**
	 * Recursively implodes an array with optional key inclusion
	 *
	 * Example of $include_keys output: key, value, key, value, key, value
	 * https://gist.github.com/jimmygle/2564610
	 *
	 * @access  public
	 *
	 * @param array   $array        multi-dimensional array to recursively implode.
	 * @param string  $glue         value that glues elements together.
	 * @param boolean $include_keys include keys before their values.
	 * @param boolean $trim_all     trim ALL whitespace from string.
	 *
	 * @return string  imploded array
	 */
	public function recursive_implode( $array = [], $glue = ',', $include_keys = false, $trim_all = false ) {
		if ( ! is_array( $array ) ) {
			return $array;
		}

		$glued_string = '';
		// Recursively iterates array and adds key/value to glued string.
		array_walk_recursive(
			$array,
			function ( $value, $key ) use ( $glue, $include_keys, &$glued_string ) {
				if ( $include_keys ) {
					$glued_string .= $key . $glue;
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
	 * @param array $arr The array with master keys to check.
	 * @param array $arr2 Array to compare keys against.
	 * @return array
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
	 * @since 6.0
	 */
	public function normalize_data( $data = [], $normalize_key = false, $implode_value = true ) {
		if ( class_exists( 'Normalizer' ) ) {
			if ( is_array( $data ) ) {
				foreach ( $data as $post_data_key => $post_data_value ) {
					if ( is_array( $post_data_key ) ) {
						$post_data_key = $this->recursive_implode( $post_data_key, '' );
					}
					if ( is_array( $post_data_value ) ) {
						if ( $implode_value ) {
							$post_data_value = $this->recursive_implode( $post_data_value, '' );
							$post_data_value = Normalizer::normalize( $post_data_value );
						} else {
							foreach ( $post_data_value as $key => $value ) {
								if ( is_array( $value ) || is_object( $value ) ) {
									$post_data_value[ $key ] = $value;
								} else {
									$post_data_value[ $key ] = Normalizer::normalize( $value );
								}
							}
						}
					} else {
						$post_data_value = Normalizer::normalize( $post_data_value );
					}
					if ( $normalize_key ) {
						$post_data_key = Normalizer::normalize( $post_data_key );
					}
					$data[ Normalizer::normalize( $post_data_key ) ] = $post_data_value;
				}
			} elseif ( is_object( $data ) ) {
				return $data;
			} else {
				$data = Normalizer::normalize( $data );
			}
		}
		return $data;
	}

	/**
	 * Array map for keys
	 *
	 * @param callable $callback The function callback.
	 * @param array    $array The input array.
	 * @param array    $args Array of arguments.
	 * @since 6.0
	 */
	public function array_map_key( $callback, $array, $args = [] ) {
		$out = [];

		foreach ( $array as $key => $value ) {
			$mapkey         = call_user_func_array( $callback, array_merge( [ $key ], $args ) );
			$out[ $mapkey ] = $value;
		}

		return $out;
	}

	/**
	 * Safe html_entity_decode
	 *
	 * @param string $string The value to decode.
	 * @since 1.0
	 */
	public function html_entity_decode( $string = '' ) {
		return html_entity_decode( $string, version_compare( phpversion(), '5.4', '<' ) ? ENT_COMPAT : ( ENT_COMPAT | ENT_HTML401 ), 'UTF-8' );
	}

	/**
	 * Array map with html_entity_decode
	 *
	 * @param mixed $value The value to decode.
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
	 * @param array $args The parameters to pass to get_posts().
	 *
	 * @return array List of posts matching $args.
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
				if ( isset( $_SERVER ) && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
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

		if ( 'integer' === gettype( $unformatted ) || 'double' === gettype( $unformatted ) ) {
			return $unformatted;
		}

		return 0;
	}

	/**
	 * Convert url to ssl if it applies
	 *
	 * @param string $url The url.
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
	 * @param array    $array The array.
	 * @param callable $fn The callable function.
	 * @since 6.1
	 */
	public function array_some( $array, $fn ) {
		if ( is_array( $array ) && $fn ) {
			foreach ( $array as $value ) {
				if ( $fn( $value ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Convert user input to number
	 *
	 * @param string  $val The value to convert.
	 * @param boolean $noformat If a number should be returned for PHP calculations.
	 * @since 6.3
	 */
	public function convert_to_number( $val = '', $noformat = false ) {
		if ( function_exists( 'numfmt_create' ) ) {
			$locale = get_locale();
			if ( isset( $_SERVER ) && isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
				$locale = explode( ',', wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$locale = $locale[0];
				$locale = str_replace( '-', '_', $locale );
			}
			$fmt = numfmt_create( $locale, NumberFormatter::DECIMAL );
			// Set the maximum number of decimal places.
			$fmt->setAttribute( NumberFormatter::MAX_FRACTION_DIGITS, 50 );
			$val = numfmt_format( $fmt, $val );

			if ( $noformat ) {
				$thousand_separator_symbol = numfmt_get_symbol( $fmt, NumberFormatter::GROUPING_SEPARATOR_SYMBOL );
				$decimal_separator_symbol  = numfmt_get_symbol( $fmt, NumberFormatter::DECIMAL_SEPARATOR_SYMBOL );

				$val = str_replace( $thousand_separator_symbol, '', $val );
				$val = str_replace( $decimal_separator_symbol, '.', $val );
			}
		}
		return $val;
	}
}
