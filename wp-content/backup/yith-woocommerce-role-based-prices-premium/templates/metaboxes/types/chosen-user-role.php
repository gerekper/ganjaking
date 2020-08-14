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
$is_multiple = isset( $multiple ) && $multiple;
$multiple = ( $is_multiple ) ? ' multiple' : '';
$placeholder = isset( $placeholder ) ? $placeholder : '';

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
<div id="<?php echo $id ?>-container" class="chosen" <?php echo $deps_html;?> >

    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <div class="select_wrapper">
        <select<?php echo $multiple ?> id="<?php echo $id ?>" class="chosen" name="<?php echo $name ?><?php if( $is_multiple ) echo "[]" ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo ( $is_multiple )? implode(' ,', $std) : $std ?>"<?php endif ?> data-placeholder="<?php echo $placeholder;?>">
            <?php foreach ( $options as $key => $item ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php if( $is_multiple ): selected( true, in_array( $key, $value ) ); else: selected( $key, $value ); endif; ?> ><?php echo $item ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <span class="desc inline"><?php echo $desc ?></span>
</div>