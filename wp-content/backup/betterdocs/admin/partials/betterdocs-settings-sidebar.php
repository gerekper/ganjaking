<div class="betterdocs-settings-right">
    <div class="betterdocs-sidebar">
        <div class="betterdocs-sidebar-block">
            <div class="betterdocs-admin-sidebar-logo">
                <img alt="BetterDocs" src="<?php echo plugins_url( '/', __FILE__ ).'../assets/img/betterdocs-icon.svg'; ?>">
            </div>
            <div class="betterdocs-admin-sidebar-cta">
                <?php     
                    if(class_exists('Betterdocs_Pro')) {
                        printf( __( '<a rel="nofollow" href="%s" target="_blank">Manage License</a>', 'betterdocs' ), 'https://wpdeveloper.net/account' ); 
                    }else{
                        printf( __( '<a rel="nofollow" href="%s" target="_blank">Upgrade to Pro</a>', 'betterdocs' ), 'https://betterdocs.co/upgrade' );
                    }
                ?>
            </div>
        </div>
        <div class="betterdocs-sidebar-block betterdocs-license-block">
            <?php
                if( class_exists( 'Betterdocs_Pro' ) ) {
                    do_action( 'betterdocs_licensing' );
                }
            ?>
        </div>
    </div>
</div>