<?php
$threads        = $member['forum']['support_threads'];
$all_threads    = array();
$url_revoke      = wp_nonce_url( add_query_arg( 'action', 'remote-revoke', $urls->support_url . '#access' ), 'remote-revoke', 'hash' );
$url_extend      = wp_nonce_url( add_query_arg( 'action', 'remote-extend', $urls->support_url . '#access' ), 'remote-extend', 'hash' );

foreach ( $threads as $thread ) {
    $total_thread = array();
	if ( empty( $thread['title'] ) ) {
		continue;
	}
	if ( empty( $thread['status'] ) ) {
		continue;
	}

	if ( 'resolved' === $thread['status'] ) {
        continue;
    }

    if ( isset( $thread['unread'] ) && $thread['unread'] ) {
        $all_threads[] = array(
            'class' => 'sui-tag sui-tag-yellow sui-tag-sm',
            'text'  => __( 'Feedback', 'wpmudev' ),
            'title' => $thread['title'],
            'url'   => $thread['link'],
        );
    } else {
        $all_threads[]    = array(
            'class' => 'sui-tag sui-tag-blue sui-tag-sm',
            'text'  => __( 'Open', 'wpmudev' ),
            'title' => $thread['title'],
            'url'   => $thread['link'],
        );
    }

}
// BOX: Tools ?>

    <div class="sui-box">

        <div class="sui-box-header">
            <h3 class="sui-box-title">
                <i class="sui-icon-help-support" aria-hidden="true"></i>
                <?php esc_html_e( 'Support', 'wpmudev' ); ?>
            </h3>
            <?php if ( ! $staff_login->enabled ) { ?>
                <div class="sui-actions-right">
                    <a href="<?php echo esc_url( $urls->support_url . '#access' ); ?>" style="font-size:13px">
                        <?php esc_html_e( 'Grant support access', 'wpmudev' ); ?>
                    </a>
                </div>
            <?php } ?>
        </div>

        <div class="sui-box-body">
            <p><?php esc_html_e( 'Get 24/7 support for any issue you’re having. When you have active tickets they’ll be displayed here.', 'wpmudev' ); ?></p>
            <?php if( ! $staff_login->enabled && empty( $all_threads ) ) :
                printf(
                    '<a href="%s" target="_blank" class="sui-button sui-button-blue"><i class="sui-icon-plus" aria-hidden="true"></i>%s </a>',
                    esc_url( 'https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal' ),
                    esc_html__( 'Get Support' )
                );
            endif;
            ?>
        </div>
        <?php if ( $staff_login->enabled ) { ?>
            <div class="sui-notice dashui-notice-support" style="margin: 0px 30px 30px;">

                <p><?php echo esc_html( sprintf( __( "You have an active support session. If you haven't already, please let support staff know you have granted access. It will remain active for another %s.", 'wpmudev' ), human_time_diff( $staff_login->expires ) ) ); ?></p>

                <div class="sui-notice-buttons">

                    <a
                        href="<?php echo esc_url( $url_revoke ); ?>"
                        class="sui-button js-loading-link"
                    >
                        <span class="sui-loading-text">
                            <?php esc_html_e( 'END SESSION', 'wpmudev' ); ?>
                        </span>
                        <i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
                    </a>

                    <a href="<?php echo esc_url( $url_extend ); ?>"
                    class="sui-button sui-button-ghost sui-tooltip js-loading-link"
                    data-tooltip="<?php esc_attr_e( 'Add another 3 days of support access', 'wpmudev' ); ?>"
                        <?php echo( ! is_wpmudev_member() ? 'disabled="disabled"' : '' ); ?>>
                        <span class="sui-loading-text">
                            <?php esc_html_e( 'EXTEND', 'wpmudev' ); ?>
                        </span>
                        <i class="sui-icon-loader sui-loading" aria-hidden="true"></i>
                    </a>

                </div>

            </div>
        <?php
        }
        if ( ! empty( $all_threads ) ) {
        ?>
        <table class="sui-table sui-table-flushed" style="border-top:1px solid #e6e6e6; ">
            <tbody>
                    <?php foreach( $all_threads as $thread ){ ?>
                        <tr>
                            <td class="dashui-item-content">
                                <p>
                                    <?php echo esc_html( wp_trim_words( $thread['title'], 6, '...' ) ); ?>
                                </p>
                            </td>
                            <td>
                                <span class="<?php echo esc_attr( $thread['class'] ); ?>"> <?php echo esc_html( $thread['text'] ); ?></span>
                            </td>
                            <td>
                                <a class="sui-button-icon" href="<?php echo esc_url( $thread['url'] ); ?>">
                                    <i class="sui-icon-eye" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php
        }
        ?>
        <?php //box footer ?>
        <?php if ( ! empty( $all_threads ) || $staff_login->enabled ) { ?>
            <div class="sui-box-footer">
                <?php if ( ! empty( $all_threads ) ) { ?>
                    <a href="<?php echo esc_url( $urls->support_url ); ?>" class="sui-button sui-button-ghost">
                        <i class="sui-icon-eye" aria-hidden="true"></i>
                        <?php esc_html_e( 'VIEW ALL', 'wpmudev' ); ?>
                    </a>
                <?php } ?>
                <div class="sui-actions-right">
                    <?php
                        printf(
                            '<a href="%s" target="_blank" class="sui-button sui-button-blue"><i class="sui-icon-plus" aria-hidden="true"></i>%s </a>',
                            esc_url( 'https://premium.wpmudev.org/hub/support/#wpmud-chat-pre-survey-modal' ),
                            esc_html__( 'Get Support' )
                        );
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
