<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Groovy Menu Plugin Widget for Thrive Themes - https://thrivethemes.com.
 *
 * @since 2.4.12
 */
class GroovyMenu_ThriveThemes_Element extends TCB_Element_Abstract {

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Groovy Menu', 'groovy-menu' );
	}

	/**
	 * All these elements act as placeholders
	 *
	 * @return true
	 */
	public function is_placeholder() {
		return false;
	}

	/**
	 * Get element alternate
	 *
	 * @return string
	 */
	public function alternate() {
		return 'navigation menu, nav, nav menu, groovy';
	}


	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'menu';
	}

	/**
	 * Section element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.groovy_menu_thrive_integration';
	}

	/**
	 * HTML layout of the element for when it's dragged in the canvas
	 *
	 * @return string
	 */
	protected function html() {
		$gm_html = '';
		if ( function_exists( 'groovy_menu' ) ) {
			$gm_html = '<div class="groovy_menu_thrive_integration thrv_wrapper tve-droppable tve-draggable">' . groovy_menu( [ 'gm_echo' => false ] ) . '</div>';
		}

		return $gm_html;
	}

	/**
	 * Returns the HTML placeholder for an element (contains a wrapper, and a button with icon + element name)
	 *
	 * @param string $title Optional. Defaults to the name of the current element
	 *
	 * @return string
	 */
	public function html_placeholder( $title = null ) {
		if ( empty( $title ) ) {
			$title = $this->name();
		}

		$html_template = '<div class="groovy_menu_thrive_integration thrv_wrapper tve-droppable tve-draggable"><p>' . $title . '</p></div>';

		return $html_template;
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return static::get_thrive_advanced_label();
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'borders'          => array(
				'blocked_controls' => array(
					'Corners' => __( 'This is disabled for the current element because it can have an unpredictable behaviour', 'groovy-menu' ),
				),
				'config'           => array(
					'Borders' => array(
						'important' => true,
					),
					'Corners' => array(
						'important' => true,
					),
				),
			),
			'shadow'           => array(
				'config' => array(
					'important'      => true,
					'default_shadow' => 'none',
				),
			),
			'layout'           => array(
				'disabled_controls' => array(
					'.tve-advanced-controls',
					'Height',
					'Width',
					'Alignment',
				),
			),
			'typography'       => array( 'hidden' => true ),
			'animation'        => array( 'hidden' => true ),
			'responsive'       => array( 'hidden' => true ),
			'styles-templates' => array( 'hidden' => true ),
		);
	}

	/**
	 * Element info
	 *
	 * @return string|string[][]
	 */
	public function info() {
		return array(
			'instructions' => array(
				'type' => 'help',
				'url'  => 'menu',
				'link' => 'https://grooni.com/docs/groovy-menu/',
			),
		);
	}
}
