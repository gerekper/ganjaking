<?php

	/**
	 * Dropdown List Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
	 *  items           => a callback to return items or an array of items to select
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */
	class Wbcr_FactoryForms436_GoogleFontControl extends Wbcr_FactoryForms436_FontControl {

		public $type = 'google-font';
		const APIKEY = 'AIzaSyB-3vazYv7Q-5QZA04bmSKFrWcw_VhC40w';

		public function __construct($options, $form, $provider = null)
		{
			parent::__construct($options, $form, $provider);
			$this->addCssClass('factory-font');

			$option_google_font_data = array(
				'name' => $this->options['name'] . '__google_font_data',
				'cssClass' => 'factory-google-font-data'
			);

			$this->google_font_data = new Wbcr_FactoryForms436_HiddenControl($option_google_font_data, $form, $provider);
			$this->inner_controls[] = $this->google_font_data;
		}

		/**
		 * @return array|mixed
		 */
		public function getDefaultFonts()
		{

			$cache_fonts = get_transient('wbcr_factory_google_fonts');

			if( !empty($cache_fonts) ) {
				return $cache_fonts;
			}

			$google_fonts = $this->getGoogleFonts();

			$fonts = array(
				array('inherit', __('(use default website font)', 'wbcr_factory_forms_436'))
			);

			$fontsCommon = array(
				'group',
				__('Standard:', 'wbcr_factory_forms_436'),
				array(

					array('Arial, "Helvetica Neue", Helvetica, sans-serif', 'Arial'),
					array('"Helvetica Neue", Helvetica, Arial, sans-serif', 'Helvetica'),
					array('Tahoma, Verdana, Segoe, sans-serif', 'Tahoma'),
					array('Verdana, Geneva, sans-serif', 'Verdana'),

				)
			);

			$fontsGoogleFonts = array('group', __('Google Fonts:', 'wbcr_factory_forms_436'), array());

			foreach($google_fonts->items as $item) {

				$alt_font = $item->category;
				if( in_array($alt_font, array('handwriting', 'display')) ) {
					$alt_font = 'serif';
				}

				$listItem = array(
					'title' => $item->family,
					'value' => $item->family . ', ' . $item->category,
					'hint' => '<em>Google Font</em>',
					'data' => array(
						'google-font' => true,
						'family' => $item->family,
						'variants' => $item->variants,
						'subsets' => $item->subsets
					)
				);

				$fontsGoogleFonts[2][] = $listItem;
			}

			$fonts[] = $fontsCommon;
			$fonts[] = $fontsGoogleFonts;

			set_transient('wbcr_factory_google_fonts', $fonts, 60 * 60 * 6);

			return $fonts;
		}

		/**
		 * @return array|mixed|object
		 */
		protected function getGoogleFonts()
		{

			$body = get_transient('wbcr_factory_google_fonts_raw');
			if( !empty($body) ) {
				return $body;
			}

			$response = wp_remote_get(sprintf('https://www.googleapis.com/webfonts/v1/webfonts?key=%s', self::APIKEY));

			$this->error = false;
			$this->defailed_error = false;

			if( is_wp_error($response) ) {

				$this->error = __('Unable to retrieve the list of Google Fonts.', 'wbcr_factory_forms_436');
				$this->defailed_error = $response->get_error_message();

				return $body;
			}

			if( !isset($response['body']) ) {

				$this->error = __('Invalide response from the Google Fonts API.', 'wbcr_factory_forms_436');
				$this->defailed_error = $response['body'];

				return $body;
			}

			$body = json_decode($response['body']);

			if( empty($body->items) ) {

				$this->error = __('Unexpected error. The list of Google Fonts are empty.', 'wbcr_factory_forms_436');

				return $body;
			}

			set_transient('wbcr_factory_google_fonts_raw', $body, 60 * 60 * 6);

			return $body;
		}

		public function afterControlsHtml()
		{
			?>
			<?php $this->google_font_data->html() ?>
		<?php
		}
	}
