<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * SettingsSettingn class.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.0.0
 */
class Settings_Setting {

	/**
	 * Unique Id for this setting.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $id = '';


	/**
	 * The title.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $title = '';


	/**
	 * Type of the setting.
	 *
	 * @since 2.0.0
	 * @var string button,checkbox,text,etc.
	 */
	public $type = '';


	/**
	 * The label.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $label = '';


	/**
	 * Description.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $description = '';


	/**
	 * The name for the input.
	 * @s@ince 2.0.0
	 *
	 * @var string
	 */
	public $name = '';


	/**
	 * Default value.
	 *
	 * @since 2.0.0
	 * @var mixed
	 */
	public $default = '';


	/**
	 * The value.
	 *
	 * @since 2.0.0
	 * @var mixed
	 */
	public $value = '';


	/**
	 * A link for a button.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $href = '';


	/**
	 * Array of CSS classes.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $class = array();


	/**
	 * Array of options allowed in select dropdown boxes.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $options = array();


	/**
	 * If a select dropdown box should be multiple.
	 *
	 * @since 2.0.0
	 * @var bool
	 */
	public $multiple = false;


	/**
	 * If this value should be autoloaded.
	 *
	 * @since 2.0.0
	 * @var bool
	 */
	public $autoload = false;


	/**
	 * A callback to use to sanitize the entered value.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	public $sanitize_callback = '';


	public function __construct( $args = array() ) {

		foreach ( $args as $k => $v ) {
			$this->{$k} = $v;
		}

		$this->value = $this->default;

		if ( ! empty( $this->name ) ) {
			$this->value = get_option( $this->get_option_name(), $this->default );
		}

		$this->id = uniqid();
	}


	/**
	 * Renders a settings field.
	 *
	 * @param array $args
	 *
	 * @since 2.0.0
	 *
	 */
	public function render( $args ) {

		$args = wp_parse_args( $args, array(
			'page_hook' => '',
			'label_for' => '',
			'section'   => null,
			'setting'   => null,
		) );

		/**
		 * @var string                                  $page_hook
		 * @var string                                  $label_for
		 * @var \wpbuddy\rich_snippets\Settings_Section $section
		 * @var \wpbuddy\rich_snippets\Settings_Setting $setting
		 */
		extract( $args );

		switch ( $setting->type ) {
			case 'button':
				printf(
					'<a class="button %s" href="%s">%s</a>',
					implode( ' ', $setting->class ),
					esc_url( $setting->href ),
					esc_html( $setting->label )
				);
				break;
			case 'checkbox':
				printf(
					'<input id="%1$s" type="checkbox" name="%2$s" value="1" %3$s /> <label for="%1$s">%4$s</label>',
					$label_for,
					$setting->get_option_name(),
					checked( (bool) $setting->value, true, false ),
					$setting->label
				);
				break;
			case 'select':
				$values = ! is_array( $setting->value ) ? array( $setting->value ) : $setting->value;
				printf(
					'<label for="%1$s">%3$s</label><select id="%1$s" name="%2$s%5$s" %4$s>',
					$label_for,
					$setting->get_option_name(),
					$setting->label,
					$setting->multiple ? 'size="8" multiple' : '',
					$setting->multiple ? '[]' : ''
				);
				foreach ( $setting->options as $option_value => $option_label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $option_value ),
						selected( in_array( $option_value, $values ), true, false ),
						esc_html( $option_label )
					);
				}

				print( '</select>' );
				break;
			case 'number':
			case 'input':
				printf(
					'<input id="%1$s" type="%2$s" name="%3$s" value="%4$s" class="%6$s" /> <label for="%1$s">%5$s</label>',
					$label_for,
					$setting->type,
					$setting->get_option_name(),
					esc_attr( $setting->value ),
					$setting->label,
					implode( ' ', $setting->class )
				);
				break;
		}

		if ( ! empty( $this->description ) ) {
			printf( '<p class="description">%s</p>', $this->description );
		}
	}


	/**
	 * Returns the option named (that gets saved to database).
	 *
	 * @return string
	 * @since 2.0.0
	 *
	 */
	public function get_option_name() {

		return sprintf( 'wpb_rs/setting/%s', $this->name );
	}


	/**
	 * Initializes database values.
	 *
	 * @since 2.8.3
	 */
	public function init() {
		global $wpdb;


		if ( empty( $this->name ) ) {
			return;
		}

		$option_name = $this->get_option_name();

		$value = intval( $wpdb->get_var( $wpdb->prepare(
			"SELECT option_id FROM {$wpdb->options} WHERE option_name = %s",
			$option_name
		) ) );

		if ( $value > 0 ) {
			return;
		}

		add_option(
			$option_name,
			$this->default,
			'',
			$this->autoload
		);

	}
}
