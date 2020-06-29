<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $args );
$is_multiple = isset( $multiple ) && $multiple;
$multiple    = ( $is_multiple ) ? ' multiple' : '';
$class       = isset( $class ) ? $class : '';

if ( $is_multiple ) {
    $value = !empty( $value ) && is_array( $value ) ? $value : array();
}
?>
<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps[ 'ids' ] ?>" data-value="<?php echo $deps[ 'values' ] ?>" <?php endif ?>>
    <label for="<?php echo $id ?>"><?php echo $label ?></label>
    <div class="yith-wccos-select_wrapper">
        <select<?php echo $multiple ?> id="<?php echo $id ?>" class="<?php echo $class ?>" name="<?php echo $name ?><?php if ( $is_multiple ) echo "[]" ?>" <?php if ( isset( $std ) ) : ?>data-std="<?php echo ( $is_multiple ) ? implode( ' ,', $std ) : $std ?>"<?php endif ?>>
            <?php foreach ( $options as $key => $item ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>" <?php if ( $is_multiple ): selected( true, in_array( $key, $value ) );
                else: selected( $key, $value ); endif; ?> ><?php echo $item ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <span class="desc inline"><?php echo $desc ?></span>
</div>