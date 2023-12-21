<?php
/**
 * Dashboard manager
 *
 * Package: Happy_Addons_Pro
 * @since 0.9.9
 */
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Dashboard {

    public static function init() {
        add_filter( 'plugin_action_links_' . plugin_basename( HAPPY_ADDONS_PRO__FILE__ ), [ __CLASS__, 'add_action_links' ] );
        add_filter( 'happyaddons_dashboard_get_tabs', [ __CLASS__, 'add_tabs' ] );
        add_action( 'admin_notices', [ __CLASS__, 'add_activate_license_notice' ] );
    }

    public static function add_activate_license_notice() {
        if ( hapro_get_appsero()->license()->is_valid() ) {
            return;
        }
        ?>
        <style>
            .ha-notice {
                position: relative;
                border: 0;
                margin: 5px 0 15px;
                color: #fff;
                display: flex;
                padding: 0;
                align-items: center;
                padding-left: 25px;
                padding-right: 25px;
                border-radius: 5px;
                background-image: linear-gradient(135deg, #562dd4 -25%, #e2498a 75%);
                text-align: right;
            }

            .ha-notice img {
                height: 100%;
                flex-shrink: 0;
                margin-right: -22px;
            }

            .ha-notice p {
                font-size: 14px;
                display: inline-block;
                margin-left: auto;
            }

            .ha-notice a {
                display: inline-block;
                text-decoration: none;
                color: #e2498a;
                border-radius: 5px;
                padding: 10px 30px;
                background-color: #fff;
                margin-left: 20px;
                font-size: 14px;
            }

            .ha-notice a:hover,
            .ha-notice a:focus {
                background-color: #562dd4;
                color: #fff;
            }
        </style>

        <div class="notice ha-notice">
            <img src="<?php echo HAPPY_ADDONS_PRO_ASSETS; ?>imgs/license-notice.svg" alt="">
            <p>Your plugin is not licensed yet. Please insert the license key first to experience all the widgets and features.</p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=happy-addons-license' ) ); ?>">Activate</a>
        </div>
        <?php
    }

    public static function add_tabs( $tabs ) {
        $tabs['pro'] = [
            'title' => esc_html( hapro_get_appsero()->license()->is_valid() ? __( 'License', 'happy-addons-pro' ) : __( 'Activate License', 'happy-addons-pro' ) ),
            'href' => admin_url( 'admin.php?page=happy-addons-license' ),
        ];
        return $tabs;
    }

    public static function add_action_links( $default_links ) {
        $links = [
            sprintf( '<a href="%s">%s</a>',
                ha_get_dashboard_link(),
                esc_html__( 'Settings', 'happy-addons-pro' )
            )
        ];

        array_push( $links, sprintf( '<a style="color:#e2498a; font-weight: bold;" href="%s">%s</a>',
            admin_url( 'admin.php?page=happy-addons-license' ),
            hapro_get_appsero()->license()->is_valid() ? __( 'License', 'happy-addons-pro' ) : __( 'Activate License', 'happy-addons-pro' )
        ) );

        return array_merge( $links, $default_links );
    }
}

Dashboard::init();
