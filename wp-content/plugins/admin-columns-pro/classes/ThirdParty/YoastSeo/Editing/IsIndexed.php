<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use AC;
use ACP\Editing;

class IsIndexed extends Editing\Model\Meta {

	/**
	 * @var bool
	 */
	private $default_value;

	public function __construct( AC\Column\Meta $column, $default_value ) {
		$this->default_value = (bool) $default_value;

		parent::__construct( $column );
	}

	public function get_view_settings() {
		$post_type = $this->column->get_post_type();
		$labels = get_post_type_labels( get_post_type_object( $post_type ) );
		$currently = $this->default_value ? __( 'Yes' ) : __( 'No' );

		return [
			'type'    => 'select',
			'options' => [
				0 => sprintf( __( 'Default for %s, currently: %s', 'codepress-admin-columns' ), $labels->name, $currently ),
				1 => __( 'No' ),
				2 => __( 'Yes' ),
			],
		];
	}

}