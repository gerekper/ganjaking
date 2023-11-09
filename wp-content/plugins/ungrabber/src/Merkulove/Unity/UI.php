<?php
/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.4
 * @copyright       (C) 2018 - 2023 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\Ungrabber\Unity;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

final class UI {

	/**
	 * The one true UI.
	 *
     * @since 1.0.0
	 * @var UI
	 **/
	private static $instance;

    /**
     * CSS sides
     * @var string[]
     */
    private static $css_sides = [
        'top',
        'right',
        'bottom',
        'left'
    ];

    /**
     * CSS units
     * @var string[]
     */
    private static $css_units = [
        'px' => 'px',
        '%' => '%',
        'em' => 'em',
        'rem' => 'rem',
        'vh' => 'vh',
        'vw' => 'vw'
    ];

    /**
     * Render select field.
     *
     * @param array $options - Options for select. Required.
     * @param string $selected - Selected value. Optional.
     * @param string $label - Label for select. Optional.
     * @param string $helper_text - Text after select. Optional.
     * @param array $attributes - Additional attributes for select: id, name, class. Optional.
     *
     * @return void
     **@since 1.0.0
     * @access public
     *
     */
    public function render_select( array $options, string $selected = '', string $label = '', string $helper_text = '', array $attributes = [] ) {

        if ( ! count( $options ) ) { return; }

        /** Prepare html attributes. */
        $name = $attributes['name'] ?? '';
        $id   = $attributes['id'] ?? 'mdp-' . uniqid( '', true );

        $class = isset( $attributes['class'] ) ? $attributes['class'] : '';
        $class = 'mdc-select mdc-select-width mdc-select--outlined ' . $class;
        $class = trim( $class );

        /** Check selected option. If we don't have it, select first one. */
        if ( ! array_key_exists( $selected,  $options ) ) {

            reset( $options );

            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $selected = key( $options );

        }
        ?>

        <div class="<?php echo esc_attr( $class ); ?>">
            <input type="hidden"
                   <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
                   <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                   value="<?php echo esc_attr( $selected ); ?>"
            >

            <div class="mdc-select__anchor mdc-select-width">
                <i class="mdc-select__dropdown-icon"></i>
                <div id="<?php echo esc_attr( $id ) ?>-text" class="mdc-select__selected-text" aria-labelledby="outlined-select-label"><?php esc_html_e( $options[$selected] ); ?></div>
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <?php if ( $label ) : ?>
                            <span id="<?php echo esc_attr( $id ) ?>-label" class="mdc-floating-label"><?php echo esc_html( $label ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>

            <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-select-width" role="listbox">
                <ul class="mdc-list">
                    <?php foreach ( $options as $key => $value ) : ?>
                        <?php $selected_class = ( $key === $selected ) ? 'mdc-list-item--selected' : ''; ?>
                        <li class="mdc-list-item <?php echo esc_attr( $selected_class ); ?>" data-value="<?php echo esc_attr( $key ) ?>" role="option"><?php echo wp_kses_post( $value ) ?></li>
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

	/**
	 * Render slider field.
	 *
	 * @param $value - Current value. Required.
	 * @param $value_min - The min value the slider can have. Optional.
	 * @param $value_max - The max value the slider can have. Optional.
	 * @param $step - The step value of the slider. Optional.
	 * @param string $label - Label for slider. Optional.
	 * @param string $helper_text - Text after slider. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 * @param bool $discrete - Continuous or Discrete Slider. Optional.
	 *
     * @return void
     * @noinspection PhpMissingParamTypeInspection
     */
	public function render_slider( $value, $value_min = 0, $value_max = 10, $step = 1, string $label = '', string $helper_text = '', array $attributes = [], bool $discrete = true ) {

        /** The step value can be any positive floating-point number, or 0.
         * When the step value is 0, the slider is considered to not have any step.
         * A error will be thrown if you are trying to set step value to be a negative number.
         **/
        if ( $step < 0 ) {
            $step = 0;
        }

        /** Prepare html attributes. */
        $id   = $attributes['id'] ?? '';
        $name = $attributes['name'] ?? '';

        $class = 'mdc-slider mdc-slider-width ';
        if ( $discrete ) { // Continuous or Discrete Slider
            $class .= ' mdc-slider--discrete ';
        }
        if ( isset( $attributes['class'] ) ) {
            $class .= ' ' . $attributes[ 'class' ];
        }
        $class = trim( $class );

        $type   = $attributes['type'] ?? '';

        ?>
        <div
            <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
            <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
            <?php echo ( $step ) ? 'data-step="' . esc_attr( $step ) . '"' : ''; ?>
                role="slider"
                aria-valuemin="<?php echo esc_attr( $value_min ); ?>"
                aria-valuemax="<?php echo esc_attr( $value_max ); ?>"
                aria-valuenow="<?php echo esc_attr( $value ); ?>"
                aria-label="<?php echo esc_attr( $label ); ?>">

            <!--suppress HtmlFormInputWithoutLabel -->
            <input
                <?php echo ( $id . '-input' ) ? 'id="' . esc_attr( $id . '-input' ) . '"' : ''; ?>
                <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                <?php echo ( $type ) ? 'type="' . esc_attr( $type ) . '"' : 'type="hidden"'; ?>
                    value="<?php echo esc_attr( $value ); ?>">
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

	/**
	 * Render input field.
	 *
	 * @param $value - Value for input. Required.
	 * @param string $label - Label for input. Optional.
	 * @param string $helper_text - Text after input. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 *
     * @return void
     */
	public function render_input( $value, string $label = '', string $helper_text = '', array $attributes = [] ) {

        /** Prepare html attributes. */
        $id   = $attributes['id'] ?? '';
        $name = $attributes['name'] ?? '';
        $class = $attributes['class'] ?? '';
        $maxlength = $attributes['maxlength'] ?? '';
        $spellcheck = $attributes['spellcheck'] ?? '';
        $required = $attributes['required'] ?? '';
        ?>

        <div class="mdc-text-field mdc-input-width mdc-text-field--outlined <?php echo ( $class ) ? esc_attr( $class ) : ''; ?>">
            <!--suppress HtmlFormInputWithoutLabel -->
            <input
                <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ' '; ?>
                <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ' '; ?>
                <?php echo ( $maxlength ) ? 'maxlength="' . esc_attr( $maxlength ) . '"' : ' '; ?>
                <?php echo ( $spellcheck ) ? 'spellcheck="' . esc_attr( $spellcheck ) . '"' : ' '; ?>
                <?php echo ( $required ) ? 'required="' . esc_attr( $required ) . '"' : ' '; ?>
                <?php echo ( $value !== '' ) ? 'value="' . esc_attr( $value ) . '"' : 'value="" '; ?>
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

	/**
	 * Render color-picker field.
	 *
	 * @param $value - Value for input. Required.
	 * @param string $label
	 * @param string $helper_text
	 * @param array $attributes - Additional attributes for select: id, name, class, readonly. Required.
     * Important note: id attribute is required.
	 *
     * @return void
     */
	public function render_colorpicker( $value, string $label = '', string $helper_text = '', array $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = $attributes['id'] ?? '';
		$name = $attributes['name'] ?? '';
		$readonly = $attributes['readonly'] ?? '';

		$class = $attributes['class'] ?? '';
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
                value="<?php echo esc_attr( $value ); ?>"
                type="text">
            <div class="mdc-notched-outline">
                <div class="mdc-notched-outline__leading"
                     style  =  " background-color: <?php echo esc_attr( $value ); ?>"></div>

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
	 * @access public
	 *
	 * @param $message - HTML message to show.
	 * @param string $design - Snackbar message design (info, error, warning)
     * @param int $timeout - Auto-close timeout (-1 or 4000-10000)
	 * @param bool $closeable - Can a user close?
	 * @param array $buttons - Additional buttons array( [ 'caption', 'link' ] )
     * @param string $class_name - CSS class name
     *
     * @return void
     **/
	public function render_snackbar( $message, string $design = '', int $timeout = 5000, bool $closeable = true, array $buttons = [], string $class_name = '' ) {
		?>
        <div class="mdc-snackbar <?php echo ( $design === '' ) ? 'mdc-info' : 'mdc-' . esc_attr( $design ); ?> <?php echo esc_attr( $class_name ); ?>" data-timeout="<?php echo esc_attr( $timeout ); ?>">
            <div class="mdc-snackbar__surface">
                <div class="mdc-snackbar__label" role="status"
                     aria-live="polite"><?php echo wp_kses_post( $message ); ?></div>
                    <div class="mdc-snackbar__actions">
                        <?php foreach ( $buttons as $btn) : ?>
                            <button class="mdc-button mdc-snackbar__action" type="button" onclick="window.open( '<?php echo esc_attr( $btn[ 'link' ] ); ?>', '_blank' )" title="<?php echo esc_attr( $btn[ 'caption' ] ); ?>"><?php esc_html_e( $btn[ 'caption' ] ); ?></button>
                        <?php endforeach; ?>
                        <?php if ( $closeable ) : ?>
                            <button class="mdc-icon-button mdc-snackbar__dismiss material-icons" title="<?php esc_html_e( 'Dismiss' ); ?>" type="button">close</button>
                        <?php endif; ?>
                    </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Render button with icon.
	 *
	 * @param string $label - Label used as button value. Required.
	 * @param string $helper_text - Text after button. Optional.
     * @param string|false $icon - Icon ligature.
	 * @param array $attributes - Additional attributes for button: id, name, classes. Optional.
	 *
     * @return void
     */
	public function render_button( string $label = '', string $helper_text = '', $icon = false, array $attributes = [] ) {

		/** Prepare variables. */
		$id   = $attributes['id'] ?? '';
		$name = $attributes['name'] ?? '';
		$class = $attributes['class'] ?? '';
		$class = 'mdc-button ' . $class . ' mdc-ripple-upgraded';
		$class = trim( $class );
		?>

        <button
			<?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
			<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
			<?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
			<?php echo ( $label ) ? 'value="' . esc_attr( $label ) . '"' : ''; ?>
        >
            <span class="mdc-button__ripple"></span>
			<?php if ( $icon ) : ?>
                <i class="material-icons mdc-button__icon" aria-hidden="true"><?php esc_html_e( $icon ); ?></i>
			<?php endif; ?>

			<?php esc_html_e( $label ); ?>
        </button>

		<?php if ( $helper_text ) : ?>
            <div class="mdc-select-helper-text mdc-select-helper-text--persistent">
				<?php echo wp_kses_post( $helper_text ); ?>
            </div>
		<?php endif;

	}

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
     **/
    public function render_switcher( string $value, string $label = '', string $helper_text = '', array $attributes = [] ) {

        /** Prepare html attributes. */
        $id   = $attributes['id'] ?? '';
        $name = $attributes['name'] ?? '';

        $class = isset( $attributes['class'] ) ? trim( $attributes['class'] ) : '';
        $class = ' mdc-switch ' . $class;
        if ( $value === 'on' ) {
            $class .= ' mdc-switch--checked ';
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
        <label <?php echo isset( $attributes['id'] ) ? 'for="'. esc_attr( $attributes['id'] ) .'"' : '' ?>class="mdc-switch-label"><?php echo esc_html( $label ) ?></label>

        <?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-switcher-helper mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
        <?php endif;

    }

    /**
     * Render Header
     *
     * @param string $header - Header text.
     * @param string $description - Description. Optional.
     * @param string $tag - Header tag: h1-h6
     *
     * @return void
     */
    public function render_header( string $header, string $description = '', string $tag = 'h3' ) {

	    echo wp_sprintf(
		    '<%1$s class="mdc-typography--headline5">%2$s</%1$s>',
		    esc_attr( $tag ),
		    wp_kses_post( $header )
	    );

	    if ( $description ) {
		    echo wp_sprintf(
			    '<p class="mdc-typography--body2">%s</p>',
			    wp_kses_post( $description )
		    );
	    }

    }

    /**
     * Render textarea field.
     *
     * @param $value - Value for textarea. Required.
     * @param string $label - Label for textarea. Optional.
     * @param string $helper_text - Text after textarea. Optional.
     * @param array $attributes - Additional attributes for select: id, name, class. Optional.
     *
     * @since 1.0.0
     * @access public
     **/
    public function render_textarea( $value, string $label = '', string $helper_text = '', array $attributes = [] ) {

        /** Prepare html attributes. */
        $id   = $attributes['id'] ?? '';
        $name = $attributes['name'] ?? '';

        $class = $attributes['class'] ?? '';
        $class = ' mdc-text-field__input ' . $class;
        $class = trim( $class );

        ?>
        <div class="mdc-text-field mdc-text-field--textarea">
            <!--suppress HtmlFormInputWithoutLabel -->
            <textarea
                <?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ' '; ?>
                <?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ' '; ?>
                <?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ' '; ?>
            ><?php echo esc_textarea( $value ); ?></textarea>
            <div class="mdc-notched-outline mdc-notched-outline--upgraded">
                <div class="mdc-notched-outline__leading"></div>

                <?php if ( $label ) : ?>
                    <div class="mdc-notched-outline__notch">
                        <label <?php if ( $id ) { echo 'for="' . esc_attr( $id ) . '"'; } ?> class="mdc-floating-label"><?php echo wp_kses_post( $label ); ?></label>
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
    public function render_icon( $value, string $label = '', string $helper_text = '', array $attributes = [], array $meta = [] ) {

        wp_enqueue_media(); // WordPress Image library.

        /** Prepare html attributes. */
        $id   = $attributes['id'] ?? '';
        $name = $attributes['name'] ?? '';

        $class = $attributes['class'] ?? '';
        $class = 'mdc-icon-field '. $class;

        /** Get icon folder URL. */
        $icon_folder_url = Plugin::get_url() . 'images/mdc-icons/';

        /** Collect icons from all .json to one array. */
        $icons_arr = [];
        foreach ( $meta as $meta_json ) {

            /** Load icons from meta.json */
            $json = wp_remote_get( $icon_folder_url . $meta_json, array(
                'timeout' => 15,
                'sslverify'  => Settings::get_instance()->options[ 'check_ssl' ] === 'on'
            ) );
            if ( is_wp_error( $json ) ){
                echo wp_kses_post( $json->get_error_message() );
            }

            /** Decode icons to array. */
            $meta_arr = json_decode( $json['body'], true );

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
                        <img src="<?php echo esc_url( $src ); ?>" class="svg" alt="">
                    <?php endif; ?>
                </div>
                <div class="mdc-icon-field-remove" title="Remove"><i class="material-icons">delete_forever</i></div>
                <button
                        class="mdc-icon-field-library-btn mdc-button mdc-button--outlined mdc-ripple-upgraded"
                        data-library="<?php echo esc_attr( $icons_json ); ?>"
                        data-folder="<?php echo esc_attr( $icon_folder_url ); ?>"
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

	/**
	 * Render Media Library control
	 *
	 * @param $value
	 * @param string $helper_text
	 * @param array $attributes
	 *
	 * @return void
	 */
	public function render_media_library( $value, string $helper_text = '', array $attributes = [] ) {

		wp_enqueue_media();

		/** Prepare html attributes. */
		$id   = $attributes['id'] ?? '';
		$name = $attributes['name'] ?? '';

		$class = $attributes['class'] ?? '';
		$class = 'mdc-media-library-field ' . $class;

		/** @noinspection HtmlUnknownTarget */
		$image = intval( $value ) > 0 ?
			wp_get_attachment_image(
				$value,
				'medium',
				false,
				array(
					'id' => $id . '-preview-image'
				)
			) :
			wp_sprintf(
				'<img src="%1$s" id="%2$s" alt="%3$s" />',
				esc_url( Plugin::get_url() . 'src/Merkulove/Unity/assets/images/ui/empty.svg' ),
				$id . '-preview-image',
				esc_html__( 'Preview image' , 'ungrabber' )
			);

		?><div class="<?php echo esc_attr( $class ); ?>" data-uid="<?php esc_attr_e( $id ); ?>"><?php

		echo wp_sprintf(
			'<input id="%s" name="%s" value="%s" type="hidden">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value )
		);

		echo wp_sprintf(
			'<div class="mdc-media-library-field-img-box">
					<div class="mdc-media-library-field-image">%1$s</div>
					<button id="%3$s-remove" class="mdc-media-library-field-remove" title="%2$s">
						<i class="material-icons">delete_forever</i>
					</button>
					<button id="%3$s-add" class="mdc-media-library-field-btn mdc-button mdc-button--outlined mdc-ripple-upgraded">
						<span class="dashicons dashicons-admin-media"></span>%4$s
					</button>
				</div>',
			$image,
			esc_attr__( 'Remove', 'ungrabber' ),
			esc_attr( $id ),
			esc_html__( 'Media library', 'ungrabber' )
		);

		?></div><?php

		if ( $helper_text ) {

			echo wp_sprintf(
				'<div class="mdc-media-library-field-helper-line">
					<div class="mdc-media-library-field-helper-text">%s</div>
				</div>',
				wp_kses_post( $helper_text )
			);

		}

	}

	/**
	 * Render chosen select field.
	 *
	 * @param array $options     - Options for select. Required.
	 * @param array|string $selected    - Selected value. Optional.
	 * @param string $helper_text - Text after select. Optional.
	 * @param array $attributes  - Additional attributes for select: id, name, class. Optional.
	 *
	 * @return void
	 */
	public function render_chosen( array $options, $selected = [], string $helper_text = '', array $attributes = [] ) {

		/** Prepare html attributes for single chosen mode. */
		if ( ! is_array( $selected ) ) { $selected = array( $selected ); }
		array_filter( $selected );

		/** Prepare html attributes. */
		$name = $attributes['name'] ?? '';
		$multiple = $attributes['multiple'] ?? '';

		/** Save array for multiple values. */
		if ( 'multiple' === $multiple ) {
			$name .= '[]';
		}

		$class = $attributes['class'] ?? '';
		$class = 'mdp-chosen chosen-select ' . $class;
		$class = trim( $class );

		?>
		<div>
			<?php
			/** @noinspection HtmlWrongAttributeValue */
			/** @noinspection HtmlUnknownAttribute */
			echo wp_sprintf(
				'<select id="%1$s" name="%2$s" %3$s data-placeholder="%4$s" class="%5$s">',
				esc_attr( $attributes['id'] ?? '' ),
				esc_attr( $name ),
				esc_attr( $multiple ),
				esc_attr( $attributes['placeholder'] ?? '' ),
				esc_attr( $class )
			);

			foreach ( $options as $key => $value ) :

				$isSelected = ( in_array( $key, $selected, true ) ) ? 'selected=""' : '';

				/** @noinspection HtmlUnknownAttribute */
				echo wp_sprintf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $key ),
					esc_attr( $isSelected ),
					esc_html( $value )
				);

			endforeach;

			echo '</select>';
			?>
		</div>
		<?php if ( $helper_text ) : ?>
			<div class="mdc-text-field-helper-line">
				<div class="mdc-select-helper-text mdc-select-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
			</div>
		<?php endif;
	}

	/**
	 * Render layout box
	 *
	 * @param array $layouts - Options list. Required.
	 * @param string $value - Value for layout box. Required.
	 * @param string $label - Label for select. Optional
	 * @param string $helper_text - Helper Text displaying after layout control. Optional.
	 * @param array $attributes - Additional attributes for select: id, name, class. Optional.
	 */
	public function render_layouts( array $layouts , string $value, string $label = '', string $helper_text = '', array $attributes = [] ) {

		/** Prepare html attributes. */
		$id   = $attributes['id'] ?? '';
		$name = $attributes['name'] ?? '';
		$class = trim( $attributes['class'] ?? '' );

		?>

        <input
			<?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
			<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
			<?php echo ( $class ) ? 'class="' . esc_attr( $class ) . '"' : ''; ?>
                value="<?php echo esc_attr( $value ); ?>"
                type="hidden" >

        <div class="mdp-button-dropdown mdp-layout mdc-select-width" data-mdp-dropdown="{mode:'click'}">

            <button class="mdp-button mdp-button-image" data-val="<?php echo esc_attr( $value ); ?>">
                <img src="<?php echo esc_attr( Plugin::get_url() . 'images/fields/layout/' . esc_attr( $value ) . '.svg' ); ?>" alt="">
                <i class="mdc-select__dropdown-icon"></i>
            </button>

            <div class="mdp-dropdown mdp-dropdown-width-2 mdp-dropdown-bottom">
                <ul class="mdp-nav mdp-nav-dropdown">

					<?php
					foreach( $layouts as $val => $alt) {
						?>
                        <li>
                            <a href="#" data-val="<?php echo esc_attr( $val )?>" <?php if ( $val === $value ) { echo 'class="mdp-active"'; } ?>>
                                <img src="<?php echo esc_attr( Plugin::get_url() . 'images/fields/layout/' . esc_attr( $val ) . '.svg' ); ?>" alt="<?php echo esc_html( $alt ); ?>">
                            </a>
                        </li>
						<?php
					}
					?>

                </ul>
            </div>

        </div>

		<?php if ( $helper_text ) : ?>
            <div class="mdc-text-field-helper-line">
                <div class="mdc-text-field-helper-text mdc-text-field-helper-text--persistent"><?php echo wp_kses_post( $helper_text ); ?></div>
            </div>
		<?php endif;

	}

	/**
	 * Render select field with helper images.
	 *
	 * @param        $options     - Options for select. Required.
	 * @param string $selected    - Selected value. Optional.
	 * @param string $label       - Label for select. Optional.
	 * @param string $helper_text - Text after select. Optional.
	 * @param array $attributes  - Additional attributes for select: id, name, class. Optional.
	 *
	 * @return void
	 */
	public function render_select_img( $options, string $selected = '', string $label = '', string $helper_text = '', array $attributes = [] ) {

		if ( ! count( $options ) ) { return; }

		/** Prepare html attributes. */
		$name = $attributes['name'] ?? '';
		$id   = $attributes['id'] ?? '';

		$class = $attributes['class'] ?? '';
		$class = trim( 'mdc-select mdc-select-width mdc-select--outlined ' . $class );

		/** Check selected option. If we don't have it, select first one. */
		if ( ! array_key_exists( $selected,  $options ) ) {
			reset( $options );
			$selected = key( $options );
		}
		?>

        <div class="<?php echo esc_attr( $class ); ?>">
            <input
                    type="hidden"
				<?php echo ( $id ) ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
				<?php echo ( $name ) ? 'name="' . esc_attr( $name ) . '"' : ''; ?>
                    value="<?php echo esc_attr( $selected ); ?>"
            >

            <div class="mdc-select__anchor mdc-select-width">
                <i class="mdc-select__dropdown-icon"></i>
                <div id="demo-selected-text" class="mdc-select__selected-text" aria-labelledby="outlined-select-label"><?php esc_html_e( $options[$selected] ); ?></div>
                <div class="mdc-notched-outline">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
						<?php if ( $label ) : ?>
                            <span id="outlined-select-label" class="mdc-floating-label"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>

            <div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-select-width" role="listbox">
                <ul class="mdc-list">
					<?php foreach ( $options as $key => $value ) : ?>
						<?php $selected_class = ( $key === $selected ) ? 'mdc-list-item--selected' : ''; ?>
                        <li class="mdc-list-item <?php echo esc_attr( $selected_class ); ?>" data-value="<?php echo esc_attr( $key ) ?>" role="option">
							<?php $img_file = Plugin::get_path() . 'images/fields/select/' . $key . '.svg'; ?>
							<?php if ( file_exists( $img_file ) ) : ?>
                                <img src="<?php echo esc_url( Plugin::get_url() . "images/fields/select/" . $key . ".svg" ); ?>" alt="<?php echo esc_attr( $key ); ?>" >
							<?php endif; ?>
							<?php echo esc_attr( $value ) ?>
                        </li>
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

    /**
     * Render Drag and Drop field.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     *
     * @noinspection PhpUnused
     **/
    public function render_import( $value, $label, $helper, $key, $tab ) {

        $key_exist = $value !== '';
        ?>
        <div class="mdp-dnd">
            <!--suppress HtmlFormInputWithoutLabel -->
            <div class="mdc-text-field mdc-input-width mdc-text-field--outlined mdc-hidden">
                <!--suppress HtmlFormInputWithoutLabel -->
                <input  type="text"
                        class="mdc-text-field__input mdp-drop-zone-input"
                        name="mdp_ungrabber_<?php echo esc_attr( $tab ); ?>_settings[<?php echo esc_attr( $key ); ?>]"
                        id="mdp-ungrabber-settings-dnd-<?php echo esc_attr( $key ); ?>"
                        value="<?php esc_attr_e( $value ); ?>"
                >
                <div class="mdc-notched-outline mdc-notched-outline--upgraded mdc-notched-outline--notched">
                    <div class="mdc-notched-outline__leading"></div>
                    <div class="mdc-notched-outline__notch">
                        <label for="mdp-ungrabber-settings-dnd-<?php echo esc_attr( $key ); ?>" class="mdc-floating-label mdc-floating-label--float-above"><?php echo esc_html( $label ); ?></label>
                    </div>
                    <div class="mdc-notched-outline__trailing"></div>
                </div>
            </div>
            <div id="mdp-<?php echo esc_attr( $key ); ?>-drop-zone" class="<?php if ( $key_exist ) : ?>mdp-key-uploaded <?php endif; ?>mdp-drop-zone">
                <?php if ( $key_exist ) : ?>
                    <span class="material-icons">check_circle_outline</span><?php esc_html_e( 'File exist', 'ungrabber' ); ?>
                    <span class="mdp-drop-zone-hover"><?php esc_html_e( 'Drop file here or click to upload', 'ungrabber' ); ?></span>
                <?php else : ?>
                    <span class="material-icons">cloud</span><?php esc_html_e( 'Drop file here or click to upload.', 'ungrabber' ); ?>
                <?php endif; ?>
            </div>
            <?php if ( $key_exist ) : ?>
                <div class="mdp-messages mdc-text-field-helper-line mdc-text-field-helper-text mdc-text-field-helper-text--persistent">
                    <?php esc_html_e( 'Drag and drop or click on the form to replace file. |', 'ungrabber' ); ?>
                    <a href="#" class="mdp-reset-key-btn"><?php esc_html_e( 'Reset', 'ungrabber' ); ?></a>
                </div>
            <?php else : ?>
                <div class="mdp-messages mdc-text-field-helper-line mdc-text-field-helper-text mdc-text-field-helper-text--persistent">
                    <?php esc_html_e( 'Drag and drop or click on the form to add file', 'ungrabber' ); ?>
                </div>
            <?php endif; ?>
            <input id="mdp-dnd-file-input" type="file" name="name" class="mdc-hidden" />
        </div>
        <?php

    }

    public function render_file_dnd( $key, $tab, $field ) {

        $file_exist = isset( Settings::get_instance()->options[ $key ] ) && Settings::get_instance()->options[ $key ] !== '';
        ?>
        <div class="mdp-dnd mdp-file">
            <?php

            // Input field.
            echo wp_sprintf(
                '<div class="mdc-text-field mdc-input-width mdc-text-field--outlined mdc-hidden">
					<input  type="text"
							class="mdc-text-field__input mdp-drop-zone-input"
							name="mdp_speaker_%1$s_settings[%2$s]"
							id="mdp-speaker-settings-dnd-%2$s"
							value="%3$s"
					>
					<div class="mdc-notched-outline mdc-notched-outline--upgraded mdc-notched-outline--notched">
						<div class="mdc-notched-outline__leading"></div>
						
						<div class="mdc-notched-outline__notch">
							<label for="mdp-speaker-settings-dnd-%2$s" class="mdc-floating-label mdc-floating-label--float-above">%4$s</label>
						</div>
						<div class="mdc-notched-outline__trailing"></div>
					</div>
				</div>',
                esc_attr( $tab ),
                esc_attr( $key ),
                esc_attr( Settings::get_instance()->options[ $key ] ?? $field[ 'default' ] ?? '' ),
                isset( $field[ 'label' ] ) ? esc_html( $field[ 'label' ] ) : esc_html__( 'File', 'ungrabber' )
            );

            // Drop zone.
            echo wp_sprintf(
                '<div id="mdp-%1$s-drop-zone" class="mdp-drop-zone%2$s">',
                esc_attr( $key ),
                $file_exist ? ' mdp-key-uploaded' : ''
            );

            if ( $file_exist ) :

                echo wp_sprintf(
                    '<span class="material-icons">check_circle_outline</span>%1$s<span class="mdp-drop-zone-hover">%2$s</span>',
                    esc_html__( 'File exist', 'ungrabber' ),
                    esc_html__( 'Drop file here or click to upload', 'ungrabber' )
                );

            else :

                echo wp_sprintf(
                    '<span class="material-icons">cloud</span>%1$s',
                    esc_html__( 'Drop file here or click to upload.', 'ungrabber' )
                );

            endif;
            echo '</div>';

            // Helper text.
            echo '<div class="mdp-messages mdc-text-field-helper-line mdc-text-field-helper-text mdc-text-field-helper-text--persistent">';

            if ( $file_exist ) :

                echo wp_sprintf(
                    '%1$s | <a href="#" class="mdp-reset-key-btn">%2$s</a> %3$s',
                    esc_html__( 'Drag and drop or click on the form to replace file.', 'ungrabber' ),
                    esc_html__( 'Reset', 'ungrabber' ),
	                $field[ 'description' ] ?? ''
                );

            else :

                $file_types = implode( ', ', $field[ 'file_types' ] ?? array() );
                echo wp_sprintf(
                    '%1$s %2$s<span class="mdp-file--file-types">%3$s</span>',
                    esc_html__( 'Drag and drop or click on the form to add file.', 'ungrabber' ),
                    $field[ 'description' ] ?? '',
                    isset( $field[ 'file_types' ] )
                        ? esc_html__( 'Allowed file types:', 'ungrabber' ) . ' <b>' . esc_attr( $file_types ) . '</b>' : ''
                );

            endif;
            echo '</div>';

            ?>
            <input id="mdp-dnd-file-input" type="file" name="name" class="mdc-hidden" />
        </div>
        <?php

    }

    /**
     * Side dimension controls for CSS properties( top, right, bottom, left ).
     * @param $key      - field slug name
     * @param $tab_slug - slug of the tab
     * @param $field    - filed settings from Config.php
     *
     * @return void
     */
    public function render_sides( $key, $tab_slug, $field ) {

        $options = Settings::get_instance()->options;
        $default = $field[ 'default' ] ?? array();

        $linked = $this->get_sides_linked( $options, $key, $default ); // linked value
        $values = $this->get_sides_values( $options, $key, $default ); // values for each side
        $unit = $this->get_sides_unit( $options, $key, $default ); // unit value

        ?><div class="mdp-controls-sides"><?php

        echo wp_sprintf( '<div class="mdp-controls-sides-single mdp-controls-sides-lock%1$s" title="%2$s">
                <input id="%3$s" name="%4$s" value="%5$s" type="hidden">
            </div>',
            'true' === $linked ? ' active-lock' : '',
            esc_html__('Linked', 'ungrabber' ),
            $this->get_control_id( $key . '_linked', $tab_slug, $field ),
            $this->get_control_name( $key . '_linked', $tab_slug, $field ),
            esc_attr( $linked )
        );

        foreach ( self::$css_sides as $number_key ) {
            ?><div class="mdp-controls-sides-single mdp-controls-sides-<?php esc_attr_e( $number_key ); ?>"><?php
            UI::get_instance()->render_input(
                $values[ $number_key ],
	            $field[ 'labels' ][ $number_key ] ?? esc_html__( $number_key, 'ungrabber' ),
                '',
                [
                    'id' => $this->get_control_id( $key . '_' . $number_key, $tab_slug, $field ),
                    'name' => $this->get_control_name( $key . '_' . $number_key, $tab_slug, $field ),
                ]
            );
            ?></div><?php
        }

        UI::get_instance()->render_select(
            self::$css_units,
            $unit,
            esc_html__( 'Units', 'ungrabber' ),
            '',
            [
                'id' => $this->get_control_id( $key . '_unit', $tab_slug, $field ),
                'name' => $this->get_control_name( $key . '_unit', $tab_slug, $field ),
            ]
        );

        ?></div><?php

    }

    /**
     * Get control id for markup
     *
     * @param $key
     * @param $tab_slug
     * @param $field
     *
     * @return mixed|null
     */
    private function get_control_id( $key, $tab_slug, $field ) {

        $id = 'mdp_ungrabber_' . $tab_slug . '_settings_' . $key;
        return apply_filters( 'mdp_ungrabber_settings_control_id', $id, $key, $tab_slug, $field );

    }

    /**
     * Get control name for markup
     *
     * @param $key
     * @param $tab_slug
     * @param $field
     *
     * @return mixed|null
     */
    private function get_control_name( $key, $tab_slug, $field ) {

        $name = 'mdp_ungrabber_' . $tab_slug . '_settings[' . $key . ']';
        return apply_filters( 'mdp_ungrabber_settings_control_name', $name, $key, $tab_slug, $field );

    }

	/**
	 * Get values for side dimension controls.
	 *
	 * @param $options      - all settings
	 * @param $slug         - slug of the setting
	 * @param array $default      - default value for the setting
	 *
	 * @return array
	 */
	private function get_sides_values( $options, $slug, array $default = array() ): array {

		$empty = 0;
		$values = array();
		foreach ( self::$css_sides as $side ) {

			if ( isset( $options[ $slug . '_' . $side ] ) ) {

				// Value from DB
				$values[ $side ] = $options[ $slug . '_' . $side ];

			} else if ( isset( $options[ $slug ] ) /*&& ! isset( $options[ $slug . '_unit' ] )*/ ) {

				if ( is_array( $options[ $slug ] ) ) {

					// Proper default settings
					$values[ $side ] = $options[ $slug ][ $side ] ?? $empty;

				} else {

					// Migration from old version
					$values[ $side ] = $options[ $slug ];

				}

			} else {

				// Default value
				$values[ $side ] = $default[ $side ] ?? $empty;

			}

		}

		return apply_filters( 'ungrabber_settings_side_values', $values, $slug );

	}

    /**
     * Get unit value for side dimension controls
     *
     * @param $options              - all settings
     * @param $slug                 - slug of the setting
     * @param array $default        - default value for unit
     *
     * @return mixed|null
     */
    private function get_sides_unit( $options, $slug, array $default = array() ) {

        $unit = $options[ $slug . '_unit' ] ?? $default[ 'unit' ] ?? 'px';

        return apply_filters( 'ungrabber_settings_sides_unit', $unit, $slug );

    }

    /**
     * Get linked value for side dimension controls
     *
     * @param $options          - all settings
     * @param $slug             - slug of the setting
     * @param array $default    - default value for linked
     *
     * @return mixed|null
     */
    private function get_sides_linked( $options, $slug, array $default = array() ) {

        $linked = $options[ $slug . '_linked' ] ?? $default[ 'linked' ] ?? 'false';

        return apply_filters( 'ungrabber_settings_sides_linked', $linked, $slug );

    }

	/**
	 * Slider with units control
	 *
	 * @param $key
	 * @param $tab_slug
	 * @param $field
	 *
	 * @return void
	 */
	public function render_unit_slider( $key, $tab_slug, $field ) {

		$options = Settings::get_instance()->options;

		echo '<div class="mdp-controls-unit-slider">';

		echo '<div class="mdp-controls-unit-slider-slider">';
		UI::get_instance()->render_slider(
			$options[ $key ] ?? $field[ 'default' ] ?? 0,
			$field[ 'min' ],
			$field[ 'max' ],
			$field[ 'step' ],
			$field[ 'label' ] ?? '',
			$this->unit_slider_helper_value( $options, $key, $field ),
			TabGeneral::get_instance()->prepare_attr( $key, $tab_slug, $field ),
			$field[ 'discrete' ]
		);
		echo '</div>';

		echo '<div class="mdp-controls-unit-slider-units">';
		UI::get_instance()->render_select(
			$field[ 'units' ] ?? self::$css_units,
			$options[$key . '_unit'] ?? '%', // Selected option.
			esc_html__( 'Unit', 'ungrabber' ),
			'',
			[
				'id' => 'mdp_ungrabber_' . $tab_slug . '_settings_' . $key . '_unit',
				'name' => 'mdp_ungrabber_' . $tab_slug . '_settings[' . $key . '_unit' . ']',
			]
		);
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Slider with units control
	 *
	 * @param $options  - all settings
	 * @param $key      - key of the setting
	 * @param $field    - field of the setting
	 *
	 * @return string
	 */
	private function unit_slider_helper_value( $options, $key, $field ): string {

		$value = $options[ $key ] ?? $field[ 'default' ] ?? 0;
		$unit = $options[ $key . '_unit' ] ?? $field[ 'default_unit' ] ?? '%';

		return wp_sprintf(
			'%s <strong>%s</strong> %s',
			$field[ 'description' ] ?? '',
			esc_attr( $value ),
			esc_attr( $unit )
		);

	}

	/**
	 * Main UI Instance.
     *
     * @return UI
     **/
	public static function get_instance(): UI {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

			self::$instance = new self;

		}

		return self::$instance;

	}

}
