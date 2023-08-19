<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export\Exportable;
use ACP\Export\Model\StrippedValue;
use ACP\Settings;

class GutenbergBlocks extends AC\Column
	implements Exportable {

	public function __construct() {
		$this->set_type( 'column-post_gutenberg_blocks' );
		$this->set_label( __( 'Gutenberg Blocks', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$post = get_post( $id );

		if ( ! has_blocks( $post->post_content ) ) {
			return $this->get_empty_char();
		}

		switch ( $this->get_setting( 'gutenberg_display' )->get_value() ) {
			case 'structure':

				return $this->get_structure_value( parse_blocks( $post->post_content ) );
			case 'count':
			default:

				return $this->get_count_value( parse_blocks( $post->post_content ) );

		}
	}

	/**
	 * @param array $blocks
	 *
	 * @return string
	 */
	private function get_structure_value( $blocks ) {
		$setting_limit = $this->get_setting( 'number_of_items' );

		return ac_helper()->html->more( $this->get_block_structure( $blocks ), $setting_limit ? $setting_limit->get_value() : false, '<br>' );
	}

	private function get_block_structure( $blocks, $values = [], $prefix = '' ) {
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && $block['blockName'] ) {
				$values[] = sprintf( '%s[%s]', $prefix, $block['blockName'] );
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$values = $this->get_block_structure( $block['innerBlocks'], $values, $prefix . '&nbsp;&nbsp;&nbsp;' );
			}
		}

		return $values;
	}

	/**
	 * @param array $blocks
	 *
	 * @return string
	 */
	private function get_count_value( $blocks ) {
		$values = [];

		foreach ( $this->count_blocks( $blocks, [] ) as $name => $block_count ) {
			$values[] = sprintf( '%s <span class="ac-rounded">%s</span>', $name, $block_count );
		}

		return implode( '<br>', $values );
	}

	private function count_blocks( $blocks, $grouped_count = [] ) {
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}

			$name = $block['blockName'];

			if ( ! isset( $grouped_count[ $name ] ) ) {
				$grouped_count[ $name ] = 1;
			} else {
				$grouped_count[ $name ]++;
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$grouped_count = $this->count_blocks( $block['innerBlocks'], $grouped_count );
			}
		}

		return $grouped_count;
	}

	public function register_settings() {
		$this->add_setting( new Settings\Column\Gutenberg( $this ) );
	}

	public function export() {
		return new StrippedValue( $this );
	}

}