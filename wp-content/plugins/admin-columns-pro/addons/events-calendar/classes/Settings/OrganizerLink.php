<?php

namespace ACA\EC\Settings;

class OrganizerLink extends NonPublicPostLink {

	protected function get_display_options() {
		$options = parent::get_display_options();
		$options['email'] = __( 'Email', 'codepress-admin-columns' );
		$options['website'] = __( 'Website', 'codepress-admin-columns' );

		return $options;
	}

	public function format( $value, $original_value ) {

		$id = $original_value;

		switch ( $this->get_post_link_to() ) {
			case 'website' :
				$url = get_post_meta( $id, '_OrganizerWebsite', true );

				return $url ? sprintf( '<a href="%s">%s</a>', $url, $value ) : $value;

			case 'email' :
				$email = get_post_meta( $id, '_OrganizerEmail', true );

				return $email ? sprintf( '<a href="mailto:%s">%s</a>', $email, $value ) : $value;

			default :
				return parent::format( $value, $original_value );
		}
	}

}