<?php

use WCML\Options\WPML;

class WCML_Setup_Translation_Options_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;
	private $next_step_url;

	public function __construct( $woocommerce_wpml, $next_step_url ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->next_step_url    = $next_step_url;

	}

	public function get_model() {

		$custom_posts_unlocked = apply_filters( 'wpml_get_setting', false, 'custom_posts_unlocked_option' );
		$custom_posts_sync     = apply_filters( 'wpml_get_setting', false, 'custom_posts_sync_option' );

		$is_display_as_translated_checked = isset( $custom_posts_unlocked['product'], $custom_posts_sync['product'] )
											&& 1 === $custom_posts_unlocked['product']
											&& WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED === $custom_posts_sync['product'];

		$model = [
			'strings'                          => [
				'step_id'                     => 'translation_options_step',
				'heading'                     => __( 'Translation Options', 'woocommerce-multilingual' ),
				'description'                 => __( 'How do you want to translate and display your products in secondary languages?', 'woocommerce-multilingual' ),
				'tooltip_translate_everything' => sprintf(
					/* translators: %1$s/%2$s are opening and closing HTML strong tags and %3$s/%4$s are opening and closing HTML link tags */
					__( 'This option is only available when using %1$sTranslate Everything%2$s mode. %3$sRead More →%4$s', 'woocommerce-multilingual' ),
					'<strong>',
					'</strong>',
					'<a target="blank" class="wpml-external-link" rel="noopener" href="' . WCML_Tracking_Link::getWcmlMainDoc( '#translating-your-products-automatically' ) . '">',
					'</a>'
				),
				'label_translate_everything'  => __( 'Translate all products automatically as I create and edit them.', 'woocommerce-multilingual' ),
				'label_translate_some'        => __( "I'll choose which products to translate. Only show translated products.", 'woocommerce-multilingual' ),
				'label_display_as_translated' => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					__(
						'I\'ll choose which products to translate. If products don\'t have translations, %1$sshow them as untranslated%2$s.',
						'woocommerce-multilingual'
					),
					'<a target="blank" class="wpml-external-link" rel="noopener" href="' . WCML_Tracking_Link::getWcmlDisplayAsTranslatedDoc() . '">',
					'</a>'
				),
				/* translators: %1$s and %2$s are opening and closing HTML strong tags */
				'description_footer'          => __( 'To change this later, go to %1$sWPML &raquo; Settings.%2$s', 'woocommerce-multilingual' ),
				'continue'                    => __( 'Continue', 'woocommerce-multilingual' ),
			],
			'is_translate_some_mode'           => ! WPML::shouldTranslateEverything(),
			'is_display_as_translated_checked' => $is_display_as_translated_checked,
			'continue_url'                     => $this->next_step_url,
		];

		return $model;

	}

	protected function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/',
		];
	}

	public function get_template() {
		return '/setup/translation-options.twig';
	}


}
