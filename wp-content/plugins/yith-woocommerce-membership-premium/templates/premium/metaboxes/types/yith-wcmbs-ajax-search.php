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
wp_enqueue_script( 'woocommerce_admin' );
extract( $args );
$class = isset( $class ) ? $class : '';
if ( isset( $no_value ) && $no_value ) {
    $value = array();
}
$data              = ( !empty( $data ) && is_array( $data ) ) ? $data : array();
$custom_attributes = array();
foreach ( $data as $data_key => $data_value ) {
    if ( !in_array( $data_key, array( 'placeholder', 'allow_clear', 'multiple', 'action' ) ) )
        $custom_attributes[ 'data-' . $data_key ] = $data_value;
}


$name  = isset( $name ) ? $name : '';
$style = isset( $style ) ? $style : '';

if ( !is_array( $value ) && !!$value && is_string( $value ) ) {
    $post_ids = explode( ',', $value );
} else {
    $post_ids = (array) $value;
}

$data_selected = array();

foreach ( $post_ids as $post_id ) {
    $title                     = get_the_title( $post_id );
    $data_selected[ $post_id ] = $title;
}

if ( !$value ) {
    $value = '';
}

?>

<div id="<?php echo $id ?>-container" <?php if ( isset( $deps ) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps[ 'ids' ] ?>"
     data-value="<?php echo $deps[ 'values' ] ?>" <?php endif ?>>

    <label for="<?php echo $id ?>"><?php echo $label ?></label>

    <div style="width:400px;">
        <?php
        yit_add_select2_fields( array(
                                    'id'                => $id,
                                    'name'              => $name,
                                    'class'             => $class,
                                    'style'             => $style,
                                    'data-placeholder'  => isset( $data[ 'placeholder' ] ) ? $data[ 'placeholder' ] : '',
                                    'data-selected'     => $data_selected,
                                    'data-allow_clear'  => isset( $data[ 'allow_clear' ] ) ? $data[ 'allow_clear' ] : false,
                                    'data-multiple'     => true,
                                    'data-action'       => isset( $data[ 'action' ] ) ? $data[ 'action' ] : false,
                                    'custom-attributes' => $custom_attributes,
                                    'value'             => $value,
                                ) );
        ?>
    </div>

    <span class="description"><?php echo $desc ?></span>
</div>