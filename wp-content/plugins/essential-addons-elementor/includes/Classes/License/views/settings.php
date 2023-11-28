<?php settings_fields( $this->page_slug ); ?>
<?php if ( $status == false || $status !== 'valid' ) : ?>
    <div class="eael-block p45 eael-activate__license__block">
        <div class="eael__flex eael__flex--wrap align__center mb30">
            <h3>Just one more step to go!</h3>
            <img src="<?php echo esc_url( EAEL_PLUGIN_URL . 'assets/admin/images/steps.svg' ); ?>" alt="">
        </div>
        <p><?php _e( 'Enter your license key here, to activate <strong>Essential Addons for Elementor</strong>, and get automatic updates and premium support.', $this->text_domain ); ?></p>
        <p><?php printf( __( 'Visit the <a href="%s" target="_blank">Validation Guide</a> for help.', $this->text_domain ), 'https://essential-addons.com/elementor/docs/getting-started/validating-license/' ); ?></p>
        <ol>
            <li><p><?php printf( __( 'Log in to <a href="%s" target="_blank">your account</a> to get your license key.', $this->text_domain ), 'https://wpdeveloper.com/account/' ); ?></p></li>
            <li><p><?php printf( __( 'If you don\'t yet have a license key, get <a href="%s" target="_blank">Essential Addons for Elementor now</a>.', $this->text_domain ), 'https://wpdeveloper.com/in/upgrade-essential-addons-elementor' ); ?></p></li>
            <li><?php _e( __( 'Copy the license key from your account and paste it below.', $this->text_domain ) ); ?></li>
            <li><?php _e( __( 'Click on <strong>"Activate License"</strong> button.', $this->text_domain ) ); ?></li>
        </ol>
        <div class="license__form__block">
            <div class="eael-license-form-block">
                <form method="post" action="options.php" id="eael-license-form">
					<?php wp_nonce_field( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ); ?>
                    <input id="<?php echo $this->product_slug; ?>-license-key"
                           name="<?php echo $this->product_slug; ?>-license-key" type="text" class="eael-form__control"
                           placeholder="Place Your License Key & Activate">
                    <input type="hidden" name="<?php echo $this->product_slug; ?>_license_activate"/>
                    <button type="submit" class="eael-button button__themeColor">Activate</button>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ( $status !== false && $status == 'valid' ) { ?>

    <div class="eael-block p45 eael-activate__license__block">
        <div class="eael-grid">
            <div class="eael-col-md-6">
                <ul class="eael-feature__list ls-none">
                    <li class="feature__item">
                                    <span class="icon">
                                        <img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-auto-update.svg'; ?>"
                                             alt="essential-addons-auto-update">
                                    </span>
                        <div class="content">
                            <h4>Premium Support</h4>
                            <p>Supported by professional and courteous staff.</p>
                        </div>
                    </li>
                    <li class="feature__item">
                                    <span class="icon">
                                        <img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-auto-update.svg'; ?>"
                                             alt="essential-addons-auto-update">
                                    </span>
                        <div class="content">
                            <h4>Auto Update</h4>
                            <p>Update the plugin right from your WordPress Dashboard.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="eael-col-md-6">
                <div class="license__form__block">
                    <div class="eael-license-form-block">
                        <form method="post" action="options.php" id="eael-license-form">
							<?php wp_nonce_field( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ); ?>
                            <input class="eael-form__control" disabled
                                   id="<?php echo $this->product_slug; ?>-license-key"
                                   name="<?php echo $this->product_slug; ?>-license-key" type="text"
                                   class="regular-text"
                                   value="<?php echo esc_attr( self::get_hidden_license_key() ); ?>"" placeholder="Place
                            Your License Key and Activate" />
                            <input type="hidden" name="action" value="eae_pro_deactivate_license"/>
                            <input type="hidden" name="<?php echo $this->product_slug; ?>_license_deactivate"/>
                            <button type="submit" class="eael-button button__danger">Deactivate</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
