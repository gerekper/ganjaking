<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

use Merkulove\UnGrabber as UnGrabber;

/**
 * SINGLETON
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 */
final class UI {

	/**
	 * The one true UI.
	 *
	 * @var UI
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new UI instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {}

	/**
	 * Render chosen select field.
	 *
	 * @param array $options - Options for select. Required.
	 * @param string $selected - Selected value. Optional.
	 * @param string $helper_text - Text after select. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_chosen_dropdown( $options, $selected = [], $helper_text = '', $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$multiple = isset( $attributes['multiple'] ) ? $attributes['multiple'] : '';
		$placeholder = isset( $attributes['placeholder'] ) ? $attributes['placeholder'] : '';

		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$class = 'mdp-chosen chosen-select ' . $class;
		$class = trim( $class );

        ?>
        <div>
            <!--suppress HtmlFormInputWithoutLabel -->
            <select
	                <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
	                <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
	                <?php echo ( $multiple ) ? 'multiple="' . esc_attr( $multiple ) . '"' : ''; ?>
	                <?php echo ( $placeholder ) ? 'data-placeholder="' . esc_attr( $placeholder ) . '"' : ''; ?>
                    class="<?php esc_attr_e( $class ); ?>">
		        <?php foreach ( $options as $key => $value ) : ?>
                    <?php $isSelected = ( in_array( $key, $selected ) ) ? 'selected=""' : ''; ?>
                    <option value="<?php esc_attr_e( $key ); ?>" <?php esc_attr_e( $isSelected ); ?>><?php esc_attr_e( $value ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
		<?php if ( $helper_text ) : ?>
            <div class="mdc-select-helper-text mdc-select-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
		<?php endif;
    }

	/**
	 * Render dropdown select field.
	 *
	 * @param $options - Options for select. Required.
	 * @param string $selected - Selected value. Optional.
	 * @param string $label - Label for select. Optional.
	 * @param string $helper_text - Text after select. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	**/
	public function render_select_dropdown( $options, $selected = '', $label = '', $helper_text = '',  $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';

		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$class = 'mdc-select mdc-select-width mdc-select--outlined ' . $class;
		$class = trim( $class );

	    ?>
        <div>
            <div class="<?php esc_attr_e( $class ); ?>">
                <input
                    type="hidden"
                    <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
                    <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                    value="<?php esc_attr_e( $selected ); ?>">
                <i class="mdc-select__dropdown-icon"></i>
                <div class="mdc-select__selected-text">
                    <span><?php echo esc_html( $options[ $selected ] ); ?></span>
                </div>
                <div class="mdc-select__menu mdc-menu mdc-menu-surface">
                    <ul class="mdc-list mdc-select-width">
                        <?php foreach ( $options as $key => $value ) : ?>
                            <?php $selected_class = ( $key == $selected ) ? ' mdc-list-item--selected' : ''; ?>
                            <li class="mdc-list-item <?php esc_attr_e( $selected_class ); ?>" data-value="<?php esc_attr_e( $key ); ?>" role='option'>
                                <?php $img_file = UnGrabber::$path . 'images/' . $key . '.svg'; ?>
                                <?php if ( file_exists( $img_file ) ) : ?>
                                    <img src="<?php echo esc_url( UnGrabber::$url . "images/" . $key . ".svg" ); ?>" alt="<?php echo esc_attr( $key ); ?>" >
                                <?php endif; ?>
                                <?php esc_html_e( $value ); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>

                    <?php if ( $label ) : ?>
                        <div class="mdc-notched-outline__notch">
                            <label class="mdc-floating-label mdc-floating-label--float-above"><?php echo esc_html( $label ); ?></label>
                        </div>
                    <?php endif; ?>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
	        <?php if ( $helper_text ) : ?>
                <div class="mdc-select-helper-text mdc-select-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
	        <?php endif; ?>
        </div>
        <?php
    }

	/**
	 * Render select field.
	 *
	 * @param $options - Options for select. Required.
	 * @param string $selected - Selected value. Optional.
	 * @param string $label - Label for select. Optional.
	 * @param string $helper_text - Text after select. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_select( $options, $selected = '', $label = '', $helper_text = '', $attributes = [] ) {

		if ( ! count( $options ) ) { return; }

		/** Prepare html attributes. */
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : 'mdp-' . uniqid();

		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$class = 'mdc-select mdc-select-width mdc-select--outlined ' . $class;
		$class = trim( $class );

		$disabled = isset( $attributes['disabled'] ) ? $attributes['disabled'] : '';
		$badge = isset( $attributes['badge'] ) ? $attributes['badge'] : '';

		/** Check selected option. If we don't have it, select first one. */
		if ( ! array_key_exists( $selected,  $options ) ) {
			reset( $options );
			$selected = key( $options );
		}
		?>

