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
	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_FontControl') ) {

		class Wbcr_FactoryForms436_FontControl extends Wbcr_FactoryForms436_ComplexControl {

			public $type = 'font';

			public function __construct($options, $form, $provider = null)
			{
				parent::__construct($options, $form, $provider);

				$option_font_size = array(
					'name' => $this->options['name'] . '__size',
					'units' => $this->options['units'],
					'default' => isset($this->options['default'])
						? $this->options['default']['size']
						: null
				);

				$option_font_family = array(
					'name' => $this->options['name'] . '__family',
					'data' => $this->getFonts(),
					'default' => isset($this->options['default'])
						? $this->options['default']['family']
						: null
				);

				$optionFontColor = array(
					'name' => $this->options['name'] . '__color',
					'default' => isset($this->options['default'])
						? $this->options['default']['color']
						: null,
					'pickerTarget' => '.factory-control-' . $this->options['name'] . ' .factory-picker-target'
				);

				$this->size = new Wbcr_FactoryForms436_IntegerControl($option_font_size, $form, $provider);
				$this->family = new Wbcr_FactoryForms436_DropdownControl($option_font_family, $form, $provider);
				$this->color = new Wbcr_FactoryForms436_ColorControl($optionFontColor, $form, $provider);

				$this->innerControls = array($this->family, $this->size, $this->color);
			}

			public function getFonts()
			{

				$fonts = $this->getDefaultFonts();

				$fonts = apply_filters('wbcr_factory_forms_436_fonts', $fonts);
				$fonts = apply_filters('wbcr_factory_forms_436_fonts-' . $this->options['name'], $fonts);

				return $fonts;
			}

			public function getDefaultFonts()
			{

				$fonts = array(

					array('inherit', __('(use default website font)', 'wbcr_factory_forms_436')),
					array(
						'group',
						__('Sans Serif:', 'wbcr_factory_forms_436'),
						array(
							array('Arial, "Helvetica Neue", Helvetica, sans-serif', 'Arial'),
							array('"Arial Black", "Arial Bold", Gadget, sans-serif', 'Arial Black'),
							array('"Arial Narrow", Arial, sans-serif', 'Arial Narrow'),
							array(
								'"Arial Rounded MT Bold", "Helvetica Rounded", Arial, sans-serif',
								'Arial Rounded MT Bold'
							),
							array(
								'"Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif',
								'Avant Garde'
							),
							array('Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif', 'Calibri'),
							array('Candara, Calibri, Segoe, "Segoe UI", Optima, Arial, sans-serif', 'Candara'),
							array('"Century Gothic", CenturyGothic, AppleGothic, sans-serif', 'Century Gothic'),
							array(
								'"Franklin Gothic Medium", "Franklin Gothic", "ITC Franklin Gothic", Arial, sans-serif',
								'Franklin Gothic Medium'
							),
							array('Futura, "Trebuchet MS", Arial, sans-serif', 'Futura'),
							array('Geneva, Tahoma, Verdana, sans-serif', 'Geneva'),
							array('"Gill Sans", "Gill Sans MT", Calibri, sans-serif', 'Gill Sans'),
							array('"Helvetica Neue", Helvetica, Arial, sans-serif', 'Helvetica'),
							array(
								'Impact, Haettenschweiler, "Franklin Gothic Bold", Charcoal, "Helvetica Inserat", "Bitstream Vera Sans Bold", "Arial Black", sans serif',
								'Impact'
							),
							array(
								'"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Geneva, Verdana, sans-serif',
								'Lucida Grande'
							),
							array('Optima, Segoe, "Segoe UI", Candara, Calibri, Arial, sans-serif', 'Optima'),
							array(
								'"Segoe UI", Frutiger, "Frutiger Linotype", "Dejavu Sans", "Helvetica Neue", Arial, sans-serif',
								'Segoe UI'
							),
							array(
								'Montserrat, "Segoe UI", "Helvetica Neue", Arial, sans-serif',
								'Montserrat'
							),
							array('Tahoma, Verdana, Segoe, sans-serif', 'Tahoma'),
							array(
								'"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif',
								'Trebuchet MS'
							),
							array('Verdana, Geneva, sans-serif', 'Verdana'),
						)
					),
					array(
						'group',
						__('Serif:', 'wbcr_factory_forms_436'),
						array(
							array(
								'Baskerville, "Baskerville Old Face", "Hoefler Text", Garamond, "Times New Roman", serif',
								'Baskerville'
							),
							array('"Big Caslon", "Book Antiqua", "Palatino Linotype", Georgia, serif', 'Big Caslon'),
							array(
								'"Bodoni MT", Didot, "Didot LT STD", "Hoefler Text", Garamond, "Times New Roman", serif',
								'Bodoni MT'
							),
							array(
								'"Book Antiqua", Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif',
								'Book Antiqua'
							),
							array(
								'"Calisto MT", "Bookman Old Style", Bookman, "Goudy Old Style", Garamond, "Hoefler Text", "Bitstream Charter", Georgia, serif',
								'Calisto MT'
							),
							array('Cambria, Georgia, serif', 'Cambria'),
							array('Didot, "Didot LT STD", "Hoefler Text", Garamond, "Times New Roman", serif', 'Didot'),
							array(
								'Garamond, Baskerville, "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif',
								'Garamond'
							),
							array('Georgia, Times, "Times New Roman", serif', 'Georgia'),
							array(
								'"Goudy Old Style", Garamond, "Big Caslon", "Times New Roman", serif',
								'Goudy Old Style'
							),
							array(
								'"Hoefler Text", "Baskerville old face", Garamond, "Times New Roman", serif',
								'Hoefler Text'
							),
							array('"Lucida Bright", Georgia, serif', 'Lucida Bright'),
							array(
								'Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
								'Palatino'
							),
							array(
								'Perpetua, Baskerville, "Big Caslon", "Palatino Linotype", Palatino, "URW Palladio L", "Nimbus Roman No9 L", serif',
								'Perpetua'
							),
							array(
								'Rockwell, "Courier Bold", Courier, Georgia, Times, "Times New Roman", serif',
								'Rockwell'
							),
							array('"Rockwell Extra Bold", "Rockwell Bold", monospace', 'Rockwell Extra Bold'),
							array(
								'TimesNewRoman, "Times New Roman", Times, Baskerville, Georgia, serif',
								'Times New Roman'
							)
						)
					),
					array(
						'group',
						__('Monospaced:', 'wbcr_factory_forms_436'),
						array(
							array('"Andale Mono", AndaleMono, monospace', 'Andale Mono'),
							array('Consolas, monaco, monospace', 'Consolas'),
							array(
								'"Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace',
								'Courier New'
							),
							array(
								'"Lucida Console", "Lucida Sans Typewriter", Monaco, "Bitstream Vera Sans Mono", monospace',
								'Lucida Console'
							),
							array(
								'"Lucida Sans Typewriter", "Lucida Console", Monaco, "Bitstream Vera Sans Mono", monospace',
								'Lucida Sans Typewriter'
							),
							array('Monaco, Consolas, "Lucida Console", monospace', 'Monaco')
						)
					)

				);

				return $fonts;
			}

			/**
			 * Removes \" in the font family value.
			 *
			 * @since 3.1.0
			 * @return mixed[]
			 */
			public function getValuesToSave()
			{
				$values = parent::getValuesToSave();

				$family_key = $this->options['name'] . '__family';
				$values[$family_key] = sanitize_text_field($values[$family_key]);

				return $values;
			}

			public function beforeControlsHtml()
			{
			}

			public function afterControlsHtml()
			{
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				?>
				<div <?php $this->attrs() ?>>
					<div class="factory-control-row">
						<?php $this->beforeControlsHtml() ?>

						<div class="factory-family-wrap">
							<?php $this->family->html() ?>
						</div>
						<div class="factory-size-wrap">
							<?php $this->size->html() ?>
						</div>
						<div class="factory-color-wrap">
							<?php $this->color->html() ?>
						</div>

						<?php $this->afterControlsHtml() ?>
					</div>
					<div class="factory-picker-target"></div>
				</div>
			<?php
			}
		}
	}

