<?php  
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * This is construct of class where all users coupons listed.
 * 
 * @name User_reports_Log_List_Table
 * @category Class
 * @author makewebbetter<webmaster@makewebbetter.com>
 * @link https://www.makewebbetter.com/
 */
class User_reports_Log_List_Table extends WP_List_Table {

    public $example_data; 

    /**
     * This construct colomns in users logs table.
     * 
     * @name get_columns.
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
    function get_columns(){

        $columns = array(
            'user_name'     => __('User Name', 'coupon-referral-program'),
            'user_email'     => __('User Email', 'coupon-referral-program'),
            'referred_users'    => __('Referred Users', 'coupon-referral-program'),
            'utilize'   => __('Total Utilization', 'coupon-referral-program'),
            'no_of_coupons' => __('No. Of Coupons','coupon-referral-program'),
            );
        return $columns;
    }
    /**
     * This show users logs table list.  
     * 
     * @name column_default.
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */

    function column_default($item, $column_name){

        switch($column_name){
            
            case 'user_name':
            $actions = [
            'view_details_log' => '<a href="'.admin_url('admin.php?page=wc-reports&tab=crp_report&user_id='.$item['id'].'&action=view_details_log"').'">'.__('View Details','coupon-referral-program').'</a>',
            ];
            return $item[$column_name]. $this->row_actions( $actions );
            case 'user_email':
            return '<b>'.$item[$column_name].'</b>';
            case 'referred_users':
            return '<b>'.$item[$column_name].'</b>';
            case 'utilize':
            return '<b>'.wc_price($item[$column_name]).'</b>';
            case 'no_of_coupons':
            return '<b>'.$item[$column_name].'</b>';  
            default:
            return false; 
        }
    }

    /**
     * Returns an associative array containing the bulk action for sorting.
     *
     * @name get_sortable_columns.
     * @return array
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'user_name'    => array('user_name',false),
            'user_email'  => array('user_email',false),
            'referred_users'  => array('referred_users',false),
            'utilize'  => array('utilize',false),
            'no_of_coupons'  => array('no_of_coupons',false),
            );
        return $sortable_columns;
    }

    /**
     * This function is return data of the all users.
     *
     * @name get_user_report_data.
     * @return array
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
    function get_user_report_data($user_id) {
    	$mwb_crp_data = array(); 
    	$mwb_crp_user_role = get_userdata($user_id)->roles['0'];
    	if($mwb_crp_user_role == "customer" || $mwb_crp_user_role == "subscriber") {
    		$crp_Public_obj = new Coupon_Referral_Program_Public('coupon-referral-program', '1.0.0');
    		$users_crp_data = $crp_Public_obj->get_revenue($user_id);
    		$mwb_crp_user_name = get_userdata($user_id)->data->display_name;
    		$mwb_crp_user_email = get_userdata($user_id)->data->user_email;
            $get_utilize_coupon_amount = $crp_Public_obj->get_utilize_coupon_amount($user_id);
    		$mwb_crp_data = array(
    			'id'			=> $user_id,
    			'user_name' 	=> $mwb_crp_user_name,
    			'user_email'	=> $mwb_crp_user_email,
    			'referred_users'=> $users_crp_data['referred_users'],
    			'utilize'		=> $get_utilize_coupon_amount,
    			'no_of_coupons' => $users_crp_data['total_coupon'],
    			);
    	}
    	return $mwb_crp_data;
    }
     /**
     * This function is return data of the all users.
     *
     * @name mwb_get_report_data.
     * @return array
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
     function mwb_get_report_data() {
     	$users = get_users( array( 'fields' => array( 'ID' ) ) );
     	$mwb_crp_data_array = array();
     	$user_id = "";
     	if(isset($_REQUEST['s']) && !empty($_REQUEST['s'])){
     		$args['search'] = '*' . $_REQUEST['s'] . '*';
     		$user_data = get_user_by("email",$_REQUEST['s']);
     		if(isset($user_data) && !empty($user_data)) {	
     			$user_id = $user_data->ID;
     		}
     		$user_data = get_user_by("login",$_REQUEST['s']);
     		if(isset($user_data) && !empty($user_data)) {	
     			$user_id = $user_data->ID;
     		}
     		if(!empty($user_id)) {
     			$mwb_crp_data =  $this->get_user_report_data($user_id);
     			if(!empty($mwb_crp_data) && is_array($mwb_crp_data)) {
     				array_push($mwb_crp_data_array, $mwb_crp_data);
     			}
     		}
     		return $mwb_crp_data_array ;

     	}
     	foreach($users as $user_id) {
     		$mwb_crp_data =  $this->get_user_report_data($user_id->ID);
     		if(!empty($mwb_crp_data) && is_array($mwb_crp_data)) {
     			array_push($mwb_crp_data_array, $mwb_crp_data);
     		}
     	}
     	return $mwb_crp_data_array;
     }

    /**
     * Prepare items for sorting.
     *
     * @name prepare_items.
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
    function prepare_items() 
    {
        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        
        $this->example_data = $this->mwb_get_report_data();
        $data = $this->example_data;
        
        usort($data, array($this, 'mwb_crp_usort_reorder'));
        
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                 
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
            ) );       
        
    }

    /**
     * Return sorted associative array.
     *
     * @name mwb_wpr_usort_reorder.
     * @return array
     * @author makewebbetter<webmaster@makewebbetter.com>
     * @link https://www.makewebbetter.com/
     */
    function mwb_crp_usort_reorder( $cloumna,$cloumnb ){
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; 
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
        if(is_numeric($cloumna[$orderby]) && is_numeric($cloumnb[$orderby])){
            if ($cloumna[$orderby] == $cloumnb[$orderby]) {
                return 0;
            }
            elseif($cloumna[$orderby] < $cloumnb[$orderby]){
                $result =  -1;
                return ($order==='asc') ? $result : -$result;
            }
            elseif($cloumna[$orderby] > $cloumnb[$orderby]){
                $result = 1;
                return ($order==='asc') ? $result : -$result;
            }
        }
        else{
            $result = strcmp($cloumna[$orderby], $cloumnb[$orderby]);
            return ($order==='asc') ? $result : -$result;
        }
    }
}

