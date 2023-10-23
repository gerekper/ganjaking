<?php
/**
 * Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Functions
 * @version 4.0.0.
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yith_wcan_dropdown_attributes' ) ) {
	/**
	 * Return a dropdown with Woocommerce attributes
	 *
	 * @param string $selected Selected value.
	 * @param bool   $echo     Whether to print or return template.
	 *
	 * @return void|string Html template of the dropdown, when $echo is false
	 */
	function yith_wcan_dropdown_attributes( $selected, $echo = true ) {
		$_woocommerce = function_exists( 'wc' ) ? wc() : null;
		$options      = '';
		$attributes   = array();

		if ( ! empty( $_woocommerce ) ) {

			if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
				$attribute_taxonomies = wc_get_attribute_taxonomies();
			} else {
				$attribute_taxonomies = $_woocommerce->get_attribute_taxonomies();
			}

			if ( empty( $attribute_taxonomies ) ) {
				return '';
			}

			foreach ( $attribute_taxonomies as $attribute ) {

				/* FIX TO WOOCOMMERCE 2.1 */
				if ( function_exists( 'wc_attribute_taxonomy_name' ) ) {
					$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );
				} else {
					$taxonomy = $_woocommerce->attribute_taxonomy_name( $attribute->attribute_name );
				}

				if ( taxonomy_exists( $taxonomy ) ) {
					$attributes[] = $attribute->attribute_name;
				}
			}

			foreach ( $attributes as $attribute ) {
				$options .= "<option name='{$attribute}' " . selected( $attribute, $selected, false ) . ">{$attribute}</option>";
			}
		}

		if ( $echo ) {
			echo $options; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			return $options;
		}
	}
}

if ( ! function_exists( 'yith_wcan_attributes_table' ) ) {
	/**
	 * Print the widgets options already filled
	 *
	 * @param string $type      One of the following values: list|colors|label.
	 * @param string $attribute Terms taxonomy.
	 * @param string $id        Id to use in <input /> tags.
	 * @param string $name      Name to use in <input /> tags.
	 * @param array  $values    Array of values (could be empty if this is an ajax call).
	 * @param bool   $echo      Whether to print or return template.
	 *
	 * @return void|string Html template of the dropdown, when $echo is false
	 */
	function yith_wcan_attributes_table( $type, $attribute, $id, $name, $values = array(), $echo = true ) {
		$return = '';

		if ( empty( $attribute ) ) {
			return $return;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'pa_' . $attribute,
				'hide_empty' => '0',
			)
		);

		if ( 'list' === $type ) {
			$return = '<input type="hidden" name="' . $name . '[colors]" value="" /><input type="hidden" name="' . $name . '[labels]" value="" />';
		} elseif ( 'color' === $type ) {
			if ( ! empty( $terms ) ) {
				$return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>', __( 'Term', 'yith-woocommerce-ajax-navigation' ), __( 'Color', 'yith-woocommerce-ajax-navigation' ) );

				foreach ( $terms as $term ) {
					if ( $term instanceof WP_Term ) {
						$return .= "<tr><td><label for='{$id}{$term->term_id}'>{$term->name}</label></td><td><input type='text' id='{$id}{$term->term_id}' name='{$name}[colors][{$term->term_id}]' value='" . ( isset( $values[ $term->term_id ] ) ? $values[ $term->term_id ] : '' ) . "' size='3' class='yith-colorpicker' /></td></tr>";
					}
				}

				$return .= '</table>';
			}

			$return .= '<input type="hidden" name="' . $name . '[labels]" value="" />';
		} elseif ( 'multicolor' === $type ) {
			if ( ! empty( $terms ) ) {
				$return = sprintf( '<table class="yith-wcan-multicolor"><tr><th>%s</th><th>%s</th><th>%s</th></tr>', __( 'Term', 'yith-woocommerce-ajax-navigation' ), _x( 'Color 1', 'For multicolor: I.E. white and red T-Shirt', 'yith-woocommerce-ajax-navigation' ), _x( 'Color 2', 'For multicolor: I.E. white and red T-Shirt', 'yith-woocommerce-ajax-navigation' ) );

				foreach ( $terms as $term ) {

					$return .= '<tr>';

					$return .= "<td><label for='{$id}{$term->term_id}'>{$term->name}</label></td>";

					$return .= "<td><input type='text' id='{$id}{$term->term_id}_1' name='{$name}[multicolor][{$term->term_id}][]' value='" . ( isset( $values[ $term->term_id ][0] ) ? $values[ $term->term_id ][0] : '' ) . "' size='3' class='yith-colorpicker multicolor' /></td>";
					$return .= "<td><input type='text' id='{$id}{$term->term_id}_2' name='{$name}[multicolor][{$term->term_id}][]' value='" . ( isset( $values[ $term->term_id ][1] ) ? $values[ $term->term_id ][1] : '' ) . "' size='3' class='yith-colorpicker multicolor' /></td>";

					$return .= '</tr>';
				}

				$return .= '</table>';
			}

			$return .= '<input type="hidden" name="' . $name . '[labels]" value="" />';
		} elseif ( 'label' === $type ) {
			if ( ! empty( $terms ) ) {
				$return = sprintf( '<table><tr><th>%s</th><th>%s</th></tr>', __( 'Term', 'yith-woocommerce-ajax-navigation' ), __( 'Labels', 'yith-woocommerce-ajax-navigation' ) );

				foreach ( $terms as $term ) {
					if ( $term instanceof WP_Term ) {
						$return .= "<tr><td><label for='{$id}{$term->term_id}'>{$term->name}</label></td><td><input type='text' id='{$id}{$term->term_id}' name='{$name}[labels][{$term->term_id}]' value='" . ( isset( $values[ $term->term_id ] ) ? $values[ $term->term_id ] : '' ) . "' size='3' /></td></tr>";
					}
				}

				$return .= '</table>';
			}

			$return .= '<input type="hidden" name="' . $name . '[colors]" value="" />';
		}

		if ( $echo ) {
			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		return $return;
	}
}

