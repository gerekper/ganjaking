<?php
/**
 * class-woocommerce-product-search-term-node-renderer.php
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
 * @since 2.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce_Product_Search_Term_Node_Renderer {

	public function render( $node, $depth = 0 ) {
		$output = '';
		if ( $node->get_term_id() !== null ) {
			if ( $node->has_children() ) {
				$output .= str_repeat( ' ', $depth - 1 );
				$output .= '+';
			} else {
				$output .= str_repeat( ' ', $depth );
			}

			$output .= ' [' . $node->get_term_id() . ']';
			$term = get_term( $node->get_term_id(), $node->get_taxonomy() );
			if ( $term instanceof WP_Term ) {
				$output .= ' ';
				$output .= esc_html( $term->name );
			}
			$output .= "\n";
		}
		foreach ( $node->get_children() as $child ) {
			$output .= $this->render( $child, $depth + 1 );
		}
		return $output;
	}
}
