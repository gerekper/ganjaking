<?php
global $userpro;
$requests = get_option('userpro_verify_requests');
if (is_array($requests) && $requests != '' && !empty($requests) ) {
    $count = count($requests);
} else {
    $count = 0;
}
?>

<h3><?php _e('Users Requesting Verified Status','userpro'); ?> <span><?php echo $count; ?></span></h3>
<div class="upadmin-panel">
    <table>
        <tr valign="top">
            <th scope="row"><label for="userpro_sortby_verified"><?php _e('Sortby','userpro'); ?></label></th>
            <td>
                <select name="userpro_sortby_verified" id="userpro_sortby_verified" class="chosen-select" style="width:300px">
                    <option value="default" ><?php _e('Default Order','userpro'); ?></option>
                    <option value="ascending" ><?php _e('Ascending Order','userpro'); ?></option>
                    <option value="descending" ><?php _e('Descending Order','userpro'); ?></option>

                </select>
            </td>
        </tr>
    </table>

    <?php
    if (is_array($requests) && $requests != '' && !empty($requests) ) :
        $requests = array_reverse($requests);
        ?>
        <div class="up-container">

            <?php
            $i=1;
            $users_ids_array = array();
            foreach( $requests as $user_id) : 
                $user = get_userdata($user_id); 
                array_push($users_ids_array, $user_id);
                if ($user) : ?>

                    <div class="up-card upadmin-pending-verify upadmin-pending-verify boxv">
                        <div class="up-card__header">
                            <a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo get_avatar( $user_id, 150 ); ?></a>
                        </div>
                        <div class="up-card__body">
                            <div class="up-card__block">
                                <span><?php _e('Display Name','userpro') ?></span>
                                <p><a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo userpro_profile_data('display_name', $user_id); ?></a></p>
                            </div>
                            <div class="up-card__block">
                                <span><?php _e('Email', 'userpro') ?></span>
                                <p><?php echo $user->user_email; ?></p>
                            </div>


                            <div class="up-card__footer">
                                <a class="up-admin-btn small approve upadmin-verify" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Approve', 'userpro') ?></a>
                                <a class="up-admin-btn small remove upadmin-unverify" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Remove', 'userpro') ?></a>
                            </div>
                        </div>
                    </div>
            <?php else :

                global $userpro_admin;
                $userpro_admin->delete_pending_request($user_id); endif;?><?php $i=$i+1; endforeach;

            ?></div>
            <br/>    
            <div class="align-div-content-right">             
                <!-- Adding buttons for verifying all requests at once -->
                <a href="#" class="upadmin-verify-all up-admin-btn up-admin-btn--dark-blue small"  
                data-user="<?php echo implode(' ', $users_ids_array); ?>"><?php _e('Verify all users','userpro'); ?></a>
                <a href="#" class="upadmin-unverify-all up-admin-btn small remove" 
                data-user="<?php echo implode(' ', $users_ids_array); ?>"><?php _e('Reject all users','userpro'); ?></a>
            </div>
            <br/> 

    <?php endif; ?>

</div>

<?php

$total_users = get_users(array(

    'meta_key'     => '_account_status',

    'meta_value'   => 'pending_admin',

    'meta_compare' => '=')); //get all the lists of users


$total_users = count($total_users);

/*
 * Pagination query
 */
$number = 50;

$paged = max( 1, (int) filter_input( INPUT_GET, 'paged' ) ); //current number of page

$offset = ($paged - 1) * $number; //page offset

$users = get_users(array(

    'meta_key'     => '_account_status',

    'meta_value'   => 'pending_admin',

    'meta_compare' => '=',

    'orderby'        => 'registered',

    'offset' => $offset,

    'number' => $number,

));

?>