        <div class="<?php esc_attr_e( $class ); ?>">
            <input type="hidden"
				<?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
				<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                   value="<?php esc_attr_e( $selected ); ?>"
            >

            <div class="mdc-select__anchor mdc-select-width">
                <i class="mdc-select__dropdown-icon"></i>
                <div id="<?php esc_attr_e( $id ) ?>-text" class="mdc-select__selected-text" aria-labelledby="outlined-select-label"><?php esc_html_e( $options[$selected] ); ?></div>
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
						<?php if ( $label ) : ?>
                            <span id="<?php esc_attr_e( $id ) ?>-label" class="mdc-floating-label"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>

            <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-select-width" role="listbox">
                <ul class="mdc-list">
					<?php foreach ( $options as $key => $value ) : ?>
						<?php $selected_class = ( $key === $selected ) ? 'mdc-list-item--selected' : ''; ?>
                        <li class="mdc-list-item <?php esc_attr_e( $selected_class ); ?>" data-value="<?php esc_attr_e( $key ) ?>" role="option"><?php esc_attr_e( $value ) ?></li>
					<?php endforeach; ?>
                </ul>
            </div>
        </div>

		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-select-helper-text mdc-select-helper-text--persistent" aria-hidden="true"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;

	}

	/** @noinspection PhpUnused */
	/**
	 * Render slider field.
	 *
	 * @param $value - Current value. Required.
	 * @param int $value_min - The min value the slider can have. Optional.
	 * @param int $value_max - The max value the slider can have. Optional.
	 * @param int $step - The step value of the slider. Optional.
	 * @param string $label - Label for slider. Optional.
	 * @param string $helper_text - Text after slider. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 * @param bool $discrete - Continuous or Discrete Slider. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_slider( $value, $value_min = 0, $value_max = 10, $step = 1, $label = '', $helper_text = '', $attributes = [], $discrete = true ) {

		/** The step value can be any positive floating-point number, or 0.
		 * When the step value is 0, the slider is considered to not have any step.
		 * A error will be thrown if you are trying to set step value to be a negative number.
		 **/
		if ( $step < 0 ) {
			$step = 0;
		}

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';

		$class = 'mdc-slider ';
		if ( $discrete ) { // Continuous or Discrete Slider
			$class .= ' mdc-slider--discrete ';
		}
		if ( isset( $attributes['class'] ) ) {
			$class = $class . ' ' . $attributes['class'];
		}
		$class = trim( $class );

        $type   = isset( $attributes['type'] ) ? $attributes['type'] : '';

		?>
        <div
	        <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
            <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
	        <?php echo ( $step ) ? 'data-step="' . esc_attr( $step ) . '"' : ''; ?>
            role="slider"
            aria-valuemin="<?php esc_attr_e( $value_min ); ?>"
            aria-valuemax="<?php esc_attr_e( $value_max ); ?>"
            aria-valuenow="<?php esc_attr_e( $value ); ?>"
            aria-label="<?php esc_attr_e( $label ); ?>">

            <!--suppress HtmlFormInputWithoutLabel -->
            <input
	            <?php echo ( $id . '-input' ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
	            <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
	            <?php echo ( $type ) ? 'type="' . esc_attr( $type ) . '"' : 'type="hidden"'; ?>
                value="<?php esc_attr_e( $value ); ?>">
            <div class="mdc-slider__track-container">
                <div class="mdc-slider__track"></div>
            </div>
            <div class="mdc-slider__thumb-container">
                <div class="mdc-slider__pin">
                    <span class="mdc-slider__pin-value-marker"></span>
                </div>
                <svg class="mdc-slider__thumb">
                    <!--suppress RequiredAttributes -->
                    <circle></circle>
                </svg>
                <div class="mdc-slider__focus-ring"></div>
            </div>
        </div>
		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;
	}

	/** @noinspection PhpUnused */
	/**
	 * Render icon field.
	 *
	 * @param $value - Value for icon. Required.
	 * @param string $label - Label for icon. Optional.
	 * @param string $helper_text - Text after icon. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
     * @param array $meta - Array with custom meta.json files with icons.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_icon( $value, $label = '', $helper_text = '', $attributes = [], $meta = [] ) {

		wp_enqueue_media(); // WordPress Image library.

	    /** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';

		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$class = 'mdc-icon-field '. $class;

		/** Get icon folder URL. */
		$icon_folder_url = UnGrabber::$url . 'images/mdc-icons/';

		/** Collect icons from all .json to one array. */
		$icons_arr = [];
		foreach ( $meta as $meta_json ) {

			/** Load icons from meta.json */
			$json = file_get_contents( $icon_folder_url . $meta_json, false, stream_context_create( ['http' => ['ignore_errors' => true] ] ) );

			/** Check for errors. */
			$headers = Helper::get_instance()->parse_headers( $http_response_header );
			if ( ! (
				isset( $headers['response_code'] ) AND
				( $headers['response_code'] >= 200 && $headers['response_code'] < 300 )
			) ) {
				echo '<div class="mdp-alert mdp-alert-warning">'. esc_html__( 'Error: Icon library not found.', 'ungrabber' ) . '</div>';
				return;
			}

			/** Decode icons to array. */
			$meta_arr = json_decode( $json, true );

			/** Collect icons from all .json to one array. */
			$icons_arr[] = $meta_arr[0];
        }

		/** Generate big json with all icons. */
		$icons_json = json_encode( $icons_arr );

		?>
        <div <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ' '; ?>>

            <?php if ( $label ) : ?>
                <div class="mdc-icon-field-label">
                    <label <?php if ( $id ) { echo 'for="' . esc_attr( $id ) . '"';} ?>>
                        <?php echo wp_kses_post( $label ); ?>
                    </label>
                </div>
	        <?php endif; ?>

            <!--suppress HtmlFormInputWithoutLabel -->
            <input
				    <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ' '; ?>
				    <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ' '; ?>
				    <?php echo ( $value ) ? 'value="' . esc_attr( $value ) . '"' : 'value="" '; ?>
                    type="hidden">

            <?php if ( is_numeric( $value ) ) : ?>
	            <?php $ico_attributes = wp_get_attachment_image_src( $value ); // Get icon by id. ?>
                <?php $src = $ico_attributes[0]; ?>
            <?php elseif( $value ) : ?>
	            <?php $src = $icon_folder_url . $value; ?>
            <?php else : ?>
                <?php $src = $value; ?>
            <?php endif; ?>

            <div class="mdc-icon-field-img-box <?php if ( $src ) : ?>mdc-with-image<?php endif; ?>">
                <div class="mdc-icon-field-image">
                    <?php if ( $src ) : ?>
                        <img src="<?php esc_attr_e( $src ); ?>" class="svg" alt="">
                    <?php endif; ?>
                </div>
                <div class="mdc-icon-field-remove" title="Remove"><i class="material-icons">delete_forever</i></div>
                <button
                        class="mdc-icon-field-library-btn mdc-button mdc-button--outlined mdc-ripple-upgraded"
                        data-library="<?php esc_attr_e( $icons_json ); ?>"
                        data-folder="<?php esc_attr_e( $icon_folder_url ); ?>"
                ><?php esc_html_e( 'Icon Library', 'ungrabber' ); ?></button>
                <button class="mdc-icon-field-upload-btn mdc-button mdc-button--outlined mdc-ripple-upgraded"><?php esc_html_e( 'Upload SVG', 'ungrabber' ); ?></button>
            </div>

        </div>
		<?php if ( $helper_text ) : ?>
            <div class="mdc-icon-field-helper-line">
                <div class="mdc-icon-field-helper-text"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;
    }

	/** @noinspection PhpUnused */
	/**
	 * Render input field.
	 *
	 * @param $value - Value for input. Required.
	 * @param string $label - Label for input. Optional.
	 * @param string $helper_text - Text after input. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_input( $value, $label = '', $helper_text = '', $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		?>

        <div class="mdc-text-field mdc-input-width mdc-text-field--outlined <?php echo ( $class ) ? esc_attr( $class ) : ''; ?>">
            <!--suppress HtmlFormInputWithoutLabel -->
            <input
                <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ' '; ?>
	            <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ' '; ?>
	            <?php echo ( $value != '' ) ? 'value="' . esc_attr( $value ) . '"' : 'value="" '; ?>
                class="mdc-text-field__input" type="text">

            <div class="mdc-notched-outline">
                <div class="mdc-notched-outline__leading"></div>

				<?php if ( $label ) : ?>
                    <div class="mdc-notched-outline__notch">
                        <label <?php if ( $id ) {
							echo 'for="' . esc_attr( $id ) . '"';
						} ?> class="mdc-floating-label"><?php echo wp_kses_post( $label ); ?></label>
                    </div>
				<?php endif; ?>

                <div class="mdc-notched-outline__trailing"></div>
            </div>

        </div>
		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;
	}

	/** @noinspection PhpUnused */
	/**
	 * Render colorpicker field.
	 *
	 * @param $value - Value for input. Required.
	 * @param string $label
	 * @param string $helper_text
	 * @param array $attributes - Additional attributes for select: id, name, class, readonly. Required.
     * Important note: id attribute is required.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_colorpicker( $value, $label = '', $helper_text = '', $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name = isset( $attributes['name'] ) ? $attributes['name'] : '';
		$readonly = isset( $attributes['readonly'] ) ? $attributes['readonly'] : '';

		$class = isset( $attributes['class'] ) ? $attributes['class'] : '';
		$class = 'mdc-text-field__input ' . $class;
		$class = trim( $class );


		?>
        <div class="mdc-text-field mdc-input-width mdc-colorpicker mdc-text-field--outlined">
            <!--suppress HtmlFormInputWithoutLabel -->
            <input
                <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
                <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
                <?php echo ( $readonly ) ? 'readonly="' . esc_attr( $readonly ) . '"' : ''; ?>
                value="<?php esc_attr_e( $value ); ?>"
                type="text">
            <div class="mdc-notched-outline">
                <div class="mdc-notched-outline__leading"
                     style="background-color: <?php esc_attr_e( $value ); ?>"></div>

				<?php if ( $label ) : ?>
                    <div class="mdc-notched-outline__notch">
                        <label <?php echo ( $id ) ? 'for="' . esc_attr( $id ) . '"' : ''; ?> class="mdc-floating-label mdc-floating-label--float-above"><?php echo wp_kses_post( $label ); ?></label>
                    </div>
				<?php endif; ?>

                <div class="mdc-notched-outline__trailing">
                    <i class="material-icons mdc-text-field__icon">colorize</i>
                </div>
            </div>
        </div>

		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;
	}

	/**
	 * Render snackbar.
	 *
	 * @see https://material-components.github.io/material-components-web-catalog/#/component/snackbar
	 * @since 1.0.0
	 * @access public
	 *
	 * @param $message - HTML message to show.
	 * @param string $design - Snackbar message design (info, error, warning)
     * @param int $timeout - Auto-close timeout (-1 or 4000-10000)
	 * @param bool $closeable - Can a user close?
	 * @param array $buttons - Additional buttons array( [ 'caption', 'link' ] )
     * @param string $class_name - CSS class name
	 **/
	public function render_snackbar( $message, $design = '', $timeout = 5000, $closeable = true, $buttons = array(), $class_name = '' ) {

		?>
        <div class="mdc-snackbar <?php echo $design === '' ? 'mdc-info' : 'mdc-' . $design; ?> <?php echo $class_name; ?>" data-timeout="<?php echo $timeout; ?>">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label" role="status"
                     aria-live="polite"><?php echo wp_kses_post( $message ); ?></div>
                    <div class="mdc-snackbar__actions">
                        <?php foreach ( $buttons as $btn) { ?>
                        <button class="mdc-button mdc-snackbar__action" type="button" onclick="window.open( '<?php echo $btn[ 'link' ]; ?>', '_blank' )" title="<?php echo $btn[ 'caption' ]; ?>"><?php echo $btn[ 'caption' ]; ?></button>
                        <?php } ?>
                        <button class="mdc-icon-button mdc-snackbar__dismiss material-icons" <?php echo !$closeable ? 'style="display: none"' : '' ?> title="<?php esc_html_e( 'Dismiss' ); ?>" type="button">close</button>
                    </div>
            </div>
        </div>
		<?php
	}

	/** @noinspection PhpUnused */
	/**
	 * Render the Switch
	 *
	 * @param string $value - Switch value on/off
	 * @param string $label - Switch label
	 * @param string $helper_text - Switch helper text
	 * @param array $attributes - Additional attributes for the switch: id, name, class. Optional.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_switches( $value, $label = '', $helper_text = '', $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = isset( $attributes['id'] ) ? $attributes['id'] : '';
		$name   = isset( $attributes['name'] ) ? $attributes['name'] : '';

		$class = isset( $attributes['class'] ) ? trim( $attributes['class'] ) : '';
		$class = ' mdc-switch ' . $class;
		if ( $value === 'on' ) {
			$class = $class . ' mdc-switch--checked ';
		}
		$class = trim( $class );
		?>

        <div <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>>
            <div class="mdc-switch__track"></div>
            <div class="mdc-switch__thumb-underlay">
                <div class="mdc-switch__thumb">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input
						<?php echo ( $id ) ? 'id="' . esc_attr( $id . '-i' ) . '"' : ''; ?>
						<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                            type='hidden'
                            value='off'>
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input
						<?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
						<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                            class="mdc-switch__native-control" type="checkbox" role="switch" <?php echo ( $value === 'on' ) ? 'checked' : ''; ?>>
                </div>
            </div>
        </div>
        <label <?php echo isset( $attributes['id'] ) ? 'for="'. esc_attr( $attributes['id'] ) .'"' : '' ?>class="mdc-switch-label"><?php echo wp_kses_post( $label ) ?></label>

		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;

	}

	/**
	 * Main UI Instance.
	 *
	 * Insures that only one instance of UI exists in memory at any one time.
	 *
	 * @static
	 * @return UI
	 * @since 1.0.0
	 **/
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof UI ) ) {
			self::$instance = new UI();
		}

		return self::$instance;

	}

} // End Class UI.
