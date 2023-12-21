<?php
/**
 * Team Carousel
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Team_Carousel extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'members';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['title', 'job_title', 'bio', 'website', 'email', 'facebook', 'twitter', 'instagram', 'github', 'linkedin'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'title':
				return __( 'Team Carousel: Title', 'happy-addons-pro' );
			case 'job_title':
				return __( 'Team Carousel: Job Title', 'happy-addons-pro' );
			case 'bio':
				return __( 'Team Carousel: Short Bio', 'happy-addons-pro' );
			case 'website':
				return __( 'Team Carousel: Website', 'happy-addons-pro' );
			case 'email':
				return __( 'Team Carousel: Email', 'happy-addons-pro' );
			case 'facebook':
				return __( 'Team Carousel: Facebook', 'happy-addons-pro' );
			case 'twitter':
				return __( 'Team Carousel: Twitter', 'happy-addons-pro' );
			case 'instagram':
				return __( 'Team Carousel: Instagram', 'happy-addons-pro' );
			case 'github':
				return __( 'Team Carousel: Github', 'happy-addons-pro' );
			case 'linkedin':
				return __( 'Team Carousel: LinkedIn', 'happy-addons-pro' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'title':
			case 'job_title':
			case 'website':
			case 'email':
			case 'facebook':
			case 'twitter':
			case 'instagram':
			case 'github':
			case 'linkedin':
				return 'LINE';
			case 'bio':
				return 'AREA';
			default:
				return '';
		}
	}
}
