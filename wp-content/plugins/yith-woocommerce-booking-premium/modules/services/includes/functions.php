<?php
/**
 * Functions
 *
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_service' ) ) {
	/**
	 * Get a service.
	 *
	 * @param YITH_WCBK_Service|WP_Term|int $service The service.
	 *
	 * @return YITH_WCBK_Service|false
	 */
	function yith_wcbk_get_service( $service ) {
		try {
			return new YITH_WCBK_Service( $service );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_services' ) ) {
	/**
	 * Get all services by arguments.
	 *
	 * @param array $args Argument for get_terms.
	 *
	 * @return YITH_WCBK_Service[]|WP_Term[]|int[]|string[]|string|WP_Error
	 */
	function yith_wcbk_get_services( array $args = array() ) {
		$default_args = array(
			'hide_empty' => false,
			'return'     => 'services',
		);

		$args   = wp_parse_args( $args, $default_args );
		$return = $args['return'];

		unset( $args['return'] );

		switch ( $return ) {
			case 'id=>name':
				$args['fields'] = 'id=>name';
				break;
			case 'terms':
				$args['fields'] = 'all';
				break;
			case 'ids':
			case 'services':
			default:
				$args['fields'] = 'ids';
				break;
		}

		$args['taxonomy'] = YITH_WCBK_Post_Types::SERVICE_TAX;
		$services         = get_terms( $args );
		$services         = ! ! $services ? $services : array();

		if ( 'services' === $return ) {
			$services = array_map( 'yith_wcbk_get_service', $services );
		}

		return $services;
	}
}

if ( ! function_exists( 'yith_wcbk_get_service_type_labels' ) ) {
	/**
	 * Get service type labels.
	 *
	 * @return array
	 */
	function yith_wcbk_get_service_type_labels() {
		$services_labels = array(
			'additional' => yith_wcbk_get_label( 'additional-services' ),
			'included'   => yith_wcbk_get_label( 'included-services' ),
		);

		return apply_filters( 'yith_wcbk_get_service_type_labels', $services_labels );
	}
}

if ( ! function_exists( 'yith_wcbk_split_services_by_type' ) ) {
	/**
	 * Split services by type.
	 *
	 * @param int[]|YITH_WCBK_Service[] $services       Services.
	 * @param bool                      $include_hidden Include hidden flag.
	 *
	 * @return mixed|void
	 */
	function yith_wcbk_split_services_by_type( $services, $include_hidden = true ) {
		$split_services = array(
			'additional' => array(),
			'included'   => array(),
		);
		if ( ! ! $services && is_array( $services ) ) {
			foreach ( $services as $service_id ) {
				$service = yith_wcbk_get_service( $service_id );

				if ( ! $include_hidden && $service->is_hidden() ) {
					continue;
				}

				if ( $service->is_optional() ) {
					$split_services['additional'][] = $service;
				} else {
					$split_services['included'][] = $service;
				}
			}
		}

		return apply_filters( 'yith_wcbk_split_services_by_type', $split_services );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_services_html' ) ) {
	/**
	 * Booking services HTML.
	 *
	 * @param string[] $service_names Service names.
	 *
	 * @return string
	 */
	function yith_wcbk_booking_services_html( $service_names ) {
		$separator = apply_filters( 'yith_wcbk_booking_services_separator', ', ' );

		return apply_filters( 'yith_wcbk_booking_services_html', implode( $separator, $service_names ) );
	}
}

