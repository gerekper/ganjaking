<?php
if( !defined('ABSPATH'))
    exit;

$all_roles = ywcrbp_get_user_role();

$roles_options = array(
    'regular'=> __('Regular price','yith-woocommerce-role-based-prices'),
    'on_sale' => __('On sale price','yith-woocommerce-role-based-prices' ),
    'your_price' => __('Role-based price', 'yith-woocommerce-role-based-prices' ),
    'add_to_cart'=> __('Add to Cart','yith-woocommerce-role-based-prices'),
    'how_show_price' => __('Show Price Incl Tax','yith-woocommerce-role-based-prices'),
    'show_percentage' => __( 'Show total discount/markup ( % )', 'yith-woocommerce-role-based-prices' ),
    'show_price_as' => __( 'Show price for a different user role', 'yith-woocommerce-role-based-prices' )
);

$show_prices_role = get_option( $option['id'] );

?>
<tr valign="top">
    <td>
        <table class="ywcrbp_show_price_option_content">
            <tbody>
                <tr valign="top">
                    <th></th>
                    <?php foreach($roles_options as $key=>$role_option ):?>
                    <th class="ywcrp_header_show_price <?php echo $key;?>"><?php echo $role_option;?></th>
                    <?php endforeach;?>
                </tr>
                <?php foreach( $all_roles as $key=> $role ):?>
                <tr valign="top">
                    <td class="ywcrbp_column column_<?php echo $key;?>"><?php echo $role;?></td>
                    <?php foreach($roles_options as $key_role=>$role_option ):?>

                    <?php if( 'show_price_as' == $key_role ):
                        $value = isset( $show_prices_role[$key][$key_role]  ) ? $show_prices_role[$key][$key_role] : '';
                     ?>
                        <td class="ywcrbp_column column_show_price_as">
                            <select name="<?php echo $option['id'];?>[<?php echo $key;?>][<?php echo $key_role;?>]" class="wc-enhanced-select">
                                <option value="" <?php selected( '', $value );?>><?php _e('None', 'yith-woocommerce-role-based-prices' );?></option>
                                <?php
                                    foreach( $all_roles as $key2 => $role2 ):

                                            if( $key2 == $key ) {
	                                            continue;
                                            }
                                    ?>
                                    <option value="<?php echo $key2;?>" <?php selected( $key2, $value );?>><?php echo $role2;?></option>
                                <?php endforeach;
                                ?>
                            </select>
                        </td>
                    <?php else:?>
                    <td class="ywcrbp_column column_<?php echo $key_role;?>">
                        <?php $checked = isset( $show_prices_role[$key][$key_role] )? 'yes' : 'no';?>
                        <input type="checkbox" name="<?php echo $option['id'];?>[<?php echo $key;?>][<?php echo $key_role;?>]" class="ywcrbp_check_show_prices" value="1" <?php checked( $checked, 'yes');?> >
                    </td>
                    <?php endif;?>
                        <?php endforeach;?>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <div class="description desc_table">
            <div class="content_cases">
                <?php

                $description = sprintf('<p>%s</p><p><strong>%s</strong></p>',
                            __('Use this table to set what to show to each user role. You can choose whether to display regular price, on-sale price, role-based price and Add to cart button.','yith-woocommerce-role-based-prices'),
                            __('Attention: whether you show it or not, Role-based price is the actual price that users are charged of.','yith-woocommerce-role-based-prices' ) );
                echo $description;

             ?>
                <div class="content_case">
                     <h3><?php _e('Example 1','yith-woocommerce-role-based-prices');?></h3>
                     <img src="<?php echo YWCRBP_ASSETS_URL.'images/case1.png';?>" alt="<?php _e('Case 1','yith-woocommerce-role-based-prices' );?>" >
                    <div class="text_case"><?php

                        $case_1_text = sprintf('<p>%s</p><p>%s</p>', __('In a case like the above one, guests will neither be shown price nor "Add to cart" button.','yith-woocommerce-role-based-prices'),
                                                                     __('As all prices are hidden (regular, on-sale and role-based price), the alternative text will be shown.','yith-woocommerce-role-based-prices' ) );
                        echo $case_1_text;
                        ?>
                    </div>
                </div>
                <div class="content_case">
                    <h3><?php _e('Example 2','yith-woocommerce-role-based-prices');?></h3>
                    <img src="<?php echo YWCRBP_ASSETS_URL.'images/case2.png';?>" alt="<?php _e('Case 2','yith-woocommerce-role-based-prices' );?>" >
                    <div class="text_case"><?php
                        $case_2_text = sprintf('<p>%s</p><p>%s</p>', __('In the above example, guests can see regular price, on-sale price, "Add to cart" button, but they are not shown their role-based price.','yith-woocommerce-role-based-prices'),
                                                        __(' Consider that if a price rule has been set for the role "Guests", whether you show it or not, Role-based price is the actual price that users are charged of during checkout.','yith-woocommerce-role-based-prices'));
                        echo $case_2_text;
                    ?>
                    </div>
            </div>
        </div>
    </td>
</tr>