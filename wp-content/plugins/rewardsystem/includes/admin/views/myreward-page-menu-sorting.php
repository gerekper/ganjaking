<?php
/**
 * My Reward Menu Sorting.
 * */
if( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
?>
<table style="width:590px;"class="form-table rs_myrewards_menu_sorting">
    <h3 class="rs_myrewards_menu_heading"><?php esc_html_e( 'My Reward Points Menu Sorting' , SRP_LOCALE ) ; ?></h3>    
    <tbody class="sortable_menu">
        <?php
        $defaultcolumn = array(
            'rs_myrewards_table'        => esc_html__( 'My Rewards Table' , SRP_LOCALE ) ,
            'rs_nominee_field'          => esc_html__( 'Nominee Field' , SRP_LOCALE ) ,
            'rs_gift_voucher_field'     => esc_html__( 'Gift Voucher Field' , SRP_LOCALE ) ,
            'rs_referral_table'         => esc_html__( 'Referral Table' , SRP_LOCALE ) ,
            'rs_generate_referral_link' => esc_html__( 'Generate Referral Link' , SRP_LOCALE ) ,
            'rs_refer_a_friend_form'    => esc_html__( 'Refer a Friend Form' , SRP_LOCALE ) ,
            'rs_my_cashback_table'      => esc_html__( 'My Cashback Table' , SRP_LOCALE ) ,
            'rs_email_subscribe_link'   => esc_html__( 'Email - Subscribe Link' , SRP_LOCALE ) ,
                ) ;

        $sortedcolumn = srp_check_is_array( get_option( 'rs_sorted_menu_settings_list' ) ) ? get_option( 'rs_sorted_menu_settings_list' ) : $defaultcolumn ;

        foreach( $sortedcolumn as $column_key => $column_value ) {
            ?>
            <tr>
                <th class="myrewards_sortable_menu_header"><?php echo esc_attr( $defaultcolumn[ $column_key ] ) ; ?></th>                 
                <td class="myrewards_sortable_menu_data">
                    <input type="hidden" name="rs_sorted_reward_menu_list[<?php echo esc_attr( $column_key ) ; ?>]" value="<?php echo esc_attr( $column_value ) ?>">
                    <img src="<?php echo esc_url( SRP_PLUGIN_DIR_URL ) ; ?>/assets/images/drag-icon.png"/>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
<?php
