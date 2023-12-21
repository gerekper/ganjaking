<?php
/**
 * Show something awesome!
 *
 * @since 2.3.0
 */
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Attention_Seeker {

    public static function init() {
        add_action( 'admin_notices', [ __CLASS__, 'seek_attention' ] );
        add_action( 'wp_ajax_ignore_attention_seeker', [ __CLASS__, 'process_ignore_request' ] );
        add_action( 'admin_head', [ __CLASS__, 'setup_environment' ] );
    }

    public static function setup_environment() {
        ?>
        <script>
            jQuery(function($) {
                var $seeker = $('.ha-seeker'),
                    ajaxUrl = '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    nonce = '<?php echo wp_create_nonce( 'ignore_attention_seeker' ); ?>';

                $seeker.on('click.onSeekerIgnore', '.notice-dismiss', function (e) {
                    e.preventDefault();
                    var $seeker = $(e.delegateTarget);

                    console.log('seeker ', $seeker);

                    $.post(
                        ajaxUrl,
                        {
                            action: 'ignore_attention_seeker',
                            nonce: nonce,
                            id: $seeker.data('id')
                        },
                        function(res) {
                            if (res.success) {
                                console.log('Thanks! We will bring more awesome offer next time 🙂')
                            }
                        }
                    )
                });
            });
        </script>
        <?php
    }

    public static function process_ignore_request() {
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        $id = isset( $_POST['id'] ) ? sanitize_text_field($_POST['id']) : '';
        if ( wp_verify_nonce( $nonce, 'ignore_attention_seeker' ) && $id ) {
            $seeker = wp_list_filter( self::get_attentions(), ['_id' => $id] );
            $expire_date = $seeker[0]['end_date'] - time();
            set_transient( self::generate_db_key( $id ), 'ignore', $expire_date );
            wp_send_json_success();
        }

        exit;
    }

    private static function should_try( $attention ) {
        if ( ha_has_pro() ) {
            return false;
        }

        if ( ! isset( $attention['_id'], $attention['start_date'], $attention['end_date'], $attention['render_cb'] ) ) {
            return false;
        }

        if ( ! is_callable( $attention['render_cb'] ) ) {
            return false;
        }

        if ( get_transient( self::generate_db_key( $attention['_id'] ) ) === 'ignore' ) {
            return false;
        }

        if ( time() >= $attention['start_date'] && time() <= $attention['end_date'] ) {
            return true;
        }

        return false;
    }

    private static function generate_db_key( $id ) {
        return 'ha-seeker-' . substr( md5( $id ), 0, 7 );
    }

    public static function seek_attention() {
        foreach ( self::get_attentions() as $attention ) {
            if ( self::should_try( $attention ) ) {
                call_user_func( $attention['render_cb'], $attention['_id'] );
            }
        }
    }

    private static function get_attentions() {
        return [];
    }
}

Attention_Seeker::init();
