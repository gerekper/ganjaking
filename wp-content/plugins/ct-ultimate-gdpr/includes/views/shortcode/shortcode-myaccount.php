<?php
/**
 * The template for displaying [ultimate_gdpr_myaccount] shortcode view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/shortcode folder
 *
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// DEFINE THIS VARIABLE WITH USER CHOICE FOR SKIN
// eg.
$ct_form_skin       = $options['form_shape'];
$ct_form_skin_class = ( strlen( $ct_form_skin ) > 0 ) ? ' ' . $ct_form_skin : '';
$ct_page_link       = get_permalink();
?>

<div class="ct-ultimate-gdpr-container ct-ultimate-gdpr-my-account <?php echo esc_attr( $ct_form_skin_class ); ?>">
	
	<?php if ( isset( $options['notices'] ) ) : ?>
		<?php foreach ( $options['notices'] as $notice ) : ?>

            <div class="notice-info notice">
				<?php echo esc_html( $notice ); ?>
            </div>
		
		<?php endforeach; endif; ?>

    <div id="tabs">
        <ul>
            <li>
                <a href="#tabs-1">
					<?php echo esc_html__( 'Personal Data Access', 'ct-ultimate-gdpr' ); ?>
                </a>
            </li>
            <li>
                <a href="#tabs-2">
					<?php echo esc_html__( 'Forget me', 'ct-ultimate-gdpr' ); ?>
                </a>
            </li>
            <li>
                <a href="#tabs-3">
					<?php echo esc_html__( 'Data rectification', 'ct-ultimate-gdpr' ); ?>
                </a>
            </li>
			<?php if ( $options['unsubscribe_hide_unsubscribe_tab'] != "on" ): ?>
                <li>
                    <a href="#tabs-4">
						<?php echo esc_html__( 'Unsubscribe', 'ct-ultimate-gdpr' ); ?>
                    </a>
                </li>
			<?php endif; ?>
        </ul>

        <div id="tabs-1">

            <div class="ct-headerContent">
				<?php echo esc_html__( 'Below, you can request for your personal data that\'s collected by this website sent to you in an email.', 'ct-ultimate-gdpr' ); ?>
            </div>

            <form id="ct-ultimate-gdpr-data-access" method="post">
                <label for="ct-ultimate-gdpr-email"><?php echo esc_html__( 'Email:', 'ct-ultimate-gdpr' ); ?></label>
				<?php if (
                    $ct_form_skin == ''
                    || $ct_form_skin == 'ct-ultimate-gdpr-simple-form'
                    || $ct_form_skin == 'ct-ultimate-gdpr-rounded-form'
                ) : ?>
                    <input type="email" name="ct-ultimate-gdpr-email" value="" required="" id="ct-ultimate-gdpr-email">
				<?php endif; ?>
                <label for="ct-ultimate-gdpr-consent-data-access">
                    <input type="checkbox" id="ct-ultimate-gdpr-consent-data-access"
                           name="ct-ultimate-gdpr-consent-data-access" required="">
                    <span class="ct-checkbox"></span>
					<?php echo esc_html__( "I consent to have my email collected in order to receive my requested data. See Privacy Policy page for more information.", 'ct-ultimate-gdpr' ); ?>
                </label>
				<?php
					if ( $ct_form_skin == 'ct-ultimate-gdpr-tabbed-form' ) :
						?>
                        <input type="email" name="ct-ultimate-gdpr-email" value="" required="">
					<?php endif; ?>
				
				<?php if ( $options['recaptcha_key'] ) : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $options['recaptcha_key'] ); ?>"></div>
				<?php endif; ?>

                <input type="hidden" name="ct-ultimate-gdpr-request-url" value="<?php echo $ct_page_link; ?>"/>
                <input type="submit" name="ct-ultimate-gdpr-data-access-submit"
                       value="<?php echo esc_html__( "Submit", 'ct-ultimate-gdpr' ); ?>">

            </form>
        </div>

        <div id="tabs-2">
            <div class="ct-headerContent">
				<?php echo esc_html__( 'Below, you can browse services which collects your personal data on this website. Check services you wish to be forgotten by. This will send a request to the website admin. You will be notified by email once this is done."', 'ct-ultimate-gdpr' ); ?>
            </div>

            <form id="ct-ultimate-gdpr-forget" method="post">

                <div class="ct-ultimate-gdpr-services-list">
					
					<?php
						
						/** @var CT_Ultimate_GDPR_Service_Abstract $service */
						foreach ( $options['services'] as $service ):
							
							if ( ! $service->is_forgettable() ) {
								continue;
							}
							
							?>
                            <div class="ct-ultimate-gdpr-service-options">
                                <div class="ct-ultimate-gdpr-service-option">
                                    <input type="checkbox" name="ct-ultimate-gdpr-service-forget[]"
                                           value="<?php echo esc_attr( $service->get_id() ); ?>">
                                    <span class="ct-checkbox"></span>
                                </div>
                                <div class="ct-ultimate-gdpr-service-details">
                                    <div class="ct-ultimate-gdpr-service-title"><?php echo esc_html( $service->get_service_name() ); ?></div>
                                    <div class="ct-ultimate-gdpr-service-description"><?php echo esc_html( $service->get_description() ); ?></div>
                                </div>
                            </div>
						
						
						<?php endforeach; ?>


                    <div class="ct-ultimate-gdpr-services-email">

                        <label for="ct-ultimate-gdpr-forget-email"><?php echo esc_html__( 'Email:', 'ct-ultimate-gdpr' ); ?></label>
						<?php if (
                            $ct_form_skin == '' ||
                            $ct_form_skin == 'ct-ultimate-gdpr-simple-form'
                            || $ct_form_skin == 'ct-ultimate-gdpr-rounded-form'
                        ) : ?>
                            <input type="email" name="ct-ultimate-gdpr-email" value="" required=""
                                   id="ct-ultimate-gdpr-forget-email">
						<?php endif; ?>
                    </div>

                    <label for="ct-ultimate-gdpr-consent-forget-me">
                        <input type="checkbox" id="ct-ultimate-gdpr-consent-forget-me"
                               name="ct-ultimate-gdpr-consent-forget-me" required="">
                        <span class="ct-checkbox"></span>
						<?php echo esc_html__( "I consent to have my email collected in order to process this request. See Privacy Policy page for more information.", 'ct-ultimate-gdpr' ); ?>
                    </label>
					<?php
						if ( $ct_form_skin == 'ct-ultimate-gdpr-tabbed-form' ) :
							?>
                            <input type="email" name="ct-ultimate-gdpr-email" value="" required="">
						<?php endif; ?>
					
					<?php if ( $options['recaptcha_key'] ) : ?>
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo esc_attr( $options['recaptcha_key'] ); ?>"></div>
					<?php endif; ?>

                    <input type="hidden" name="ct-ultimate-gdpr-request-url" value="<?php echo $ct_page_link; ?>"/>
                    <input type="submit" class="ct-ultimate-gdpr-forget-submitBtn" name="ct-ultimate-gdpr-forget-submit"
                           value="<?php echo esc_html__( "Submit", 'ct-ultimate-gdpr' ); ?>">


                </div>

            </form>
        </div>

        <div id="tabs-3">
            <div class="ct-headerContent">
				<?php echo esc_html__( 'Below, you can send a request to have your data rectified by  the website admin. Enter what you would like to be rectified. You will be notified by email once this is done.', 'ct-ultimate-gdpr' ); ?>
            </div>

            <form id="ct-ultimate-gdpr-rectification" method="post">

                <div class="ct-ultimate-gdpr-services-email">

                    <label for="ct-ultimate-gdpr-rectification-data-current" class="ct-u-display-block">
						<?php echo esc_html__( "Current data", 'ct-ultimate-gdpr' ); ?>
                    </label>
                    <textarea
                            name="ct-ultimate-gdpr-rectification-data-current"
                            rows="5"
                            required
                            id="ct-ultimate-gdpr-rectification-data-current"
                    ></textarea>

                </div>

                <div class="ct-ultimate-gdpr-services-email">

                    <label for="ct-ultimate-gdpr-rectification-data-rectified" class="ct-u-display-block">
						<?php echo esc_html__( "Rectified data", 'ct-ultimate-gdpr' ); ?>
                    </label>
                    <textarea
                            name="ct-ultimate-gdpr-rectification-data-rectified"
                            rows="5"
                            required
                            id="ct-ultimate-gdpr-rectification-data-rectified"
                    ></textarea>

                </div>

                <div class="ct-ultimate-gdpr-services-email">

                    <label for="ct-ultimate-gdpr-rectify-email"><?php echo esc_html__( 'Email:', 'ct-ultimate-gdpr' ); ?></label>
					<?php if (
                        $ct_form_skin == ''
                        || $ct_form_skin == 'ct-ultimate-gdpr-simple-form'
                        || $ct_form_skin == 'ct-ultimate-gdpr-rounded-form'
                    ) : ?>
                        <input
                                type="email"
                                name="ct-ultimate-gdpr-email"
                                value=""
                                required=""
                                id="ct-ultimate-gdpr-rectify-email"
                        >
					<?php endif; ?>
                </div>

                <label for="ct-ultimate-gdpr-consent-data-rectification">
                    <input type="checkbox" id="ct-ultimate-gdpr-consent-data-rectification"
                           name="ct-ultimate-gdpr-consent-data-rectification" required="">
                    <span class="ct-checkbox<?php echo esc_attr( $ct_form_skin_class ); ?>"></span>
					<?php echo esc_html__( "I consent to have my email collected in order to process this request. See Privacy Policy page for more information.", 'ct-ultimate-gdpr' ); ?>
                </label>
				<?php
					if ( $ct_form_skin == 'ct-ultimate-gdpr-tabbed-form' ) :
						?>
                        <input type="email" name="ct-ultimate-gdpr-email" value="" required="">
					<?php endif; ?>
				
				<?php if ( $options['recaptcha_key'] ) : ?>
                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $options['recaptcha_key'] ); ?>"></div>
				<?php endif; ?>

                <input type="hidden" name="ct-ultimate-gdpr-request-url" value="<?php echo $ct_page_link; ?>"/>
                <input type="submit" class="ct-ultimate-gdpr-forget-submitBtn"
                       name="ct-ultimate-gdpr-rectification-submit"
                       value="<?php echo esc_html__( "Submit", 'ct-ultimate-gdpr' ); ?>">


            </form>
        </div>
		<?php if ( $options['unsubscribe_hide_unsubscribe_tab'] != "on" ): ?>
            <div id="tabs-4">
                <div class="ct-headerContent">
					<?php echo esc_html__( 'Below, you can browse services which sign up users to newsletters. Check services which newsletters you wish to be unsubscribed from. The subscription will stop immediately.', 'ct-ultimate-gdpr' ); ?>
                </div>
                <form id="ct-ultimate-gdpr-unsubscribe" method="post">
                    <div class="ct-ultimate-gdpr-services-list">
						<?php
							/** @var CT_Ultimate_GDPR_Service_Abstract $service */
							foreach ( $options['services'] as $service ):
								if ( ! $service->is_subscribeable() ) {
									continue;
								}
								?>
                                <div class="ct-ultimate-gdpr-service-options">
                                    <div class="ct-ultimate-gdpr-service-option">
                                        <input type="checkbox" name="ct-ultimate-gdpr-service-unsubscribe[]"
                                               value="<?php echo esc_attr( $service->get_id() ); ?>">
                                        <span class="ct-checkbox"></span>
                                    </div>
                                    <div class="ct-ultimate-gdpr-service-details">
                                        <div class="ct-ultimate-gdpr-service-title"><?php echo esc_html( $service->get_service_name() ); ?></div>
                                        <div class="ct-ultimate-gdpr-service-description"><?php echo esc_html( $service->get_description() ); ?></div>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        <div class="ct-ultimate-gdpr-services-email">
                            <label for="ct-ultimate-gdpr-unsubscribe-email"><?php echo esc_html__( 'Email:', 'ct-ultimate-gdpr' ); ?></label>
							<?php if (
                                $ct_form_skin == ''
                                || $ct_form_skin == 'ct-ultimate-gdpr-simple-form'
                                || $ct_form_skin == 'ct-ultimate-gdpr-rounded-form'
                            ) : ?>
                                <input type="email" name="ct-ultimate-gdpr-email" value="" required=""
                                       id="ct-ultimate-gdpr-unsubscribe-email">
							<?php endif; ?>
                        </div>
                        <label for="ct-ultimate-gdpr-consent-unsubscribe">
                            <input type="checkbox" id="ct-ultimate-gdpr-consent-unsubscribe"
                                   name="ct-ultimate-gdpr-consent-unsubscribe" required="">
                            <span class="ct-checkbox"></span>
							<?php echo esc_html__( "I consent to have my email collected in order to process this request. See Privacy Policy page for more information.", 'ct-ultimate-gdpr' ); ?>
                        </label>
						<?php
							if ( $ct_form_skin == 'ct-ultimate-gdpr-tabbed-form' ) :
								?>
                                <input type="email" name="ct-ultimate-gdpr-email" value="" required="">
							<?php endif; ?>
						<?php if ( $options['recaptcha_key'] ) : ?>
                            <div class="g-recaptcha"
                                 data-sitekey="<?php echo esc_attr( $options['recaptcha_key'] ); ?>"></div>
						<?php endif; ?>
                        <input type="hidden" name="ct-ultimate-gdpr-request-url" value="<?php echo $ct_page_link; ?>"/>
                        <input type="submit" class="ct-ultimate-gdpr-unsubscribe-submitBtn"
                               name="ct-ultimate-gdpr-unsubscribe-submit"
                               value="<?php echo esc_html__( "Submit", 'ct-ultimate-gdpr' ); ?>">
                    </div>
                </form>
            </div>
		<?php endif; ?>
    </div>

</div>