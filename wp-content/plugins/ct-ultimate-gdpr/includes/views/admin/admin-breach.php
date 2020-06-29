<?php

/**
 * The template for displaying breach controller view in wp-admin
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
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>



    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-forgotten' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Right To Be Forgotten', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-dataaccess' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Data Access', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" class="nav-tab nav-tab-active">
		    <?php echo esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-rectification' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Data Rectification', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>


    <form id="<?php echo esc_attr( CT_Ultimate_GDPR_Controller_Breach::ID ); ?>" method="post" action="options.php">

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Breach::ID );
		do_settings_sections( CT_Ultimate_GDPR_Controller_Breach::ID );


		?>

        <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
            <p><?php echo esc_html__("Press 'Save changes' before sending emails if you changed any options","ct-ultimate-gdpr"); ?></p>
            <?php
            submit_button();
            ?>
        </div>

    </form>



    <form method="post">
		<?php

		submit_button(
			esc_html__( 'Go to send emails screen', 'ct-ultimate-gdpr' ),
			'primary',
			"ct-ultimate-gdpr-breach-send-screen-submit",
			false
		);

		?>
    </form>

</div>

<script type="text/javascript">
    jQuery('.ct-ultimate-gdpr-field').on('change', function () {
        jQuery('input[name^=ct-ultimate-gdpr-breach-send-screen-submit]').attr("disabled", "disabled");
    });
</script>