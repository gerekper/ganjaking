<?php
$args = array(
    "app_id"        => WIS_FACEBOOK_CLIENT_ID,
    "state"         => $current_url
);
$autorize_url = "https://instagram.cm-wp.com/facebook/?" . http_build_query( $args );
$accounts = WIS_Plugin::app()->getPopulateOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME, array() );
?>
<div class="factory-bootstrap-424 factory-fontawesome-000">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div id="wis-add-account-button" class="">
						<?php
						if ( count( $accounts ) && !WIS_Plugin::app()->is_premium()) : ?>
                            <span class="wis-btn-facebook-account btn-instagram-account-disabled">
                                <?php _e('Add Account','instagram-slider-widget')?></span>
                            <span class="instagram-account-pro"><?php echo sprintf( __( "More accounts in <a href='%s'>PRO version</a>", 'instagram-slider-widget' ), WIS_Plugin::app()->get_support()->get_pricing_url(true, "wis_settings") );?></span>
						<?php else: ?>
                            <a class="wis-btn-facebook-account" target="_self" href="<?php echo $autorize_url; ?>" title="Add Account">
								<?php _e('Add Account','instagram-slider-widget')?>
                            </a>
                            <span style="float: none; margin-top: 0;" class="spinner" id="wis-spinner"> </span>
						<?php endif; ?>
                    </div>
                    <div class="wis-help-text"><?php echo sprintf( __( "After adding an account, go to the <a href='%s'>widget settings</a> and change the \"Search Facebook for\" setting to Account", 'instagram-slider-widget' ), admin_url('widgets.php')) ?></div>
					<?php
					if ( count( $accounts )) :
						?>
                        <br>
                        <table class="widefat wis-table">
                            <thead>
                            <tr>
                                <th><?php echo __( 'Image', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'ID', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Name', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Token', 'instagram-slider-widget' ); ?></th>
                                <th><?php echo __( 'Action', 'instagram-slider-widget' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							foreach ( $accounts as $profile_info ) {
								$image    = $profile_info['picture']['data']['url'];
								$fullname = $profile_info['name'];
								?>
                                <tr>
                                    <td class="profile-picture">
                                        <img src="<?php echo esc_url( $image ); ?>"
                                             width="30"/>
                                    </td>
                                    <td><?php echo esc_attr( $profile_info['id'] ); ?></td>
                                    <td>
                                        <a href="https://www.facebook.com/<?php echo esc_html( $profile_info['username'] ); ?>"><?php echo esc_html( $profile_info['name'] ); ?></a>
                                    </td>
                                    <td>
                                        <input id="<?php echo esc_attr( $profile_info['id'] ); ?>-access-token"
                                               type="text"
                                               value="<?php echo esc_attr( $profile_info['token'] ); ?>"
                                               class="wis-text-token" readonly/>
                                    </td>
                                    <td>
                                        <a href="#"
                                           data-item_id="<?php echo esc_attr( $profile_info['id'] ); ?>"
                                           class="btn btn-danger wis-delete-account">
                                            <span class="dashicons dashicons-trash"></span><?php echo __( 'Delete', 'instagram-slider-widget' ); ?>
                                        </a>
                                        <span class="spinner"
                                              id="wis-delete-spinner-<?php echo esc_attr( $profile_info['id'] ); ?>"></span>
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
    </div>
</div>