if ( ! function_exists( 'yith_wcan_can_be_displayed' ) ) {
	/**
	 * Can the widget be displayed?
	 *
	 * @return bool
	 */
	function yith_wcan_can_be_displayed() {
		$return = false;

		if (
			(
				is_active_widget( false, false, 'yith-woo-ajax-navigation', true ) ||
				is_active_widget( false, false, 'yith-woo-ajax-navigation-sort-by', true ) ||
				is_active_widget( false, false, 'yith-woo-ajax-navigation-stock-on-sale', true ) ||
				is_active_widget( false, false, 'yith-woo-ajax-navigation-list-price-filter', true )
			) && ( is_shop() || defined( 'SHOP_IS_ON_FRONT' ) || is_product_taxonomy() || is_product_category() )
		) {
			$return = true;
		}

		return apply_filters( 'yith_wcan_can_be_displayed', $return );
	}
}

if ( ! function_exists( 'yit_reorder_terms_by_parent' ) ) {
	/**
	 * Sort the array of terms associating the child to the parent terms
	 *
	 * @param WP_Term[] $terms    Array of terms to sort.
	 * @param string    $taxonomy Taxonomy for the terms.
	 *
	 * @return WP_Term[]
	 * @since 1.3.1
	 */
	function yit_reorder_terms_by_parent( $terms, $taxonomy ) {

		/* Extract Child Terms */
		$child_terms  = array();
		$terms_count  = 0;
		$parent_terms = array();

		foreach ( $terms as $array_key => $term ) {

			if ( $term->parent ) {

				$term_parent = $term->parent;
				while ( true ) {
					$temp_parent_term = get_term_by( 'id', $term_parent, $taxonomy );
					if ( $temp_parent_term->parent ) {
						$term_parent = $temp_parent_term->parent;
					} else {
						break;
					}
				}

				if ( isset( $child_terms[ $term_parent ] ) && ! is_null( $child_terms[ $term_parent ] ) ) {
					$child_terms[ $term_parent ] = array_merge( $child_terms[ $term_parent ], array( $term ) );
				} else {
					$child_terms[ $term_parent ] = array( $term );
				}
			} else {
				$parent_terms[ $terms_count ] = $term;
			}
			$terms_count ++;
		}

		/* Reorder Terms */
		$terms_count = 0;
		$terms       = array();

		foreach ( $parent_terms as $term ) {

			$terms[ $terms_count ] = $term;

			/* The term as child */
			if ( array_key_exists( $term->term_id, $child_terms ) ) {

				if ( 'product' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) && ! is_wp_error( $child_terms[ $term->term_id ] ) ) {
					usort( $child_terms[ $term->term_id ], 'yit_terms_sort' );
				} elseif ( 'alphabetical' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
					usort( $child_terms[ $term->term_id ], 'yit_alphabetical_terms_sort' );
				}

				foreach ( $child_terms[ $term->term_id ] as $child_term ) {
					$terms_count ++;
					$terms[ $terms_count ] = $child_term;
				}
			}
			$terms_count ++;
		}

		if ( 'product' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) && ! is_wp_error( $parent_terms ) ) {
			usort( $terms, 'yit_terms_sort' );
		} elseif ( 'alphabetical' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) ) {
			usort( $terms, 'yit_alphabetical_terms_sort' );
		}

		return $terms;
	}
}

