<?php
/**
 * Customizer Range Control
 *
 * @version 1.0.0
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

class KIA_Customizer_Radio_Image_Control extends \WP_Customize_Control {
	public $type = 'kia-radio-image';

	private $version = '1.0.1';

	/**
	 * Enqueue scripts/styles.
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_script( 'kia-customizer-radio-image-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/js/script' . $suffix . '.js' ), array( 'jquery' ), $this->version, true );
		wp_enqueue_style( 'kia-customizer-radio-image-control', $this->abs_path_to_url( dirname( __FILE__ ) . '/css/style.css' ), array(), $this->version );
	}

	/**
	 * Add custom JSON parameters to use in the JS template.
	 */
	public function to_json() {
		parent::to_json();

		// We need to make sure we have the correct image URL.
		foreach ( $this->choices as $value => $args ) {
			$this->choices[ $value ]['image'] = esc_url( $args['image'] );
		}

		$this->json['choices'] = $this->choices;
		$this->json['link']    = $this->get_link();
		$this->json['value']   = $this->value();
		$this->json['id']      = $this->id;
	}

	/**
	 * Underscore JS template to handle the control's output.
	 */
	public function content_template() { ?>

		<# if ( ! data.choices ) {
			return;
		} #>

		<fieldset class="customize-control-kia-radio-images">

			<# if ( data.label ) { #>
				<legend class="customize-control-title">{{ data.label }}</legend>
			<# } #>

			<# if ( data.description ) { #>
				<p class="description customize-control-description">{{{ data.description }}}</p>
			<# } #>

			<div class="radio-image__options-wrapper">
		
				<# for ( key in data.choices ) { #>

					<input type="radio" value="{{ key }}" name="_customize-{{ data.type }}-{{ data.id }}" id="{{ data.id }}-{{ key }}" {{{ data.link }}} <# if ( key === data.value ) { #> checked="checked" <# } #> />

					<label for="{{ data.id }}-{{ key }}" class="{{ key }}">
						<# if ( data.choices[ key ]['image'] ) { #>
							<img src="{{ data.choices[ key ]['image'] }}" alt="{{ data.choices[ key ]['label'] }}" />
						<# } #>
						<span class="description">{{ data.choices[ key ]['label'] }}</span>
					</label>

				<# } #>

			</div>

		</fieldset><!-- .customize-control-kia-radio-images -->
		<?php
	}

	/**
	 * Sanitize the image radios.
	 *
	 * @param string $value
	 * @param Wp_Customizer_Setting
	 * @return string
	 */
	public static function sanitize( $value, $setting ) {
		// Input must be a slug: lowercase alphanumeric characters, dashes and underscores are allowed only
		$value = sanitize_key( $value );

		// Get the list of possible radio box options
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// Return value if valid or return default option
		return ( array_key_exists( $value, $choices ) ? $value : $setting->default );
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