/* This scetion used for the listing of the details of the users */
if(isset($_GET['action']) && isset($_GET['user_id']))
{
    if($_GET['action'] == 'view_details_log')
    {
        $user_id = sanitize_post($_GET['user_id']);
        $crp_Public_obj = new Coupon_Referral_Program_Public('coupon-referral-program', '1.0.0');
        ?>
    <div class="mwb-crp-referral-table-wrapper">
        <table id="mwb-crp-referral-table" class="mwb-crp-referral-table">
            <thead >
                <tr >
                    <th class="mwb_crp_reporting_heading"><?php _e('Coupon ','coupon-referral-program') ?></th>
                    <th class="mwb_crp_reporting_heading"><?php _e('Coupon Created','coupon-referral-program') ?></th>
                    <th class="mwb_crp_reporting_heading"><?php _e('Expiry Date','coupon-referral-program') ?></th>
                    <th class="mwb_crp_reporting_heading"><?php _e('Event','coupon-referral-program') ?></th>
                    <th class="mwb_crp_reporting_heading"><?php _e('Referred Users','coupon-referral-program') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($crp_Public_obj->get_signup_coupon($user_id)) && is_array($crp_Public_obj->get_signup_coupon($user_id))):
                    $signup_coupon  = $crp_Public_obj->get_signup_coupon($user_id);
                    $coupon = new WC_Coupon($signup_coupon['singup'] );
                    if ( 'publish' == get_post_status ( $signup_coupon['singup'] ) ) :
                ?>
                <tr>
                    <td data-th="Coupon">
                        <div class="mwb-crp-coupon-code">
                            <p id="<?php echo 'mwb'.$signup_coupon['singup']; ?>">
                                <?php echo $coupon->get_code();?>
                            </p>
                            <span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
                                wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?>
                            </span> 
                            <img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> 
                            <span class="mwb-crp-coupon-wrap">
                                <button class="mwb-crp-coupon-btn-copy"
                                data-clipboard-target="#mwb<?php echo $signup_coupon['singup']; ?>" aria-label="copied">
                                    <span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
                                    <span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
                            
                               </button>
                            </span>
                        </div>
                    </td>
                    <td data-th="Coupon Created"><?php echo (!empty($coupon->get_date_created()))?$coupon->get_date_created()->date(wc_date_format()):"---"; ?></td>
                    <td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
                    <td data-th="Event"><?php _e('Signup Coupon','coupon-referral-program') ?></td>
                    <td data-th="Referred Users">----</td>
                </tr>
                <?php endif;?>
                <?php endif;?>
                <?php if(!empty($crp_Public_obj->mwb_crp_get_referal_signup_coupon($user_id))):
                  foreach ($crp_Public_obj->mwb_crp_get_referal_signup_coupon($user_id) as $coupon_code => $user_id_crp_coupon):
                      $coupon = new WC_Coupon($coupon_code);
                      $flag = false;
                     if ( 'publish' == get_post_status ( $coupon_code ) ) :
                        $flag = true;
                ?>
                <tr>
                    <td data-th="Coupon">
                        <div class="mwb-crp-coupon-code"><p id="mwb<?php echo $coupon_code; ?>"><?php echo $coupon->get_code(); ?></p>
                            <span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
                                wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?></span> <img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> <span class="mwb-crp-coupon-wrap">
                                <button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo $coupon_code; ?>" aria-label="copied">
                                    <span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
                                    <span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
                                </button>
                            </span>
                        </div>
                    </td>
                    <td data-th="Coupon Created"><?php echo !empty($coupon->get_date_created())?$coupon->get_date_created()->date(wc_date_format()):""; ?></td>
                    <td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
                    <td data-th="Event"><?php _e('Referral Signup','coupon-referral-program'); ?></td>
                    <td data-th="Referred Users"><?php echo (get_userdata($user_id_crp_coupon))?get_userdata($user_id_crp_coupon)->data->display_name:__("User has been deleted","coupon-referral-program");?></td>
                </tr>
                <?php endif?>
                <?php endforeach;
                endif;?>
                <?php if(!empty($crp_Public_obj->get_referral_purchase_coupons($user_id))):
                  foreach ($crp_Public_obj->get_referral_purchase_coupons($user_id) as $coupon_code => $user_id_crp_coupon):
                      $coupon = new WC_Coupon($coupon_code);
                      $flag = false;
                     if ( 'publish' == get_post_status ( $coupon_code ) ) :
                        $flag = true;
                ?>
                <tr>
                    <td data-th="Coupon">
                        <div class="mwb-crp-coupon-code"><p id="mwb<?php echo $coupon_code; ?>"><?php echo $coupon->get_code(); ?></p>
                            <span class="mwb-crp-coupon-amount"><?php echo ($coupon->get_discount_type() == "fixed_cart")?
                                wc_price($coupon->get_amount()) : $coupon->get_amount()."%"; ?></span> <img class="mwb-crp-coupon-scissors" src="<?php echo COUPON_REFERRAL_PROGRAM_DIR_URL."public/images/scissors.png"; ?>" alt=""> <span class="mwb-crp-coupon-wrap">
                                <button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb<?php echo $coupon_code; ?>" aria-label="copied">
                                    <span class="mwb-crp-coupon-tooltiptext"><?php _e('Copy','coupon-referral-program') ;?></span>
                                    <span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext"><?php _e('Copied','coupon-referral-program') ;?></span>
                                </button>
                            </span>
                        </div>
                    </td>
                    <td data-th="Coupon Created"><?php echo !empty($coupon->get_date_created())?$coupon->get_date_created()->date(wc_date_format()):""; ?></td>
                    <td data-th="Expiry Date"><?php echo $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( wc_date_format()) : _e("Never","coupon-referral-program"); ?></td>
                    <td data-th="Event"><?php _e('Referral Purchase','coupon-referral-program'); ?></td>
                    <td data-th="Referred Users"><?php echo (get_userdata($user_id_crp_coupon))?get_userdata($user_id_crp_coupon)->data->display_name:__("User has been deleted","coupon-referral-program");?></td>
                </tr>
                <?php endif?>
                <?php endforeach;
                endif;?>
                <?php if(empty($crp_Public_obj->get_referral_purchase_coupons($user_id)) && empty($crp_Public_obj->get_signup_coupon($user_id))): ?>
                    <tr>
                        <td colspan="5"><?php _e('No Coupons Or No Referred Users Yet','coupon-referral-program'); ?></td>
                    </tr>
                <?php endif;?>
            </tbody>
            
        </table>
    </div>
       <br> 
        <a  href="<?php echo admin_url('admin.php?page=wc-reports&tab=crp_report'); ?>" style="line-height: 2" class="button button-primary mwb_wpr_save_changes"><?php _e("Go Back",'coupon-referral-program'); ?></a> <?php

    }
}
else
{
   
    ?>
    <form method="post">
        <?php
        $myListTable = new User_reports_Log_List_Table();
        $myListTable->prepare_items();
        $myListTable->search_box( __( 'Search Users','coupon-referral-program' ), 'mwb-crp-user' );
        $myListTable->display();
        ?>
    </form>
    <?php
}

