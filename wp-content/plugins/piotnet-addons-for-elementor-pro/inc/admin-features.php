<?php
// var_dump(PAFE_FEATURES_FREE);
if ( !defined('PAFE_VERSION') ) :
    ?>
    <p><?php _e('Please Install or Active Free Version on Wordpress Repository to Enable Free Features','pafe'); ?> <a href="https://wordpress.org/plugins/piotnet-addons-for-elementor" target="_blank">https://wordpress.org/plugins/piotnet-addons-for-elementor</a></p>
<?php endif; ?>

<form method="post" action="options.php" data-pafe-features>
    <?php settings_fields( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
    <?php do_settings_sections( 'piotnet-addons-for-elementor-features-settings-group' ); ?>
    <div class="pafe-toggle-features">
        <div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
        <div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
        <div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
        <br>
    </div>
    
    <?php
    require_once( __DIR__ . '/features.php' );
    $features = json_decode( PAFE_FEATURES, true );
    //$features_free = array();
    $features_all = array();

    /* if (defined('PAFE_FEATURES_FREE')) {
         $features_free = json_decode( PAFE_FEATURES_FREE, true );
     }

     if (!empty($features_free)) {
         foreach ($features_free as $feature) {
             unset($feature['extension']);
             unset($feature['form-builder']);
             unset($feature['widget']);

             $features_all[] = $feature;
         }
     }*/

    if (!empty($features)) {
        foreach ($features as $feature) {
            if (!in_array($feature, $features_all)) {
                $features_all[] = $feature;
            }
        }
    }

    $modified_features_all = array();
    foreach ($features_all as $feature) {
        if (!$has_valid_license) {
            if (get_option($feature['option'], 2) == 1) {
                update_option($feature['option'], 3);
            }
            if (get_option($feature['option'], 2) == 2) {
                update_option($feature['option'], '');
            }
        } else {
            if (get_option($feature['option'], 2) == 3) {
                update_option($feature['option'], 1);
            }
        }

        $feature_disable = '';
        $feature_enable = 0;
        if (defined('PAFE_VERSION') && !$feature['pro'] || defined('PAFE_PRO_VERSION') && $feature['pro']) {
            $feature_enable = esc_attr(get_option($feature['option'], 2));
            if ($feature_enable == 2) {
                $feature_enable = 1;
            }
        }

        if (!defined('PAFE_VERSION') && !$feature['pro'] || !defined('PAFE_PRO_VERSION') && $feature['pro']) {
            $feature_enable = 0;
            $feature_disable = 1;
        }

        $feature['feature_enable'] = $feature_enable;
        $feature['feature_disable'] = $feature_disable;

        $modified_features_all[] = $feature;
    }

    $features_all = $modified_features_all;
    ?>

    <h2 class="pafe-features__headline">Form Builder</h2>
    <ul class="pafe-features">
        <?php
        foreach ($features_all as $feature) :
            if (isset($feature['form-builder']) && $feature['form-builder'] == true) :
                ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
    </ul>

    <h2 class="pafe-features__headline">Extensions</h2>
    <ul class="pafe-features">
        <?php
        foreach ($features_all as $feature) :
            if (isset($feature['extension']) && $feature['extension'] == true) :
                ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
    </ul>

    <h2 class="pafe-features__headline">Widgets</h2>
    <ul class="pafe-features">
        <?php
        foreach ($features_all as $feature) :
            if (isset($feature['widget']) && $feature['widget'] == true) :
                ?>
                <li>
                    <label class="pafe-switch">
                        <input type="checkbox"<?php if( empty( $feature['feature_disable'] ) ) : ?> name="<?php echo $feature['option']; ?>"<?php endif; ?> value="1" <?php checked( $feature['feature_enable'], 1 ); ?><?php if( !empty( $feature['feature_disable'] ) ) { echo ' disabled'; } ?>>
                        <span class="pafe-slider round"></span>
                    </label>
                    <a href="<?php echo $feature['url']; ?>" target="_blank"><?php echo $feature['name']; ?><?php if( $feature['pro'] ) : ?><span class="pafe-pro-version"></span><?php endif; ?></a>
                </li>
            <?php endif; endforeach; ?>
    </ul>
    <div class="pafe-toggle-features">
        <div class="pafe-toggle-features__button" data-pafe-toggle-features-enable>Enable All</div>
        <div class="pafe-toggle-features__button pafe-toggle-features__button--disable" data-pafe-toggle-features-disable>Disable All</div>
        <div class="pafe-toggle-features__button" data-pafe-features-save><?php _e('Save Settings','pafe'); ?></div>
        <br>
    </div>
</form>