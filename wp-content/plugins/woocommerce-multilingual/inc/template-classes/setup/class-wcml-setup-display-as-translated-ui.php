<?php

use WCML\Options\WPML;

class WCML_Setup_Display_As_Translated_UI extends WCML_Setup_Step {

	const SLUG = 'translation-options-2';

	public function get_model() {

		$custom_posts_unlocked = apply_filters( 'wpml_get_setting', false, 'custom_posts_unlocked_option' );
		$custom_posts_sync     = apply_filters( 'wpml_get_setting', false, 'custom_posts_sync_option' );

		$is_display_as_translated_checked = isset( $custom_posts_unlocked['product'], $custom_posts_sync['product'] )
											&& 1 === $custom_posts_unlocked['product']
											&& WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED === $custom_posts_sync['product'];

		return [
			'strings'                          => [
				'step_id'                          => 'display_as_translated_step',
				'heading'                          => __( 'What do you want to do with products that are not translated?', 'woocommerce-multilingual' ),
				'label_display_as_translated'      => sprintf(
					/* translators: %1$s and %2$s are opening and closing HTML link tags */
					esc_html__(
						'Allow viewing products in languages they are not translated to but %1$sdisplay their content in the default language%2$s',
						'woocommerce-multilingual'
					),
					'<a target="_blank" class="wpml-external-link" rel="noopener" href="' . WCML_Tracking_Link::getWcmlDisplayAsTranslatedDoc( [ 'utm_term' => WCML_Tracking_Link::UTM_TERM_WIZARD ] ) . '">',
					'</a>'
				),
				'label_dont_display_as_translated' => __( 'Do not allow viewing products in languages they are not translated to', 'woocommerce-multilingual' ),
				/* translators: %1$s and %2$s are opening and closing HTML strong tags */
				'description_footer'               => esc_html__( 'You can change these settings later by going to %1$sWPML &raquo; Settings.%2$s', 'woocommerce-multilingual' ),
				'continue'                         => __( 'Continue', 'woocommerce-multilingual' ),
				'go_back'                          => __( 'Go back', 'woocommerce-multilingual' ),
			],
			'is_display_as_translated_checked' => $is_display_as_translated_checked,
			'continue_url'                     => $this->next_step_url,
			'go_back_url'                      => $this->previous_step_url,
		];
	}

	public function get_template() {
		return '/setup/display-as-translated.twig';
	}
}
