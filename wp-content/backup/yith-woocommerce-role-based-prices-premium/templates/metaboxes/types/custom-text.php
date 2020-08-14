<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

extract( $args );


if( isset( $class ) ){

    if( $class === 'wc_input_price' )
        $value = wc_format_localized_price( $value );
    elseif( $class === 'wc_input_decimal' ){

        $value = wc_format_localized_decimal( $value );
    }

}else {
	$class = '';
}
$deps_html = '';
if ( function_exists( 'yith_field_deps_data' ) ) {
	$deps_html = yith_field_deps_data( $args );
} else {
	if ( isset( $deps ) ) {
		$deps_ids    = $deps['ids'];
		$deps_values = $deps['values'];
		$deps_html   = "data-field='$id' data-dep='{$deps_ids}' data-value='$deps_values'";
	}
}

?>
<div id="<?php echo $id ?>-container" <?php echo $deps_html;?> >
    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <p>
        <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" class="<?php echo $class;?>" value="<?php echo esc_attr( $value )  ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std ?>"<?php endif ?> />
        <span class="desc inline"><?php echo $desc ?></span>
    </p>
</div>