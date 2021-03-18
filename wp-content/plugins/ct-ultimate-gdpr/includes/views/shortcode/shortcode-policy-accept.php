<?php

/**
 * The template for displaying [ultimate_gdpr_policy_accept] shortcode view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/shortcode folder
 *
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var array $options */
$policy_btn_style = '';
$btn_shape = '';
$cookie_options =  CT_Ultimate_GDPR::instance()->get_admin_controller()->get_options( CT_Ultimate_GDPR_Controller_Cookie::ID );
if ( isset( $options['policy_btn_styling'] ) ) :
	if ( $options['policy_btn_styling'] == 'policy_cookie_btn' ) :
		$policy_btn_style = 'ct-ultimate-gdpr-btn-cookie';
		if ( $cookie_options['cookie_button_shape'] == 'rounded' ) :
			$btn_shape = 'ct-ultimate-gdpr-btn-cookie-rounded';
		endif;
	endif;
endif;
?>

<div class="ct-ultimate-gdpr-container container">
	<?php if ( ! empty( $options['policy_accepted'] ) ) : ?>

        <div id="ct-ultimate-gdpr-policy-accepted">
			<?php echo esc_html__( 'You have already accepted Privacy Policy', 'ct-ultimate-gdpr' ); ?>
        </div>

        <button id="ct-ultimate-gdpr-policy-decline" class="ct-ultimate-gdpr-button <?php echo esc_attr( $policy_btn_style ); ?> <?php echo esc_attr( $btn_shape); ?>"
			<?php if( $policy_btn_style ) : ?>
                style="color: <?php echo esc_attr( $cookie_options['cookie_button_text_color'] ); ?>;
                        border-color: <?php echo esc_attr( $cookie_options['cookie_button_border_color'] ); ?>;
                        background-color: <?php echo esc_attr( $cookie_options['cookie_button_bg_color'] ); ?>;"
			<?php endif; ?>>
			<?php echo esc_html__( 'Decline', 'ct-ultimate-gdpr' ); ?>
        </button>

	<?php else: ?>

        <button id="ct-ultimate-gdpr-policy-accept" class="ct-ultimate-gdpr-button <?php echo esc_attr( $policy_btn_style ); ?> <?php echo esc_attr( $btn_shape); ?>"
			<?php if( $policy_btn_style ) : ?>
                style="color: <?php echo esc_attr( $cookie_options['cookie_button_text_color'] ); ?>;
                        border-color: <?php echo esc_attr( $cookie_options['cookie_button_border_color'] ); ?>;
                        background-color: <?php echo esc_attr( $cookie_options['cookie_button_bg_color'] ); ?>;"
			<?php endif; ?>>
			<?php echo esc_html__( 'Accept', 'ct-ultimate-gdpr' ); ?>
        </button>

	<?php endif; ?>
</div>