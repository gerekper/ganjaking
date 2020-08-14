<?php
// Exit if accessed directly
!defined( 'YITH_POS' ) && exit();

$args = $field;
extract( $field );

global $post, $register;

$value = !!$value && is_array($value) ? $value : array();

$class = isset( $class ) ? $class : '';
if ( $register ) {
    $store_id = $register->get_store_id();
} else {
    if ( get_post_type( $post->ID ) === YITH_POS_Post_Types::$store ) {
        $store_id = $post->ID;
    } else {
        $register = yith_pos_get_register( $post->ID );
        $store_id = $register->get_store_id();
    }
}

$cashier_ids   = yith_pos_get_employees( 'cashier', $store_id );
$cashier_names = array_map( 'yith_pos_get_employee_name', $cashier_ids );
$cashiers      = array_combine( $cashier_ids, $cashier_names );

?>
<div id="<?php esc_attr_e( $id ); ?>-container" <?php echo yith_field_deps_data( $args ); ?>
     class="yith-plugin-fw-metabox-field-row <?php echo $class ?>">
    <span class="show-cashiers-select"><?php yith_plugin_fw_get_field( array(
                                                                           'id'      => $id . "[type]",
                                                                           'name'    => $name . "[type]",
                                                                           'class'   => 'wc-enhanced-select no-bottom',
                                                                           'type'    => 'select',
                                                                           'label'   => '',
                                                                           'options' => array(
                                                                               'show' => __( 'Show Register to', 'yith-point-of-sale-for-woocommerce' ),
                                                                               'hide' => __( 'Hide Register to', 'yith-point-of-sale-for-woocommerce' ),
                                                                           ),
                                                                           'value'   => isset( $value[ 'type' ] ) ? $value[ 'type' ] : 'show'
                                                                       ), true, false ); ?></span>
    <span class="show-cashiers-list">
    <?php yith_plugin_fw_get_field( array(
                                        'id'          => $id . "[cashiers]",
                                        'name'        => $name . "[cashiers]",
                                        'class'       => 'wc-enhanced-select no-bottom',
                                        'type'        => 'select',
                                        'placeholder' => __( "Select a Cashier", 'yith-point-of-sale-for-woocommerce' ),
                                        'multiple'    => true,
                                        'label'       => '',
                                        'options'     => $cashiers,
                                        'value'       => isset( $value[ 'cashiers' ] ) ? $value[ 'cashiers' ] : array()
                                    ), true, false ); ?>
    </span>
</div>