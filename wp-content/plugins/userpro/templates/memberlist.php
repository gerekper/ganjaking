<div class="userpro userpro-users userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data($args); ?>>
    
    <div class="userpro-body userpro-body-nopad">
    
    <?php if ($search) { ?>
        <div class="userpro-search">
            <form class="userpro-search-form" action="" method="get">
                
                <?php if ($memberlist_default_search) { ?><input type="text" name="searchuser" id="searchuser" value="<?php echo stripcslashes($_GET['searchuser']) ; ?>" placeholder="<?php _e('Search for a user...', 'userpro'); ?>" /><?php 
                } ?>
                
                <?php do_action('userpro_modify_search_filters', $args); ?>
                
                <button type="submit" class="userpro-icon-search userpro-tip" title="<?php _e('Search', 'userpro'); ?>"></button>
                
                <button type="button" class="userpro-icon-remove userpro-clear-search userpro-tip" title="<?php _e('Clear your Search', 'userpro'); ?>"></button>
                            
            </form>
        </div>
    <?php
    if (isset($users['total']) && !empty($users['total']) && $userpro->memberlist_in_search_mode($args) ) {
        echo '<div class="userpro-search-results">'.$userpro->found_members($users['total']).'</div>';
    }
    ?>
    <?php } ?>
        
    <?php if ($userpro->memberlist_in_search_mode($args) ) { ?>

    <?php if ($memberlist_paginate == 1 && $memberlist_paginate_top == 1 && isset($users['paginate'])) { ?><div class="userpro-paginate top"><?php echo $users['paginate']; ?></div><?php 
    } ?>
    
    <?php if (isset($users['users']) && !empty($users['users'])) { ?>
    <?php foreach($users['users'] as $user) : $user_id = $user->ID; ?>
        
        <div class="userpro-user" data-pic_size="<?php echo $memberlist_pic_size; ?>">
            
            <a href="<?php echo $userpro->permalink($user_id); ?>" class="<?php userpro_user_via_popup($args); ?> userpro-user-img" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>">
                <img src="<?php echo $userpro->profile_photo_url($user_id); ?>" alt="profile-pic">
                <span><i class="userpro-icon-plus"></i></span>
            </a>
            
    <?php if ($memberlist_show_name) {?>
            <div class="userpro-user-link">
                <a href="<?php echo $userpro->permalink($user_id); ?>" class="<?php userpro_user_via_popup($args); ?>" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>"><?php echo $user->display_name; ?><i class="userpro-icon-caret-up"></i></a>
            </div>
    <?php } ?>
            
        </div>
        
    <?php endforeach; ?>
    <?php } else { ?><div class="userpro-search-noresults"><?php _e('No users match your search. Please try again.', 'userpro'); ?></div><?php 
    } ?>

    <?php if ($memberlist_paginate == 1 && $memberlist_paginate_bottom == 1 && isset($users['paginate'])) { ?><div class="userpro-paginate bottom"><?php echo $users['paginate']; ?></div><?php 
    } ?>
        
    <?php } // initial results off/on ?>
    
    </div>

</div>
