<div class="yith-live-chat-console-container">
    <div id="YLC_console" class="yith-live-chat-console">
        <div id="YLC_sidebar_left" class="console-sidebar-left">
            <div class="sidebar-header">
                <div class="user-avatar"><img src="<?php echo apply_filters( 'ylc_console_avatar', YLC_ASSETS_URL . '/images/default-avatar-admin.png' ); ?>" /></div>
                <div class="user-name"><?php echo apply_filters( 'ylc_nickname', YITH_Live_Chat()->user->display_name ) ?></div>
                <div id="YLC_connect" class="connect button button-disabled offline">
                    <span class="ylc-icons ylc-icons-onoff"></span>
                </div>
            </div>
            <div id="YLC_users" class="sidebar-users">
                <div id="YLC_queue" class="sidebar-queue"></div>
                <div id="YLC_notify" class="sidebar-notify">
					<?php esc_html_e( 'Please wait', 'yith-live-chat' ); ?>...
                </div>
            </div>
        </div>
        <div class="console-footer">
			<?php echo apply_filters( 'ylc_console_branding', '<span>' . date( 'Y' ) . ' YITH Live Chat</span>' ) ?>
        </div>
        <div id="YLC_popup_cnv" class="chat-content chat-welcome">
            <div id="YLC_cnv" class="chat-wrapper">
                <div id="YLC_load_msg" class="chat-load-msg">
					<?php esc_html_e( 'Please wait', 'yith-live-chat' ) ?>
                </div>
            </div>
            <div id="YLC_cnv_bottom" class="chat-bottom">
                <div class="chat-notify">
                    <div id="YLC_popup_ntf"></div>
                </div>
                <div class="chat-cnv-reply">
                    <div class="user-avatar">
                        <img src="" />
                    </div>
                    <div class="chat-cnv-input">
                        <textarea name="msg" class="chat-reply-input" id="YLC_cnv_reply" placeholder="<?php esc_html_e( 'Type here and hit enter to chat', 'yith-live-chat' ) ?>"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div id="YLC_sidebar_right" class="console-sidebar-right">
            <div class="sidebar-header">
				<?php
				if ( file_exists( YLC_DIR . 'templates/chat-backend/console-save-btn-premium.php' ) ) {
					ylc_get_template( 'chat-backend/console-save-btn-premium.php', array() );
				}
				?>
                <button id="YLC_end_chat" data-cnv-id="0" class="button">
                    <span class="ylc-icons ylc-icons-close"></span>
					<?php esc_html_e( 'End chat', 'yith-live-chat' ) ?>
                </button>
                <input type="hidden" id="YLC_active_cnv" />
                <span id="YLC_save_ntf"></span>
            </div>
            <div class="sidebar-info info-name">
                <strong><span class="ylc-icons ylc-icons-user-name"></span> <?php esc_html_e( 'User Name', 'yith-live-chat' ) ?></strong>
                <span></span>
            </div>
            <div class="sidebar-info info-ip">
                <strong><span class="ylc-icons ylc-icons-user-ip"></span> <?php esc_html_e( 'IP Address', 'yith-live-chat' ) ?></strong>
                <span></span>
            </div>
            <div class="sidebar-info info-email">
                <strong><span class="ylc-icons ylc-icons-user-email"></span> <?php esc_html_e( 'User Email', 'yith-live-chat' ) ?></strong>
                <a href=""></a>
            </div>
            <div class="sidebar-info info-page">
                <strong><span class="ylc-icons ylc-icons-user-page"></span> <?php esc_html_e( 'Current Page', 'yith-live-chat' ) ?></strong>
                <a id="YLC_active_page" href="" target="_blank">
                </a>
            </div>
			<?php
			if ( file_exists( YLC_DIR . 'templates/chat-backend/console-user-tools-premium.php' ) ) {
				ylc_get_template( 'chat-backend/console-user-tools-premium.php', array() );
			}
			?>
        </div>
        <div id="YLC_firebase_offline" class="firebase-offline">
            <div><?php esc_html_e( 'Firebase offline or not available. Please wait...', 'yith-live-chat' ); ?></div>
        </div>
    </div>
</div>