<?php //phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<div class="summary-compression" style="max-width:600px;margin:0px auto;width:100%;">
    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="margin:0px auto;padding:0 0;max-width:600px;">
        <tbody>
            <tr>
                <td colspan="3" style="margin:0px auto;padding:0 0;max-width:600px;">
                    <div style="padding:0 25px;margin:0px auto;max-width:600px;">
                        <h2 style="color:#333;font-family:inherit;font-size: 25px;line-height:30px;color:inherit;margin:0px auto;max-width: 600px;padding-top:10px;padding-bottom: 35px">
                            <?php
		                        $clean_site_url = preg_replace( '#http(s)?://(www.)?#', '', $site_url );
                                printf(
                                    esc_html( $mail_title ),
                                    '<a href="' . esc_url( $site_url ) . '" style="text-decoration: none">' . esc_html( $clean_site_url ) . '</a>'
                                );
                                ?>
                        </h2>
                        <p style="color: #1A1A1A; font-family: Roboto, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 24px; margin:0px auto;max-width: 600px; padding: 0 0 24px; text-align: left; word-wrap: normal;">
                            <?php
                            printf(
                                /* translators: %s - Name */
                                esc_html__( 'Hi %s,', 'wp-smushit' ),
                                esc_html( $name )
                            );
                            ?>
                        </p>
                        <p style="color: #1A1A1A; font-family: Roboto, Arial, sans-serif; font-size: 16px; font-weight: normal; line-height: 24px; margin:0px auto;max-width: 600px; padding: 0 0 32px; text-align: left; word-wrap: normal;letter-spacing: 0;">
                            <span><?php echo esc_html( $mail_desc ); ?></span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="margin:0px auto;max-width:600px;padding:3px 25px;border-top:1px solid #F8F8F8;border-bottom:1px solid #F8F8F8" class="smush-summary-row">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="color:#1A1A1A;font-family: Roboto, Arial, sans-serif; font-size: 16px; margin: 0 auto;max-width:600px">
                        <tbody>
                        <tr>
                            <td align="left" style="width:30px;vertical-align:top;text-align:left; padding:10px 0 0 0;">
                                <img style="max-width:100%;color:#666666;width:auto;height:16px;" src="<?php echo WP_SMUSH_URL . 'app/assets/images/email/info.png'; ?>" alt="ℹ" />
                            </td>
                            <td align="left" style="vertical-align:top;padding:10px 0;text-align:left;line-height: 25px;">
                                <strong style="font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:500;letter-spacing:-0.25px;color:#1A1A1A;">
                                    <?php echo esc_html( $total_title ); ?>
                                </strong>
                                <span style="line-height:18px;padding-top:3px;display:block;font-family: Roboto, Arial, sans-serif;font-size: 13px;font-weight:400;letter-spacing:-0.307692px;color:#1A1A1A;"><?php echo esc_html( $total_desc ); ?></span>
                            </td>
                            <td align="right" width="60" style="vertical-align:top;padding:10px 0;text-align:right;white-space:nowrap;font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:800;color:#1A1A1A;">
                                <?php echo number_format( $total_items ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="margin:0px auto;max-width:600px;padding:3px 25px;border-top:1px solid #F8F8F8;border-bottom:1px solid #F8F8F8" class="smush-summary-row">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="color:#1A1A1A;font-family: Roboto, Arial, sans-serif; font-size: 16px; margin: 0 auto;max-width:600px;">
                        <tbody>
                        <tr>
                            <td align="left" style="width:30px;vertical-align:top;text-align:left;padding:14px 0 0 0;">
                                <img style="max-width:100%;color:#11bf9c;width:auto;height:16px;" src="<?php echo WP_SMUSH_URL . 'app/assets/images/email/success.png'; ?>" alt="✔" />
                            </td>
                            <td align="left" style="vertical-align:top;padding:14px 0;text-align:left;line-height: 25px;">
                                <strong style="font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:500;letter-spacing:-0.25px;color:#1A1A1A;">
                                    <?php echo esc_html( $smushed_title ); ?>
                                </strong>
                                <span style="line-height:18px;padding-top:3px;display:block;font-family: Roboto, Arial, sans-serif;font-size: 13px;font-weight:400;letter-spacing:-0.307692px;color:#1A1A1A;"><?php echo esc_html( $smushed_desc ); ?></span>
                            </td>
                            <td align="right" width="60" style="vertical-align:top;padding:14px 0;text-align:right;white-space:nowrap;font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:800;color:#1A1A1A;">
                                <?php echo number_format( $smushed_items ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="margin:0px auto;max-width:600px;padding:3px 25px;border-top:1px solid #F8F8F8;border-bottom:1px solid #F8F8F8" class="smush-summary-row">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%" style="color:#1A1A1A;font-family: Roboto, Arial, sans-serif; font-size: 16px; margin: 0 auto;max-width:600px">
                        <tbody>
                        <tr>
                            <td align="left" style="width:30px;vertical-align:top;text-align:left; padding:10px 0 0 0;">
                                <img style="max-width:100%;color:#fb6e07;width:auto;height:16px;" src="<?php echo WP_SMUSH_URL . 'app/assets/images/email/warning.png'; ?>" alt="⚠" />
                            </td>
                            <td align="left" style="vertical-align:top;padding:10px 0;text-align:left;line-height: 25px;">
                                <strong style="font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:500;letter-spacing:-0.25px;color:#1A1A1A;">
                                    <?php echo esc_html( $failed_title ); ?>
                                </strong>
                                <span style="line-height:18px;padding-top:3px;display:block;font-family: Roboto, Arial, sans-serif;font-size: 13px;font-weight:400;letter-spacing:-0.307692px;color:#1A1A1A;">
                                    <?php echo esc_html( $failed_desc ); ?>
                                </span>
                            </td>
                            <td align="right" width="60" style="vertical-align:top;padding:10px 0;text-align:right;white-space:nowrap;font-family: Roboto, Arial, sans-serif;font-size: 16px;font-weight:800;color:#1A1A1A;">
                                <?php echo number_format( $failed_items ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <p style="margin:27px 0 11px;padding: 0;text-align: center">
        <a class="button"
        style="background:#286EFA;border-radius: 6px;font-family: Roboto, Arial, sans-serif;font-size: 13px;width: 141px;height:40px;padding: 0!important;font-weight: 500;line-height: 40px;text-align: center;margin-bottom: 0;display:inline-block!important;color:#fff!important;text-decoration:none!important;"
        href="<?php echo esc_url( $redirect_url ); ?>"><?php esc_html_e( 'View full details', 'wp-smushit' ); ?></a>
    </p>
</div>
<?php //phpcs:enable ?>