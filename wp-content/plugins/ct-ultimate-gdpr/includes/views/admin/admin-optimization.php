<?php

/**
 * The template for displaying plugins controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<div class="ct-ultimate-gdpr-wrap">

    <div class="ct-ultimate-gdpr-branding">
        <div class="ct-ultimate-gdpr-img">
            <img src="<?php echo ct_ultimate_gdpr_url() . '/assets/css/images/branding.jpg' ?>">
        </div>
        <div class="text">
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR & CCPA', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>


    <!-- TABS ( cc ) -->
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Introduction', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-plugins' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Compatibility', 'ct-ultimate-gdpr' ); ?>
        </a>
		<a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Optimization', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>
    <!-- END TABS ( cc ) -->

    <div class="ct-ultimate-gdpr-width"><!-- ADD DIV WITH .ct-ultimate-gdpr-width ( cc ) -->

        
        <form method="post" action="options.php" enctype="multipart/form-data">

            <?php
            
            // This prints out all hidden setting fields
            settings_fields( CT_Ultimate_GDPR_Controller_Optimization::ID );
            ct_ultimate_gdpr_do_settings_sections( CT_Ultimate_GDPR_Controller_Optimization::ID );

            ?>

            
            <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
                <p><?php echo esc_html__("Press 'Save changes' before downloading logs if you changed any options","ct-ultimate-gdpr"); ?></p>
                <?php
                submit_button();
                ?>
            </div>

        </form>
    
    </div><!-- ADD CLOSING DIV FOR .ct-ultimate-gdpr-width ( cc ) -->

</div>