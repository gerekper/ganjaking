<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAPL_Label
 *
 * Create product label object
 *
 * @class		WAPL_Label
 * @author		Jeroen Sormani
 * @package		WooCommerce Advanced Product Labels
 * @version		1.0.0
 */
class WAPL_Label {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @deprecated 1.1.0
	 */
	public function __construct( $type = 'label', $text = '', $style = '', $align = '', $style_attr = '' ) {
		_deprecated_function( 'class new WAPL_Label()', '1.1.0', 'wapl_get_label_html()' );
		return wapl_get_label_html( compact( 'type', 'text', 'style', 'align', 'style_attr' ) );
	}


}
