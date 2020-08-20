<?php

/**
 * The template for displaying [ultimate_gdpr_center] shortcode view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/shortcode folder
 *
 * @version 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="container detailed-features">

    <div class="row">

		<?php if ( $url = ct_ultimate_gdpr_get_value( 'myaccount_url', $options ) ) : ?>

            <div class="feature"><a href="<?php
		            echo esc_url(
			            add_query_arg(
				            array(
					            '#tabs-1' => '',
				            ),
				            $url
			            )
		            );
		            ?>" target="_blank"
                                       class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-database" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section"><?php echo esc_html__( 'Data access', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a
                            href="<?php
							echo esc_url(
								add_query_arg(
									array(
										'#tabs-1' => '',
									),
									$url
								)
							);
							?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?></a> <span class="fa fa-list"></span></div>
            </div>

            <div class="feature"><a href="<?php
		            echo esc_url(
			            add_query_arg(
				            array(
					            '#tabs-2' => '',
				            ),
				            $url
			            )
		            );
		            ?>" target="_blank"
                                       class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-eye-slash" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section"><?php echo esc_html__( 'Right to be forgotten', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a
                            href="<?php
                            echo esc_url(
	                            add_query_arg(
		                            array(
			                            '#tabs-2' => '',
		                            ),
		                            $url
	                            )
                            );
                            ?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span class="fa fa-list"></span></a></div>
            </div>

            <div class="feature"><a href="<?php
		            echo esc_url(
			            add_query_arg(
				            array(
					            '#tabs-3' => '',
				            ),
				            $url
			            )
		            );
		            ?>" target="_blank"
                                       class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-folder-open" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section"><?php echo esc_html__( 'Data rectification', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a
                            href="<?php
                            echo esc_url(
	                            add_query_arg(
		                            array(
			                            '#tabs-3' => '',
		                            ),
		                            $url
	                            )
                            );
                            ?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span class="fa fa-list"></span></a></div>
            </div>

            <div class="feature"><a href="<?php
		            echo esc_url(
			            add_query_arg(
				            array(
					            '#tabs-4' => '',
				            ),
				            $url
			            )
		            );
		            ?>" target="_blank" class="ct-full-link"></a>
					<div class="icon-wrapper section"><span class="fa fa-envelope" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
					<div class="text section text-capitalize"><?php echo esc_html__( 'Unsubscribe', 'ct-ultimate-gdpr' ); ?></div>
					<div class="ct-btn section text-uppercase"><a href="<?php
						echo esc_url(
							add_query_arg(
								array(
									'#tabs-4' => '',
								),
								$url
							)
						);
						?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span class="fa fa-list"></span></a></div>
			</div>

		<?php endif; ?>

            <div class="feature"><a href="#" target="_blank" class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-cog" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section text-capitalize"><?php echo esc_html__( 'Cookie settings', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase">[ultimate_gdpr_cookie_popup]<?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?>
                    [/ultimate_gdpr_cookie_popup]<span class="fa fa-list"></span></div>
            </div>

		<?php if ( $url = ct_ultimate_gdpr_get_value( 'policy_url', $options ) ) : ?>

            <div class="feature"><a href="<?php echo esc_url( $url ); ?>" target="_blank" class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-calendar-check-o" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section text-capitalize"><?php echo esc_html__( 'Privacy policy', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span
                                class="fa fa-list"></span></a></div>
            </div>

		<?php endif; ?>

		<?php if ( $url = ct_ultimate_gdpr_get_value( 'terms_url', $options ) ) : ?>

            <div class="feature"><a href="<?php echo esc_url( $url ); ?>" target="_blank"
                                       class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-check-circle" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section text-capitalize"><?php echo esc_html__( 'Terms &amp; conditions pages', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span class="fa fa-list"></span></a></div>
            </div>

		<?php endif; ?>

		<?php if ( $url = ct_ultimate_gdpr_get_value( 'contact_url', $options ) ) : ?>

            <div class="feature"><a href="<?php echo esc_url( $url ); ?>" target="_blank" class="ct-full-link"></a>
                <div class="icon-wrapper section"><span class="fa fa-user" style="color:<?php echo esc_attr( $options['icon_color'] ); ?>"></span></div>
                <div class="text section"><?php echo esc_html__( 'Contact DPO', 'ct-ultimate-gdpr' ); ?></div>
                <div class="ct-btn section text-uppercase"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Read more', 'ct-ultimate-gdpr' ); ?> <span
                                class="fa fa-list"></span></a></div>
            </div>

		<?php endif; ?>

    </div>
</div>
