<?php
/**
 * class-woocommerce-product-search-term-walker.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walker
 */
class WooCommerce_Product_Search_Term_Walker extends Walker {

	public $tree_type = 'product_cat';

	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug'
	);

	public $current_terms = array();

	public $current_term_ancestors = array();

	public $show_names = true;

	public $show_thumbnails = false;

	private $elements_displayed = 0;

	/**
	 * Constructor
	 *
	 * @param string $taxonomy
	 */
	public function __construct( $taxonomy = 'product_cat' ) {

		$this->tree_type = $taxonomy;
	}

	/**
	 * Starts the output for a level if the style is 'list'.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 *
	 * @param int $depth Depth of the item.
	 * @param array $args An array of additional arguments.
	 *
	 * @see Walker::start_lvl()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' === $args['style'] ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "$indent<ul class='children'>\n";
		}
		if ( $depth === 0 ) {
			$this->elements_displayed = 0;
		}
	}

	/**
	 * Ends the output for a level.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of the item.
	 * @param array $args An array of additional arguments.
	 * @see Walker::end_lvl()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		if ( 'list' === $args['style'] ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "$indent</ul>\n";
		}
	}

	/**
	 * Starts the element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $object The data object.
	 * @param int $depth Depth of the item.
	 * @param array $args An array of additional arguments.
	 * @param int $current_object_id ID of the current item.
	 *
	 * @see Walker::start_el()
	 */
	public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {


		switch ( $this->tree_type ) {
			case 'product_cat' :
				$anchor_content       = _x( $object->name, 'product category name', 'woocommerce-product-search' ); 
				$item_class           = 'cat-item';
				$current_class        = 'current-cat';
				$parent_class         = 'cat-parent';
				$current_parent_class = 'current-cat-parent';
				$current_ancestor_class = 'current-cat-ancestor';
				break;
			default :
				if ( in_array( $this->tree_type, wc_get_attribute_taxonomy_names() ) ) {

					$anchor_content       = _x( $object->name, 'product attribute name', 'woocommerce-product-search' ); 
					$item_class           = 'attribute-item ' . $this->tree_type . '-item';
					$current_class        = 'current-attribute current-' . $this->tree_type;
					$parent_class         = 'attribute-parent ' . $this->tree_type . '-parent';
					$current_parent_class = 'current-attribute-parent current-' . $this->tree_type . '-parent';
					$current_ancestor_class = 'current-attribute-ancestor current-' . $this->tree_type . '-ancestor';
				} else {

					$anchor_content       = _x( $object->name, 'product term name', 'woocommerce-product-search' ); 
					$item_class           = 'term-item ' . $this->tree_type . '-item';
					$current_class        = 'current-term current-' . $this->tree_type;
					$parent_class         = 'term-parent ' . $this->tree_type . '-parent';
					$current_parent_class = 'current-term-parent current-' . $this->tree_type . '-parent';
					$current_ancestor_class = 'current-term-ancestor current-' . $this->tree_type . '-ancestor';
				}
		}

		$class = sprintf(
			'%s %s-%s product-search-%s-filter-item product-search-attribute-filter-item',
			$item_class,
			$item_class,
			$object->term_id,
			$this->tree_type
		);

		if ( isset( $args['current_category'] ) ) {
			if ( $args['current_category'] == $object->term_id ) {
				$class .= ' ' . $current_class;
			}
		}

		$is_current_term = false;
		if ( !empty( $this->current_terms ) && is_array( $this->current_terms ) ) {
			if ( in_array( $object->term_id, $this->current_terms ) ) {
				$class .= ' ' . $current_class;
				$is_current_term = true;
			}
		}

		$is_current_term_ancestor = false;
		if ( !empty( $this->current_term_ancestors )  && is_array( $this->current_term_ancestors ) ) {
			if ( in_array( $object->term_id, $this->current_term_ancestors ) ) {
				$class .= ' ' . $current_ancestor_class;
				$is_current_term_ancestor = true;
			}
		}

		$is_expandable = false;
		if ( isset( $args['has_children'] ) && isset( $args['hierarchical'] ) ) {
			if ( $args['has_children'] && $args['hierarchical'] ) {
				$class .= ' ' . $parent_class;
				if ( !$is_current_term && !$is_current_term_ancestor ) {
					if ( isset( $args['expandable'] ) && $args['expandable'] ) {
						$class .= ' expandable';
						if ( isset( $args['auto_expand'] ) && $args['auto_expand'] ) {
							$class .= ' auto-expand';
						}
						if ( isset( $args['auto_retract'] ) && $args['auto_retract'] ) {
							$class .= ' auto-retract';
						}
						$is_expandable = true;
					}
				}
			}
		}

		if ( isset( $args['current_category_ancestors'] ) && isset( $args['current_category'] ) ) {
			if ( $args['current_category_ancestors'] && $args['current_category'] && in_array( $object->term_id, $args['current_category_ancestors'] ) ) {
				$class .= ' ' . $current_parent_class;
			}
		}

		$anchor_content =
			( $this->show_thumbnails ? WooCommerce_Product_Search_Thumbnail::term_thumbnail( $object ) : '' ) .
			( $this->show_names ? '<span class="term-name">' . esc_html( $anchor_content ) . '</span>' : '' );

		$output .= sprintf(
			'<li data-term="%s" data-taxonomy="%s" class="%s">',
			esc_attr( $object->term_id ),
			esc_attr( $this->tree_type ),
			esc_attr( $class )
		);
		$output .= sprintf(
			'<a href="%s">',
			esc_url( get_term_link( (int) $object->term_id, $this->tree_type ) )
		);
		$output .= apply_filters( 'woocommerce_product_search_term_walker_anchor_content', $anchor_content, $object, $depth, $args, $current_object_id );
		$output .= '</a>';


		if ( $args['show_count'] ) {
			$object_count = $object->count;
			if ( apply_filters( 'woocommerce_product_search_term_walker_apply_get_term_count', true, $object ) ) {
				$object_count = WooCommerce_Product_Search_Service::get_term_count( $object->term_id );
			}
			$output .= ' <span class="count">(' . $object_count . ')</span>'; 
		}

		$expander = isset( $args['expander'] ) ? $args['expander'] : false;
		if ( $expander && $is_expandable && strlen( $anchor_content ) > 0 ) {
			$expand = apply_filters( 'woocommerce_product_search_term_walker_expand', '&#xf067;', $object ); 
			$retract = apply_filters( 'woocommerce_product_search_term_walker_retract', '&#xf068;', $object ); 
			$output .=
				'<span class="term-expander">' .
				sprintf( '<span class="expand">%s</span>', esc_html( $expand ) ) .
				sprintf( '<span class="retract">%s</span>', esc_html( $retract ) ) .
				'</span>';
		}
	}

	/**
	 * Ends element output.
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $object The data object.
	 * @param int $depth Depth of the item.
	 * @param array $args An array of additional arguments.
	 *
	 * @see Walker::end_el()
	 */
	public function end_el( &$output, $object, $depth = 0, $args = array() ) {
		$output .= '</li>';
		$output .= "\n";
	}

	/**
	 * Display the element.
	 *
	 * @param object $element Data object.
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args An array of arguments.
	 * @param string $output Passed by reference. Used to append additional content.
	 *
	 * @see Walker::display_element()
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

		if ( ! ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) ) {
			parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
			$this->elements_displayed++;
		}
	}

	/**
	 * Returns the number of elements displayed.
	 *
	 * @return int
	 */
	public function get_elements_displayed() {
		return $this->elements_displayed;
	}

	/**
	 * Increase the elements displayed counter.
	 */
	public function increase_elements_displayed() {
		$this->elements_displayed++;
	}
}
