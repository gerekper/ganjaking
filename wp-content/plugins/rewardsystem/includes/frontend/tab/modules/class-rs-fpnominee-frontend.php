<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForNominee' ) ) {

    class RSFunctionForNominee {

        public static function init() {

            add_action( 'woocommerce_after_order_notes' , array( __CLASS__ , 'display_nominee_field_in_checkout' ) ) ;

            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'save_nominee_in_checkout' ) , 10 , 2 ) ;

            if ( get_option( 'rs_reward_content' ) == 'yes' ){
            add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'display_nominee_field_in_my_account' ) ) ;
            }
        }

        public static function display_nominee_field_in_checkout() {
            if ( get_option( 'rs_show_hide_nominee_field_in_checkout' ) == 2 )
                return ;

            $UserList = self::get_users_for_nominee( 'checkout' ) ;
            if ( ! srp_check_is_array( $UserList ) ) {
                _e( 'You have no Nominee' , SRP_LOCALE ) ;
            } else {
                self::nominee_field( 'checkout' ) ;
            }
        }

        public static function display_nominee_field_in_my_account() {

            if ( get_option( 'rs_show_hide_nominee_field' ) == 2 )
                return ;

            $NomineeData = array(
                'usertype' => get_option( 'rs_select_type_of_user_for_nominee' ) ,
                'userlist' => get_option( 'rs_select_users_list_for_nominee' ) ,
                'title'    => get_option( 'rs_my_nominee_title','My Nominee' ) ,
                'name'     => get_option( 'rs_select_type_of_user_for_nominee_name' ) ,
                'userrole' => get_option( 'rs_select_users_role_for_nominee' ) ,
                    ) ;
            self::nominee_field( 'myaccount' , $NomineeData ) ;
        }

        public static function nominee_field( $Nominee , $NomineeData = array() ) {
            $BanType = check_banning_type( get_current_user_id() ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            $ClassName = ($Nominee == 'checkout') ? "rs_select_nominee_in_checkout" : "rs_select_nominee" ;
            $Title     = ($Nominee == 'checkout') ? get_option( 'rs_my_nominee_title_in_checkout' ) : $NomineeData[ 'title' ] ;
            $NomineeId = ($Nominee == 'checkout') ? get_user_meta( get_current_user_id() , 'rs_selected_nominee_in_checkout' , true ) : get_user_meta( get_current_user_id() , 'rs_selected_nominee' , true ) ;
            $Name      = ($Nominee == 'checkout') ? get_option( 'rs_select_type_of_user_for_nominee_name_checkout' ) : $NomineeData[ 'name' ] ;
            $UserList  = self::get_users_for_nominee( $Nominee , $NomineeData ) ;
            ob_start() ;
            ?>
            <h2><?php echo $Title ; ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <td style="width:150px;">
                        <label style="font-size:16px;font-weight: bold;"><?php _e( 'Select Nominee' , SRP_LOCALE ) ; ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <td style="width:300px;">
                        <select name="<?php echo $ClassName ; ?>" style="width:300px;" id="<?php echo $ClassName ; ?>" class="short <?php echo $ClassName ; ?>">
                            <option value=""><?php _e( 'Choose Nominee' , SRP_LOCALE ) ; ?></option>
                            <?php
                            foreach ( $UserList as $UserId ) {
                                $UserInfo = get_user_by( 'id' , $UserId ) ;
                                ?>
                                <option value="<?php echo $UserId ; ?>" <?php echo $NomineeId == $UserId ? "selected=selected" : '' ?>>
                                    <?php
                                    if ( $Name == '1' ) {
                                        echo esc_html( $UserInfo->display_name ) . ' (#' . absint( $UserInfo->ID ) . ' &ndash; ' . esc_html( $UserInfo->user_email ) . ')' ;
                                    } else {
                                        echo esc_html( $UserInfo->display_name ) ;
                                    }
                                    ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php if ( $Nominee != 'checkout' ) { ?>
                    <tr valign="top">
                        <td style="width:150px;">
                            <input type="button" value="Add" class="rs_add_nominee"/>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <?php
            return ob_get_contents() ;
        }

        public static function get_users_for_nominee( $Nominee , $NomineeData = array() ) {
            $UserIds           = array() ;
            $UserSelectionType = ($Nominee == 'checkout') ? get_option( 'rs_select_type_of_user_for_nominee_checkout' ) : $NomineeData[ 'usertype' ] ;
            if ( $UserSelectionType == 1 ) {
                $UserList = ($Nominee == 'checkout') ? get_option( 'rs_select_users_list_for_nominee_in_checkout' ) : $NomineeData[ 'userlist' ] ;
                if ( ! empty( $UserList ) )
                    $UserIds  = srp_check_is_array( $UserList ) ? $UserList : array_filter( array_map( 'absint' , ( array ) explode( ',' , $UserList ) ) ) ;
            } else {
                $UserRoles = ($Nominee == 'checkout') ? get_option( 'rs_select_users_role_for_nominee_checkout' ) : $NomineeData[ 'userrole' ] ;
                if ( ! srp_check_is_array( $UserRoles ) )
                    return $UserIds ;

                $UserIds = get_users( array( 'role__in' => $UserRoles , 'fields' => 'ID' ) ) ;
            }
            return $UserIds ;
        }

        public static function save_nominee_in_checkout( $OrderId , $UserId ) {
            $NomineeId = isset( $_POST[ 'rs_select_nominee_in_checkout' ] ) ? $_POST[ 'rs_select_nominee_in_checkout' ] : '' ;
            update_post_meta( $OrderId , 'rs_selected_nominee_in_checkout' , $NomineeId ) ;
        }

    }

    RSFunctionForNominee::init() ;
}