if ( ! function_exists( 'yit_get_terms' ) ) {
	/**
	 * Get the array of objects terms
	 *
	 * @param string     $case     Type of term to display (all/hierarchical/parent).
	 * @param string     $taxonomy Taxonomy for the terms.
	 * @param array|bool $instance Widget instance; false to ignore.
	 *
	 * @return WP_Term[]
	 *
	 * @since  1.3.1
	 */
	function yit_get_terms( $case, $taxonomy, $instance = false ) {

		$exclude   = apply_filters( 'yith_wcan_exclude_terms', array(), $instance );
		$include   = apply_filters( 'yith_wcan_include_terms', array(), $instance );
		$reordered = false;

		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'exclude'    => $exclude,
		);

		$args = apply_filters( 'yit_get_terms_args', $args, $instance );

		switch ( $case ) {

			case 'all':
				$terms = yith_wcan_wp_get_terms( $args );
				break;

			case 'hierarchical':
				$terms = yith_wcan_wp_get_terms( $args );
				if ( ! in_array( $instance['type'], apply_filters( 'yith_wcan_display_type_list', array( 'list' ) ), true ) ) {
					$terms     = yit_reorder_terms_by_parent( $terms, $taxonomy );
					$reordered = true;
				}
				break;

			case 'parent':
				$args['parent'] = false;
				$terms          = yith_wcan_wp_get_terms( $args );
				break;

			default:
				$args['include'] = $include;

				if ( 'parent' === $instance['display'] ) {
					$args['parent'] = false;
				}

				$terms = yith_wcan_wp_get_terms( $args );

				if ( 'hierarchical' === $instance['display'] ) {
					if ( ! in_array( $instance['type'], apply_filters( 'yith_wcan_display_type_list', array( 'list' ) ), true ) ) {
						$terms     = yit_reorder_terms_by_parent( $terms, $taxonomy );
						$reordered = true;
					}
				}
				break;
		}

		if ( ! $reordered ) {
			$terms     = yit_reorder_terms_by_parent( $terms, $taxonomy );
			$reordered = true;
		}

		if ( apply_filters( 'yith_wcan_skip_shop_term_order', true, $taxonomy, $instance ) && 'product' === yith_wcan_get_option( 'yith_wcan_ajax_shop_terms_order', 'alphabetical' ) && 'hierarchical' !== $instance['display'] && ! is_wp_error( $terms ) && ! $reordered ) {
			usort( $terms, 'yit_terms_sort' );
		}

		return apply_filters( 'yith_wcan_get_terms_list', $terms, $taxonomy, $instance );
	}
}

if ( ! function_exists( 'yit_term_is_child' ) ) {
	/**
	 * Return true if the term is a child, false otherwise
	 *
	 * @param WP_Term $term The term object.
	 *
	 * @return bool
	 *
	 * @since 1.3.1
	 */
	function yit_term_is_child( $term ) {
		return isset( $term->parent ) && 0 !== $term->parent;
	}
}

if ( ! function_exists( 'yit_term_is_parent' ) ) {
	/**
	 * Return true if the term is a parent, false otherwise
	 *
	 * @param WP_Term $term The term object.
	 *
	 * @return bool
	 *
	 * @since 1.3.1
	 */
	function yit_term_is_parent( $term ) {
		return ( isset( $term->parent ) && 0 === $term->parent );
	}
}

if ( ! function_exists( 'yit_term_has_child' ) ) {
	/**
	 * Return true if the term has a child, false otherwise
	 *
	 * @param WP_Term $term     The term object.
	 * @param string  $taxonomy the taxonomy to search.
	 *
	 * @return bool
	 *
	 * @since 1.3.1
	 */
	function yit_term_has_child( $term, $taxonomy ) {
		$count       = 0;
		$child_terms = yith_wcan_wp_get_terms(
			array(
				'taxonomy' => $taxonomy,
				'child_of' => $term->term_id,
			)
		);

		if ( ! is_wp_error( $child_terms ) ) {

			if ( apply_filters( 'yith_wcan_skip_check_on_product_in_term', false, $child_terms ) ) {
				return true;
			}

			foreach ( $child_terms as $child_term ) {
				$_products_in_term = get_objects_in_term( $child_term->term_id, $taxonomy );
				$count            += count( array_intersect( $_products_in_term, YITH_WCAN()->frontend->layered_nav_product_ids ) );
			}
		}

		return empty( $count ) ? false : true;
	}
}

