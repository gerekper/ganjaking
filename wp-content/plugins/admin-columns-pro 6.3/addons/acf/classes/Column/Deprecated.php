<?php

namespace ACA\ACF\Column;

use AC;
use AC\Type\Url;
use ACA\ACF\Helper;
use ACA\ACF\Settings\Column\HiddenDeprecated;

class Deprecated extends AC\Column {

	public function __construct() {
		$this
			->set_type( 'column-acf_field' )
			->set_label( __( 'ACF (Deprecated)', 'codepress-admin-columns' ) )
			->set_group( 'acf' );
	}

	protected function register_settings() {
		parent::register_settings();

		$hash = $this->get_option( 'field' );

		if ( ! $hash ) {
			return;
		}

		$acf_field = acf_get_field( $hash );

		$acf_group = ( new Helper() )->get_field_group( $hash );
		$edit_group_url = ( new Helper() )->get_field_edit_link( $hash );
		$documentation_url = new Url\Documentation( Url\Documentation::ARTICLE_ACF_UPGRADE_V2_TO_V3 );

		$message = new AC\Settings\Column\Message( $this );
		$message->set_label( __( 'Update Message', 'codepress-admin-columns' ) );
		$message->set_message(
			sprintf( '<div class="acf-deprecated-message">%s %s<div class="acf-deprecated-message-field">%s<br>%s<br>%s<br>%s<br>%s<br>%s</div></div>',
				__( 'This ACF column could not be updated from the v2 to v3 version.', 'codepress-admin-column' ),
				sprintf( __( 'Read more about this in %s.' ),
					sprintf( '<a href="%s">%s</a>', $documentation_url, __( 'our documentation', 'codepress-admin-column' ) )
				),
				sprintf( '<strong>%s</strong>', __( 'ACF Field', 'codepress-admin-columns' ) ),
				sprintf( __( 'Field Label: %s', 'codepress-admin-column' ), sprintf( '<em>%s</em>', $acf_field['label'] ) ),
				sprintf( __( 'Field Name: %s', 'codepress-admin-column' ), sprintf( '<em>%s</em>', $acf_field['name'] ) ),
				sprintf( __( 'Field Type: %s', 'codepress-admin-column' ), sprintf( '<em>%s</em>', $acf_field['type'] ) ),
				sprintf( __( 'Field Key: %s', 'codepress-admin-column' ), sprintf( '<em>%s</em>', $hash ) ),
				sprintf( __( 'Field Group: %s', 'codepress-admin-column' ), sprintf( '<a href="%s">%s</a>', $edit_group_url, sprintf( '<em>%s</em>', $acf_group['title'] ) ) )
			)
		);

		$this->add_setting( $message );

		$options = $this->get_options();

		foreach ( $this->get_available_acf_settings() as $setting ) {
			if ( array_key_exists( $setting, $options ) ) {
				$this->add_setting( new HiddenDeprecated( $this, $setting ) );
			}
		}
	}

	public function get_available_acf_settings() {
		return [
			'bulk_edit',
			'character_limit',
			'date_format',
			'display_author_as',
			'edit',
			'excerpt_length',
			'field',
			'filter',
			'filter_label',
			'filter_format',
			'flex_display',
			'link_label',
			'number_format',
			'number_of_items',
			'password',
			'post_property_display',
			'post_link_to',
			'oembed',
			'repeater_display',
			'sort',
			'sub_field',
			'term_property',
			'term_link_to',
			'user_link_to',
		];
	}

}