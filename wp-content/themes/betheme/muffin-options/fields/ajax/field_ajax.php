<?php
class MFN_Options_ajax extends MFN_Options
{

	protected $field = array();
	protected $value = '';
	protected $prefix = false;

	/**
	 * Constructor
	 */

	public function __construct($field = array(), $value = '', $prefix = false)
	{
		$this->field = $field;
		$this->value = $value;
		$this->prefix = $prefix;

		$this->enqueue();
	}

	/**
	 * Render
	 */

	public function render($meta = false)
	{
		$action = isset($this->field['action']) ? $this->field['action'] : '';
		$param 	= isset($this->field['param']) ? $this->field['param'] : '';

		echo '<a href="javascript:void(0);" class="btn-blue mfn-opts-ajax" data-ajax="'. esc_url(admin_url('admin-ajax.php')) .'" data-action="'. esc_attr($action) .'" data-param="'. esc_attr($param) .'">'. esc_html__('Randomize', 'mfn-opts') .'</a>';

		if (isset($this->field['desc'])) {
			echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}
	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-ajax', MFN_OPTIONS_URI .'fields/ajax/field_ajax.js', array('jquery'), MFN_THEME_VERSION, true);
	}
}
