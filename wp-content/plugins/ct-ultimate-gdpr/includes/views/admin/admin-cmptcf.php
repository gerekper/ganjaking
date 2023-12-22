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
<style>
@media (min-width: 1279px){
    .tcf-columns {
        display: flex;
        max-width: 1200px; /* Adjust as needed */
    }
    .tcf-columns > div {
        flex: 1;
        margin-right:20px;
    }
}
</style>
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

    <h3><?php echo esc_html__('TCF Compliance', 'ct-ultimate-gdpr'); ?></h3>

    <form method="post" action="options.php">
        <div class="ct-ultimate-gdpr-wrap ct-clearfix ct-ultimate-gdpr-width tcf-columns">
            <?php
            // This prints out all hidden setting fields
            settings_fields( CT_Ultimate_GDPR_Controller_Cmptcf::ID );
            ct_ultimate_gdpr_do_settings_sections(CT_Ultimate_GDPR_Controller_Cmptcf::ID )
            ?>
        </div>
        <div class="clear"></div>
        <!-- / ct-ultimate-gdpr-wrap -->
        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">

            <?php
            submit_button();
            ?>
        </div>
    </form>
</div>


