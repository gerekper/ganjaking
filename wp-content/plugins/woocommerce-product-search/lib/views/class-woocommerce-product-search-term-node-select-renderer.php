<?php
/**
 * class-woocommerce-product-search-term-node-select-renderer.php
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
 * @since 2.4.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders product category filter as select.
 */
class WooCommerce_Product_Search_Term_Node_Select_Renderer {

	const PADDING_STEP = 8; 

	private $args = null;

	private $taxonomy = null;

	private $current_term_ids = null;

	private $current_term_ancestor_ids = null;

	private $elements_displayed = 0;

	private $hierarchical = true;

	private $multiple = false;

	private $none_selected = '';

	private $render_root_container = true;

	private $root_class = '';

	private $root_id = '';

	private $root_name = '';

	private $show_names = true;

	private $show_thumbnails = false;

	private $show_count = false;

	private $size = '';

	private $term_counts = null;

	public function __construct( $args = array() ) {

		$this->args = $args;

		if ( isset( $args['current_term_ids'] ) ) {
			$this->current_term_ids = $args['current_term_ids'];
		}
		if ( isset( $args['current_term_ancestor_ids'] ) ) {
			$this->current_term_ancestor_ids = $args['current_term_ancestor_ids'];
		}
		if ( isset( $args['none_selected'] ) ) {
			$this->none_selected = $args['none_selected'];
		}
		if ( isset( $args['hierarchical'] ) ) {
			$this->hierarchical = $args['hierarchical'];
		}
		if ( isset( $args['multiple'] ) ) {
			$this->multiple = $args['multiple'];
		}
		if ( isset( $args['render_root_container'] ) ) {
			$this->render_root_container = $args['render_root_container'];
		}
		if ( isset( $args['root_class'] ) ) {
			$this->root_class = $args['root_class'];
		}
		if ( isset( $args['root_id'] ) ) {
			$this->root_id = $args['root_id'];
		}
		if ( isset( $args['root_name'] ) ) {
			$this->root_name = $args['root_name'];
		}
		if ( isset( $args['show_names'] ) ) {
			$this->show_names = $args['show_names'];
		}
		if ( isset( $args['show_thumbnails'] ) ) {
			$this->show_thumbnails = $args['show_thumbnails'];
		}
		if ( isset( $args['show_count'] ) ) {
			$this->show_count = $args['show_count'];
		}
		if ( isset( $args['size'] ) ) {
			$this->size = $args['size'];
		}
	}

	public function render( $node ) {
		$this->elements_displayed = 0;
		$this->taxonomy = $node->get_taxonomy();
		if ( $this->show_count ) {
			$this->term_counts = WooCommerce_Product_Search_Service::get_term_counts( $this->taxonomy );
		}
		$output = $this->render_level( $node );
		return $output;
	}

	public function get_elements_displayed() {
		return $this->elements_displayed;
	}

