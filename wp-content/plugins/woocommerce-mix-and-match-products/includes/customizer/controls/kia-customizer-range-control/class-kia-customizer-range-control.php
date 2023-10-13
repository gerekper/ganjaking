<?php
/**
 * Customizer Range Control
 * 
 * @version 1.1.0
 * @author Kathy Darling
 * @license GPL-3.0
 * 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WP_Customize_Control does not exsist.
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class KIA_Customizer_Range_Control extends \WP_Customize_Control {
	public $type = 'kia-range';

	private $version = '1.1.0';

	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'kia-customizer-range-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/js/script' . $suffix . '.js' ), array( 'jquery' ), $this->version, true );
		wp_enqueue_style( 'kia-customizer-range-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/css/style.css' ), array(), $this->version );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 */
	public function to_json() {
		parent::to_json();

		// The setting value.
		$this->json['id']                  = $this->id;
		$this->json['value']               = $this->value();
		$this->json['link']                = $this->get_link();
		$this->json['defaultValue']        = $this->setting->default;
		$this->json['input_attrs']['min']  = ( isset( $this->input_attrs['min'] ) ) ? $this->input_attrs['min'] : '0';
		$this->json['input_attrs']['max']  = ( isset( $this->input_attrs['max'] ) ) ? $this->input_attrs['max'] : '100';
		$this->json['input_attrs']['step'] = ( isset( $this->input_attrs['step'] ) ) ? $this->input_attrs['step'] : '1';
	}

	/**
	 * Don't render the content via PHP.  This control is handled with a JS template.
	 */
	public function render_content() {}

	/**
	 * An Underscore (JS) template for this control's content.
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see    WP_Customize_Control::print_template()
	 */
	protected function content_template() {
		?>
		<label class="customize-control-kia-range">

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<p class="customize-control-description">{{ data.description }}</p>
		<# } #>

		<div class="kia-range">
			<input id="range-{{ data.id }}" type="number" class="kia-range__number-input" value="{{ data.value }}" data-default-value="{{ data.defaultValue }}" min="{{ data.input_attrs['min'] }}" max="{{ data.input_attrs['max'] }}" step="{{ data.input_attrs['step'] }}" {{{ data.link }}} <# if ( data.value ) { #> checked="checked" <# } #> />
			<input type="range" data-input-type="range" class="kia-range__track" value="{{ data.value }}" data-default-value="{{ data.defaultValue }}"  min="{{ data.input_attrs['min'] }}" max="{{ data.input_attrs['max'] }}" step="{{ data.input_attrs['step'] }}" {{{ data.link }}} />
			 <a type="button" value="reset" class="kia-range__reset"></a>
		</div>

		</label>
		<?php
	}

	/**
	 * Plugin / theme agnostic path to URL
	 *
	 * @see https://wordpress.stackexchange.com/a/264870/14546
	 * @param string $path  file path
	 * @return string       URL
	 */
	private function abs_path_to_url( $path = '' ) {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);
		return esc_url_raw( $url );
	}
}
