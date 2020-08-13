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

$ageLimitToEnter = $options['age_limit_to_enter'];
$ageLimitToSell  = $options['age_limit_to_sell'];

// DEFINE THIS VARIABLE WITH USER CHOICE FOR SKIN
// eg.
$ct_form_skin       = $options['form_shape'];
$ct_form_skin_class = ( strlen( $ct_form_skin ) > 0 ) ? ' ' . $ct_form_skin : '';
$ct_page_link       = get_permalink();

$unsusbcribe_subheader = ct_ultimate_gdpr_get_value('unsubscribe_subheader', $options, esc_html__( 'Below, you can browse services which sign up users to newsletters. Check services which newsletters you wish to be unsubscribed from. The subscription will stop immediately.', 'ct-ultimate-gdpr' ), false);
$defaultDateTime = isset($options['age_date_array']['date']) ? strtotime($options['age_date_array']['date']) : strtotime($options['age_placeholder']);

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
            <?php if ( $options['age_enabled'] ): ?>
                <li>
                    <a href="#tabs-5">
                        <?php echo esc_html__( 'Age Verification', 'ct-ultimate-gdpr' ); ?>
                    </a>
                </li>
            <?php endif; ?>
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

        <?php if ( $options['age_enabled'] ): ?>

            <div id="tabs-5">

                <form id="ct_ultimate_gdpr_age_set_date" method="post">

                    <div class="ct-headerContent">
                        <?php echo sprintf(esc_html__('You need to be at least %s years old to enter this website', 'ct-ultimate-gdpr'), $ageLimitToEnter); ?>
                    </div>

                    <label for="ct-ultimate-gdpr-age-date"><?php echo esc_html__('Date of birth', 'ct-ultimate-gdpr'); ?></label>
                    <input type="date" id="ct-ultimate-gdpr-age-date" name="ct-ultimate-gdpr-age-date" required
                           value="<?php echo date('Y-m-d', $defaultDateTime); ?>"
                           min="1900-01-01" max="<?php echo date('Y-m-d'); ?>"
                    >

                    <?php if (!empty($options['age_is_user_underage'])): ?>

                        <br>
                        <div class="ct-headerContent">
                            <?php echo esc_html__('Parent or guard authorization.', 'ct-ultimate-gdpr'); ?> <?php echo sprintf(esc_html__('Your guard needs to be at least %s years old.', 'ct-ultimate-gdpr'), $ageLimitToSell); ?>

                        </div>

                        <label for="ct-ultimate-gdpr-age-guard-name"><?php echo esc_html__('Name', 'ct-ultimate-gdpr'); ?></label>
                        <input type="text" id="ct-ultimate-gdpr-age-guard-name" name="ct-ultimate-gdpr-age-guard-name" value="<?php echo esc_html(isset($options['age_date_array']['guard_name']) ? $options['age_date_array']['guard_name'] : ''); ?>"/>

                        <label for="ct-ultimate-gdpr-age-guard-date"><?php echo esc_html__('Date of birth', 'ct-ultimate-gdpr'); ?></label>
                        <input type="date" id="ct-ultimate-gdpr-age-guard-date" name="ct-ultimate-gdpr-age-guard-date" required
                               value="<?php echo date('Y-m-d', isset($options['age_date_array']['guard_date']) ? strtotime($options['age_date_array']['guard_date']) : time()); ?>"
                               min="1900-01-01" max="<?php echo date('Y-m-d'); ?>"
                        >

                    <?php endif; ?>

                    <input type="submit" name="ct-ultimate-gdpr-age-submit"
                           value="<?php echo esc_html__("Submit", 'ct-ultimate-gdpr'); ?>">

                </form>
            </div>

        <?php endif; ?>

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
                <?php echo ( $options['my_account_disclaimer'] )? "<p>".esc_html__($options['my_account_disclaimer'], 'ct-ultimate-ccpa')."</p>":""; ?>
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
                                    <div class="ct-ultimate-gdpr-service-title"><?php echo esc_html__( $service->get_service_name(), 'ct-ultimate-gdpr' ); ?></div>
                                    <div class="ct-ultimate-gdpr-service-description"><?php echo esc_html__( $service->get_description(), 'ct-ultimate-gdpr' ); ?></div>
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
                    <?php echo ( $options['my_account_disclaimer'] )? "<p>".esc_html__($options['my_account_disclaimer'], 'ct-ultimate-ccpa')."</p>":""; ?>
                    <?php echo $unsusbcribe_subheader; ?>
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
                                        <div class="ct-ultimate-gdpr-service-title"><?php echo esc_html__( $service->get_service_name(), 'ct-ultimate-gdpr' ); ?></div>
                                        <div class="ct-ultimate-gdpr-service-description"><?php echo esc_html__( $service->get_description(), 'ct-ultimate-gdpr'  ); ?></div>
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