	private function render_level( $node, $depth = 0 ) {

		$sp = WPS_DEBUG ? str_repeat( "\t", $depth ) : '';
		$nl = WPS_DEBUG ? "\n" : '';

		$output = '';

		$term = null;
		if ( $node->get_term_id() !== null ) {
			$_term = get_term( $node->get_term_id(), $node->get_taxonomy() );
			if ( $_term instanceof WP_Term ) {
				$term = $_term;
			}
		}

		if ( $depth === 0 ) {
			if ( $this->render_root_container ) {
				$id_attribute = !empty( $this->root_id ) ? sprintf( 'id="%s"', esc_attr( $this->root_id ) ) : '';
				$name_attribute = !empty( $this->root_name ) ? sprintf( 'name="%s"', esc_attr( $this->root_name ) ) : '';
				$class_attribute = !empty( $this->root_class ) ? sprintf( 'class="%s"', esc_attr( $this->root_class ) ) : '';

				$output .= $nl . $sp . sprintf(
					'<select %s %s %s data-taxonomy="%s" %s %s>',
					$name_attribute,
					$id_attribute,
					$class_attribute,
					esc_attr( $this->taxonomy ),
					$this->multiple ? ' multiple ' : '',
					empty( $this->size ) ? '' : ' size="' . intval( $this->size ) . '" '
				) . $nl;

				$output .= sprintf( '<option value="" %s>%s</option>', empty( $this->current_term_ids ) ? ' selected="selected" ' : '', esc_html( $this->none_selected ) ); 
			}
		} else {
			if ( $term !== null ) {
				$option_content  = '';
				$option_padding  = '';
				$option_label    = '';
				$padding_string  = '';
				$padding_content = '';
				if ( apply_filters( 'woocommerce_product_search_term_node_select_apply_padding' , true, $term, $depth ) ) {
					$padding_string  = apply_filters( 'woocommerce_product_search_term_node_select_padding_string', "&nbsp;", $term, $depth );
					$padding_content = str_repeat( $padding_string, $depth > 1 ? $depth - 1 : 0 );
					$option_padding  = apply_filters( 'woocommerce_product_search_term_node_select_padding', $padding_content, $term, $depth );
				}
				switch ( $this->taxonomy ) {
					case 'product_cat' :
						$option_label .= _x( $term->name, 'product category name', 'woocommerce-product-search' ); 
						break;
					default :
						if ( in_array( $this->taxonomy, wc_get_attribute_taxonomy_names() ) ) {

							$option_label .= _x( $term->name, 'product attribute name', 'woocommerce-product-search' ); 
						} else {

							$option_label .= _x( $term->name, 'product term name', 'woocommerce-product-search' ); 
						}
				}
				if ( $this->show_count ) {
					$object_count = $term->count; 
					if ( apply_filters( 'woocommerce_product_search_term_walker_apply_get_term_count', true, $term ) ) {
						if ( isset( $this->term_counts[$term->term_id] ) ) {
							$object_count = $this->term_counts[$term->term_id];
						}
					}
					$option_label .= apply_filters( 'woocommerce_product_search_term_node_select_render_count' , ' (' . intval( $object_count ) . ')', $term, $depth, $object_count );
				}
				$option_content = $option_padding . $option_label;
				$option_datas = '';
				if ( $this->show_thumbnails ) {
					$option_data = array(
						sprintf( 'data-depth="%s"', esc_attr( $depth ) ),
						sprintf( 'data-padding="%s"', esc_attr( $option_padding ) ),
						sprintf( 'data-label="%s"', esc_attr( $option_label ) ),
						sprintf( 'data-padding_step="%s"', esc_attr( intval( apply_filters( 'woocommerce_product_search_term_node_select_padding_step', self::PADDING_STEP, $term, $depth ) ) ) )
					);
					$thumbnail_datas = WooCommerce_Product_Search_Thumbnail::term_thumbnail( $term, array( 'return' => 'array' ) );
					if ( is_array( $thumbnail_datas ) && count( $thumbnail_datas ) > 0 ) {
						foreach ( $thumbnail_datas as $key => $value ) {
							switch ( $key ) {
								case 'html' :

									$option_data[] = sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
									break;
							}
						}
					}
					if ( count( $option_data ) > 0 ) {
						$option_datas .= ' ' . implode( ' ', $option_data ) . ' ';
					}
				}
				$output .= sprintf(
					'<option value="%d" %s %s>%s</option>',
					esc_attr( $term->term_id ),
					in_array( $term->term_id, $this->current_term_ids ) ? ' selected="selected" ' : '',
					$option_datas,
					esc_html( $option_content )
				);
				$this->elements_displayed++;
			}
		}

		if ( $node->has_children() ) {
			foreach ( $node->get_children() as $child ) {
				$output .= $this->render_level( $child, $depth + 1 );
			}
		}

		if ( $depth === 0 ) {
			if ( $this->render_root_container && !empty( $this->root_id ) ) {
				$output .= $nl . $sp . '</select>' . $nl;
				$output .= '<script type="text/javascript">';
				$output .= 'document.getElementById("' . esc_attr( $this->root_id ) . '").disabled = true;';
				$output .= '</script>';
			}
		}

		return $output;
	}

}
