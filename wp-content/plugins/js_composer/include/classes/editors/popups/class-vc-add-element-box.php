<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Add element for VC editors with a list of mapped shortcodes.
 *
 * @since 4.3
 */
class Vc_Add_Element_Box {
	/**
	 * Enable show empty message
	 *
	 * @since 4.8
	 * @var bool
	 */
	protected $show_empty_message = false;

	/**
	 * @param $params
	 *
	 * @return string
	 */
	protected function getIcon( $params ) {
		$data = '';
		if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
			$data = ' data-is-container="true"';
		}

		return '<i class="vc_general vc_element-icon' . ( ! empty( $params['icon'] ) ? ' ' . esc_attr( sanitize_text_field( $params['icon'] ) ) : '' ) . '" ' . $data . '></i> ';
	}

	/**
	 * Single button html template
	 *
	 * @param $params
	 *
	 * @return string
	 */
	public function renderButton( $params ) {
		if ( ! is_array( $params ) || empty( $params ) ) {
			return '';
		}
		$output = $class = $class_out = $data = $category_css_classes = '';
		if ( ! empty( $params['class'] ) ) {
			$class_ar = $class_at_out = explode( ' ', $params['class'] );
			$count = count( $class_ar );
			for ( $n = 0; $n < $count; $n ++ ) {
				$class_ar[ $n ] .= '_nav';
				$class_at_out[ $n ] .= '_o';
			}
			$class = ' ' . implode( ' ', $class_ar );
			$class_out = ' ' . implode( ' ', $class_at_out );
		}
		if ( isset( $params['_category_ids'] ) ) {
			foreach ( $params['_category_ids'] as $id ) {
				$category_css_classes .= ' js-category-' . $id;
			}
		}
		if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
			$data .= ' data-is-container="true"';
		}
		$data .= ' data-vc-ui-element="add-element-button"';
		$description = ! empty( $params['description'] ) ? '<span class="vc_element-description">' . htmlspecialchars( esc_html( $params['description'] ), ENT_QUOTES, 'UTF-8' ) . '</span>' : '';
		$name = '<span data-vc-shortcode-name>' . htmlspecialchars( esc_html( stripslashes( $params['name'] ) ), ENT_QUOTES, 'UTF-8' ) . '</span>';
		$output .= '<li data-element="' . esc_attr( $params['base'] ) . '" ' . ( isset( $params['presetId'] ) ? 'data-preset="' . esc_attr( $params['presetId'] ) . '"' : '' ) . ' class="wpb-layout-element-button vc_col-xs-12 vc_col-sm-4 vc_col-md-3 vc_col-lg-2' . ( isset( $params['deprecated'] ) ? ' vc_element-deprecated' : '' ) . esc_attr( $category_css_classes ) . esc_attr( $class_out ) . '" ' . $data . '><div class="vc_el-container"><a id="' . esc_attr( $params['base'] ) . '" data-tag="' . esc_attr( $params['base'] ) . '" class="dropable_el vc_shortcode-link' . esc_attr( $class ) . '" href="javascript:;" data-vc-clickable>' . $this->getIcon( $params ) . $name . $description . '</a></div></li>';

		return $output;
	}

	/**
	 * Get mapped shortcodes list.
	 *
	 * @return array
	 * @throws \Exception
	 * @since 4.4
	 */
	public function shortcodes() {
		return apply_filters( 'vc_add_new_elements_to_box', WPBMap::getSortedUserShortCodes() );
	}

	/**
	 * Render list of buttons for each mapped and allowed VC shortcodes.
	 * vc_filter: vc_add_element_box_buttons - hook to override output of getControls method
	 * @return mixed
	 * @throws \Exception
	 * @see WPBMap::getSortedUserShortCodes
	 */
	public function getControls() {
		$output = '<ul class="wpb-content-layouts">';
		/** @var array $element */
		$buttons_count = 0;
		$shortcodes = $this->shortcodes();
		foreach ( $shortcodes as $element ) {
			if ( isset( $element['content_element'] ) && false === $element['content_element'] ) {
				continue;
			}
			$button = $this->renderButton( $element );
			if ( ! empty( $button ) ) {
				$buttons_count ++;
			}
			$output .= $button;
		}
		$output .= '</ul>';
		if ( 0 === $buttons_count ) {
			$this->show_empty_message = true;
		}

		return apply_filters( 'vc_add_element_box_buttons', $output );
	}

	/**
	 * Get categories list from mapping data.
	 * @return array
	 * @throws \Exception
	 * @since 4.5
	 */
	public function getCategories() {
		return apply_filters( 'vc_add_new_category_filter', WPBMap::getUserCategories() );
	}

	/**
	 *
	 */
	public function render() {
		vc_include_template( 'editors/popups/vc_ui-panel-add-element.tpl.php', array(
			'box' => $this,
			'template_variables' => array(
				'categories' => $this->getCategories(),
			),
		) );
	}

	/**
	 * Render icon for shortcode
	 *
	 * @param $params
	 *
	 * @return string
	 * @since 4.8
	 */
	public function renderIcon( $params ) {
		return $this->getIcon( $params );
	}

	/**
	 * @return boolean
	 */
	public function isShowEmptyMessage() {
		return $this->show_empty_message;
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function getPartState() {
		return vc_user_access()->part( 'shortcodes' )->getState();
	}
}
