<?php

namespace ACP\Editing\Model;

use AC;
use ACP\Editing\Model;
use ACP\Editing\Settings;

class CustomField extends Model {

	/**
	 * @var AC\Column\CustomField
	 */
	protected $column;

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );
	}

	protected function save( $id, $value ) {
		return false !== update_metadata(
				$this->column->get_list_screen()->get_meta_type(),
				$id,
				$this->column->get_meta_key(),
				$value
			);
	}

	protected function is_editing_enabled() {
		return ( new Settings\CustomField() )->is_enabled();
	}

	public function register_settings() {
		if ( $this->is_editing_enabled() ) {

			// Settings
			parent::register_settings();
		} else {

			// Message
			$message = new AC\Settings\Column\Message( $this->column );
			$message
				->set_label( __( 'Inline Editing', 'codepress-admin-columns' ) )
				->set_message( sprintf( __( 'Inline Editing for Custom Fields is not enabled. Enable inline editing for Custom Fields on the %s.', 'codepress-admin-columns' ), ac_helper()->html->link( ac_get_admin_url( 'settings' ), __( 'settings screen', 'codepress-admin-columns' ) ) ) );

			$this->column->add_setting( $message );
		}
	}

}