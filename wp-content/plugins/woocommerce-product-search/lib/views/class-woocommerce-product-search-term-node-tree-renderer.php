<?php
/**
 * class-woocommerce-product-search-term-node-tree-renderer.php
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

class WooCommerce_Product_Search_Term_Node_Tree_Renderer {

	private $args = null;

	private $walker = null;

	private $taxonomy = null;

	private $auto_expand = true;

	private $auto_retract = true;

	private $current_term_ids = null;

	private $current_term_ancestor_ids = null;

	private $expandable_from_depth = 0;

	private $expander = true;

	private $hierarchical = true;

	private $render_root_container = true;

	private $root_class = '';

	private $root_id = '';

	private $show_names = true;

	private $show_thumbnails = false;

	private $show_count = false;

	public function __construct( $args = array() ) {

		$this->args = $args;

		if ( isset( $args['auto_expand'] ) ) {
			$this->auto_expand = $args['auto_expand'];
		}
		if ( isset( $args['auto_retract'] ) ) {
			$this->auto_retract = $args['auto_retract'];
		}
		if ( isset( $args['current_term_ids'] ) ) {
			$this->current_term_ids = $args['current_term_ids'];
		}
		if ( isset( $args['current_term_ancestor_ids'] ) ) {
			$this->current_term_ancestor_ids = $args['current_term_ancestor_ids'];
		}
		if ( isset( $args['expander'] ) ) {
			$this->expander = $args['expander'];
		}
		if ( isset( $args['expandable_from_depth'] ) ) {

			$this->expandable_from_depth = $args['expandable_from_depth'] + 1;
		}
		if ( isset( $args['hierarchical'] ) ) {
			$this->hierarchical = $args['hierarchical'];
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
		if ( isset( $args['show_names'] ) ) {
			$this->show_names = $args['show_names'];
		}
		if ( isset( $args['show_thumbnails'] ) ) {
			$this->show_thumbnails = $args['show_thumbnails'];
		}
		if ( isset( $args['show_count'] ) ) {
			$this->show_count = $args['show_count'];
		}

	}

	public function render( $node ) {
		$this->taxonomy = $node->get_taxonomy();
		require_once WOO_PS_VIEWS_LIB . '/class-woocommerce-product-search-term-walker.php';
		$this->walker = new WooCommerce_Product_Search_Term_Walker( $node->get_taxonomy() );
		$this->walker->current_terms = $this->current_term_ids;
		$this->walker->current_term_ancestors = $this->current_term_ancestor_ids;
		$this->walker->show_names = $this->show_names;
		$this->walker->show_thumbnails = $this->show_thumbnails;
		$output = $this->render_level( $node );
		return $output;
	}

	public function get_elements_displayed() {
		$n = 0;
		if ( $this->walker !== null ) {
			$n = $this->walker->get_elements_displayed();
		}
		return $n;
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

		$element_args = array(
			'current_terms' => $this->current_term_ids,
			'has_children'  => $node->has_children(),
			'hierarchical'  => $this->hierarchical,

			'show_count'    => $this->show_count,
			'expandable'    => $depth >= $this->expandable_from_depth, 
			'expander'      => $this->expander,
			'auto_expand'   => $this->auto_expand,
			'auto_retract'  => $this->auto_retract
		);

		if ( $depth === 0 ) {
			if ( $this->render_root_container ) {
				$id_attribute = !empty( $this->root_id ) ? sprintf( 'id="%s"', esc_attr( $this->root_id ) ) : '';
				$class_attribute = !empty( $this->root_class ) ? sprintf( 'class="%s"', esc_attr( $this->root_class ) ) : '';
				$output .= $nl . $sp . sprintf( '<ul %s %s>', $id_attribute, $class_attribute ) . $nl; 
			}
		} else {
			if ( $term !== null ) {
				$this->walker->start_el( $output, $term, $depth, $element_args );
			} else {
				$output .= $sp . '<li>';
			}
		}

		if ( $node->has_children() ) {
			if ( $depth !== 0 ) {
				$output .= $nl . $sp . '<ul class="children">' . $nl;
			}
			foreach ( $node->get_children() as $child ) {
				$output .= $this->render_level( $child, $depth + 1 );
			}
			if ( $depth !== 0 ) {
				$output .= $nl . $sp . '</ul>' . $nl;
			}
		}

		if ( $depth === 0 ) {
			if ( $this->render_root_container ) {
				$output .= $nl . $sp . '</ul>' . $nl;
			}
		} else {
			if ( $term !== null ) {
				$this->walker->end_el( $output, $term, $depth, $element_args );
				$this->walker->increase_elements_displayed();
			} else {
				$output .= $sp . '</li>' . $nl;
			}
		}

		return $output;
	}

	public function render_test( $node, $depth = 0 ) {

		$sp = WPS_DEBUG ? str_repeat( "\t", $depth ) : '';
		$nl = WPS_DEBUG ? "\n" : '';

		$output = '';

		if ( $depth === 0 ) {
			$output .= $nl . $sp . '<ul>' . $nl;
		} else {
			$output .= $sp . '<li>';
		}

		if ( $node->get_term_id() !== null ) {
			$output .= ' [' . $node->get_term_id() . ']';
			$term = get_term( $node->get_term_id(), $node->get_taxonomy() );
			if ( $term instanceof WP_Term ) {
				$output .= ' ';
				$output .= esc_html( $term->name );
			}
		}

		if ( $node->has_children() ) {
			if ( $depth !== 0 ) {
				$output .= $nl . $sp . '<ul>' . $nl;
			}
			foreach ( $node->get_children() as $child ) {
				$output .= $this->render( $child, $depth + 1 );
			}
			if ( $depth !== 0 ) {
				$output .= $nl . $sp . '</ul>' . $nl;
			}
		}

		if ( $depth === 0 ) {
			$output .= $nl . $sp . '</ul>' . $nl;
		} else {
			$output .= $sp . '</li>' . $nl;
		}

		return $output;
	}
}