if ( ! function_exists( 'yith_wcan_get_option' ) ) {
	/**
	 * Retrieve the plugin option
	 *
	 * @param mixed|bool $option_name The option name. If false return alla options array.
	 * @param mixed|bool $default     Default option value. Default to false.
	 *
	 * @return mixed|array|string The option(s)
	 *
	 * @since    1.3.1
	 */
	function yith_wcan_get_option( $option_name = false, $default = false ) {
		// new option style.
		$option = get_option( $option_name );

		if ( $option ) {
			return $option;
		}

		// backward compatibility for older options.
		$options = get_option( 'yit_wcan_options' );

		if ( ! $option_name ) {
			return $options;
		}

		return isset( $options[ $option_name ] ) ? $options[ $option_name ] : $default;
	}
}

if ( ! function_exists( 'yit_get_filter_args' ) ) {
	/**
	 * Retrieve the filter query args option
	 *
	 * @param array $args Array of arguments.
	 * @return array The option(s)
	 *
	 * @since    1.4
	 */
	function yit_get_filter_args( $args = array() ) {
		$default_args = array(
			'check_price_filter' => true,
			'queried_object'     => null,
		);

		/**
		 * Extracted variables:
		 *
		 * @var $check_price_filter bool
		 * @var $queried_object     object
		 */
		$args = wp_parse_args( $args, $default_args );

		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract

		$filter_value = array();
		$regexs       = array(
			'/^filter_[a-zA-Z0-9]/',
			'/^query_type_[a-zA-Z0-9]/',
			'/product_tag/',
			'/product_cat/',
			'/source_id/',
			'/source_tax/',
			'/s/',
		);

		/* Support to YITH WooCommerce Brands */
		if ( yith_wcan_brands_enabled() ) {
			$brands_taxonomy = YITH_WCBR::$brands_taxonomy;
			$regexs[]        = "/{$brands_taxonomy}/";
		}

		$query_vars = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! empty( $query_vars ) ) {
			foreach ( $regexs as $regex ) {
				foreach ( $query_vars as $query_var => $value ) {
					if ( preg_match( $regex, $query_var ) ) {
						$filter_value[ $query_var ] = $value;
					}
				}
			}
		}

		if ( ! empty( $check_price_filter ) ) {
			// WooCommerce Price Filter.
			if ( isset( $query_vars['min_price'] ) ) {
				$filter_value['min_price'] = (float) $query_vars['min_price'];
			}

			if ( isset( $query_vars['max_price'] ) ) {
				$filter_value['max_price'] = (float) $query_vars['max_price'];
			}
		}

		// WooCommerce In Stock/On Sale filters.
		if ( isset( $query_vars['instock_filter'] ) ) {
			$filter_value['instock_filter'] = (int) $query_vars['instock_filter'];
		}

		if ( isset( $query_vars['onsale_filter'] ) ) {
			$filter_value['onsale_filter'] = (int) $query_vars['onsale_filter'];
		}

		if ( isset( $query_vars['orderby'] ) ) {
			$filter_value['orderby'] = sanitize_text_field( wp_unslash( $query_vars['orderby'] ) );
		}

		if ( isset( $query_vars['product_tag'] ) ) {
			$filter_value['product_tag'] = urlencode( sanitize_text_field( wp_unslash( $query_vars['product_tag'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
		} elseif ( is_product_tag() && ! empty( $queried_object ) ) {
			$filter_value['product_tag'] = $queried_object->slug;
		}

		if ( isset( $query_vars['product_cat'] ) ) {
			$filter_value['product_cat'] = urlencode( sanitize_text_field( wp_unslash( $query_vars['product_cat'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
		} elseif ( is_product_category() && ! empty( $queried_object ) ) {
			$filter_value['product_cat'] = $queried_object->slug;
		}

		if ( isset( $query_vars['source_id'] ) && isset( $query_vars['source_tax'] ) ) {
			$filter_value['source_id']  = sanitize_text_field( wp_unslash( $query_vars['source_id'] ) );
			$filter_value['source_tax'] = sanitize_text_field( wp_unslash( $query_vars['source_tax'] ) );
		} elseif ( ! is_shop() && is_product_taxonomy() && ! empty( $queried_object ) && ! isset( $filter_value['source_id'] ) && ! isset( $filter_value['source_tax'] ) ) {
			$filter_value['source_id']  = $queried_object->term_id;
			$filter_value['source_tax'] = $queried_object->taxonomy;
		}

		return apply_filters( 'yit_get_filter_args', $filter_value );
	}
}

if ( ! function_exists( 'yit_check_active_price_filter' ) ) {
	/**
	 * Check if there is an active price filter
	 *
	 * @param float $min_price Min price to check.
	 * @param float $max_price Max price to check.
	 *
	 * @return bool True if the filter is active, false otherwise
	 *
	 * @since    1.4
	 */
	function yit_check_active_price_filter( $min_price, $max_price ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$query_min_price = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : false;
		$query_max_price = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : false;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return $query_min_price === (float) $min_price && $query_max_price === (float) $max_price;
	}
}

if ( ! function_exists( 'yit_remove_price_filter_query_args' ) ) {
	/**
	 * Remove min_price and max_price query args from filters array value
	 *
	 * @param array $filter_value Array of filters.
	 *
	 * @return array The array params
	 *
	 * @since    1.4
	 */
	function yit_remove_price_filter_query_args( $filter_value ) {
		foreach ( array( 'min_price', 'max_price' ) as $remove ) {
			unset( $filter_value[ $remove ] );
		}

		return $filter_value;
	}
}

if ( ! function_exists( 'yit_get_woocommerce_layered_nav_link' ) ) {
	/**
	 * Get current layered link
	 *
	 * @return string|bool The new link
	 *
	 * @since    1.4
	 */
	function yit_get_woocommerce_layered_nav_link() {
		$return = get_post_type_archive_link( 'product' );
		$return = apply_filters( 'yith_wcan_untrailingslashit', false ) && is_string( $return ) ? untrailingslashit( $return ) : $return;

		return apply_filters( 'yith_wcan_get_woocommerce_layered_nav_link', $return );
	}
}

if ( ! function_exists( 'yit_wcan_localize_terms' ) ) {
	/**
	 * Get current layered link
	 *
	 * @param int    $term_id  The term id.
	 * @param string $taxonomy The taxonomy name.
	 *
	 * @return string The new term_id
	 *
	 * @since    1.4
	 */
	function yit_wcan_localize_terms( $term_id, $taxonomy ) {
		/* === WPML Support === */
		global $sitepress;
		if ( ! empty( $sitepress ) && function_exists( 'wpml_object_id_filter' ) ) {
			$term_id = wpml_object_id_filter( $term_id, $taxonomy, true, $sitepress->get_default_language() );
		}

		return $term_id;
	}
}

if ( ! function_exists( 'yit_wcan_get_product_taxonomy' ) ) {
	/**
	 * Get the product taxonomy array
	 *
	 * @return array Product taxonomy array
	 *
	 * @since    2.2
	 */
	function yit_wcan_get_product_taxonomy() {
		global $_attributes_array;
		$product_taxonomies = ! empty( $_attributes_array ) ? $_attributes_array : get_object_taxonomies( 'product' );

		return array_merge( $product_taxonomies, apply_filters( 'yith_wcan_product_taxonomy_type', array() ) );
	}
}

if ( ! function_exists( 'yit_terms_sort' ) ) {
	/**
	 * Utility used to sort terms by count
	 *
	 * @param WP_Term $a Term a.
	 * @param WP_term $b Term b.
	 *
	 * @return int Result of the comparison (-1, 0, 1)
	 */
	function yit_terms_sort( $a, $b ) {
		$result = 0;
		if ( $a->count < $b->count ) {
			$result = 1;
		} elseif ( $a->count > $b->count ) {
			$result = - 1;
		}

		return $result;
	}
}

if ( ! function_exists( 'yit_alphabetical_terms_sort' ) ) {
	/**
	 * Utility used to sort terms alphabetically
	 *
	 * @param WP_Term $a Term a.
	 * @param WP_term $b Term b.
	 *
	 * @return int Result of the comparison (-1, 0, 1)
	 */
	function yit_alphabetical_terms_sort( $a, $b ) {
		return strnatcmp( strtolower( $a->name ), strtolower( $b->name ) );
	}
}

if ( ! function_exists( 'yit_get_brands_taxonomy' ) ) {
	/**
	 * Get the product brands taxonomy name
	 *
	 * @return string the product brands taxonomy name if YITH WooCommerce Brands addons is currently activated
	 *
	 * @since    2.7.6
	 */
	function yit_get_brands_taxonomy() {
		$taxonomy = '';

		if ( yith_wcan_brands_enabled() ) {
			// Support to YITH WooCommerce Brands Add-on.
			$taxonomy = YITH_WCBR::$brands_taxonomy;
		} elseif ( class_exists( 'MGWB' ) ) {
			// Support to Ultimate WooCommerce Brands PRO.
			$taxonomy = 'product_brand';
		}

		return $taxonomy;
	}
}

if ( ! function_exists( 'yit_reorder_hierachical_categories' ) ) {
	/**
	 * Enable multi level taxonomies management
	 *
	 * @param int    $parent_term_id Parent term used to retrieve children.
	 * @param string $taxonomy       Taxonomy for the term.
	 *
	 * @return array the full terms array
	 *
	 * @since    2.8.1
	 */
	function yit_reorder_hierachical_categories( $parent_term_id, $taxonomy = 'product_cat' ) {
		$exclude = apply_filters( 'yith_wcan_exclude_terms', array(), array() );
		$include = apply_filters( 'yith_wcan_include_terms', array(), array() );

		$childs = yith_wcan_wp_get_terms(
			array(
				'taxonomy'     => $taxonomy,
				'parent'       => $parent_term_id,
				'hierarchical' => true,
				'hide_empty'   => false,
				'include'      => $include,
				'exclude'      => $exclude,
			)
		);

		if ( ! empty( $childs ) ) {
			$temp = array();
			foreach ( $childs as $child ) {
				$temp[ $child->term_id ] = yit_reorder_hierachical_categories( $child->term_id, $taxonomy );
			}

			return $temp;
		} else {
			return array();
		}
	}
}

if ( ! function_exists( 'yith_remove_premium_query_arg' ) ) {
	/**
	 * Remove Premium query args
	 *
	 * @param string $link Link to update.
	 * @return bool|string Updated url.
	 *
	 * @since    2.8.1
	 */
	function yith_remove_premium_query_arg( $link ) {
		$reset           = array( 'orderby', 'onsale_filter', 'instock_filter', 'product_tag', 'product_cat' );
		$brands_taxonomy = yit_get_brands_taxonomy();
		if ( ! empty( $brands_taxonomy ) ) {
			$reset[] = $brands_taxonomy;
		}

		return remove_query_arg( $reset, $link );
	}
}

if ( ! function_exists( 'yit_is_filtered_uri' ) ) {
	/**
	 * Get is the current uri are filtered
	 *
	 * @return bool true if the url are filtered, false otherwise
	 *
	 * @since    2.8.6
	 */
	function yit_is_filtered_uri() {
		$_chosen_attributes = YITH_WCAN()->get_layered_nav_chosen_attributes();
		$brands             = yit_get_brands_taxonomy();
		$query_vars         = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// check if current page is filtered.
		$is_filtered_uri = isset( $query_vars['product_cat'] ) || count( $_chosen_attributes ) > 0 || isset( $query_vars['min_price'] ) || isset( $query_vars['max_price'] ) || isset( $query_vars['orderby'] ) || isset( $query_vars['instock_filter'] ) || isset( $query_vars['onsale_filter'] ) || isset( $query_vars['product_tag'] ) || isset( $query_vars[ $brands ] );

		return apply_filters( 'yit_wcan_is_filtered_uri', $is_filtered_uri );
	}
}

if ( ! function_exists( 'yit_plus_character_hack' ) ) {
	/**
	 * Hack for plus character
	 *
	 * @param string $link Url where to apply fix.
	 * @return string the filtered link
	 *
	 * @since    2.8.6
	 */
	function yit_plus_character_hack( $link ) {
		return str_replace( '+', '%2B', $link );
	}
}

if ( ! function_exists( 'yit_in_array_ignore_case' ) ) {
	/**
	 * Case insensitive version of in array function
	 *
	 * @param mixed $needle   Item to search in the array.
	 * @param array $haystack Array where to search.
	 *
	 * @return bool
	 *
	 * @since    2.8.6
	 */
	function yit_in_array_ignore_case( $needle, $haystack ) {
		return in_array( strtolower( $needle ), array_map( 'strtolower', $haystack ), true );
	}
}

if ( ! function_exists( 'yith_wcan_is_product_attribute' ) ) {
	/**
	 * Return true when on an attribute archive page
	 *
	 * @param string $attribute  Attribute to test.
	 *
	 * @return bool
	 */
	function yith_wcan_is_product_attribute( $attribute = '' ) {
		global $wp_query;

		if ( ! $wp_query ) {
			return false;
		}

		return preg_match( '/pa_' . $attribute . '.*/', get_query_var( 'taxonomy' ) );
	}
}

if ( ! function_exists( 'yith_wcan_wp_get_terms' ) ) {
	/**
	 * Wrapper for get_terms function, to support old WordPress Version
	 *
	 * @param array $args Array of arguments for get_terms function.
	 *
	 * @return bool
	 */
	function yith_wcan_wp_get_terms( $args ) {
		global $wp_version;

		$pre_terms = apply_filters( 'pre_yith_wcan_wp_get_terms', false, $args );

		if ( false !== $pre_terms ) {
			return $pre_terms;
		}

		if ( version_compare( $wp_version, '4.6', '<' ) ) {
			$terms = get_terms( $args['taxonomy'], $args );
		} else {
			$terms = get_terms( $args );
		}

		if ( ! is_array( $terms ) ) {
			$terms = array();
		}

		return $terms;
	}
}

if ( ! function_exists( 'yith_wcan_brands_enabled' ) ) {
	/**
	 * Check if brands add-on premium is enabled
	 *
	 * @return bool
	 */
	function yith_wcan_brands_enabled() {
		return apply_filters( 'yith_wcan_brands_enabled', defined( 'YITH_WCBR' ) && YITH_WCBR );
	}
}

if ( ! function_exists( 'yith_wcan_add_rel_nofollow_to_url' ) ) {
	/**
	 * Check if the user want to add the rel="nofollow" in filter uri
	 *
	 * @param bool $get_html_rel_attribute Whether to return attribute, or just a bool indicating whether attribute should be print.
	 * @param bool $echo                   When $get_html_rel_attribute is true, you can choose to echo output, instead of returning it.
	 *
	 * @return bool|string
	 */
	function yith_wcan_add_rel_nofollow_to_url( $get_html_rel_attribute = false, $echo = false ) {
		$enable_seo          = 'yes' === yith_wcan_get_option( 'yith_wcan_enable_seo' );
		$enable_rel_nofollow = 'yes' === yith_wcan_get_option( 'yith_wcan_seo_rel_nofollow', 'no' );
		$enabled             = $enable_seo && $enable_rel_nofollow;
		$return              = $enabled;

		if ( $get_html_rel_attribute ) {
			$return = $enabled ? 'rel="nofollow"' : '';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'yith_wcan_doing_filters' ) ) {
	/**
	 * Checks is current page was requested by filter plugin
	 *
	 * @return bool Whether current page is requested by filter plugin
	 */
	function yith_wcan_doing_filters() {
		if ( ! isset( $_SERVER['HTTP_X_YITH_WCAN'] ) ) {
			return false;
		}

		return ! ! sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_YITH_WCAN'] ) );
	}
}

if ( ! function_exists( 'yith_wcan_get_preset' ) ) {
	/**
	 * Wrapper for YITH_WCAN_Preset_Factory::get_preset( $preset ) static function
	 *
	 * @param string|int|YITH_WCAN_Preset $preset  Preset id or slug.
	 *
	 * @return YITH_WCAN_Preset|bool
	 * @since 4.0.0
	 */
	function yith_wcan_get_preset( $preset = array() ) {
		return YITH_WCAN_Preset_Factory::get_preset( $preset );
	}
}

if ( ! function_exists( 'yith_wcan_get_filter' ) ) {
	/**
	 * Wrapper for YITH_WCAN_Filter_Factory::get_filter( $filter ) static function
	 *
	 * @param array $filter Filter data.
	 *
	 * @return YITH_WCAN_Filter|bool
	 * @since 4.0.0
	 */
	function yith_wcan_get_filter( $filter = array() ) {
		return YITH_WCAN_Filter_Factory::get_filter( $filter );
	}
}

if ( ! function_exists( 'yith_wcan_get_template' ) ) {
	/**
	 * Wrapper for wc_get_template function
	 *
	 * @param string $template Template to include.
	 * @param array  $atts     Array of attributes for the template.
	 * @param bool   $echo     Whether to echo template or return it.
	 *
	 * @return string|void Template content.
	 *
	 * @since 4.0.0
	 */
	function yith_wcan_get_template( $template, $atts = array(), $echo = true ) {
		$default_path  = YITH_WCAN_DIR . 'templates/';
		$template_path = apply_filters( 'yith_wcan_template_path', WC()->template_path() . 'yith-wcan', $template, $atts, $echo );

		ob_start();
		wc_get_template( $template, $atts, $template_path, $default_path );
		$template = ob_get_clean();

		if ( $echo ) {
			echo $template; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		return $template;
	}
}

if ( ! function_exists( 'yith_wcan_separate_terms' ) ) {
	/**
	 * Transform query var containing term slugs into array of term slugs
	 *
	 * @param string $terms Terms string (separated by + or ,).
	 *
	 * @return array Array of terms.
	 */
	function yith_wcan_separate_terms( $terms ) {
		return array_map( 'sanitize_title', preg_split( '/,|\+|%2c|%2b/i', $terms ) );
	}
}

if ( ! function_exists( 'yith_wcan_esc_term_slug' ) ) {
	/**
	 * Escapes term slug before printing it (urldecode)
	 *
	 * @param string $term_slug Slug to escape.
	 *
	 * @return string Escaped slug.
	 */
	function yith_wcan_esc_term_slug( $term_slug ) {
		return rawurldecode( $term_slug );
	}
}

if ( ! function_exists( 'yith_wcan_hex2rgb' ) ) {
	/**
	 * Convert HEX color code to RGB color components
	 *
	 * @param string $hex Hex color code.
	 * @return array Array of RGB components
	 */
	function yith_wcan_hex2rgb( $hex ) {
		// check first if is already in rgb mode.
		if ( preg_match( '/rgb(a)?\((\d{1,3}),(\d{1,3}),(\d{1,3})(,([\d.]+))?\)/', $hex, $matches ) ) {
			return array(
				$matches[2],
				$matches[3],
				$matches[4],
			);
		}

		// otherwise try to convert hex.
		$color = str_replace( '#', '', $hex );
		if ( strlen( $color ) !== 6 ) {
			return array( 0, 0, 0 );
		}

		$rgb = array();

		for ( $x = 0; $x < 3; $x ++ ) {
			$rgb[ $x ] = hexdec( substr( $color, ( 2 * $x ), 2 ) );
		}

		return $rgb;
	}
}

if ( ! function_exists( 'yith_wcan_get_rating_html' ) ) {
	/**
	 * Prints template for rating review
	 *
	 * @param int $rating Rate for template printing.
	 * @return string Template for specified rate.
	 */
	function yith_wcan_get_rating_html( $rating ) {
		$template = '';

		$label = yith_wcan_get_rating_label( $rating );

		$template .= '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">';
		$template .= wc_get_star_rating_html( $rating );
		$template .= '</div>';
		$template .= esc_html( sprintf( '%d %s', $rating, _nx( 'star', 'stars', $rating, '[FRONTEND] Star rating template', 'yith-woocommerce-ajax-navigation' ) ) );

		return $template;
	}
}

if ( ! function_exists( 'yith_wcan_get_rating_label' ) ) {
	/**
	 * Prints label for rating review
	 *
	 * @param int $rating Rate for template printing.
	 * @return string Label for specified rate.
	 */
	function yith_wcan_get_rating_label( $rating ) {
		// translators: 1. Rating.
		return apply_filters( 'yith_wcan_rating_label', sprintf( _x( 'Rated %s out of 5', '[FRONTEND] Star rating label', 'yith-woocommerce-ajax-navigation' ), $rating ), $rating );
	}
}

if ( ! function_exists( 'yith_wcan_get_sidebar_with_filters' ) ) {
	/**
	 * Returns an array of sidebars containing YITH WCAN widgets
	 *
	 * @return array Array of sidebars.
	 */
	function yith_wcan_get_sidebar_with_filters() {
		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( ! empty( $sidebars_widgets ) ) {
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				$filtered_widgets = array_filter( $widgets, 'yith_wcan_is_filter_widget' );

				if ( ! $filtered_widgets ) {
					unset( $sidebars_widgets[ $sidebar ] );
				}
			}
		}

		return $sidebars_widgets;
	}
}

if ( ! function_exists( 'yith_wcan_is_filter_widget' ) ) {
	/**
	 * Returns true iw widget name matches structure expected for a filter widget
	 *
	 * @param string $name Widget name.
	 * @return bool Whether name matches or not.
	 */
	function yith_wcan_is_filter_widget( $name ) {
		return preg_match( '/yith-woo-ajax(-reset)?-navigation/', $name );
	}
}

if ( ! function_exists( 'yith_wcan_deprecated_filters' ) ) {
	/**
	 * Legacy support for deprecated filters
	 *
	 * @param array $deprecated_filters_map Array that maps deprecated filters to error message to show.
	 * @return void
	 *
	 * @since 3.11.7
	 */
	function yith_wcan_deprecated_filter( $deprecated_filters_map ) {
		foreach ( $deprecated_filters_map as $deprecated_filter => $options ) {
			$new_filter = $options['use'];
			$params     = $options['params'];
			$since      = $options['since'];
			add_filter(
				$new_filter,
				function () use ( $deprecated_filter, $since, $new_filter ) {
					$args = func_get_args();
					$r    = $args[0];

					if ( has_filter( $deprecated_filter ) ) {
						error_log( sprintf( 'Deprecated filter: %s since %s. Use %s instead!', $deprecated_filter, $since, $new_filter ) ); //phpcs:ignore

						$r = call_user_func_array( 'apply_filters', array_merge( array( $deprecated_filter ), $args ) );
					}

					return $r;
				},
				10,
				$params
			);
		}
	}
}

if ( ! function_exists( 'yith_wcan_merge_in_array' ) ) {
	/**
	 * Merges an array of items into a specific position of an array
	 *
	 * @param array  $array    Origin array.
	 * @param array  $element  Elements to merge.
	 * @param string $pivot    Index to use as pivot.
	 * @param string $position Where elements should be merged (before or after the pivot).
	 *
	 * @return array Result of the merge
	 */
	function yith_wcan_merge_in_array( $array, $element, $pivot, $position = 'after' ) {
		// search for the pivot inside array.
		$pos = array_search( $pivot, array_keys( $array ), true );

		if ( false === $pos ) {
			return $array;
		}

		// separate array into chunks.
		$i      = 'after' === $position ? 1 : 0;
		$part_1 = array_slice( $array, 0, $pos + $i );
		$part_2 = array_slice( $array, $pos + $i );

		return array_merge( $part_1, $element, $part_2 );
	}
}
