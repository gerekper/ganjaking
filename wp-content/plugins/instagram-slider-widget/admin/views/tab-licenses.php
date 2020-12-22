<?php

/**
 * @var array $licenses
 */


?>
<div class="wbcr-factory-page-group-header"><?php

    _e('<strong>Licenses</strong>.', 'titan-security') ?>
    <p>
        <?php _e('These are licenses for a plugin and components page.', 'titan-security') ?>
    </p>
</div>
<div class="wbcr-clearfy-components">
    <?php foreach((array)$licenses as $license): ?>
        <div class="plugin-card">
            <div class="plugin-card-top">
                <div class="name column-name">
                    <h3>
                        <?= $license['title'] ?>
                        <img src="<?php echo esc_attr($license['icon']) ?>" class="plugin-icon"
                             alt="<?php echo esc_attr($license['title']) ?>">
                    </h3>
                </div>
                <div class="desc column-description">
                    <p><?php echo esc_html($license['description']); ?></p>
                </div>
            </div>
            <div class="plugin-card-bottom">
                <a href="<?php echo admin_url('admin.php?page=' . $license['license_url']) ?>"
                   class="button button-primary settings-button"><?php _e('License', 'instagram-slider-widget'); ?></a>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="clearfix"></div>
</div>


