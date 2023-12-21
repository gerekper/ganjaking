<?php
/**
 * Dashboard pro tab template
 */

defined( 'ABSPATH' ) || die();
?>
<div class="ha-dashboard-panel">
    <div class="ha-home-banner">
        <div class="ha-home-banner__content">
            <img class="ha-home-banner__logo" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/halogo.svg" alt="">
            <span class="ha-home-banner__divider"></span>
            <h2><span><?php esc_html_e('What\'s Inside ', 'happy-elementor-addons'); ?></span><br><?php esc_html_e('HappyAddons Pro', 'happy-elementor-addons'); ?></h2>
        </div>
    </div>
    <div class="ha-home-body">
        <div class="ha-row ha-py-5 ha-align-items-center ha-align-center">
            <div class="ha-col ha-col-12">
                <div class="ha-badge"><?php esc_html_e('PRO', 'happy-elementor-addons'); ?></div>
                <h2 class="ha-section-title ha-text-primary"><?php esc_html_e('Features', 'happy-elementor-addons'); ?></h2>
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-pt-0 ha-align-items-center">
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/cross-domain.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Cross Domain Copy Paste', 'happy-elementor-addons'); ?></h3>
                <p class="f18"><?php esc_html_e('Do Cross-Domain Widget Copy Paste within different websites!
                    You can easily copy any widgets from your previously designed website and paste it to your newly created website.', 'happy-elementor-addons'); ?></p>
            </div>
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-pl-2" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/pa-1.png" alt="">
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-pt-0 ha-align-items-center">
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-pr-2" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/pa-2.png" alt="">
            </div>
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/preset.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Preset', 'happy-elementor-addons'); ?></h3>
                <p class="f16"><?php esc_html_e('400+ Preset Library for Widgets with Drop Down facility. Experience the Instagram Photo Editing like experience in Elementor!', 'happy-elementor-addons'); ?></p>
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-pt-0 ha-align-items-center">
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-title-icon-size" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/nesting.svg" alt="">
                <h3 class="ha-feature-title"><?php esc_html_e('Unlimited Section Nesting', 'happy-elementor-addons'); ?></h3>
                <p class="f18"><?php esc_html_e('Don’t you wish to use multiple sections at the same time in Elementor? With HappyAddons, now you can. Create as many sections as you want and organize your elements more effectively.', 'happy-elementor-addons'); ?></p>
            </div>
            <div class="ha-col ha-col-6">
                <img class="ha-img-fluid ha-pl-2" src="<?php echo HAPPY_ADDONS_ASSETS; ?>imgs/admin/pa-3.png" alt="">
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-pt-0 ha-align-items-center ha-align-center">
            <div class="ha-col ha-col-12">
                <div class="ha-badge"><?php esc_html_e('PRO', 'happy-elementor-addons'); ?></div>
                <h2 class="ha-section-title ha-text-primary"><?php esc_html_e('Widgets', 'happy-elementor-addons'); ?></h2>
            </div>
        </div>

        <div class="ha-row ha-py-5 ha-pt-0 ha-align-items-center ha-align-center">
            <?php
            $pro_widgets = \Happy_Addons\Elementor\Widgets_Manager::get_pro_widget_map();

            foreach ( $pro_widgets as $widget ) :
                $title = isset( $widget['title'] ) ? $widget['title'] : 'Widget Title';
                $icon = isset( $widget['icon'] ) ? $widget['icon'] : 'hm hm-happyaddons';
                $demo = isset( $widget['demo'] ) ? $widget['demo'] : 'https://happyaddons.com/go/get-pro';
                ?>
                <div class="ha-col ha-col-4">
                    <a class="ha-pro-widget" href="<?php echo esc_url( $demo ); ?>" target="_blank" rel="noopener"><i class="<?php echo $icon; ?>"></i> <?php echo $title; ?></a>
                </div>
                <?php
            endforeach;
            ?>
        </div>

        <hr>

        <div class="ha-row ha-py-5 ha-pt-0- ha-align-items-center ha-align-center">
            <div class="ha-col ha-col-12">
                <h2 class="ha-feature-title ha-mb-3"><?php esc_html_e('Get Pro and Experience all those exciting features and widgets', 'happy-elementor-addons'); ?></h2>
                <a style="padding: 20px 40px" class="ha-btn ha-btn-secondary" target="_blank" rel="noopener" href="https://happyaddons.com/go/get-pro"><?php esc_html_e('GET PRO', 'happy-elementor-addons'); ?></a>
            </div>
        </div>

    </div>
</div>
