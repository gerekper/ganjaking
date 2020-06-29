<?php
$args = array(
    "client_id"     => WIS_INSTAGRAM_CLIENT_ID,
    "redirect_uri"  => "https://instagram.cm-wp.com/basic-api",
    "scope"         => "user_profile,user_media",
    "response_type" => "code",
    "state"         => $current_url,
);
$autorize_url_instagram = "https://api.instagram.com/oauth/authorize?" . http_build_query( $args );

$args = array(
	"app_id"        => WIS_FACEBOOK_CLIENT_ID,
	"state"         => $current_url.'&type=business'
);
$autorize_url_business = "https://instagram.cm-wp.com/api/?" . http_build_query( $args );


$accounts          = WIS_Plugin::app()->getPopulateOption( 'account_profiles', array() );
$accounts_business = WIS_Plugin::app()->getPopulateOption( 'account_profiles_new', array() );
$count_accounts    = count($accounts) + count( $accounts_business );
?>
<div class="factory-bootstrap-424 factory-fontawesome-000">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div id="wis-add-account-button" class="">
                        <?php
                        if ( $count_accounts >= 1 && !WIS_Plugin::app()->is_premium()) : ?>
                            <span class="wis-btn-instagram-account-disabled btn-instagram-account-disabled">
                                <?php _e('Add Account','instagram-slider-widget')?></span>
                            <span class="instagram-account-pro"><?php echo sprintf( __( "More accounts in <a href='%s'>PRO version</a>", 'instagram-slider-widget' ), WIS_Plugin::app()->get_support()->get_pricing_url(true, "wis_settings") );?></span>
                        <?php else: ?>
                            <a class="wis-btn-instagram-account" target="_self" href="#" title="Add Account">
                                <?php _e('Add Account','instagram-slider-widget')?>
                            </a>
                            <span style="float: none; margin-top: 0;" class="spinner" id="wis-spinner"> </span>
                        <?php endif; ?>
                        <!--<a class="wis-not-working" target="_blank" href="#">Button not working?</a>-->
                    </div>
                    <div id="wis-add-token" style="display: none;">
                        <form action="<?php echo admin_url( 'admin.php' ); ?>" method="GET">
                            <input type="hidden" id="page" name="page" value="settings-wisw">
                            Access token <input type="text" id="wis-manual-token" name="access_token" size="60">
                            <input type="submit" class="button button-primary button-large" value="Add account">
                            <a class="" target="_blank" href="https://instagram.cm-wp.com/get-token/">Get access token</a>
                            <span class="spinner" id="wis-add-token-spinner"></span>
                        </form>
                    </div>
                    <div class="wis-help-text"><?php echo sprintf( __( "After adding an account, go to the <a href='%s'>widget settings</a> and change the \"Search Instagram for\" setting to Account or Business Account", 'instagram-slider-widget' ), admin_url('widgets.php')) ?></div>
                    <!-- Personal accounts -->
                    <?php
                    if ( count( $accounts )) :
                        ?>
                        <h3>Personal Accounts</h3>
                        <table class="widefat wis-table wis-personal-status">
                            <thead>
                            <tr>
                                <th><?php echo __( 'ID', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'User', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Token', 'instagram-slider-widget' ); ?></th>
                                <th style="width: 256px"><?php echo __( 'Action', 'instagram-slider-widget' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ( $accounts as $profile_info ) {
                                ?>
                                <tr>
                                    <td><?php echo esc_attr( $profile_info['id'] ); ?></td>
                                    <td>
                                        <a href="https://www.instagram.com/<?php echo esc_html( $profile_info['username'] ); ?>">@<?php echo esc_html( $profile_info['username'] ); ?></a>
                                    </td>
                                    <td>
                                        <input id="<?php echo esc_attr( $profile_info['id'] ); ?>-access-token"
                                               type="text"
                                               value="<?php echo esc_attr( $profile_info['token'] ); ?>"
                                               class="wis-text-token" readonly/>
                                    </td>
                                    <td>
                                        <a href="#"
                                           data-item_id="<?php echo !empty($profile_info['id']) ? $profile_info['id'] : 0; ?>"
                                           data-is_business="0"
                                           class="btn btn-danger wis-delete-account">
                                            <span class="dashicons dashicons-trash"></span><?php echo __( 'Delete', 'instagram-slider-widget' ); ?>
                                        </a>
                                        <span class="spinner"
                                              id="wis-delete-spinner-<?php echo !empty($profile_info['id']) ? $profile_info['id'] : 0; ?>"></span>
                                        <?php
                                        if(isset($_GET['access_token']) && $_GET['access_token'] === $profile_info['token'])
                                        {
                                            ?><span class="wis-div-added">Successfully connected</span><?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php wp_nonce_field( $this->plugin->getPrefix() . 'settings_form', $this->plugin->getPrefix() . 'nonce' ); ?>
                    <?php endif; ?>
                    <!-- Business accounts -->
                    <?php
                    if ( count( $accounts_business )) :
                        ?>
                        <h3>Business Accounts</h3>
                        <table class="widefat wis-table wis-business-status">
                            <thead>
                            <tr>
                                <th><?php echo __( 'Image', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'ID', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'User', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Name', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Token', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Action', 'instagram-slider-widget' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ( $accounts_business as $profile_info ) {
                                $image    = $profile_info['profile_picture_url'];
                                $fullname = $profile_info['name'];
                                ?>
                                <tr>
                                    <td class="profile-picture">
                                        <img src="<?php echo esc_url( $image ); ?>"
                                             width="30"/>
                                    </td>
                                    <td><?php echo esc_attr( $profile_info['id'] ); ?></td>
                                    <td>
                                        <a href="https://www.instagram.com/<?php echo esc_html( $profile_info['username'] ); ?>">@<?php echo esc_html( $profile_info['username'] ); ?></a>
                                    </td>
                                    <td><?php echo esc_html( $fullname ); ?></td>
                                    <td>
                                        <input id="<?php echo esc_attr( $profile_info['id'] ); ?>-access-token"
                                               type="text"
                                               value="<?php echo esc_attr( $profile_info['token'] ); ?>"
                                               class="wis-text-token" readonly/>
                                    </td>
                                    <td>
                                        <a href="#"
                                           data-item_id="<?php echo !empty($profile_info['id']) ? $profile_info['id'] : 0; ?>"
                                           data-is_business="1"
                                           class="btn btn-danger wis-delete-account">
                                            <span class="dashicons dashicons-trash"></span><?php echo __( 'Delete', 'instagram-slider-widget' ); ?>
                                        </a>
                                        <span class="spinner"
                                              id="wis-delete-spinner-<?php echo esc_attr( $profile_info['id'] ); ?>"></span>
                                        <span class="wis-div-added" style="display: none;">Successfully connected</span>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php wp_nonce_field( $this->plugin->getPrefix() . 'settings_form', $this->plugin->getPrefix() . 'nonce' ); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div id="wis-dashboard-widget" class="wis-right-widget">
                <?php
                if(!WIS_Plugin::app()->is_premium())
                {
                    WIS_Plugin::app()->get_adverts_manager()->render_placement( 'right_sidebar');
                }
                ?>
            </div>
        </div>

        <div id="wis_add_account_modal" class="wis_accounts_modal wis_closed">
            <div class="wis_modal_header">
                Select type of account
            </div>
            <div class="wis_modal_content">

                <div class='wis-row-style'>
                    <a href="<?php echo $autorize_url_instagram; ?>" class='wis-btn-instagram-account'>Personal account</a>
                </div>
                <div class='wis-row-style'>
                    <a href="<?php echo $autorize_url_business; ?>" class='wis-btn-facebook-account'>Business account</a>
                </div>
            </div>
        </div>
        <div id="wis_add_account_modal_overlay" class="wis_modal_overlay wis_closed"></div>

    </div>
</div>