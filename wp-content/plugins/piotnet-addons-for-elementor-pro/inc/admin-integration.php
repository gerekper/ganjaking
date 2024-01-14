<?php
    function PAFE_generate_key($key){
        $new_key = '';
        if(!empty($key) || $key != false){

            return '****************************' . substr($key, -5);;
        }
        return $new_key;
    }
    function PAFE_generate_input_api($key, $name){
        if(!empty($key)){
            $html = '<div style="display: flex;"><input type="text" class="regular-text pafe-preview-value" value="' .PAFE_generate_key($key). '" readonly/>';
            $html .= '<input type="hidden" name="' . $name . '" value="' .$key. '" class="regular-text pafe-hidden-value" />';
            $html .= '<button type="button" class="pafe-change-api-button">Change</button><button type="button" data-value="'.$key.'" class="pafe-cancel-api-button" style="display:none;">Cancel</button></div>';
        }else{
            $html = '<input type="text" name="' . $name . '" class="regular-text" />';
        }
        return $html;
    }
?>
<?php if( get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-sheets-connector', 2 ) == 1 ) : ?>

<h3 data-pafe-dashboard-accordion-trigger='google-sheet' class='pafe-dashboard-accordion-trigger'><?php _e('Google Sheets Integration','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='google-sheet'>
    <iframe width="560" height="315" src="https://www.youtube.com/embed/5W1fMu3fFj0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-sheets-group' ); ?>
        <?php

        $client_id     = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-sheets-client-id' ) );
        $client_secret = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-sheets-client-secret' ) );
        $redirect =  get_admin_url(null,'admin.php?page=piotnet-addons-for-elementor&connect_type=google_sheets'); //For PAFE

        if ( ! empty( $_GET['connect_type'] ) && $_GET['connect_type'] == 'google_sheets' && ! empty( $_GET['code'] ) ) {
            // Authorization
            $code = $_GET['code'];
            // Token
            $url  = 'https://accounts.google.com/o/oauth2/token';
            $curl = curl_init();
            $data = "code=$code&client_id=$client_id&client_secret=$client_secret&redirect_uri=" . urlencode($redirect) . "&grant_type=authorization_code";

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://accounts.google.com/o/oauth2/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            //echo $response;
            $array = json_decode( $response );

            if ( ! empty( $array->access_token ) && ! empty( $array->refresh_token ) && ! empty( $array->expires_in ) ) {
                $pafe_ggsheets_expired_at = time() + $array->expires_in;
				update_option( 'piotnet-addons-for-elementor-pro-google-sheets-expires', $array->expires_in );
                update_option( 'piotnet-addons-for-elementor-pro-google-sheets-expired-token', $pafe_ggsheets_expired_at );
                update_option( 'piotnet-addons-for-elementor-pro-google-sheets-access-token', $array->access_token );
                update_option( 'piotnet-addons-for-elementor-pro-google-sheets-refresh-token', $array->refresh_token );
            }
        }
        ?>
        <div style="padding-top: 30px;">
            <b><a href="https://console.developers.google.com/flows/enableapi?apiid=sheets.googleapis.com" target="_blank"><?php _e('Click here to Sign into your Gmail account and access Google Sheets’s application registration','pafe'); ?></a></b>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Client ID','pafe'); ?></th>
                <td>
                    <?php echo PAFE_generate_input_api($client_id, 'piotnet-addons-for-elementor-pro-google-sheets-client-id'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($client_secret, 'piotnet-addons-for-elementor-pro-google-sheets-client-secret'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Authorized redirect URI','pafe'); ?></th>
                <td><input type="text" readonly="readonly" value="<?php echo $redirect; ?>" class="regular-text"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Authorizaion','pafe'); ?></th>
                <td>
                    <?php if ( !empty($client_id) && !empty($client_secret) ) : ?>
                        <a class="pafe-toggle-features__button" href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo urlencode($redirect); ?>&client_id=<?php echo $client_id; ?>&response_type=code&scope=https://www.googleapis.com/auth/spreadsheets&approval_prompt=force&access_type=offline">Authorize</a>
                    <?php else : ?>
                        <?php _e('To setup Gmail integration properly you should save Client ID and Client Secret.','pafe'); ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>

<?php endif; ?>

<?php if( get_option( 'pafe-features-form-google-calendar-connector', 2 ) == 2 || get_option( 'pafe-features-form-google-calendar-connector', 2 ) == 1 ) : ?>
<h3 data-pafe-dashboard-accordion-trigger='google-calendar' class='pafe-dashboard-accordion-trigger'><?php esc_html_e( 'Google Calendar Integration', 'pafe' ); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='google-calendar'>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-calendar-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-calendar-group' ); ?>
        <?php
        $redirect      =  get_admin_url(null,'admin.php?page=piotnet-addons-for-elementor&connect_type=google_calendar');
        $gg_cld_client_id     = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-id' ) );
        $gg_cld_client_secret = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-secret' ) );
        $client_api_key = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-google-calendar-client-api-key' ) );

        if ( ! empty( $_GET['connect_type'] ) && $_GET['connect_type'] == 'google_calendar' && ! empty( $_GET['code'] ) ) {
            // Authorization
            $code = $_GET['code'];
            // Token
            $curl = curl_init();
            $data = "code=$code&client_id=$gg_cld_client_id&client_secret=$gg_cld_client_secret&redirect_uri=" . urlencode($redirect) . "&grant_type=authorization_code";

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://accounts.google.com/o/oauth2/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            //echo $response;
            $array = json_decode( $response );
            if ( ! empty( $array->access_token ) && ! empty( $array->refresh_token ) && ! empty( $array->expires_in ) ) {
                $pafe_ggcalendar_expired_at = time() + $array->expires_in;
                update_option( 'piotnet-addons-for-elementor-pro-google-calendar-expires', $array->expires_in );
                update_option( 'piotnet-addons-for-elementor-pro-google-calendar-expired-token', $pafe_ggcalendar_expired_at );
                update_option( 'piotnet-addons-for-elementor-pro-google-calendar-access-token', $array->access_token );
                update_option( 'piotnet-addons-for-elementor-pro-google-calendar-refresh-token', $array->refresh_token );
                function pafe_google_calendar_get_calendar_id($access_token, $client_api_key) {
                    $curl = curl_init();

                    curl_setopt_array( $curl, array(
                        CURLOPT_URL            => "https://www.googleapis.com/calendar/v3/users/me/calendarList?minAccessRole=writer&key=$client_api_key",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 30,
                        CURLOPT_CUSTOMREQUEST  => "GET",
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HTTPHEADER     => array(
                            "Authorization: Bearer $access_token",
                            "Accept: application/json"
                        ),
                    ));
                    $response = curl_exec( $curl );
                    curl_close( $curl );

                    $response = json_decode($response);
                    //print_r($response);
                    $gg_calendar_items = $response->items;
                    $gg_calendar_id = null;
                    foreach ( $gg_calendar_items as $gg_calendar_item ) {
                        $gg_calendar_item_id = $gg_calendar_item->id;
                        if (empty($gg_calendar_id)) {
                            $gg_calendar_id = $gg_calendar_item_id;
                        }
                        if ( !empty($gg_calendar_item->primary) && $gg_calendar_item->primary == 1 ) {
                            $gg_calendar_id = $gg_calendar_item_id;
                            break;
                        }
                    }
                    return $gg_calendar_id;
                }
                $gg_calendar_id = pafe_google_calendar_get_calendar_id($array->access_token, $client_api_key);
                update_option('piotnet-addons-for-elementor-pro-google-calendar-id', $gg_calendar_id);
            }
        }
        ?>
        <div style="padding-top: 30px;">
            <b><a href="https://console.developers.google.com/" target="_blank"><?php esc_html_e( 'Click here to Sign into your Gmail account and access Google Calendar’s application registration', 'pafe' ); ?></a></b>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Client ID', 'pafe' ); ?></th>
                <td>
                <?php echo PAFE_generate_input_api($gg_cld_client_id, 'piotnet-addons-for-elementor-pro-google-calendar-client-id'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Client Secret', 'pafe' ); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($gg_cld_client_secret, 'piotnet-addons-for-elementor-pro-google-calendar-client-secret'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'API Key', 'pafe' ); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($client_api_key, 'piotnet-addons-for-elementor-pro-google-calendar-client-api-key'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Authorized redirect URI', 'pafe' ); ?></th>
                <td><input type="text" readonly="readonly" value="<?php echo $redirect; ?>" class="regular-text"/></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e( 'Authorize', 'pafe' ); ?></th>
                <td>
                    <?php if ( ! empty( $gg_cld_client_id ) && ! empty( $gg_cld_client_secret ) ) : ?>
                        <a class="pafe-toggle-features__button" href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo urlencode($redirect); ?>&client_id=<?php echo $gg_cld_client_id; ?>&response_type=code&scope=https://www.googleapis.com/auth/calendar.readonly https://www.googleapis.com/auth/calendar.events&approval_prompt=force&access_type=offline">Authorize</a>
                    <?php else : ?>
                        <?php esc_html_e( 'To setup Gmail integration properly you should save Client ID and Client Secret.', 'pafe' ); ?>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php submit_button( __( 'Save Settings', 'pafe' ) ); ?>
    </form>
</div>
<hr>

<?php endif; ?>

<?php if( get_option( 'pafe-features-address-autocomplete-field', 2 ) == 2 || get_option( 'pafe-features-address-autocomplete-field', 2 ) == 1 ) : ?>
<h3 data-pafe-dashboard-accordion-trigger='google-maps' class='pafe-dashboard-accordion-trigger'><?php _e('Google Maps Integration','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='google-maps'>
    <iframe width="400" height="250" src="https://www.youtube.com/embed/_YhQWreCZwA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-google-maps-group' ); ?>
        <?php
        $google_maps_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-google-maps-api-key') );
        ?>
        <div style="padding-top: 30px;">
            <b><a href="https://cloud.google.com/maps-platform/?apis=maps,places" target="_blank"><?php _e('Click here to get Google Maps API Key','pafe'); ?></a></b>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Google Maps API Key','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($google_maps_api_key, 'piotnet-addons-for-elementor-pro-google-maps-api-key'); ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>
<?php endif; ?>

<?php if( get_option( 'pafe-features-stripe-payment', 2 ) == 2 || get_option( 'pafe-features-stripe-payment', 2 ) == 1 ) : ?>

<h3 data-pafe-dashboard-accordion-trigger='stripe' class='pafe-dashboard-accordion-trigger'><?php _e('Stripe Integration','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='stripe'>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-stripe-group' ); ?>
        <?php
        $publishable_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-publishable-key') );
        $secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-stripe-secret-key') );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Publishable Key','pafe'); ?></th>
                <td>
                <?php echo PAFE_generate_input_api($publishable_key, 'piotnet-addons-for-elementor-pro-stripe-publishable-key'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($secret_key, 'piotnet-addons-for-elementor-pro-stripe-secret-key'); ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>

<?php endif; ?>

<?php if( get_option( 'pafe-features-paypal-payment', 2 ) == 2 || get_option( 'pafe-features-paypal-payment', 2 ) == 1 ) : ?>

<h3 data-pafe-dashboard-accordion-trigger='paypal' class='pafe-dashboard-accordion-trigger'><?php _e('Paypal Integration','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='paypal'>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-paypal-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-paypal-group' ); ?>
        <?php
        $client_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-paypal-client-id') );
        $client_secret = esc_attr( get_option('piotnet-addons-for-elementor-pro-paypal-client-secret') );
        ?>
        <table class="form-table">
            <div style="padding-top: 30px;">
                <b><a href="https://developer.paypal.com/developer/applications/" target="_blank"><?php _e('Click here to Create app and get the Client ID','pafe'); ?></a></b>
            </div>
            <tr valign="top">
                <th scope="row"><?php _e('Client ID','pafe'); ?></th>
                <td>
                <?php echo PAFE_generate_input_api($client_id, 'piotnet-addons-for-elementor-pro-paypal-client-id'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($client_secret, 'piotnet-addons-for-elementor-pro-paypal-client-secret'); ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>

<?php endif; ?>

<h3 data-pafe-dashboard-accordion-trigger='mollie' class='pafe-dashboard-accordion-trigger'><?php _e('Mollie Payment','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='mollie'>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-mollie-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mollie-group' ); ?>
        <?php
        $mollie_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mollie-api-key') );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('API Key','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($mollie_api_key, 'piotnet-addons-for-elementor-pro-mollie-api-key'); ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>

<h3 data-pafe-dashboard-accordion-trigger='razorpay' class='pafe-dashboard-accordion-trigger'><?php _e('Razorpay Payment','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='razorpay'>
    <form method="post" action="options.php">
        <?php settings_fields( 'piotnet-addons-for-elementor-pro-razorpay-group' ); ?>
        <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-razorpay-group' ); ?>
        <?php
            $razor_key_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-razorpay-api-key') );
            $razor_key_secret = esc_attr( get_option('piotnet-addons-for-elementor-pro-razorpay-secret-key') );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Key ID','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($razor_key_id, 'piotnet-addons-for-elementor-pro-razorpay-api-key'); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Key Secret','pafe'); ?></th>
                <td class="pafe-settings-page-td">
                <?php echo PAFE_generate_input_api($razor_key_secret, 'piotnet-addons-for-elementor-pro-razorpay-secret-key'); ?>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>
<hr>
<?php if( get_option( 'pafe-features-hubspot', 2 ) == 2 || get_option( 'pafe-features-hubspot', 2 ) == 1 ) : ?>

<h3 data-pafe-dashboard-accordion-trigger='hubspot' class='pafe-dashboard-accordion-trigger'><?php _e('Hubspot Integration','pafe'); ?></h3>
<div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='hubspot'>
    <form method="post" action="options.php">
		<?php settings_fields( 'piotnet-addons-for-elementor-pro-hubspot-group' ); ?>
		<?php do_settings_sections( 'piotnet-addons-for-elementor-pro-hubspot-group' ); ?>
		<?php $hubspot_access_token = esc_attr( get_option( 'piotnet-addons-for-elementor-pro-hubspot-access-token' ) ); ?>

        <div style="padding-top: 30px;">
            <b><a href="https://app.hubspot.com/login" target="_blank"><?php esc_html_e( 'Click here to Sign into your Hubspot Account and take Access Token', 'pafe' ); ?></a></b>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Private App Access Token','pafe'); ?></th>
                <td>
                <?php echo PAFE_generate_input_api($hubspot_access_token, 'piotnet-addons-for-elementor-pro-hubspot-access-token'); ?>
                </td>
            </tr>
        </table>
		<?php submit_button(__('Save Settings','pafe')); ?>
    </form>
</div>

<hr>

<?php endif; ?>

<?php if( get_option( 'pafe-features-form-builder', 2 ) == 2 || get_option( 'pafe-features-form-builder', 2 ) == 1 ) : ?>

    <h3 data-pafe-dashboard-accordion-trigger='mailchimp' class='pafe-dashboard-accordion-trigger'><?php _e('MailChimp Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='mailchimp'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailchimp-group' ); ?>
            <?php
            $api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($api_key, 'piotnet-addons-for-elementor-pro-mailchimp-api-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='mailerlite' class='pafe-dashboard-accordion-trigger'><?php _e('MailerLite Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='mailerlite'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-mailerlite-group' ); ?>
            <?php
            $api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($api_key, 'piotnet-addons-for-elementor-pro-mailerlite-api-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='sendinblue' class='pafe-dashboard-accordion-trigger'><?php _e('Sendinblue Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='sendinblue'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-sendinblue-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-sendinblue-group' ); ?>
            <?php
            $sendinblue_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-sendinblue-api-key') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($sendinblue_api_key, 'piotnet-addons-for-elementor-pro-sendinblue-api-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='activecampaign' class='pafe-dashboard-accordion-trigger'><?php _e('ActiveCampaign Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='activecampaign'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-activecampaign-group' ); ?>
            <?php
            $api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key') );
            $api_url = esc_attr( get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($api_key, 'piotnet-addons-for-elementor-pro-activecampaign-api-key'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('API URL','pafe'); ?></th>
                    <td>
                    <?php echo PAFE_generate_input_api($api_url, 'piotnet-addons-for-elementor-pro-activecampaign-api-url'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='getresponse' class='pafe-dashboard-accordion-trigger'><?php _e('GetResponse Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='getresponse'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-getresponse-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-getresponse-group' ); ?>
            <?php
            $getresponseapi_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-getresponse-api-key') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($getresponseapi_key, 'piotnet-addons-for-elementor-pro-getresponse-api-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='recaptcha' class='pafe-dashboard-accordion-trigger'><?php _e('reCAPTCHA (v3) Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='recaptcha'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-recaptcha-group' ); ?>
            <?php
            $site_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-site-key') );
            $secret_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key') );
            ?>
            <div style="padding-top: 30px;" data-pafe-dropdown>
                <b><a href="#" data-pafe-dropdown-trigger><?php _e('Click here to view tutorial','pafe'); ?></a></b>
                <div data-pafe-dropdown-content>
                    <p>Very first thing you need to do is register your website on Google reCAPTCHA to do that click <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>.</p>

                    <p>Login to your Google account and create the app by filling the form. Select the reCAPTCHA v3 and in that select “I am not a robot” checkbox option.</p>
                    <div>
                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-1.jpg">
                    </div>

                    <p>Once submitted, Google will provide you with the following two information: Site key, Secret key.</p>
                    <div>
                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>google-recaptcha-2.jpg">
                    </div>
                </div>
            </div>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Site Key','pafe'); ?></th>
                    <td>
                    <?php echo PAFE_generate_input_api($site_key, 'piotnet-addons-for-elementor-pro-recaptcha-site-key'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Secret Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($secret_key, 'piotnet-addons-for-elementor-pro-recaptcha-secret-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='twilio' class='pafe-dashboard-accordion-trigger'><?php _e('Twilio Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='twilio'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-twilio-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-twilio-group' ); ?>
            <?php
            $account_sid = esc_attr( get_option('piotnet-addons-for-elementor-pro-twilio-account-sid') );
            $author_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-twilio-author-token') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Account SID','pafe'); ?></th>
                    <td>
                    <?php echo PAFE_generate_input_api($account_sid, 'piotnet-addons-for-elementor-pro-twilio-account-sid'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Author Token','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($author_token, 'piotnet-addons-for-elementor-pro-twilio-author-token'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='sendfox' class='pafe-dashboard-accordion-trigger'><?php _e('Sendfox Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='sendfox'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-sendfox-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-sendfox-group' ); ?>
            <?php
            $sendfox_access_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-sendfox-access-token') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('SendFox Personal Aceess Token','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($sendfox_access_token, 'piotnet-addons-for-elementor-pro-sendfox-access-token'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='constant' class='pafe-dashboard-accordion-trigger'><?php _e('Constant contact','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='constant'>
        <?php
        $c_ID = esc_attr( get_option('piotnet-addons-for-elementor-pro-constant-contact-client-id') );
        $app_secret = get_option('piotnet-addons-for-elementor-pro-constant-contact-app-secret-id');
        $redirectURI = admin_url('admin.php?page=piotnet-addons-for-elementor');
        $baseURL = "https://authz.constantcontact.com/oauth2/default/v1/authorize";
        $authURL = $baseURL . "?client_id=" . $c_ID . "&scope=contact_data+campaign_data+account_update+account_read+offline_access&response_type=code" . "&redirect_uri=" . urlencode($redirectURI).'&state=piotnet';
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-constant-contact-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-constant-contact-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($c_ID, 'piotnet-addons-for-elementor-pro-constant-contact-client-id'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('App Secret','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($app_secret, 'piotnet-addons-for-elementor-pro-constant-contact-app-secret-id'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Authorization Redirect URI','pafe'); ?></th>
                    <td><input type="text" value="<?php echo $redirectURI; ?>" class="regular-text" readonly/></td>
                </tr>
            </table>
            <div class="piotnet-addons-zoho-admin-api">
                <?php submit_button(__('Save Settings','pafe')); ?>
                <p class="submit"><a class="button button-primary" href="<?php echo $authURL; ?>" authenticate-zoho-crm disabled>Authenticate Constant Contact</a></p>
            </div>
        </form>
        <?php

        function PAFE_constantcontact_get_token($code, $redirect_uri, $api_key, $app_secret){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://authz.constantcontact.com/oauth2/default/v1/token?code='.$code.'&redirect_uri='.urlencode($redirect_uri).'&grant_type=authorization_code',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Basic '.base64_encode($api_key.':'.$app_secret)
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response);
        }

        if(!empty($_GET['code']) && $_GET['state'] == 'piotnet'){
            $token_data = PAFE_constantcontact_get_token($_GET['code'], $redirectURI, $c_ID, $app_secret);
            if(!empty($token_data->access_token)){
                update_option('piotnet-constant-contact-access-token', $token_data->access_token);
                update_option('piotnet-constant-contact-refresh-token', $token_data->refresh_token);
                update_option('piotnet-constant-contact-time-get-token', time());
            }
        }
        ?>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='convertkit' class='pafe-dashboard-accordion-trigger'><?php _e('Convertkit Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='convertkit'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-convertkit-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-convertkit-group' ); ?>
            <?php
            $convertkit_api_key = esc_attr( get_option('piotnet-addons-for-elementor-pro-convertkit-api-key') );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('API Key','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($convertkit_api_key, 'piotnet-addons-for-elementor-pro-convertkit-api-key'); ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings','pafe')); ?>
        </form>
    </div>
    <hr>

    <h3 data-pafe-dashboard-accordion-trigger='zoho' class='pafe-dashboard-accordion-trigger'><?php _e('Zoho Integration','pafe'); ?></h3>
    <div class="pafe-license pafe-dashboard-accordion-content" data-pafe-dashboard-accordion-content='zoho'>
        <form method="post" action="options.php">
            <?php settings_fields( 'piotnet-addons-for-elementor-pro-zoho-group' ); ?>
            <?php do_settings_sections( 'piotnet-addons-for-elementor-pro-zoho-group' ); ?>
            <?php
            $zoho_domain = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-domain') );
            $client_id = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-client-id') );
            $redirect_url = admin_url('admin.php?page=piotnet-addons-for-elementor');
            $client_secret = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-client-secret') );
            $token = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-token') );
            $refresh_token = esc_attr( get_option('piotnet-addons-for-elementor-pro-zoho-refresh-token') );
            $zoho_domains = ["accounts.zoho.com", "accounts.zoho.com.au", "accounts.zoho.eu", "accounts.zoho.in", "accounts.zoho.com.cn"];
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Domain','pafe'); ?></th>
                    <td>
                        <select name="piotnet-addons-for-elementor-pro-zoho-domain">
                            <?php foreach($zoho_domains as $zoho){
                                if($zoho_domain == $zoho){
                                    echo '<option value="'.$zoho.'" selected>'.$zoho.'</option>';
                                }else{
                                    echo '<option value="'.$zoho.'">'.$zoho.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Client ID','pafe'); ?></th>
                    <td>
                    <?php echo PAFE_generate_input_api($client_id, 'piotnet-addons-for-elementor-pro-zoho-client-id'); ?>
                        <a target="_blank" href="https://accounts.zoho.com/developerconsole">How to create client id and Screct key</a>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Client Secret','pafe'); ?></th>
                    <td class="pafe-settings-page-td">
                    <?php echo PAFE_generate_input_api($client_secret, 'piotnet-addons-for-elementor-pro-zoho-client-secret'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Authorization Redirect URI','pafe'); ?></th>
                    <td><input type="text" name="piotnet-addons-for-elementor-pro-zoho-redirect-url" value="<?php echo $redirect_url; ?>" class="regular-text" readonly/></td>
                </tr>
            </table>
            <div class="piotnet-addons-zoho-admin-api">
                <?php submit_button(__('Save Settings','pafe')); ?>
                <?php
                $scope_module = 'ZohoCRM.modules.all,ZohoCRM.settings.all';
                $oauth = 'https://'.$zoho_domain.'/oauth/v2/auth?scope='.$scope_module.'&client_id='.$client_id.'&response_type=code&access_type=offline&redirect_uri='.$redirect_url.'';
                echo '<p class="piotnet-addons-zoho-admin-api-authenticate submit"><a class="button button-primary" href="'.$oauth.'" authenticate-zoho-crm disabled>Authenticate Zoho CRM</a></p>';
                ?>
                <?php if(!empty($_REQUEST['code']) && !empty($_REQUEST['accounts-server'])):
                $url_get_token = 'https://'.$zoho_domain.'/oauth/v2/token?client_id='.$client_id.'&grant_type=authorization_code&client_secret='.$client_secret.'&redirect_uri='.$redirect_url.'&code='.$_REQUEST['code'].'';
                $zoho_response = wp_remote_post($url_get_token, array());
                if(!empty($zoho_response['body'])){
                    $zoho_response = json_decode($zoho_response['body']);
                    if(empty($zoho_response->error)){
                        update_option('zoho_access_token', $zoho_response->access_token);
                        update_option('zoho_refresh_token', $zoho_response->refresh_token);
                        update_option('zoho_api_domain', $zoho_response->api_domain);
                        echo "Success";
                    }else{
                        echo $zoho_response->error;
                    }
                }

                ?>
                <script type="text/javascript">
                    window.history.pushState({}, '','<?php echo admin_url('admin.php?page=piotnet-addons-for-elementor'); ?>' );
                </script>
                <?php endif; ?>
            </div>
        </form>
    </div>

<?php endif; ?>