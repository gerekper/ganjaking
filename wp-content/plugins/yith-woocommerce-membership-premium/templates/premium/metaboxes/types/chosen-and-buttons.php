<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

extract( $args );
$is_multiple = isset( $multiple ) && $multiple;
$multiple    = ( $is_multiple ) ? ' multiple' : '';
$buttons     = isset( $buttons ) ? $buttons : array();
?>
<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps[ 'ids' ] ?>"
     data-value="<?php echo $deps[ 'values' ] ?>" <?php endif ?>>

    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <div class="yith-wcmbs-select2-and-buttons-select-wrapper">
        <select<?php echo $multiple ?> id="<?php echo $id ?>" class="yith-wcmbs-select2" name="<?php echo $name ?><?php if ( $is_multiple )
            echo "[]" ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo ( $is_multiple ) ? implode( ' ,', $std ) : $std ?>"<?php endif ?>>
            <?php foreach ( $options as $key => $item ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php if ( $is_multiple ): selected( true, in_array( $key, $value ) );
                else: selected( $key, $value ); endif; ?> ><?php echo $item ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="yith-wcmbs-select2-and-buttons-buttons-wrapper">
        <?php foreach ( $buttons as $button ) :
            $b_title = isset( $button[ 'title' ] ) ? $button[ 'title' ] : '';
            $b_class = isset( $button[ 'class' ] ) ? $button[ 'class' ] : 'button';
            ?>
            <input type="button" class="<?php echo $b_class; ?>" value="<?php echo $b_title; ?>" data-container-id="<?php echo $id ?>-container" />
        <?php endforeach; ?>
    </div>

    <span class="description"><?php echo $desc ?></span>
</div>