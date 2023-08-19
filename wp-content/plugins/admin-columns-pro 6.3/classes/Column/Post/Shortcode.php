<?php

namespace ACP\Column\Post;

use AC;
use ACP;
use ACP\Export\Exportable;
use ACP\Settings;

class Shortcode extends AC\Column implements Exportable {

	public function __construct() {
		$this->set_type( 'column-render_shortcode' )
		     ->set_label( __( 'Shortcode', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$shortcode = $this->get_shortcode();

		if ( ! $shortcode ) {
			return $this->get_empty_char();
		}

		$content = get_post( $id )->post_content;

		if ( ! $content ) {
			return $this->get_empty_char();
		}

		$rendered_shortcodes = $this->get_rendered_shortcodes( $content, $shortcode );

		if ( empty( $rendered_shortcodes ) ) {
			return $this->get_empty_char();
		}

		return implode( '<br>', $rendered_shortcodes );
	}

	/**
	 * @return string|null
	 */
	private function get_shortcode() {
		$setting = $this->get_setting( 'shortcode' );

		if ( ! $setting instanceof Settings\Column\Shortcodes ) {
			return null;
		}

		return $setting->get_shortcode();
	}

	/**
	 * @param string $content
	 * @param string $shortcode
	 *
	 * @return array
	 */
	private function get_rendered_shortcodes( $content, $shortcode ) {
		$result = [];
		if ( has_shortcode( $content, $shortcode ) ) {
			preg_match_all( "/" . get_shortcode_regex() . "/", $content, $matches );

			foreach ( $matches[2] as $index => $match ) {
				if ( $shortcode === $match ) {
					$result[] = do_shortcode( $matches[0][ $index ] );
				}
			}
		}

		return array_filter( $result );
	}

	protected function register_settings() {
		$this->add_setting( new Settings\Column\Shortcodes( $this ) );
	}

	public function export() {
		return new ACP\Export\Model\Value( $this );
	}

}