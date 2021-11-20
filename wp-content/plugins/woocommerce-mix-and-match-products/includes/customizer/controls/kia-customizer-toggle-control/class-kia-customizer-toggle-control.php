<?php
/**
 * Customizer Toggle Control
 *
 * @version 1.0.0
 * @author Kathy Darling
 * @license GPL-3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WP_Customize_Control does not exsist.
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

class KIA_Customizer_Toggle_Control extends \WP_Customize_Control {
	public $type = 'kia-toggle';

	private $version = '1.0.0';

	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'kia-customizer-toggle-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/js/script' . $suffix . '.js' ), array( 'jquery' ), $this->version, true );
		wp_enqueue_style( 'kia-customizer-toggle-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/css/style.css' ), array(), $this->version );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 */
	public function to_json() {
		parent::to_json();

		// The setting value.
		$this->json['id']           = $this->id;
		$this->json['value']        = $this->value();
		$this->json['link']         = $this->get_link();
		$this->json['defaultValue'] = $this->setting->default;
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
		<label class="customize-control-kia-toggle">

			<div class="toggle--wrapper">

			<# if ( data.label ) { #>
				<span class="customize-control-title">{{ data.label }}</span>
			<# } #>

				<input id="toggle-{{ data.id }}" type="checkbox" class="toggle--input" value="{{ data.value }}" {{{ data.link }}} <# if ( data.value ) { #> checked="checked" <# } #> />
				<label for="toggle-{{ data.id }}" class="toggle--label"></label>
			</div>

			<# if ( data.description ) { #>
				<p class="description customize-control-description">{{ data.description }}</p>
			<# } #>

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

	/**
	 * Sanitize the toggle.
	 *
	 * @param string $value
	 * @param Wp_Customizer_Setting
	 * @return bool
	 */
	public static function sanitize( $value, $setting ) {
		return ( isset( $input ) ) ? true : false;
	}
}
