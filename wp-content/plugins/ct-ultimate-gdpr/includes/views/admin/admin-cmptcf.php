<?php

/**
 * The template for displaying pseudonymization controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<?php if ( isset( $options['notices'] ) ) : ?>
    <?php foreach ( $options['notices'] as $notice ) : ?>

        <div class="ct-ultimate-gdpr notice-info notice">
            <?php echo esc_html( $notice ); ?>
        </div>

    <?php endforeach; endif; ?>

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

    <form method="post" action="options.php">
        <div class="ct-ultimate-gdpr-wrap ct-clearfix ct-tab-1 ct-ultimate-gdpr-width card-columns">
            <div class="card ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-half-width ct-ultimate-gdpr-no-pad">
                <div class="card-body">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields( CT_Ultimate_GDPR_Controller_Cmptcf::ID );
                    do_settings_sections( CT_Ultimate_GDPR_Controller_Cmptcf::ID );
                    ?>
                </div>
            </div>
        </div>
        <!-- / ct-ultimate-gdpr-wrap -->
        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">

            <?php
            submit_button();
            ?>
        </div>
    </form>


</div>


