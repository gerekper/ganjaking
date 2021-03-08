<?php

/**
 * @var array $components
 */

use Instagram\Includes\WIS_Plugin;

?>
<div class="wbcr-factory-page-group-header"><?php

    _e('<strong>Plugin Components</strong>.', 'titan-security') ?>
    <p>
        <?php _e('These are components of the plugin bundle. When you activate the plugin, all the components turned on by default. If you don’t need some function, you can easily turn it off on this page.', 'titan-security') ?>
    </p>
</div>
<div class="wbcr-clearfy-components">
    <?php foreach((array)$components as $component): ?>
        <?php

        $component_const = $component['component_const'];

        $is_active_component = defined($component_const);

        $slug = $component['name'];

        if( $component['type'] == 'wordpress' ) {
            $slug = $component['base_path'];
        }

        ?>

        <div class="plugin-card">
            <div class="plugin-card-top">
                <div class="name column-name">
                    <h3>
                        <a href="<?php echo esc_url($component['url']) ?>" class="open-plugin-details-modal">
                            <?php echo esc_html($component['title']) ?>
                            <img src="<?php echo esc_attr($component['icon']) ?>" class="plugin-icon"
                                 alt="<?php echo esc_attr($component['title']) ?>">
                        </a>
                    </h3>
                </div>
                <div class="desc column-description">
                    <p><?php echo esc_html($component['description']); ?></p>
                </div>
            </div>
            <div class="plugin-card-bottom">
                <?php if($is_active_component) : ?>
                    <a href="<?php echo esc_url($component['settings_url']) ?>"
                       class="button button-primary settings-button"><?php _e('Settings', 'instagram-slider-widget'); ?></a>
                <?php else: ?>
                    <?php if( 'premium' === $component['build'] && !(WIS_Plugin::app()->premium->is_activate() && WIS_Plugin::app()->premium->is_install_package()) ): ?>
                        <a target="_blank" href="<?php echo esc_url($component['url']) ?>"
                           class="button button-default read-more"><?php _e('Read more', 'instagram-slider-widget'); ?></a>
                    <?php else: ?>
                        <?php  WIS_Plugin::app()->get_install_component_button('wordpress', $component['slug'])->render_button(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="clearfix"></div>
</div>


