<?php

$username = $usermail = '';

if ( apply_filters( 'ylc_prefill_login_logged_user', false ) == true && is_user_logged_in() ) {

    $user     = wp_get_current_user();
	$username = $user->display_name;
	$usermail = $user->user_email;

}

?>
<div id="YLC">

    <div id="YLC_chat_btn" class="chat-chat-btn btn-<?php echo $args['button_type']; ?>">
        <div class="chat-ico chat ylc-icons ylc-icons-chat"></div>
        <div class="chat-ico ylc-toggle ylc-icons ylc-icons-angle-<?php echo( $args['button_pos'] == 'bottom' ? 'up' : 'down' ) ?>"></div>
        <div class="chat-title">
			<?php echo ylc_sanitize_text( ylc_get_option( 'text-chat-title', ylc_get_default( 'text-chat-title' ) ) ) ?>
        </div>
    </div>

    <div id="YLC_chat" class="chat-widget">

        <div id="YLC_chat_header" class="chat-header">
            <div class="chat-ico chat ylc-icons ylc-icons-chat"></div>
            <div class="chat-ico ylc-toggle ylc-icons ylc-icons-angle-<?php echo( $args['button_pos'] == 'bottom' ? 'down' : 'up' ) ?>"></div>
            <div class="chat-title">
				<?php echo ylc_sanitize_text( ylc_get_option( 'text-chat-title', ylc_get_default( 'text-chat-title' ) ) ) ?>
            </div>
            <div class="chat-clear"></div>
        </div>

        <div id="YLC_chat_body" class="chat-body chat-online" style="<?php echo $args['chat_width'] ?>">
            <div class="chat-cnv" id="YLC_cnv">
                <div class="chat-welc">
					<?php echo ylc_sanitize_text( ylc_get_option( 'text-start-chat', ylc_get_default( 'text-start-chat' ) ), true ) ?>
                </div>
            </div>
            <div class="chat-tools">
                <a id="YLC_tool_end_chat" href="javascript:void(0)">
                    <i class="ylc-icons ylc-icons-close"></i>
					<?php esc_html_e( 'End chat', 'yith-live-chat' ) ?>
                </a>
                <div id="YLC_popup_ntf" class="chat-ntf"></div>
            </div>
            <div class="chat-cnv-reply">
                <div class="chat-cnv-input">
                    <textarea id="YLC_cnv_reply" name="msg" class="chat-reply-input" placeholder="<?php esc_html_e( 'Type here and hit enter to chat', 'yith-live-chat' ) ?>"></textarea>
                </div>
            </div>
        </div>

        <div id="YLC_connecting" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
            <div class="chat-sending chat-conn">
				<?php esc_html_e( 'Connecting', 'yith-live-chat' ) ?>...
            </div>
        </div>

        <div id="YLC_offline" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
            <div class="chat-lead op-offline">
				<?php echo ylc_sanitize_text( ylc_get_option( 'text-offline', ylc_get_default( 'text-offline' ) ), true ) ?>
            </div>
            <div class="chat-lead op-busy">
				<?php echo ylc_sanitize_text( ylc_get_option( 'text-busy', ylc_get_default( 'text-busy' ) ), true ) ?>
            </div>
			<?php if ( defined( 'YLC_PREMIUM' ) && YLC_PREMIUM ) : ?>

				<?php ylc_get_template( 'chat-frontend/chat-offline-form-premium.php', $args ); ?>

			<?php endif; ?>
        </div>

        <div id="YLC_login" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
            <div class="chat-lead">
				<?php echo ylc_sanitize_text( ylc_get_option( 'text-welcome', ylc_get_default( 'text-welcome' ) ), true ) ?>
            </div>
            <form id="YLC_login_form" action="">
                <label for="YLC_field_name">
					<?php esc_html_e( 'Your Name', 'yith-live-chat' ) ?>
                </label>:
                <div class="form-line">
                    <input type="text" name="user_name" id="YLC_field_name" placeholder="<?php esc_html_e( 'Please enter your name', 'yith-live-chat' ) ?>" value="<?php echo $username ?>">
                    <i class="chat-ico ylc-icons ylc-icons-user-name"></i>
                </div>
                <label for="YLC_field_email">
					<?php esc_html_e( 'Your Email', 'yith-live-chat' ) ?>
                </label>:
                <div class="form-line">
                    <input type="email" name="user_email" id="YLC_field_email" placeholder="<?php esc_html_e( 'Please enter your email', 'yith-live-chat' ) ?>" value="<?php echo $usermail ?>">
                    <i class="chat-ico ylc-icons ylc-icons-user-email"></i>
                </div>
				<?php if ( ylc_get_option( 'chat-gdpr-compliance', ylc_get_default( 'chat-gdpr-compliance' ) ) == 'yes' && defined( 'YLC_PREMIUM' ) && YLC_PREMIUM ): ?>
                    <div class="form-line">
                        <div class="chat-checkbox">
                            <input type="checkbox" id="YLC_chat_gdpr_acceptance">
                            <label for="YLC_chat_gdpr_acceptance">
								<?php echo ylc_sanitize_text( ylc_get_option( 'chat-gdpr-checkbox-label', ylc_get_default( 'chat-gdpr-checkbox-label' ) ) ) ?>
                            </label>
                        </div>
                        <br />
						<?php

						$privacy_page = ylc_get_option( 'offline-gdpr-privacy-page', ylc_get_default( 'offline-gdpr-privacy-page' ) );

						if ( $privacy_page == '' ) {
							$privacy_page = get_permalink( get_option( 'wp_page_for_privacy_policy' ) );
						}

						$gdpr_text = ylc_sanitize_text( ylc_get_option( 'chat-gdpr-checkbox-desc', ylc_get_default( 'chat-gdpr-checkbox-desc' ) ), true );
						$gdpr_text = str_replace( '{', '<a href="' . $privacy_page . '" target="_blank">', $gdpr_text );
						$gdpr_text = str_replace( '}', '</a>', $gdpr_text );

						echo $gdpr_text;

						?>
                    </div>
				<?php endif; ?>
                <div class="chat-send">
                    <div id="YLC_login_ntf" class="chat-ntf"></div>
                    <a href="javascript:void(0)" id="YLC_login_btn" class="chat-form-btn">
						<?php esc_html_e( 'Start Chat', 'yith-live-chat' ) ?>
                    </a>
                </div>
            </form>
        </div>

        <div id="YLC_end_chat" class="chat-body chat-form" style="<?php echo $args['form_width'] ?>">
            <div class="chat-lead">
				<?php echo ylc_sanitize_text( ylc_get_option( 'text-close', ylc_get_default( 'text-close' ) ), true ); ?>
            </div>
			<?php if ( defined( 'YLC_PREMIUM' ) && YLC_PREMIUM ) : ?>

				<?php ylc_get_template( 'chat-frontend/chat-end-chat-premium.php', $args ); ?>

			<?php endif; ?>
        </div>

    </div>

</div>


