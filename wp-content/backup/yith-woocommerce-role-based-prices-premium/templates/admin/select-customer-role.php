<?php
if( !defined('ABSPATH' ) )
    exit;


$default = isset( $option['default'] ) ? $option['default'] : '';
$users_role = get_option( $option['id'], $default );
$users_role = ( $users_role === '' ) ? array() : $users_role;
$placeholder = isset( $option['placeholder'] ) ? $option['placeholder'] : '';

$all_roles = ywcrbp_get_user_role();


?>

<tr valign="top">
    <th scope="row"><label for="<?php esc_attr_e( $option['id'] );?>"><?php echo( $option['name'] );?></label></th>
    <td>
        <select name="<?php esc_attr_e( $option['id'] );?>[]" multiple="multiple" id="<?php esc_attr_e( $option['id'] );?>" class="wc-enhanced-select" placeholder="<?php echo $placeholder;?>">
            <?php
                if( !empty( $all_roles ) ):
                    foreach( $all_roles as $key => $role ):?>
                     <option value="<?php echo $key;?>" <?php selected( true, in_array( $key, $users_role ) );?>><?php echo $role;?></option>
             <?php endforeach;endif; ?>
        </select>
        <span class="description"><?php echo $option['desc'];?></span>
    </td>
</tr>
