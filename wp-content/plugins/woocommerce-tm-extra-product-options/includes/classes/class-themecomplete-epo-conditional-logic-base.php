<?php
/**
 * Extra Product Options Conditional Logic class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Conditional Logic class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_Conditional_Logic_Base {

	/**
	 * Cache for the option fields
	 *
	 * @var array<mixed>
	 */
	private $fields = [];

	/**
	 * Visible elements cache
	 *
	 * @var array<mixed>
	 */
	private $visible_elements = [];

	/**
	 * Current element that is being checked if it is visible
	 *
	 * @var array<mixed>
	 */
	private $current_element_to_check = [];

	/**
	 * Current field that is being checked if it is visible
	 *
	 * @var array<mixed>
	 */
	private $current_field_to_check = [];

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Conditional_Logic_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_Conditional_Logic_Base
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
	 * Converts the conditional logic rule to the new format.
	 *
	 * @param array<mixed> $rules The rules array.
	 * @return array<mixed>
	 * @since 6.4
	 */
	public function convert_rules( $rules = [] ) {
		$rules = json_decode( wp_json_encode( $rules ), true );
		if ( isset( $rules['what'] ) ) {
			if ( 'all' === $rules['what'] ) {
				$rules['rules'] = [ $rules['rules'] ];
			} elseif ( 'any' === $rules['what'] ) {
				$rules['rules'] = array_reduce(
					$rules['rules'],
					function ( $accumulator, $elrule ) {
						$accumulator[] = [ $elrule ];
						return $accumulator;
					},
					[]
				);
			}
			unset( $rules['what'] );
		}
		return $rules;
	}

	/**
	 * Converts the conditional logic rule to the new format.
	 *
	 * @param array<mixed>   $rules The rules array.
	 * @param array<mixed>   $section_ids The sections array.
	 * @param string|boolean $variation_section_id The id of the variation section if it exists.
	 * @return array<mixed>
	 *
	 * @since 6.4
	 */
	public function transform_rules( $rules = [], $section_ids = [], $variation_section_id = false ) {

		unset( $rules['element'] );
		if ( isset( $rules['rules'] ) && is_array( $rules['rules'] ) ) {
			foreach ( $rules['rules'] as $jkey => $jrules ) {
				if ( $jrules ) {
					if ( ! is_array( $jrules ) ) {
						$jrules = [ $jrules ];
					}
					foreach ( $jrules as $jjkey => $jjrules ) {
						if ( isset( $jjrules['section'] ) && isset( $jjrules['element'] ) ) {
							if ( $variation_section_id === $jjrules['section'] && '0' === (string) $jjrules['element'] ) {
								$rules['rules'][ $jkey ][ $jjkey ] = [
									'element'  => $variation_section_id,
									'operator' => $jjrules['operator'],
									'value'    => $jjrules['value'],
								];
							} elseif ( isset( $section_ids[ $jjrules['section'] ][ $jjrules['element'] ] ) ) {
								$rules['rules'][ $jkey ][ $jjkey ] = [
									'element'  => $section_ids[ $jjrules['section'] ][ $jjrules['element'] ],
									'operator' => $jjrules['operator'],
									'value'    => $jjrules['value'],
								];
							}
						} else {
							unset( $rules['rules'][ $jkey ][ $jjkey ] );
						}
					}
				}
			}
		}

		return $rules;
	}

	/**
	 * Check if the logic is valid
	 *
	 * @param array<mixed> $logicrules The logic data.
	 * @return boolean
	 * @since 6.4
	 */
	public function is_valid_logic( $logicrules = [] ) {

		if ( isset( $logicrules['toggle'] ) && ( isset( $logicrules['element'] ) || isset( $logicrules['section'] ) ) && ! empty( $logicrules['rules'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Generate the option fields from a product ID
	 *
	 * @param integer $product_id The product id.
	 *
	 * @return array<mixed>|boolean
	 */
	public function generate_fields( $product_id = 0 ) {
		$post_id = get_the_ID();

		if ( $product_id && $product_id !== $post_id ) {
			$post_id = $product_id;
		}
		if ( ! empty( $this->fields[ $post_id ] ) ) {
			return $this->fields[ $post_id ];
		}

		$fields = false;

		$epos = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id, '', false, true );

		if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) && ! empty( $epos['price'] ) ) {
			$input = $epos['price'];

			$variation_section_id = $epos['variation_section_id'];

			$fields = [
				'init'                            => false,
				'section_ids'                     => [],
				'variation_section_id'            => false,
				'required'                        => [],
				'required_no_logic'               => [],
				'required_with_logic'             => [],
				'required_with_section_logic'     => [],
				'not_required'                    => [],
				'not_required_no_logic'           => [],
				'not_required_with_logic'         => [],
				'not_required_with_section_logic' => [],
			];

			$section_ids = array_reduce(
				$input,
				function ( $carry, $value ) {
					if ( ! isset( $carry[ $value['section_uniqueid'] ] ) ) {
						$carry[ $value['section_uniqueid'] ] = [];
					}
					$carry[ $value['section_uniqueid'] ][ $value['element'] ] = $value['uniqueid'];
					return $carry;
				},
				[]
			);

			foreach ( $input as $key => $data ) {
				$has_logic         = json_decode( $data['logic'] );
				$has_section_logic = json_decode( $data['section_logic'] );

				$logicrules = '';
				if ( $has_logic ) {
					$logicrules = $this->convert_rules( (array) json_decode( $data['logicrules'] ) );
					$has_logic  = $this->is_valid_logic( $logicrules );
				}
				$data['logicrules'] = $logicrules;

				$section_logicrules = '';
				if ( $has_section_logic ) {
					$section_logicrules = $this->convert_rules( (array) json_decode( $data['section_logicrules'] ) );
					$has_section_logic  = $this->is_valid_logic( $section_logicrules );
				}
				$data['section_logicrules'] = $section_logicrules;

				$price_mapping = array_map(
					function ( $number ) {
						return floatval( $number[0] );
					},
					$data['prices']
				);
				// Check is the $price_mapping is an associative array and if not convert it to one.
				$price_mapping = ! empty( $price_mapping ) ? ( ( count( array_filter( array_keys( $price_mapping ), 'is_string' ) ) > 0 ) ? $price_mapping : array_combine( [ '' ], $price_mapping ) ) : null;

				$price_type = array_map(
					function ( $number ) {
						return $number[0];
					},
					$data['price_type']
				);

				$value = [
					'logicrules'         => $has_logic ? $this->transform_rules( $data['logicrules'], $section_ids, $variation_section_id ) : null,
					'section_logicrules' => $has_section_logic ? $this->transform_rules( $data['section_logicrules'], $section_ids, $variation_section_id ) : null,
					'price_mapping'      => $price_mapping,
					'options'            => ! empty( $data['options'] ) ? $data['options'] : null,
					'price_type'         => $price_type,
					'type'               => $data['type'],
				];

				if ( '1' === (string) $data['required'] ) {
					$fields['required'][ $data['uniqueid'] ] = $value;
					if ( ! $has_logic && ! $has_section_logic ) {
						$fields['required_no_logic'][ $data['uniqueid'] ] = $value;
					}
					if ( $has_logic ) {
						$fields['required_with_logic'][ $data['uniqueid'] ] = $value;
					}
					if ( $has_section_logic ) {
						$fields['required_with_section_logic'][ $data['uniqueid'] ] = $value;
					}
				} else {
					$fields['not_required'][ $data['uniqueid'] ] = $value;
					if ( ! $has_logic && ! $has_section_logic ) {
						$fields['not_required_no_logic'][ $data['uniqueid'] ] = $value;
					}
					if ( $has_logic ) {
						$fields['not_required_with_logic'][ $data['uniqueid'] ] = $value;
					}
					if ( $has_section_logic ) {
						$fields['not_required_with_section_logic'][ $data['uniqueid'] ] = $value;
					}
				}
			}

			$fields['variation_section_id'] = $variation_section_id;
			$fields['section_ids']          = $section_ids;
			$fields['init']                 = true;

			$this->fields[ $post_id ] = $fields;
		}

		return $fields;
	}

	/**
	 * Check if the logic is visible in the given combination
	 *
	 * @param string               $logic_type The logic type to check (logicrules or section_logicrules).
	 * @param string               $field_name The field name.
	 * @param array<mixed>         $combination The current fields combination.
	 * @param array<mixed>         $fields The fields array.
	 * @param boolean|array<mixed> $consider_visible A special array to fetch the current value so that the field would be visible.
	 * @param string|boolean       $variation_section_id The id of the variation section if it exists.
	 * @param integer|boolean      $current_variation The current variation id.
	 *
	 * @return array<mixed>|boolean
	 */
	public function is_logic_visible( $logic_type = 'logicrules', $field_name = '', $combination = [], $fields = [], $consider_visible = false, $variation_section_id = false, $current_variation = false ) {
		// This should only happen if the field_name is the variation id.
		if ( ! isset( $fields[ $field_name ] ) ) {
			return false;
		}

		$field_data        = $fields[ $field_name ];
		$visible_condition = $field_data[ $logic_type ];

		if ( null === $visible_condition ) {
			return true; // Field is always visible.
		}

		$toggle_action    = $visible_condition['toggle'];
		$condition_groups = $visible_condition['rules'];

		$group_visible = false;
		foreach ( $condition_groups as $conditions_key => $conditions ) {
			$conditions_met = false;

			foreach ( $conditions as $condition_key => $condition ) {
				$element  = $condition['element'];
				$operator = $condition['operator'];
				$value    = $condition['value'];

				if ( $element === $field_name ) {
					$conditions_met = false;
					break; // Stop checking conditions for this group.
				}

				if ( $element === $variation_section_id ) {
					$element_value = [ intval( $current_variation ) ];
				} else {
					// Check if dependent element is visible.
					if ( ! $this->is_field_visible( $element, $combination, $fields, false, $variation_section_id, $current_variation ) ) {
						$conditions_met = false;
						break; // Stop checking conditions for this group.
					}

					// This should only happen if the field_name is the variation id.
					if ( ! isset( $combination[ $element ] ) ) {
						$conditions_met = false;
						break; // Stop checking conditions for this group.
					}

					if ( is_array( $combination[ $element ] ) ) {
						$element_value = $combination[ $element ];
					} else {
						$element_value = [ $combination[ $element ] ];
					}
				}

				foreach ( $element_value as $single_value_key => $single_element_value ) {
					if ( $element === $variation_section_id ) {
						$single_value = $single_element_value;
					} elseif ( null !== $fields[ $element ]['options'] ) {
						$single_value = $fields[ $element ]['options'][ $single_element_value ];
					} else {
						$single_value = '';
					}
					if ( false !== $consider_visible ) {
						if ( isset( $fields[ $element ]['type'] ) && in_array( $fields[ $element ]['type'], [ 'textfield', 'textarea', 'color', 'date', 'time', 'range' ], true ) ) {
							$single_value = $consider_visible[ $conditions_key ][ $condition_key ];
						}
					}
					$single_value   = (string) $single_value;
					$value          = (string) $value;
					$conditions_met = $this->tm_check_match( $single_value, $value, $operator );
					if ( $conditions_met ) {
						break;
					}
				}
			}
			if ( $conditions_met ) {
				$group_visible = true;
				break;
			}
		}

		return ( 'show' === $toggle_action && $group_visible ) || ( 'hide' === $toggle_action && ! $group_visible );
	}

	/**
	 * Check if the field is visible in the given combination
	 *
	 * @param string               $field_name The field name.
	 * @param array<mixed>         $combination The current fields combination.
	 * @param array<mixed>         $fields The fields array.
	 * @param boolean|array<mixed> $consider_visible A special array to fetch the current value so that the field would be visible.
	 * @param string|boolean       $variation_section_id The id of the variation section if it exists.
	 * @param integer|boolean      $current_variation The current variation id.
	 *
	 * @return array<mixed>|boolean
	 */
	public function is_field_visible( $field_name = '', $combination = [], $fields = [], $consider_visible = false, $variation_section_id = false, $current_variation = false ) {
		if ( in_array( $field_name, $this->current_field_to_check, true ) ) {
			return false;
		}

		$id = uniqid();

		$this->current_field_to_check[ $id ] = $field_name;

		// This should only happen if the field_name is the variation id.
		if ( ! isset( $fields[ $field_name ] ) ) {
			return false;
		}

		$field_data = $fields[ $field_name ];

		$visible_condition_element = $field_data['logicrules'];
		$visible_condition_section = $field_data['section_logicrules'];

		if ( null === $visible_condition_element && null === $visible_condition_section ) {
			return true; // Field is always visible.
		}

		$logicrules         = $this->is_logic_visible( 'logicrules', $field_name, $combination, $fields, $consider_visible, $variation_section_id, $current_variation );
		$section_logicrules = $this->is_logic_visible( 'section_logicrules', $field_name, $combination, $fields, $consider_visible, $variation_section_id, $current_variation );
		unset( $this->current_field_to_check[ $id ] );
		return $logicrules && $section_logicrules;
	}

	/**
	 * Calculate all combinations
	 *
	 * @param array<mixed> $fields The fields array.
	 *
	 * @return array<mixed>
	 */
	public function max_calculate_all_combinations( $fields = [] ) {
		$field_names         = array_keys( $fields );
		$total_fields        = count( $field_names );
		$combinations        = [ [] ];
		$pre_combinations    = [ [] ];
		$result_combinations = [];
		$max_combinations    = intval( THEMECOMPLETE_EPO()->tm_epo_global_max_combinations );
		$max_combinations    = $max_combinations > 0 ? $max_combinations : 1;

		for ( $index = 0; $index < $total_fields; $index++ ) {
			$field_name       = $field_names[ $index ];
			$field_data       = $fields[ $field_name ];
			$choices          = null === $field_data['price_mapping'] ? [ '' ] : array_keys( $field_data['price_mapping'] );
			$is_mutilple      = isset( $field_data['type'] ) && ( 'checkbox' === $field_data['type'] || 'selectmultiple' === $field_data['type'] );
			$new_combinations = [];
			if ( ! $is_mutilple && 1 < count( $choices ) ) {
				$combinations_count = count( $combinations );
				if ( $max_combinations > $combinations_count ) {
					$comb_count = 0;
					foreach ( $combinations as $combination ) {
						foreach ( $choices as $choice ) {
							if ( $max_combinations <= $comb_count ) {
								break 2;
							}
							$new_combination    = $combination + [ $field_name => $choice ];
							$new_combinations[] = $new_combination;
							++$comb_count;
						}
					}
					$combinations = $new_combinations;
				}
			} else {
				foreach ( $pre_combinations as $combination ) {
					if ( $is_mutilple ) {
						// Generate combinations for checkbox fields.
						$new_combination = $combination;
						foreach ( $choices as $choice ) {
							$new_combination[ $field_name ][] = $choice;
						}
						$new_combinations[] = $new_combination;
					} elseif ( 1 === count( $choices ) ) { // Generate combinations for non-checkbox fields.
						$new_combination    = $combination + [ $field_name => $choices[0] ];
						$new_combinations[] = $new_combination;
					}
				}
				$pre_combinations = $new_combinations;
			}
		}

		$combinations_count = isset( $combinations[0] ) ? count( $combinations[0] ) : 0;
		foreach ( $pre_combinations as $combination ) {
			$result_combination = $combination;
			for ( $index = count( $combination ) + $combinations_count; $index < $total_fields; $index++ ) {
				$field_name  = $field_names[ $index ];
				$is_mutilple = isset( $fields[ $field_name ]['type'] ) && ( 'checkbox' === $fields[ $field_name ]['type'] || 'selectmultiple' === $fields[ $field_name ]['type'] );
				if ( $is_mutilple ) {
					$result_combination[ $field_name ] = [];
				} else {
					$result_combination[ $field_name ] = null;
				}
			}
			$result_combinations[] = $result_combination;
		}

		$combinations = array_slice( $combinations, 0, $max_combinations );

		$result_combinations = [
			'base' => $pre_combinations[0],
			'var'  => $combinations,
		];
		return $result_combinations;
	}

	/**
	 * Generates all combinations for a given combination
	 * based on the field values
	 *
	 * @param array<mixed> $combination The current fields combination.
	 *
	 * @return array<mixed>
	 */
	public function generate_combinations( $combination = [] ) {

		// Convert values to arrays if they are not already.
		$combination = array_map(
			function ( $value ) {
				return is_array( $value ) ? $value : [ $value ];
			},
			$combination
		);

		// Prepare the data for combination generation.
		$combination_values = array_values( $combination );

		$combinations = [ [] ];
		foreach ( $combination_values as $array ) {
			$temp = [];
			foreach ( $combinations as $result_item ) {
				foreach ( $array as $array_item ) {
					$temp[] = array_merge( $result_item, [ $array_item ] );
				}
			}
			$combinations = $temp;
		}

		// Combine keys and combinations to get the desired format.
		$result = [];
		foreach ( $combinations as $combo ) {
			$result[] = array_combine( array_keys( $combination ), $combo );
			if ( count( $result ) > 100 ) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Calculate the minimum price of the given combination
	 *
	 * @param array<mixed>   $fields The fields array.
	 * @param array<mixed>   $combination The current fields combination.
	 * @param array<mixed>   $variation_ids The variations ids.
	 * @param string|boolean $variation_section_id The id of the variation section if it exists.
	 *
	 * @return float|array<mixed>
	 */
	public function calculate_minimum_price_for_combination( $fields = [], $combination = [], $variation_ids = [], $variation_section_id = false ) {

		$prices = [];
		if ( false === $variation_section_id || empty( $variation_ids ) ) {
			$variation_ids = [ 0 ];
		}

		foreach ( $variation_ids as $current_variation ) {
			$visible_combination = $combination;
			foreach ( $fields as $field_name => $field_data ) {
				if ( ! $this->is_field_visible( $field_name, $visible_combination, $fields, false, $variation_section_id, $current_variation ) ) {
					unset( $visible_combination[ $field_name ] );
				}
			}

			$combinations = $this->generate_combinations( $visible_combination );
			$total_price  = PHP_INT_MAX;

			foreach ( $combinations as $comb ) {
				$min_price = 0;
				foreach ( $comb as $field_name => $selected_choice ) {
					$price_mapping = $fields[ $field_name ]['price_mapping'];
					if ( isset( $price_mapping[ $selected_choice ] ) && $this->is_field_visible( $field_name, $comb, $fields, false, $variation_section_id, $current_variation ) ) {
						$min_price += floatval( $price_mapping[ $selected_choice ] );
					}
				}

				$min_price   = floatval( $min_price );
				$total_price = min( $min_price, $total_price );
			}

			if ( PHP_INT_MAX === $total_price ) {
				$total_price = 0;
			}

			$prices[ $current_variation ] = $total_price;
		}

		$total_price = $prices;
		if ( 1 === count( $prices ) && 0 === key( $prices ) ) {
			$total_price = $prices[0];
		}

		return is_array( $total_price ) ? $total_price : floatval( $total_price );
	}

	/**
	 * Calculate the minimum price for the given fields
	 *
	 * @param array<mixed>    $fields The fields array from generate_fields.
	 * @param boolean|integer $product_id The product id if variation prices are included.
	 * @param string          $minkey The key 'min' or 'minall'. The minall does not include the field required status.
	 *
	 * @return float|array<mixed>
	 */
	public function calculate_minimum_price( $fields = [], $product_id = false, $minkey = 'min' ) {
		$input = $fields['required'];
		if ( 'minall' === $minkey ) {
			$input = array_merge( $fields['required'], $fields['not_required'] );
		}

		$variation_section_id = $fields['variation_section_id'];
		if ( ! $variation_section_id ) {
			$product_id = false;
		}

		$all_combinations = $this->max_calculate_all_combinations( $input );

		$variation_ids = [];
		$product       = wc_get_product( $product_id );
		if ( false !== $product ) {
			$product_type = themecomplete_get_product_type( $product );
			if ( false !== $product_id && 'variable' === $product_type ) {
				$variation_ids = $product->get_variation_prices( false ); // @phpstan-ignore-line
				$variation_ids = array_keys( $variation_ids['price'] );
			}
		}

		$min_price = PHP_INT_MAX;

		$base_combination = $all_combinations['base'];
		$var_combinations = $all_combinations['var'];
		foreach ( $var_combinations as $var_combination ) {
			$combination   = $base_combination + $var_combination;
			$current_price = $this->calculate_minimum_price_for_combination( $input, $combination, $variation_ids, $variation_section_id );
			if ( is_array( $current_price ) ) {
				if ( ! is_array( $min_price ) ) {
					$min_price = $current_price;
				} else {
					foreach ( $current_price as $key => $value ) {
						if ( isset( $min_price[ $key ] ) ) {
							$min_price[ $key ] = min( $value, $min_price[ $key ] );
						}
					}
				}
			} else {
				$min_price = min( $min_price, $current_price );
			}
		}
		if ( PHP_INT_MAX === $min_price ) {
			$min_price = 0;
		}

		return is_array( $min_price ) ? $min_price : floatval( $min_price );
	}

	/**
	 * Calculate the maximum price of the given combination
	 *
	 * @param array<mixed>   $fields The fields array.
	 * @param array<mixed>   $combination The current fields combination.
	 * @param array<mixed>   $variation_ids The variations ids.
	 * @param string|boolean $variation_section_id The id of the variation section if it exists.
	 *
	 * @return array<mixed>|float
	 */
	public function calculate_maximum_price_for_combination( $fields = [], $combination = [], $variation_ids = [], $variation_section_id = false ) {

		$prices = [];
		if ( false === $variation_section_id || empty( $variation_ids ) ) {
			$variation_ids = [ 0 ];
		}

		foreach ( $variation_ids as $current_variation ) {
			$textfield_dependent_fields = [];

			foreach ( $combination as $combination_field_name => $combination_value ) {
				$field_data        = $fields[ $combination_field_name ];
				$visible_condition = $field_data['logicrules'];
				if ( null !== $visible_condition ) {
					foreach ( $visible_condition['rules'] as $conditions_key => $conditions ) {
						foreach ( $conditions as $condition_key => $condition ) {
							$element = $condition['element'];
							if ( isset( $fields[ $element ] ) && isset( $fields[ $element ]['type'] ) && in_array( $fields[ $element ]['type'], [ 'textfield', 'textarea', 'color', 'date', 'time', 'range' ], true ) ) {
								$textfield_dependent_fields[ $combination_field_name ][ $conditions_key ][ $condition_key ] = $condition;
							}
						}
					}
				}
			}

			$dependent_fields = [];
			foreach ( $textfield_dependent_fields as $field_name => $rules ) {
				foreach ( $rules as $conditions_key => $conditions ) {
					foreach ( $conditions as $condition_key => $condition ) {
						switch ( $condition['operator'] ) {
							case 'is':
								$dependent_fields[ $field_name ][ $conditions_key ][ $condition_key ] = $condition['value'];
								break;
							case 'isnot':
								$dependent_fields[ $field_name ][ $conditions_key ][ $condition_key ] = strrev( $condition['value'] );
								break;
							case 'isempty':
								$dependent_fields[ $field_name ][ $conditions_key ][ $condition_key ] = '';
								break;
							case 'isnotempty':
								$dependent_fields[ $field_name ][ $conditions_key ][ $condition_key ] = uniqid( '10' );
								break;
						}
					}
				}
			}

			$visible_combination = $combination;
			foreach ( $fields as $field_name => $field_data ) {
				$consider_visible = isset( $dependent_fields[ $field_name ] ) ? $dependent_fields[ $field_name ] : false;
				if ( ! $this->is_field_visible( $field_name, $visible_combination, $fields, $consider_visible, $variation_section_id, $current_variation ) ) {
					unset( $visible_combination[ $field_name ] );
				}
			}

			$combinations = $this->generate_combinations( $visible_combination );
			$total_price  = 0;

			$prevented_choices = [];
			foreach ( $combinations as $comb_id => $comb ) {
				foreach ( $comb as $field_name => $selected_choice ) {
					$consider_visible = isset( $dependent_fields[ $field_name ] ) ? $dependent_fields[ $field_name ] : false;
					if ( ! $this->is_field_visible( $field_name, $comb, $fields, $consider_visible, $variation_section_id, $current_variation ) ) {
						$visible_conditions = [
							null !== $fields[ $field_name ]['logicrules'] ? $fields[ $field_name ]['logicrules'] : [],
							null !== $fields[ $field_name ]['section_logicrules'] ? $fields[ $field_name ]['section_logicrules'] : [],
						];
						foreach ( $visible_conditions as $visible_condition ) {
							if ( ! empty( $visible_condition ) ) {
								$toggle           = $visible_condition['toggle'];
								$condition_groups = $visible_condition['rules'];
								foreach ( $condition_groups as $conditions_key => $conditions ) {
									foreach ( $conditions as $condition_key => $condition ) {
										$element  = $condition['element'];
										$operator = $condition['operator'];
										$value    = rawurldecode( $condition['value'] );

										if ( ( 'show' === $toggle && 'isnot' === $operator ) || ( 'hide' === $toggle && 'is' === $operator ) ) {
											if ( isset( $comb[ $element ] ) ) {
												$prevented_choices[ $element ][ $value ] = $value;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			foreach ( $combinations as $comb_id => $comb ) {
				$dependent_price = [];
				$max_price       = 0;
				foreach ( $comb as $field_name => $selected_choice ) {
					$consider_visible = isset( $dependent_fields[ $field_name ] ) ? $dependent_fields[ $field_name ] : false;
					if ( $this->is_field_visible( $field_name, $comb, $fields, $consider_visible, $variation_section_id, $current_variation ) ) {
						$price_mapping    = $fields[ $field_name ]['price_mapping'];
						$selected_choices = (array) $combination[ $field_name ];
						foreach ( $selected_choices as $selected_choice ) {
							$choice = isset( $fields[ $field_name ]['options'][ $selected_choice ] ) ? $fields[ $field_name ]['options'][ $selected_choice ] : '';
							if ( ! isset( $prevented_choices[ $field_name ][ $choice ] ) ) {
								if ( isset( $price_mapping[ $selected_choice ] ) ) {
									if ( false !== $consider_visible ) {
										foreach ( $consider_visible as $gkey => $group ) {
											foreach ( $group as $ckey => $condition ) {
												if ( isset( $price_mapping[ $selected_choice ] ) && null !== $fields[ $field_name ]['logicrules'] && isset( $fields[ $field_name ]['logicrules']['rules'] ) && isset( $fields[ $field_name ]['logicrules']['rules'][ $gkey ] ) && isset( $fields[ $field_name ]['logicrules']['rules'][ $gkey ][ $ckey ] ) ) {
													$dependent_price[ $fields[ $field_name ]['logicrules']['rules'][ $gkey ][ $ckey ]['element'] ][ $fields[ $field_name ]['logicrules']['rules'][ $gkey ][ $ckey ]['value'] ][ $field_name ][] = $price_mapping[ $selected_choice ];
												}
											}
										}
									} elseif ( isset( $price_mapping[ $selected_choice ] ) ) {
										$max_price += floatval( $price_mapping[ $selected_choice ] );
									}
								}
							}
						}
					}
				}

				$max_price = floatval( $max_price );
				foreach ( $dependent_price as $dependent_element => $dependent_element_data ) {
					foreach ( $dependent_element_data as $dependent_element_key => $dependent_element_value ) {
						$dependent_price[ $dependent_element ][ $dependent_element_key ] = array_reduce(
							$dependent_price[ $dependent_element ][ $dependent_element_key ],
							function ( $carry, $item ) {
								return $carry + array_sum( $item );
							},
							0
						);
					}
				}

				$dependent_price = array_map(
					function ( $value ) {
						return max( $value );
					},
					$dependent_price
				);

				$max_price  += array_sum( $dependent_price );
				$total_price = max( $max_price, $total_price );
			}

			$prices[ $current_variation ] = $total_price;
		}

		$total_price = $prices;
		if ( 1 === count( $prices ) && 0 === key( $prices ) ) {
			$total_price = $prices[0];
		}

		return is_array( $total_price ) ? $total_price : floatval( $total_price );
	}

	/**
	 * Calculate the maximum price for the given fields
	 *
	 * @param array<mixed> $fields The fields array from generate_fields.
	 *
	 * @return float
	 */
	public function calculate_maximum_price( $fields ) {
		$input = array_merge( $fields['required'], $fields['not_required'] );

		$max_price = 0;

		foreach ( $input as $field ) {
			if ( isset( $field['price_mapping'] ) && is_array( $field['price_mapping'] ) ) {
				foreach ( $field['price_mapping'] as $price ) {
					$max_price += floatval( $price );
				}
			}
		}

		return floatval( $max_price );
	}

	/**
	 * Calculate the maximum price for the given fields
	 *
	 * @param array<mixed>    $fields The fields array from generate_fields.
	 * @param boolean|integer $product_id The product id if variation prices are included.
	 *
	 * @return float
	 */
	public function calculate_real_maximum_price( $fields, $product_id = false ) {
		$input = array_merge( $fields['required'], $fields['not_required'] );

		$variation_section_id = $fields['variation_section_id'];
		if ( ! $variation_section_id ) {
			$product_id = false;
		}

		$all_combinations = $this->max_calculate_all_combinations( $input );

		$variation_ids = [];
		$product       = wc_get_product( $product_id );
		if ( false !== $product ) {
			$product_type = themecomplete_get_product_type( $product );
			if ( false !== $product_id && 'variable' === $product_type ) {
				$variation_ids = $product->get_variation_prices( false ); // @phpstan-ignore-line
				$variation_ids = array_keys( $variation_ids['price'] );
			}
		}

		$max_price = 0;

		$base_combination = $all_combinations['base'];
		$var_combinations = $all_combinations['var'];

		foreach ( $var_combinations as $var_combination ) {
			$combination   = $base_combination + $var_combination;
			$current_price = $this->calculate_maximum_price_for_combination( $input, $combination, $variation_ids, $variation_section_id );
			if ( is_array( $current_price ) ) {
				if ( ! is_array( $max_price ) ) {
					$max_price = $current_price;
				} else {
					foreach ( $current_price as $key => $value ) {
						if ( isset( $max_price[ $key ] ) ) {
							$max_price[ $key ] = max( $value, $max_price[ $key ] );
						}
					}
				}
			} else {
				$max_price = max( $max_price, $current_price );
			}
		}

		return is_array( $max_price ) ? $max_price : floatval( $max_price );
	}

	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $section The section array.
	 * @param array<mixed> $sections The sections array.
	 * @param string       $form_prefix The form prefix.
	 * @return boolean
	 * @since 1.0
	 */
	public function is_visible( $element = [], $section = [], $sections = [], $form_prefix = '' ) {
		$id                                    = uniqid();
		$this->current_element_to_check[ $id ] = [];

		return $this->is_visible_do( $id, $element, $section, $sections, $form_prefix );
	}

	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @param string       $id The index of the current element to check in the $this->current_element_to_check array.
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $section The section array.
	 * @param array<mixed> $sections The sections array.
	 * @param string       $form_prefix The form prefix.
	 * @return boolean
	 * @since 1.0
	 */
	private function is_visible_do( $id = '0', $element = [], $section = [], $sections = [], $form_prefix = '' ) {

		$is_element = false;
		$is_section = false;

		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		$uniqid = isset( $element['uniqid'] ) ? $element['uniqid'] : false;

		if ( ! $uniqid ) {
			$uniqid     = isset( $element['sections_uniqid'] ) ? $element['sections_uniqid'] : false;
			$is_section = true;
		} else {
			$is_element = true;
		}

		if ( ! $uniqid ) {
			return false;
		}
		if ( isset( $this->visible_elements[ $array_prefix ][ $uniqid ] ) ) {
			return $this->visible_elements[ $array_prefix ][ $uniqid ];
		}

		$logic = false;

		if ( $is_element ) {

			// Element.
			if ( ! $this->is_visible_do( $id, $section, [], $sections, $form_prefix ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = false;
				return false;
			}
			if ( ! isset( $element['logic'] ) || empty( $element['logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = true;
				return true;
			}
			$logic = (array) json_decode( $element['logicrules'] );
		} elseif ( $is_section ) { // @phpstan-ignore-line
			// Section.
			if ( ! isset( $element['sections_logic'] ) || empty( $element['sections_logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = true;
				return true;
			}
			$logic = (array) json_decode( $element['sections_logicrules'] );
		} else {
			$this->visible_elements[ $array_prefix ][ $uniqid ] = true;
			return true;
		}

		$logic = $this->convert_rules( $logic );

		if ( $logic ) {

			$rule_toggle = $logic['toggle'];
			$matches     = [];
			$checked     = [];
			$show        = true;

			switch ( $rule_toggle ) {
				case 'show':
					$show = false;
					break;
				case 'hide':
					$show = true;
					break;
			}

			if ( ! isset( $this->current_element_to_check[ $id ] ) ) {
				$this->current_element_to_check[ $id ] = [];
			}

			if ( in_array( $uniqid, $this->current_element_to_check[ $id ], true ) ) {
				return true;
			}

			$this->current_element_to_check[ $id ][] = $uniqid;

			foreach ( $logic['rules'] as $key => $krules ) {
				$matches[ $key ] = 0;
				if ( is_array( $krules ) ) {
					foreach ( $krules as $rule ) {
						$matches[ $key ] = $matches[ $key ] + 1;
						$checked[ $key ] = 0;
						if ( $this->tm_check_field_match( $id, $rule, $sections, $form_prefix ) ) {
							$checked[ $key ] = $checked[ $key ] + 1;
						}
					}
				}
			}
			$this->current_element_to_check[ $id ] = [];

			foreach ( $matches as $im => $match ) {
				$checkim = intval( $checked [ $im ] );
				$match   = intval( $match );
				if ( $checkim > 0 && $match === $checkim ) {
					$show = ! $show;
					break;
				}
			}

			$this->visible_elements[ $array_prefix ][ $uniqid ] = $show;

			return $show;

		}

		$this->visible_elements[ $array_prefix ][ $uniqid ] = false;

		return false;
	}

	/**
	 * Conditional logic (checks element conditions)
	 *
	 * @param string             $id The index of the current element to check in the $this->current_element_to_check array.
	 * @param object|false       $rule The rule object.
	 * @param array<mixed>|false $sections The sections array.
	 * @param string             $form_prefix The form prefix.
	 * @return boolean
	 * @since 1.0
	 */
	public function tm_check_field_match( $id = '0', $rule = false, $sections = false, $form_prefix = '' ) {
		if ( empty( $rule ) || empty( $sections ) ) {
			return false;
		}

		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		$section_id = $rule['section'];
		$element_id = $rule['element'];
		$operator   = $rule['operator'];
		$value      = isset( $rule['value'] ) ? $rule['value'] : null;

		if ( (string) $section_id === (string) $element_id ) {
			return $this->tm_check_section_match( $element_id, $operator, $rule, $sections, $form_prefix );
		}
		if ( ! isset( $sections[ $section_id ] )
			|| ! isset( $sections[ $section_id ]['elements'] )
			|| ! isset( $sections[ $section_id ]['elements'][ $element_id ] )
			|| ! isset( $sections[ $section_id ]['elements'][ $element_id ]['type'] )
		) {
			return false;
		}

		// variations logic.
		if ( 'variations' === $sections[ $section_id ]['elements'][ $element_id ]['type'] ) {
			return $this->tm_variation_check_match( $form_prefix, $value, $operator );
		}

		if ( ! isset( $sections[ $section_id ]['elements'][ $element_id ]['name_inc'] ) ) {
			return false;
		}

		$element_array    = $sections[ $section_id ]['elements'][ $element_id ];
		$element_uniqueid = $element_array['uniqid'];

		if ( isset( $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) ) {
			if ( ! $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) {
				return false;
			}
		} elseif ( in_array( $element_uniqueid, $this->current_element_to_check[ $id ], true ) ) { // phpcs:ignore
			// Getting here means that two elements depend on each other
			// This is a logical error when creating the conditional logic in the builder.
		} elseif ( ! $this->is_visible_do( $id, $element_array, $sections[ $section_id ], $sections, $form_prefix ) ) {
			return false;
		}

		$element_to_check = $element_array['name_inc'];

		$element_type = $element_array['type'];
		$posted_value = null;

		if ( 'product' === $element_type ) {
			$element_type = 'select';
		}

		switch ( $element_type ) {
			case 'radio':
				$radio_checked_length = 0;
				$element_to_check     = array_unique( $element_to_check );

				// Element array contains the form_prefix so we don't append it again.
				$element_to_check = $element_to_check[0];

				if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
					if ( ! empty( $element_array['connector'] ) ) {
						if ( in_array( $posted_value, $element_array['options'], true ) ) {
							++$radio_checked_length;
						}
					} else {
						++$radio_checked_length;
					}
					$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
				}
				if ( 'is' === $operator || 'isnot' === $operator ) {
					if ( 0 === (int) $radio_checked_length ) {
						return false;
					}
				} elseif ( 'isnotempty' === $operator ) {
					return $radio_checked_length > 0;
				} elseif ( 'isempty' === $operator ) {
					return 0 === (int) $radio_checked_length;
				}
				break;
			case 'checkbox':
				$checkbox_checked_length = 0;
				$ret                     = false;
				$element_to_check        = array_unique( $element_to_check );
				foreach ( $element_to_check as $key => $name_value ) {
					// Element array contains the form_prefix so we don't append it again.
					$element_to_check[ $key ] = $name_value;
					$posted_value             = null;
					if ( isset( $_REQUEST[ $element_to_check[ $key ] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						++$checkbox_checked_length;
						$posted_value = wp_unslash( $_REQUEST[ $element_to_check[ $key ] ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );

						if ( $this->tm_check_match( $posted_value, $value, $operator ) ) {
							$ret = true;
						} elseif ( 'isnot' === $operator ) {
							$ret = false;
							break;
						}
					}
				}
				if ( 'is' === $operator || 'isnot' === $operator ) {
					if ( 0 === (int) $checkbox_checked_length ) {
						return false;
					}

					return $ret;
				} elseif ( 'isnotempty' === $operator ) {
					return $checkbox_checked_length > 0;
				} elseif ( 'isempty' === $operator ) {
					return 0 === (int) $checkbox_checked_length;
				}
				break;
			case 'selectmultiple':
				// Element array contains the form_prefix so we don't append it again.
				if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$posted_value = map_deep( stripslashes_deep( $_REQUEST[ $element_to_check ] ), 'sanitize_text_field' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$val          = [];
					if ( is_array( $posted_value ) ) {
						foreach ( $posted_value as $copy ) {
							foreach ( $copy as $i => $option ) {
								$option    = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $option );
								$option    = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $option, '_' );
								$val[ $i ] = $option;
							}
						}
					}
					$posted_value = $val;
				}
				break;
			case 'select':
			case 'textarea':
			case 'textfield':
			case 'color':
			case 'range':
				// Element array contains the form_prefix so we don't append it again.
				if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					if ( 'select' === $element_type ) {
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
					}
				}
				break;
		}

		return $this->tm_check_match( $posted_value, $value, $operator );
	}

	/**
	 * Conditional logic (checks section conditions)
	 *
	 * @param string             $element_id The element id.
	 * @param string             $operator The logic operator.
	 * @param object|false       $rule The rule object.
	 * @param array<mixed>|false $sections The sections array.
	 * @param string             $form_prefix The form prefix.
	 * @return boolean
	 * @since 1.0
	 */
	public function tm_check_section_match( $element_id, $operator, $rule = false, $sections = false, $form_prefix = '' ) {
		$array_prefix = $form_prefix;
		if ( '' === $form_prefix ) {
			$array_prefix = '_';
		}

		if ( isset( $this->visible_elements[ $array_prefix ] ) && isset( $this->visible_elements[ $array_prefix ][ $element_id ] ) ) {

			if ( false === $this->visible_elements[ $array_prefix ][ $element_id ] ) {
				if ( 'isnotempty' === $operator ) {
					return false;
				} elseif ( 'isempty' === $operator ) {
					return true;
				}
			}
		}

		$all_checked = true;
		$section_id  = $element_id;
		if ( isset( $sections[ $section_id ] ) && isset( $sections[ $section_id ]['elements'] ) ) {
			foreach ( $sections[ $section_id ]['elements'] as $id => $element ) {
				if ( $this->is_visible_do( $id, $element, $sections[ $section_id ], $sections, $form_prefix ) ) {
					if ( ! isset( $sections[ $section_id ]['elements'][ $id ]['name_inc'] ) ) {
						continue;
					}
					$element_to_check = $sections[ $section_id ]['elements'][ $id ]['name_inc'];
					$element_type     = $sections[ $section_id ]['elements'][ $id ]['type'];

					/**
					 * The posted value
					 *
					 * @var mixed $posted_value
					 */
					$posted_value = null;

					if ( 'product' === $element_type ) {
						$element_type = 'select';
					}
					switch ( $element_type ) {
						case 'radio':
							$radio_checked_length = 0;
							$element_to_check     = array_unique( $element_to_check );

							// Element array contains the form_prefix so we don't append it again.
							$element_to_check = $element_to_check[0];

							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$_posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								$_posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );

								$element_array = [];
								if ( isset( $sections[ $section_id ]['elements'][ $element_id ] ) ) {
									$element_array = $sections[ $section_id ]['elements'][ $element_id ];
								}
								if ( ! empty( $element_array['connector'] ) ) {
									if ( in_array( $posted_value, $element_array['options'], true ) ) {
										++$radio_checked_length;
									}
								} else {
									++$radio_checked_length;
								}
							}
							if ( 'isnotempty' === $operator ) {
								$all_checked = $all_checked && $radio_checked_length > 0;
								if ( $radio_checked_length > 0 ) {
									$posted_value = $radio_checked_length;
								}
							} elseif ( 'isempty' === $operator ) {
								$all_checked = $all_checked && 0 === (int) $radio_checked_length;
							}
							break;
						case 'checkbox':
							$checkbox_checked_length = 0;

							$element_to_check = array_unique( $element_to_check );
							foreach ( $element_to_check as $key => $name_value ) {
								$element_to_check[ $key ] = $name_value . $form_prefix;
								if ( isset( $_REQUEST[ $element_to_check[ $key ] ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									++$checkbox_checked_length;
								}
							}
							if ( 'isnotempty' === $operator ) {
								$all_checked = $all_checked && $checkbox_checked_length > 0;
								if ( $checkbox_checked_length > 0 ) {
									$posted_value = $checkbox_checked_length;
								}
							} elseif ( 'isempty' === $operator ) {
								$all_checked = $all_checked && 0 === (int) $checkbox_checked_length;
							}
							break;

						case 'selectmultiple':
							$element_to_check .= $form_prefix;
							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$posted_value = map_deep( stripslashes_deep( $_REQUEST[ $element_to_check ] ), 'sanitize_text_field' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$val          = [];
								if ( is_array( $posted_value ) ) {
									foreach ( $posted_value as $copy ) {
										foreach ( $copy as $i => $option ) {
											$option    = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $option );
											$option    = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $option, '_' );
											$val[ $i ] = $option;
										}
									}
								}
								$posted_value = $val;
							}
							break;
						default:
							$element_to_check .= $form_prefix;
							if ( isset( $_REQUEST[ $element_to_check ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$posted_value = wp_unslash( $_REQUEST[ $element_to_check ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
								if ( 'select' === $element_type ) {
									$posted_value = THEMECOMPLETE_EPO_HELPER()->encode_uri_component( $posted_value );
									$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, '_' );
								}
							}
							break;
					}
					if ( is_array( $posted_value ) ) {
						$all_checked = $all_checked && THEMECOMPLETE_EPO_HELPER()->array_some(
							$posted_value,
							function ( $item ) use ( $operator ) {
								return $this->tm_check_match( $item, '', $operator );
							}
						);
					} else {
						$all_checked = $all_checked && $this->tm_check_match( $posted_value, '', $operator );
					}
				}
			}
		}

		return $all_checked;
	}

	/**
	 * Conditional logic (checks variation conditions)
	 *
	 * @param string $form_prefix The form prefix.
	 * @param string $value The value to check against.
	 * @param string $operator The logic operator.
	 * @return boolean
	 * @since 1.0
	 */
	public function tm_variation_check_match( $form_prefix, $value, $operator ) {
		$posted_value = $this->get_posted_variation_id( $form_prefix );
		return $this->tm_check_match( $posted_value, $value, $operator, true );
	}

	/**
	 * Conditional logic (checks conditions)
	 *
	 * @param mixed   $posted_value The posted value.
	 * @param string  $value The value to check against.
	 * @param string  $operator The logic operator.
	 * @param boolean $include_zero If zero value counts as empty.
	 * @return boolean
	 * @since 1.0
	 */
	public function tm_check_match( $posted_value, $value, $operator, $include_zero = false ) {
		$posted_value = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $posted_value ) ) );
		$value        = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $value ) ) );
		switch ( $operator ) {
			case 'is':
				return ( null !== $posted_value && $value === $posted_value );
			case 'isnot':
				return ( null !== $posted_value && $value !== $posted_value );
			case 'isempty':
				if ( $include_zero ) {
					return ( ! ( ( null !== $posted_value && '' !== $posted_value && '0' !== $posted_value && 0 !== $posted_value ) ) );
				}
				return ( ! ( ( null !== $posted_value && '' !== $posted_value ) ) );
			case 'isnotempty':
				if ( $include_zero ) {
					return ( ( null !== $posted_value && '' !== $posted_value && '0' !== $posted_value && 0 !== $posted_value ) );
				}

				return ( ( null !== $posted_value && '' !== $posted_value ) );
			case 'startswith':
				return THEMECOMPLETE_EPO_HELPER()->str_startswith( $posted_value, $value );
			case 'endswith':
				return THEMECOMPLETE_EPO_HELPER()->str_endsswith( $posted_value, $value );
			case 'greaterthan':
				return floatval( $posted_value ) > floatval( $value );
			case 'lessthan':
				return floatval( $posted_value ) < floatval( $value );
			case 'greaterthanequal':
				return floatval( $posted_value ) >= floatval( $value );
			case 'lessthanequal':
				return floatval( $posted_value ) <= floatval( $value );
		}

		return false;
	}

	/**
	 * Get posted variations id
	 *
	 * @param string $form_prefix The form prefix.
	 *
	 * @return mixed
	 */
	public function get_posted_variation_id( $form_prefix = '' ) {
		$variation_id = null;
		if ( isset( $_REQUEST[ 'variation_id' . $form_prefix ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$variation_id = wp_unslash( $_REQUEST[ 'variation_id' . $form_prefix ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		return $variation_id;
	}
}
