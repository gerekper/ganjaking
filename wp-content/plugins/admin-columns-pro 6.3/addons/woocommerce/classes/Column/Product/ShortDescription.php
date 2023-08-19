<?php

namespace ACA\WC\Column\Product;

namespace ACA\WC\Column\Product;

use ACA\WC\Filtering;
use ACP;
use ACP\Editing\Settings\EditableType;

/**
 * @since 3.0
 */
class ShortDescription extends ACP\Column\Post\Excerpt {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-product_short_description' )
		     ->set_label( __( 'Short Description' ) )
		     ->set_group( 'woocommerce' );
	}

	public function register_settings() {
		parent::register_settings();

		$this->add_setting( ( new ACP\Editing\Settings\Factory\EditableType( $this, ACP\Editing\Settings\Factory\EditableType::TYPE_CONTENT ) )->create() );
	}

	public function get_value( $post_id ) {
		if ( ! has_excerpt( $post_id ) ) {
			return $this->get_empty_char();
		}

		return parent::get_value( $post_id );
	}

	public function editing() {
		$view = $this->get_inline_editable_type() === EditableType\Content::TYPE_TEXTAREA
			? new ACP\Editing\View\TextArea()
			: new ACP\Editing\View\Wysiwyg();

		return new ACP\Editing\Service\Basic(
			$view,
			new ACP\Editing\Storage\Post\Field( 'post_excerpt' )
		);
	}

	public function filtering() {
		return new Filtering\Product\ShortDescription( $this );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_excerpt' );
	}

	private function get_inline_editable_type() {
		$setting = $this->get_setting( ACP\Editing\Settings::NAME );

		if ( ! $setting instanceof ACP\Editing\Settings ) {
			return null;
		}

		$section = $setting->get_section( ACP\Editing\Settings\EditableType\Content::NAME );

		return $section instanceof ACP\Editing\Settings\EditableType\Content
			? $section->get_editable_type()
			: null;
	}

}