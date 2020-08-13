<div class="betterdocs-settings-wrap">
    <?php do_action( 'betterdocs_settings_header' ); ?>
    <div class="betterdocs-left-right-settings">
        <?php do_action( 'betterdocs_before_settings_left' ); ?>
        <div class="betterdocs-settings">
            <div class="betterdocs-settings-menu">
                <ul>
                    <?php
                        $i = 1;
                        foreach( $settings_args as $key => $setting ) {
                            $active = $i++ === 1 ? 'active ' : '';
                            echo '<li class="'. $active .'" data-tab="'. $key .'"><a href="#'. $key .'">'. $setting['title'] .'</a></li>';
                        }
                    ?>
                </ul>
            </div>

            <div class="betterdocs-settings-content">
                <?php 
                    include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-settings-form.php';
                    if( ! class_exists( 'Betterdocs_Pro' ) ) {
                        include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-settings-sidebar.php';
                    }
                ?>
            </div>
            <?php include BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-settings-blocks.php'; ?>
        </div>
        <?php 
            do_action( 'betterdocs_after_settings_left' );
        ?>
    </div>
</div>
