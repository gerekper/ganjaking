<form id="YLC_popup_form" action="">
    <label for="YLC_msg_name">
		<?php esc_html_e( 'Your Name', 'yith-live-chat' ) ?>
    </label>:
    <div class="form-line">
        <input type="text" name="name" id="YLC_msg_name" placeholder="<?php esc_html_e( 'Please enter your name', 'yith-live-chat' ) ?>">
        <i class="chat-ico ylc-icons ylc-icons-user-name"></i>
    </div>
    <label for="YLC_msg_email">
		<?php esc_html_e( 'Your Email', 'yith-live-chat' ) ?>
    </label>:
    <div class="form-line">
        <input type="email" name="email" id="YLC_msg_email" placeholder="<?php esc_html_e( 'Please enter your email', 'yith-live-chat' ) ?>">
        <i class="chat-ico ylc-icons ylc-icons-user-email"></i>
    </div>
    <label for="YLC_msg_message">
		<?php esc_html_e( 'Your Message', 'yith-live-chat' ) ?>
    </label>:
    <div class="form-line">
        <textarea id="YLC_msg_message" name="message" placeholder="<?php esc_html_e( 'Write your question', 'yith-live-chat' ) ?>" class="chat-field"></textarea>
    </div>
	<?php if ( ylc_get_option( 'offline-gdpr-compliance', ylc_get_default( 'offline-gdpr-compliance' ) ) == 'yes' ): ?>
        <div class="form-line">
            <div class="chat-checkbox">
                <input type="checkbox" name="gdpr_acceptance" id="YLC_gdpr_acceptance">
                <label for="YLC_gdpr_acceptance">
					<?php echo ylc_sanitize_text( ylc_get_option( 'offline-gdpr-checkbox-label', ylc_get_default( 'offline-gdpr-checkbox-label' ) ) ) ?>
                </label>
            </div>
            <br />
			<?php

			$privacy_page = ylc_get_option( 'offline-gdpr-privacy-page', ylc_get_default( 'offline-gdpr-privacy-page' ) );

			if ( $privacy_page == '' ) {
				$privacy_page = get_permalink( get_option( 'wp_page_for_privacy_policy' ) );
			}

			$gdpr_text = ylc_sanitize_text( ylc_get_option( 'offline-gdpr-checkbox-desc', ylc_get_default( 'offline-gdpr-checkbox-desc' ) ), true );
			$gdpr_text = str_replace( '{', '<a href="' . $privacy_page . '" target="_blank">', $gdpr_text );
			$gdpr_text = str_replace( '}', '</a>', $gdpr_text );

			echo $gdpr_text ?>
        </div>
	<?php endif; ?>
    <div class="chat-send">
        <div id="YLC_offline_ntf" class="chat-ntf"></div>
        <a href="javascript:void(0)" id="YLC_send_btn" class="chat-form-btn">
			<?php esc_html_e( 'Send', 'yith-live-chat' ) ?>
        </a>
    </div>
</form>