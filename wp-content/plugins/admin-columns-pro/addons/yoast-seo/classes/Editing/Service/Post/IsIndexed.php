<?php

namespace ACA\YoastSeo\Editing\Service\Post;

use ACP\Editing;
use ACP\Editing\View;

class IsIndexed extends Editing\Service\BasicStorage {

	/**
	 * @var bool
	 */
	private $default_value;

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( $post_type, $default_value ) {
		$this->post_type = $post_type;
		$this->default_value = (bool) $default_value;

		parent::__construct( new Editing\Storage\Post\Meta( '_yoast_wpseo_meta-robots-noindex' ) );
	}

	public function get_view( string $context ): ?View {
		$post_type = $this->post_type;
		$labels = get_post_type_labels( get_post_type_object( $post_type ) );
		$currently = $this->default_value ? __( 'Yes' ) : __( 'No' );
		$options = [
			0 => sprintf( __( 'Default for %s, currently: %s', 'codepress-admin-columns' ), $labels->name, $currently ),
			1 => __( 'No' ),
			2 => __( 'Yes' ),
		];

		return new Editing\View\Select( $options );
	}

}