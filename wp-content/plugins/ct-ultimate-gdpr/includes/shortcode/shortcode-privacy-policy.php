<?php
/**
 * Created by PhpStorm.
 * User: CreateIT
 * Date: 5/18/2018
 * Time: 4:13 PM
 */

class CT_Ultimate_GDPR_Shortcode_Privacy_Policy {

    private $display_single_cookies = false;

    public function __construct($display_single_cookies = false)
    {
        $this->display_single_cookies = $display_single_cookies;

        add_action( 'wp_enqueue_scripts', array($this,'assets'));
    }

    public function render() {

?>
        <div class="ct-ultimate-gdpr-container">
            <label for="ct-ultimate-gdpr-party-filter"> <?php echo esc_html__( 'First or Third Party Filter', 'ct-ultimate-gdpr' ); ?></label>
            <select name="ct-ultimate-gdpr-party-filter" id="ct-ultimate-gdpr-party-filter">
                <option value=""><?php echo esc_html__( 'Any', 'ct-ultimate-gdpr' ); ?></option>
                <option value="<?php echo esc_html__( 'First party', 'ct-ultimate-gdpr' ); ?>"><?php echo esc_html__( 'First party', 'ct-ultimate-gdpr' ); ?></option>
                <option value="<?php echo esc_html__( 'Third party', 'ct-ultimate-gdpr' ); ?>"><?php echo esc_html__( 'Third party', 'ct-ultimate-gdpr' ); ?></option>
            </select>
        </div>
        <div id="ct-ultimate-gdpr-cookies-table" class="ct-ultimate-gdpr-table-responsive table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>
                            <?php echo esc_html__( 'Cookie names', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'Type of cookie', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'First or Third party', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'Can be blocked', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'Session or Persistent', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'Expiry Time', 'ct-ultimate-gdpr' ); ?>
                        </th>
                        <th>
                            <?php echo esc_html__( 'Purpose', 'ct-ultimate-gdpr' ); ?>
                        </th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
		<?php
    }

    private function get_cookies() {

        $cookies = array();

	    $args = array(
		    'post_type'        => 'ct_ugdpr_service',
		    'numberposts'      => - 1,
		    'suppress_filters' => false, // wpml get posts for current lang
	    );

	    $posts = get_posts($args);
		foreach( $posts as $post ) {
			$is_active = get_post_meta( $post->ID, 'is_active', true );
			if( !$is_active ) {
				continue;
			}
			$cookie_names_str = get_post_meta( $post->ID, 'cookie_name', true );
			$cookie_type = get_post_meta( $post->ID, 'type_of_cookie', true );
			$cookie_type_label = CT_Ultimate_GDPR_Model_Group::get_label($cookie_type);
            $first_or_third_party = get_post_meta( $post->ID, 'first_or_third_party', true ) == 'first_party' ?
                                            esc_html__( 'First party', 'ct-ultimate-gdpr') :
                                            esc_html__( 'Third party', 'ct-ultimate-gdpr');
			$can_be_blocked = get_post_meta( $post->ID, 'can_be_blocked', true );
            $session_or_persistent = get_post_meta( $post->ID, 'session_or_persistent', true ) == 'session' ?
                                            esc_html__( 'Session', 'ct-ultimate-gdpr') :
                                            esc_html__( 'Persistent', 'ct-ultimate-gdpr');

			$expiry_time = get_post_meta( $post->ID, 'expiry_time', true );

			$purpose = get_post_meta( $post->ID, 'purpose', true );
			if( $this->display_single_cookies ) {
				$cookie_names = array_filter( array_map( 'trim', explode( ',', $cookie_names_str ) ) );
				foreach( $cookie_names as $cookie_name ) {

                    $cookies[] = array(
                        'cookie_name'           => $cookie_name,
                        'cookie_type_label'     => $cookie_type_label,
                        'first_or_third_party'  => $first_or_third_party,
                        'can_be_blocked'        => $can_be_blocked,
                        'session_or_persistent' => $session_or_persistent,
                        'expiry_time'           => $expiry_time,
                        'purpose'               => $purpose
                    );

				}
			} else {

                $cookies[] = array(
                    'cookie_name'           => $cookie_names_str,
                    'cookie_type_label'     => $cookie_type_label,
                    'first_or_third_party'  => $first_or_third_party,
                    'can_be_blocked'        => $can_be_blocked,
                    'session_or_persistent' => $session_or_persistent,
                    'expiry_time'           => $expiry_time,
                    'purpose'               => $purpose
                );
            }
        }

        return $cookies;
    }

    public function assets()
    {
        wp_enqueue_script( 'ct-ultimate-gdpr-cookie-list', ct_ultimate_gdpr_url() . '/assets/js/cookie-list.js', array( 'jquery' ), ct_ultimate_gdpr_get_plugin_version() );
        wp_localize_script( 'ct-ultimate-gdpr-cookie-list', 'ct_ultimate_gdpr_cookie_list', array(
            'list'    => $this->get_cookies(),
        ) );
    }
}

function render_cookies_list( $atts ) {

    $obj = new CT_Ultimate_GDPR_Shortcode_Privacy_Policy(! empty( $atts['display_single_cookies'] ));
    ob_start();
	$obj->render();
    return ob_get_clean();
}

add_shortcode('render_cookies_list', 'render_cookies_list');
