<?php
/**
 * Testimonial Carousel
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class WPML_Testimonial_Carousel extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'testimonials';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return ['name', 'title', 'testimonial_content'];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'name':
				return __( 'Testimonial Carousel: Name', 'happy-addons-pro' );
			case 'title':
				return __( 'Team Carousel: Title', 'happy-addons-pro' );
			case 'testimonial_content':
				return __( 'Team Carousel: Testimonial contents', 'happy-addons-pro' );
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
