<?php

namespace ACP\Settings\Column;

use AC;

class User extends AC\Settings\Column\User {

	const PROPERTY_META = 'custom_field';
	const PROPERTY_GRAVATAR = 'gravatar';

	protected function get_display_options() {
		$options = parent::get_display_options();

		$options[ self::PROPERTY_GRAVATAR ] = __( 'Gravatar', 'codepress-admin-columns' );

		natcasesort( $options );

		// Grouped options
		return [
			[
				'title'   => __( 'User', 'codepress-admin-columns' ),
				'options' => $options,
			],
			[
				'title'   => __( 'Custom Field', 'codepress-admin-columns' ),
				'options' => [
					self::PROPERTY_META => __( 'Custom Field', 'codepress-admin-columns' ),
				],
			],
		];
	}

	public function get_dependent_settings() {

		switch ( $this->get_display_author_as() ) {
			case self::PROPERTY_META :
				return [ new UserCustomField( $this->column ) ];
			case self::PROPERTY_GRAVATAR :
				return [ new Gravatar( $this->column ) ];
			default:
				return parent::get_dependent_settings();
		}
	}

	public function format( $user_id, $original_value ) {

		switch ( $this->get_display_author_as() ) {
			case self::PROPERTY_META :
				/** @var UserCustomField $setting */
				$setting = $this->column->get_setting( UserCustomField::NAME );

				return get_user_meta( $user_id, $setting->get_field(), true );
			case self::PROPERTY_GRAVATAR :

				return get_avatar_url( $user_id );
			default:

				return parent::format( $user_id, $original_value );
		}
	}

}