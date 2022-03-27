<?php

namespace ACP\QuickAdd\Table\Checkbox;

use AC\Form\Element\Checkbox;

class ShowButton extends Checkbox {

	public function __construct( $value ) {
		parent::__construct( 'acp_new_inline_show_button', [
			1 => __( 'Add Row', 'codepress-admin-columns' ),
		] );

		$this->set_id( 'acp_new_inline_show_button' );
		$this->set_value( $value );
	}

}