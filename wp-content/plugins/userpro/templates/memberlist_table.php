<div class="userpro userpro-users userpro-users-v2 userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data($args); ?>>

    <div class="userpro-body userpro-body-nopad">

    <?php if ($search) { ?>
        <div class="userpro-search">
            <form class="userpro-search-form" action="" method="get">
                
                <?php if ($memberlist_default_search) { ?><input type="text" name="searchuser" id="searchuser" value="<?php echo $_GET['searchuser']; ?>" placeholder="<?php _e('Search for a user...', 'userpro'); ?>" /><?php 
                } ?>
                
                <?php do_action('userpro_modify_search_filters', $args); ?>
                <input type="hidden" name="page_id" value="<?php echo get_the_ID();?>">
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
        
        <table class="userpro-table">
        
    <?php echo $userpro->parse_columns('thead', $args['memberlist_table_columns'], $args); ?>
    <?php echo $userpro->parse_columns('tfoot', $args['memberlist_table_columns'], $args); ?>
        
    <?php foreach($users['users'] as $user) : $user_id = $user->ID; ?>

                <tr>
        <?php 
        $cols = explode(',', $args['memberlist_table_columns']);
        foreach($cols as $col) {
            echo $userpro->parse_column($col, $user_id, $user, $args);
        }
        ?>
                </tr>
                
    <?php endforeach; ?>
        
        </table>
        
    <?php } else { ?>

    <?php } ?>

    <?php if ($memberlist_paginate == 1 && $memberlist_paginate_bottom == 1 && isset($users['paginate'])) { ?><div class="userpro-paginate bottom"><?php echo $users['paginate']; ?></div><?php 
    } ?>
        
    <?php } // initial results off/on ?>
    
    </div>

</div>