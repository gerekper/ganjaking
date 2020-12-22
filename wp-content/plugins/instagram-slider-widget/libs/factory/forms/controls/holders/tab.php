<?php
	/**
	 * The file contains the class of Tab Control Holder.
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-forms
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_TabHolder') ) {

		/**
		 * Tab Control Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_TabHolder extends Wbcr_FactoryForms436_Holder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'tab';

			/**
			 * An align of a tab (horizontal or vertical).
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $align = 'horizontal';

			/**
			 * Creates a new instance of control holder.
			 *
			 * @since 1.0.0
			 * @param mixed[] $options A holder options.
			 * @param FactoryForms436_Form $form A parent form.
			 */
			public function __construct($options, $form)
			{
				parent::__construct($options, $form);
				$this->align = isset($options['align'])
					? $options['align']
					: 'horizontal';
			}

			/**
			 * Here we should render a beginning html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{

				$is_first_tab = true;
				$tab_class = $this->getOption('class');

				if( !empty($tab_class) ) {
					$this->addCssClass($tab_class);
				}

				$this->addCssClass('factory-align-' . $this->align);

				?>
				<div <?php $this->attrs() ?>>
				<div class="factory-headers">
					<ul class="nav nav-tabs">
						<?php foreach($this->elements as $element) {
							if( $element->options['type'] !== 'tab-item' ) {
								continue;
							}

							$tab_icon = '';
							$has_icon = isset($element->options['icon']);

							if( $has_icon ) {
								$tab_icon = $element->options['icon'];
							}

							$builder = new Wbcr_FactoryForms436_HtmlAttributeBuilder();

							$builder->addCssClass('factory-tab-item-header');
							$builder->addCssClass('factory-tab-item-header-' . $element->getName());

							if( $has_icon ) {
								$builder->addCssClass('factory-tab-item-header-with-icon');
							}
							if( $is_first_tab ) {
								$builder->addCssClass('active');
							}

							$builder->addHtmlData('tab-id', $element->getName());
							$is_first_tab = false;

							if( $has_icon ) { ?>
								<style>
									.factory-form-tab-item-header-<?php $element->name() ?> a {
										background-image: url("<?php echo $tab_icon ?>");
									}
								</style>
							<?php } ?>
							<li <?php $builder->printAttrs() ?>>
								<a href="#<?php $element->name() ?>" data-toggle="tab">
									<?php $element->title() ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
				<div class='tab-content factory-bodies'>
			<?php
			}

			/**
			 * Here we should render an end html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function afterRendering()
			{
				?>
				</div>
				</div>
			<?php
			}
		}
	}