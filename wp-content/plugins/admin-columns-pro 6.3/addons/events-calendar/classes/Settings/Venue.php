<?php

namespace ACA\EC\Settings;

use AC;

class Venue extends AC\Settings\Column\Post {

	const PROPERTY_ADDRESS = 'address';
	const PROPERTY_CITY = 'city';
	const PROPERTY_COUNTRY = 'country';
	const PROPERTY_PHONE = 'phone';
	const PROPERTY_WEBSITE = 'website';
	const PROPERTY_ZIP = 'zip';

	public function get_dependent_settings() {
		$setting = [];

		if ( 'website' !== $this->get_post_property_display() ) {
			$setting[] = new NonPublicPostLink( $this->column );
		}

		return $setting;
	}

	protected function get_display_options() {
		$options = [
			self::PROPERTY_ADDRESS => __( 'Address', 'codepress-admin-columns' ),
			self::PROPERTY_CITY    => __( 'City', 'codepress-admin-columns' ),
			self::PROPERTY_COUNTRY => __( 'Country', 'codepress-admin-columns' ),
			self::PROPERTY_PHONE   => __( 'Phone', 'codepress-admin-columns' ),
			self::PROPERTY_TITLE   => __( 'Name', 'codepress-admin-columns' ),
			self::PROPERTY_WEBSITE => __( 'Website', 'codepress-admin-columns' ),
			self::PROPERTY_ZIP     => __( 'ZIP', 'codepress-admin-columns' ),
		];

		asort( $options );

		return $options;
	}

	public function format( $value, $original_value ) {
		switch ( $this->get_post_property_display() ) {
			case self::PROPERTY_ADDRESS :
				return tribe_get_address( $original_value );
			case self::PROPERTY_COUNTRY :
				return tribe_get_country( $original_value );
			case self::PROPERTY_CITY :
				return tribe_get_city( $original_value );
			case self::PROPERTY_TITLE :
				return ac_helper()->post->get_title( $original_value );
			case self::PROPERTY_PHONE :
				return tribe_get_phone( $original_value );
			case self::PROPERTY_WEBSITE :
				return tribe_get_venue_website_link( $original_value );
			case self::PROPERTY_ZIP :
				return tribe_get_zip( $original_value );
			default:
				return false;
		}
	}

}