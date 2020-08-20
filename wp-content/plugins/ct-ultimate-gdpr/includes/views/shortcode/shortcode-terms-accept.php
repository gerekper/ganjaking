<?php

/**
 * The template for displaying [ultimate_gdpr_terms_accept] shortcode view in wp-admin
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
$term_btn_style = '';
$btn_shape = '';
$cookie_options =  CT_Ultimate_GDPR::instance()->get_admin_controller()->get_options( CT_Ultimate_GDPR_Controller_Cookie::ID );
if ( isset( $options['terms_btn_styling'] ) ) :
	if ( $options['terms_btn_styling'] == 'term_cookie_btn' ) :
		$term_btn_style = 'ct-ultimate-gdpr-btn-cookie';
		if ( $cookie_options['cookie_button_shape'] == 'rounded' ) :
			$btn_shape = 'ct-ultimate-gdpr-btn-cookie-rounded';
		endif;
	endif;
endif;
?>

<div class="ct-ultimate-gdpr-container container">
	<?php if ( ! empty( $options['terms_accepted'] ) ) : ?>

        <div id="ct-ultimate-gdpr-terms-accepted">
			<?php echo esc_html__( 'You have already accepted Terms and Conditions', 'ct-ultimate-gdpr' ); ?>
        </div>

        <button id="ct-ultimate-gdpr-terms-decline" class="ct-ultimate-gdpr-button <?php echo esc_attr( $term_btn_style ); ?> <?php echo esc_attr( $btn_shape); ?>"
			<?php if( $term_btn_style ) : ?>
                style="color: <?php echo esc_attr( $cookie_options['cookie_button_text_color'] ); ?>;
                        border-color: <?php echo esc_attr( $cookie_options['cookie_button_border_color'] ); ?>;
                        background-color: <?php echo esc_attr( $cookie_options['cookie_button_bg_color'] ); ?>;"
			<?php endif; ?>>
			<?php echo esc_html__( 'Decline', 'ct-ultimate-gdpr' ); ?>
        </button>

	<?php else: ?>

        <button id="ct-ultimate-gdpr-terms-accept" class="ct-ultimate-gdpr-button <?php echo esc_attr( $term_btn_style ); ?> <?php echo esc_attr( $btn_shape); ?>"
			<?php if( $term_btn_style ) : ?>
                style="color: <?php echo esc_attr( $cookie_options['cookie_button_text_color'] ); ?>;
                        border-color: <?php echo esc_attr( $cookie_options['cookie_button_border_color'] ); ?>;
                        background-color: <?php echo esc_attr( $cookie_options['cookie_button_bg_color'] ); ?>;"
			<?php endif; ?>>
			<?php echo esc_html__( 'Accept', 'ct-ultimate-gdpr' ); ?>
        </button>

	<?php endif; ?>
</div>