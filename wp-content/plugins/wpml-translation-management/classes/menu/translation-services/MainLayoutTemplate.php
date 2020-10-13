<?php

namespace WPML\TM\Menu\TranslationServices;

class MainLayoutTemplate {

	const SERVICES_LIST_TEMPLATE = 'services-layout.twig';

	/**
	 * @param  callable  $templateRenderer
	 * @param  callable  $activeServiceRenderer
	 * @param  bool  $hasPreferredService
	 * @param  callable  $retrieveServiceTabsData
	 */
	public static function render(
		$templateRenderer,
		$activeServiceRenderer,
		$hasPreferredService,
		$retrieveServiceTabsData
	) {
		echo $templateRenderer(
			self::getModel( $activeServiceRenderer, $hasPreferredService, $retrieveServiceTabsData ),
			self::SERVICES_LIST_TEMPLATE
		);
	}

	/**
	 * @param  callable  $activeServiceRenderer
	 * @param  bool  $hasPreferredService
	 * @param  callable  $retrieveServiceTabsData
	 *
	 * @return array
	 */
	private static function getModel( $activeServiceRenderer, $hasPreferredService, $retrieveServiceTabsData ) {
		$services = $retrieveServiceTabsData();

		return [
			'active_service'        => $activeServiceRenderer(),
			'services'              => $services,
			'has_preferred_service' => $hasPreferredService,
			'has_services'          => ! empty( $services ),
			'nonces'                => [
				ActivationAjax::NONCE_ACTION    => wp_create_nonce( ActivationAjax::NONCE_ACTION ),
				AuthenticationAjax::AJAX_ACTION => wp_create_nonce( AuthenticationAjax::AJAX_ACTION ),
			],
			'strings'               => [
				'no_service_found' => [
					__( 'WPML cannot load the list of translation services. This can be a connection problem. Please wait a minute and reload this page.',
						'wpml-translation-management' ),
					__( 'If the problem continues, please contact %s.', 'wpml-translation-management' ),
				],
				'wpml_support'     => 'WPML support',
				'support_link'     => 'https://wpml.org/forums/forum/english-support/',
				'activate'         => __( 'Activate', 'wpml-translation-management' ),
				'documentation'    => __( 'Documentation', 'wpml-translation-management' ),
				'ts'               => [
					'different'   => __( 'Looking for a different translation service?',
						'wpml-translation-management' ),
					'tell_us_url' => 'https://wpml.org/documentation/content-translation/how-to-add-translation-services-to-wpml/#add-service-form',
					'tell_us'     => __( 'Tell us which one', 'wpml-translation-management' ),
				],
				'filter'           => [
					'search'       => __( 'Search', 'wpml-translation-management' ),
					'clean_search' => __( 'Clear search', 'wpml-translation-management' ),
				],
				'columns'          => [
					'name'        => __( 'Name', 'wpml-translation-management' ),
					'description' => __( 'Description', 'wpml-translation-management' ),
					'ranking'     => __( 'Ranking', 'wpml-translation-management' ),
				],
				'pagination'       => \WPML_Admin_Pagination_Render::get_strings( 10 ),
			],
		];
	}
}