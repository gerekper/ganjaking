<?php

/**
 * The template for displaying main plugin options page
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

	<?php if ( isset( $options['notices'] ) ) : ?>
		<?php foreach ( $options['notices'] as $notice ) : ?>

            <div class="ct-ultimate-gdpr notice-info notice">
				<?php echo $notice; ?>
            </div>

		<?php endforeach; endif; ?>

    <!-- TABS ( cc ) -->
    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Introduction', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-plugins' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Compatibility', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>
    <!-- END TABS ( cc ) -->

    <div class="ct-ultimate-gdpr-width"><!-- ADD DIV WITH .ct-ultimate-gdpr-width ( cc ) -->

        <div class="ct-ultimate-gdpr-inner-wrap"><!-- ADD DIV WITH .ct-ultimate-gdpr-inner-wrap ( cc ) -->
            <p>
                <strong><?php echo esc_html__( 'The GDPR was approved and adopted by the EU Parliament in April 2016.', 'ct-ultimate-gdpr' ); ?></strong>
				<?php echo esc_html__( " The regulation will take effect after a two-year transition period and, unlike a Directive it does not require any enabling legislation to be passed by government; meaning it will be in force May 2018.", 'ct-ultimate-gdpr' ); ?>
            </p>

            <p>
				<?php echo esc_html__( "The GDPR not only applies to organisations located within the EU but it will also apply to organisations located outside of the EU if they offer goods or services to, or monitor the behaviour of, EU data subjects. It applies to all companies processing and holding the personal data of data subjects residing in the European Union, regardless of the company's location.", 'ct-ultimate-gdpr' ); ?>
            </p>

            <p>

				<?php echo esc_html__( "This plugin will create a form where users can request access to or deletion of their personal data, stored on your website. It is also possible to:", 'ct-ultimate-gdpr' ); ?>

            </p>
            <ol>
                <li><?php echo esc_html__( "Create a custom cookie notice and block all cookies until cookie consent is given.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Set up redirects for your Terms and Conditions and Privacy Policy pages until consent is given.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Browse user requests for data access/deletion and set custom email notifications.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Send custom email informing about data breach to all users which left their email at your site.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Automatically add consent boxes for various forms on your website.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Pseudonymize some of user data stored in database.", 'ct-ultimate-gdpr' ); ?></li>
                <li><?php echo esc_html__( "Check currently activated plugins for GDPR compliance.", 'ct-ultimate-gdpr' ); ?></li>
            </ol>
            <p></p>

            <p>
				<?php echo esc_html__( "To start, browse through settings. Then, create a new page with shortcode:", 'ct-ultimate-gdpr' ); ?>
                <strong> <?php echo esc_html__( '[ultimate_gdpr_myaccount]', 'ct-ultimate-gdpr' ); ?></strong>
            </p>
        </div><!-- ADD CLOSING DIV FOR .ct-ultimate-gdpr-inner-wrap ( cc ) -->

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="col-md-12 ct-ultimate-gdpr-avail-sc ct-ultimate-gdpr-inner-wrap">
                    <strong class="text-capitalize ct-ultimate-gdpr-head"><?php echo esc_html__( 'Available Shortcodes', 'ct-ultimate-gdpr' ); ?></strong>
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="ct-ultimate-gdpr-compact-list">
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'User Settings:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo esc_html__( '[ultimate_gdpr_myaccount]', 'ct-ultimate-gdpr' ); ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Privacy Policy Button:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo esc_html__( '[ultimate_gdpr_policy_accept]', 'ct-ultimate-gdpr' ); ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Terms and Conditions Button:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo esc_html__( '[ultimate_gdpr_terms_accept]', 'ct-ultimate-gdpr' ); ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Cookie Popup Link:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo esc_html__( '[ultimate_gdpr_cookie_popup]Link [/ultimate_gdpr_cookie_popup]', 'ct-ultimate-gdpr' ); ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Display Cookies List:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo esc_html__( '[render_cookies_list]', 'ct-ultimate-gdpr' ); ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Protect Content:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo '[ultimate_gdpr_protection level=4]'; ?>
                                            </strong>
                                            <br>
                                                <?php echo esc_html__( 'content', 'ct-ultimate-gdpr' ); ?>
                                            <br>
                                            <strong>
                                                <?php echo '[/ultimate_gdpr_protection]'; ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-4">
                                            <?php echo esc_html__( 'Privacy Center:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-8">
                                            <strong>
                                                <?php echo '[ultimate_gdpr_center myaccount_page=15 contact_page=18 icon_color=#e03131]'; ?>
                                            </strong>
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="col-md-12 ct-ultimate-gdpr-avail-sc ct-ultimate-gdpr-inner-wrap">
                    <strong class="text-capitalize ct-ultimate-gdpr-head"><?php echo esc_html__( 'System requirements:', 'ct-ultimate-gdpr' ); ?></strong>
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="ct-ultimate-gdpr-compact-list text-capitalize">
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-6 col-sm-4 col-md-6 col-lg-4">
                                            <?php echo esc_html__( 'PHP Version:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-6 col-sm-8 col-md-6 col-lg-8">
                                            <strong>
                                        <?php echo esc_html__( '5.4+', 'ct-ultimate-gdpr' ); ?>
                                    </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-6 col-sm-4 col-md-6 col-lg-4">
                                            <?php echo esc_html__( 'Memory limit:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-6 col-sm-8 col-md-6 col-lg-8">
                                            <strong>
                                        <?php echo esc_html__( '64 MB', 'ct-ultimate-gdpr' ); ?>
                                    </strong>
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="row no-gutters">
                                        <span class="col-6 col-sm-4 col-md-6 col-lg-4">
                                            <?php echo esc_html__( 'Disk space:', 'ct-ultimate-gdpr' ); ?>
                                        </span>
                                        <span class="col-6 col-sm-8 col-md-6 col-lg-8">
                                            <strong>
                                        <?php echo esc_html__( '10 MB', 'ct-ultimate-gdpr' ); ?>
                                    </strong>
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12 ct-ultimate-gdpr-wrap">

                <form method="post" action="options.php">

                    <?php

                    // This prints out all hidden setting fields
                    settings_fields( CT_Ultimate_GDPR_Controller_Admin::ID );
                    ct_ultimate_gdpr_do_settings_sections( CT_Ultimate_GDPR_Controller_Admin::ID );

                    ?>

                    <div class="ct-ultimate-gdpr-msg-clone-static-caution ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-width ct-submit-section">
                        <p><?php echo esc_html__( "Press 'Save changes' before downloading logs if you changed any options", "ct-ultimate-gdpr" ); ?></p>
                        <?php
                        submit_button();
                        ?>
                    </div>

                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-12 ct-ultimate-gdpr-inner-wrap">
                    <strong><?php echo esc_html__( 'Export/import options', 'ct-ultimate-gdpr' ); ?></strong>
                    <p>
						<?php echo esc_html__( 'You can export current plugin settings to a file which can be used to import the settings on other websites', 'ct-ultimate-gdpr' ); ?>
                    </p>
                    <form method="post" id="ct-ultimate-gdpr-export-form">
                        <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-export"
                               value="<?php echo esc_html__( 'Export plugin settings', 'ct-ultimate-gdpr' ); ?>">
                    </form>

                    <p>
						<?php echo esc_html__( 'Select file to import settings from. Your current settings will be overwritten.', 'ct-ultimate-gdpr' ); ?>
                    </p>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="ct-ultimate-gdpr-settings-file" id="ct-ultimate-gdpr-settings-file">
                        <br>
                        <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-import"
                               value="<?php echo esc_html__( 'Import plugin settings', 'ct-ultimate-gdpr' ); ?>">
                    </form>

                    <p>
						<?php echo esc_html__( 'Export services from Service Manager', 'ct-ultimate-gdpr' ); ?>
                    </p>
                    <form method="post" enctype="multipart/form-data">
                        <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-export-services"
                               value="<?php echo esc_html__( 'Export services', 'ct-ultimate-gdpr' ); ?>">
                    </form>

                    <p>
						<?php echo esc_html__( 'Select file to add services from. Your current services will not be removed.', 'ct-ultimate-gdpr' ); ?>
                    </p>
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="ct-ultimate-gdpr-services-file" id="ct-ultimate-gdpr-services-file">
                        <br>
                        <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-import-services"
                               value="<?php echo esc_html__( 'Import services to Service Manager', 'ct-ultimate-gdpr' ); ?>">
                    </form>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="col-xs-12 col-md-12 ct-ultimate-gdpr-inner-wrap">
                    <strong><?php echo esc_html__( 'Any questions?', 'ct-ultimate-gdpr' ); ?></strong>
                    <p>
						<?php echo esc_html__( 'Please have a look at the', 'ct-ultimate-gdpr' ); ?> <a
                                href="https://createit.support/"
                                class="text-capitalize ct-ultimate-gdpr-link"><?php echo esc_html__( 'Support forum', 'ct-ultimate-gdpr' ); ?></a> <?php echo esc_html__( 'or ask your questions via email', 'ct-ultimate-gdpr' ); ?>
                        <a href="mailto:support@createit.pl"
                           class="ct-ultimate-gdpr-link"><?php echo esc_html__( 'Any questions?', 'ct-ultimate-gdpr' ); ?></a>
                    </p>
                    <p><?php echo esc_html__( 'Link to our ', 'ct-ultimate-gdpr' ); ?> <a
                                href="http://gdpr-plugin.readthedocs.io/"
                                class="ct-ultimate-gdpr-link"><?php echo esc_html__( 'Documentation', 'ct-ultimate-gdpr' ); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div><!-- ADD CLOSING DIV FOR .ct-ultimate-gdpr-width ( cc ) -->
</div>
