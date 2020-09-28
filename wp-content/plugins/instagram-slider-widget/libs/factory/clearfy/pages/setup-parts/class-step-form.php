<?php

namespace WBCR\FactoryClearfy227\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Form extends Step {

	public function __construct(\WBCR\FactoryClearfy227\Pages\Setup $page)
	{
		parent::__construct($page);
	}

	public function get_title()
	{
		return 'Default form';
	}

	public function get_form_description()
	{
		return 'This is a sample html form, please customize the form fields, add description and title.';
	}

	public function get_form_options()
	{
		return [];
	}

	protected function instance_form($options)
	{

		$form = new \Wbcr_FactoryForms433_Form([
			'scope' => rtrim($this->plugin->getPrefix(), '_'),
			'name' => $this->page->getResultId() . "-options-" . $this->get_id()
		], $this->plugin);

		$form->setProvider(new \Wbcr_FactoryForms433_OptionsValueProvider($this->plugin));

		$form_options = [];

		$form_options[] = [
			'type' => 'form-group',
			'items' => $options,
			//'cssClass' => 'postbox'
		];

		if( isset($form_options[0]) && isset($form_options[0]['items']) && is_array($form_options[0]['items']) ) {
			foreach($form_options[0]['items'] as $key => $value) {

				if( $value['type'] == 'div' || $value['type'] == 'more-link' ) {
					if( isset($form_options[0]['items'][$key]['items']) && !empty($form_options[0]['items'][$key]['items']) ) {
						foreach($form_options[0]['items'][$key]['items'] as $group_key => $group_value) {
							$form_options[0]['items'][$key]['items'][$group_key]['layout']['column-left'] = '8';
							$form_options[0]['items'][$key]['items'][$group_key]['layout']['column-right'] = '4';
						}

						continue;
					}
				}

				if( in_array($value['type'], [
					'checkbox',
					'textarea',
					'integer',
					'textbox',
					'dropdown',
					'list',
					'wp-editor'
				]) ) {
					$form_options[0]['items'][$key]['layout']['column-left'] = '8';
					$form_options[0]['items'][$key]['layout']['column-right'] = '4';
				}
			}
		}

		$form->add($form_options);
		$this->set_form_handler($form);

		return $form;
	}

	protected function render_form(\Wbcr_FactoryForms433_Form $form)
	{
		?>
		<form method="post" id="w-factory-clearfy-227__setup-form-<?php echo $this->get_id() ?>" class="w-factory-clearfy-227__setup-form form-horizontal">
			<?php $form->html(); ?>
			<div class="w-factory-clearfy-227__form-buttons">
				<!--<input type="submit" name="skip_button_<?php /*echo $this->get_id() */ ?>" class="button-primary button button-large w-factory-clearfy-227__skip-button" value="<?php /*_e('Skip', 'wbcr_factory_clearfy_227') */ ?>">-->
				<input type="submit" name="continue_button_<?php echo $this->get_id() ?>" class="button-primary button button-large w-factory-clearfy-227__continue-button" value="<?php _e('Continue', 'wbcr_factory_clearfy_227') ?>">
			</div>
		</form>
		<?php
	}

	protected function set_form_handler(\Wbcr_FactoryForms433_Form $form)
	{
		if( isset($_POST['continue_button_' . $this->get_id()]) ) {
			$form->save();
			$this->continue_step();
		}

		if( isset($_POST['skip_button_' . $this->get_id()]) ) {
			$this->skip_step();
		}
	}

	public function html()
	{
		$form_options = $this->get_form_options();

		if( empty($form_options) ) {
			echo 'Html form is not configured.';

			return;
		}

		$form = $this->instance_form($this->get_form_options());
		?>
		<div id="WBCR" class="wrap">
			<div class="wbcr-factory-pages-435-impressive-page-template factory-bootstrap-436 factory-fontawesome-000">
				<div class="w-factory-clearfy-227-setup__inner-wrap">
					<h3><?php echo $this->get_title(); ?></h3>
					<p style="text-align: left;"><?php echo $this->get_form_description(); ?></p>
				</div>
				<?php $this->render_form($form); ?>
			</div>
		</div>
		<?php
	}

}