<h3><?php _e('Users Awaiting Manual Approval','userpro'); ?> <span><?php echo $total_users ?></span></h3>
<div class="upadmin-panel" >
    <table>
        <tr valign="top">
            <th scope="row"><label for="userpro_sortby_manual"><?php _e('Sortby','userpro'); ?></label></th>
            <td>
                <select name="userpro_sortby_manual" id="userpro_sortby_manual" class="chosen-select" style="width:300px">
                    <option value="default" ><?php _e('Default Order','userpro'); ?></option>
                    <option value="ascending" ><?php _e('Ascending Order','userpro'); ?></option>
                    <option value="descending" ><?php _e('Descending Order','userpro'); ?></option>

                </select>
            </td>
        </tr>
    </table>


    <?php

    if (!empty($users)){
        ?>       <div class="up-container">




            <?php
        $i=1;
        $users_ids_array = array();
        foreach($users as $user) {
            $user_id = $user->ID;
            array_push($users_ids_array, $user_id); // pushing all user ids to one array
            ?>

            <div class="up-card upadmin-pending-verify">
                <div class="up-card__header">
                    <a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo get_avatar( $user_id, 150 ); ?></a>
                </div>
                <div class="up-card__body">
                    <div class="up-card__block">
                        <span><?php _e('Display Name', 'userpro') ?></span>
                        <p><a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo userpro_profile_data('display_name', $user_id); ?></a></p>
                    </div>
                    <div class="up-card__block">
                        <span><?php _e('Email', 'userpro') ?></span>
                        <p><?php echo $user->user_email; ?></p>
                    </div>


                    <div class="up-card__footer">
                        <a class="up-admin-btn small approve upadmin-user-approve" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Approve', 'userpro') ?></a>
                        <a class="up-admin-btn small remove upadmin-user-deny" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Remove', 'userpro') ?></a>
                    </div>
                </div>
            </div>

            <?php
            $i=$i+1;
        }
        ?>

        <?php

        if($total_users > $number){

            $user_approval_paginate = array(
                'base'         => '%_%',
                'format'       => '?paged=%#%',
                'show_all'     => false,
                'total'    => ceil($total_users / $number),
                'current'  => max(1, $paged),
            );
            echo '<div class="userpro-paginate ">';
            echo paginate_links($user_approval_paginate);
            echo '</div>';
        }
        ?></div>
        <div class="align-div-content-right">
            <a href="#" class="upadmin-user-approve-all up-admin-btn up-admin-btn--dark-blue small" data-user="<?php echo implode(' ', $users_ids_array); ?>"><?php _e('Approve all users','userpro'); ?></a>
            <a href="#" class="upadmin-user-deny-all up-admin-btn small remove" data-user="<?php echo implode(' ', $users_ids_array); ?>"><?php _e('Delete all users','userpro'); ?></a>
        </div> 
        <?php
    }
    ?>
</div>

<?php	$users = get_users(array(
    'meta_key'     => '_account_status',
    'meta_value'   => 'pending',
    'meta_compare' => '=',
    'orderby'        => 'registered',
));
?>

<h3><?php _e('Users Awaiting E-mail Validation','userpro'); ?> <span><?php echo count($users); ?></span></h3>
<div class="upadmin-panel">
    <table>
        <tr valign="top">
            <th scope="row"><label for="userpro_sortby_email"><?php _e('Sortby','userpro'); ?></label></th>
            <td>
                <select name="userpro_sortby_email" id="userpro_sortby_email" class="chosen-select" style="width:300px">
                    <option value="default" ><?php _e('Default Order','userpro'); ?></option>
                    <option value="ascending" ><?php _e('Ascending Order','userpro'); ?></option>
                    <option value="descending" ><?php _e('Descending Order','userpro'); ?></option>

                </select>
            </td>
        </tr>
    </table>

    <?php
    if (!empty($users)){?>
        <div class="up-container">  <?php
        $i=1;
        $users_ids_array = array();
        foreach($users as $user) {
            $user_id = $user->ID;
            array_push($users_ids_array, $user_id); // pushing all user ids to one array
            ?>

            <div class="up-card upadmin-pending-verify">
                <div class="up-card__header">
                    <a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo get_avatar( $user_id, 150 ); ?></a>
                </div>
                <div class="up-card__body">
                    <div class="up-card__block">
                        <span><?php _e('Display Name', 'userpro') ?></span>
                        <p><a href="<?php echo $userpro->permalink($user_id); ?>" target="_blank"><?php echo userpro_profile_data('display_name', $user_id); ?></a></p>
                    </div>
                    <div class="up-card__block">
                        <span><?php _e('Email', 'userpro') ?></span>
                        <p><?php echo $user->user_email; ?></p>
                    </div>

                    <div class="up-card__footer">
                        <a class="up-admin-btn small approve upadmin-user-approve" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Approve', 'userpro') ?></a>
                        <a class="up-admin-btn small remove upadmin-user-deny" data-user="<?php echo $user_id; ?>" href="#"><?php _e('Remove', 'userpro') ?></a>
                    </div>
                </div>
            </div>
            <?php
            $i=$i+1;}?></div>
            <div class="align-div-content-right">             
                <!-- Adding buttons for verifying all requests at once -->
                <a href="#" class="upadmin-user-approve-all up-admin-btn up-admin-btn--dark-blue small"  
                data-user="<?php echo implode(' ', $users_ids_array); ?>"><?php _e('Verify all users','userpro'); ?></a>
                <a href="#" class="upadmin-user-deny-all up-admin-btn small remove" data-user="<?php 
                echo implode(' ', $users_ids_array); ?>"><?php _e('Deny all users','userpro'); ?></a>
            </div>
            <?php
    }
    ?>

</div>

<?php

$invitedUsers = new UP_UserInvitation();
$countUsers = !empty($invitedUsers->getAll()) ? $invitedUsers->getAll() : array();
?>
<h3><?php _e('Invited Users','userpro'); ?> <span><?php echo count($countUsers); ?></span></h3>
<div class="upadmin-panel">
<div class="response-message"></div>

    <?php if ($invitedUsers->getAll()) :

       echo $invitedUsers->template();

    endif; ?>

    </div>

