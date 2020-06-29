<?php if ( ylc_get_option( 'chat-evaluation', ylc_get_default( 'chat-evaluation' ) ) == 'yes' ): ?>

    <div class="chat-evaluation">
		<?php esc_html_e( 'Was this conversation useful? Vote this chat session.', 'yith-live-chat' ); ?>

        <div id="YLC_end_chat_ntf" class="chat-ntf"></div>
        <a href="javascript:void(0)" id="YLC_good_btn" class="good">
            <i class="ylc-icons ylc-icons-good"></i>
			<?php esc_html_e( 'Good', 'yith-live-chat' ) ?>
        </a>
        <a href="javascript:void(0)" id="YLC_bad_btn" class="bad">
            <i class="ylc-icons ylc-icons-bad"></i>
			<?php esc_html_e( 'Bad', 'yith-live-chat' ) ?>
        </a>

		<?php if ( ylc_get_option( 'transcript-send', ylc_get_default( 'transcript-send' ) ) == 'yes' ): ?>

            <div class="chat-checkbox">
                <input type="checkbox" name="request_chat" id="YLC_request_chat">
                <label for="YLC_request_chat">
					<?php esc_html_e( 'Receive the copy of the chat via e-mail', 'yith-live-chat' ) ?>
                </label>
            </div>

		<?php endif; ?>

    </div>

<?php else: ?>

	<?php if ( ylc_get_option( 'transcript-send', ylc_get_default( 'transcript-send' ) ) == 'yes' ): ?>

        <div class="chat-evaluation">
            <div id="YLC_end_chat_ntf" class="chat-ntf"></div>
            <a href="javascript:void(0)" id="YLC_chat_request">
				<?php esc_html_e( 'Receive the copy of the chat via e-mail', 'yith-live-chat' ) ?>
            </a>
        </div>

	<?php endif; ?>

<?php endif; ?>

