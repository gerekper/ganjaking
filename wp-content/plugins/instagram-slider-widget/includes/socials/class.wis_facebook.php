<?php
/**
 * Class of Facebook feed
 *
 * @author        Artem Prihodko <webtemyk@yandex.ru>, Github: https://github.com/temyk
 * @copyright (c) 28.12.2019, Webcraftic
 *
 * @version       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WIS_Facebook extends WIS_Social {

	const FACEBOOK_SELF_URL = 'https://graph.facebook.com/';

	/**
	 * Name of the Social
	 *
	 * @var string
	 */
	public $social_name = "facebook";

	/**
	 * WIS_Facebook constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_wis_add_facebook_page_by_token', array( $this, 'add_account' ) );
	}

	/**
	 * @return string
	 */
	public function getSocialName() {
		return $this->social_name;
	}

	/**
	 * Обработка данных на вкладке соцсети
	 */
	public function tabAction() {
		if ( isset( $_GET['token_error'] ) ) {
			//TODO: Обрабатывать ошибки, возможно логировать
			$_SERVER['REQUEST_URI'] = str_replace( '#_', '', remove_query_arg( 'token_error' ));
		} else {
			if ( isset( $_GET['access_token'] ) ) {
				$token = $_GET['access_token'];
				$result = $this->update_account_profiles( $token );
				$_SERVER['REQUEST_URI'] = remove_query_arg( 'access_token' );
				?>
				<div id="wis_accounts_modal" class="wis_accounts_modal">
					<div class="wis_modal_header">
						Choose Account:
					</div>
					<div class="wis_modal_content">
						<?php echo $result[0]; ?>
					</div>
				</div>
				<div id="wis_modal_overlay" class="wis_modal_overlay"></div>
				<span class="wis-overlay-spinner is-active">&nbsp;</span>
				<?php
			}
		}
	}
	/**
	 * Stores the fetched data from Facebook in WordPress DB using transients
	 *
	 * @param string $search_for  Facebook page name to fetch
	 * @param string $cache_hours Cache hours for transient
	 * @param string $nr_images   Nr of images to fetch
	 *
	 * @return string|array of localy saved facebook data
	 */
	public function get_data( $search_for, $cache_hours, $nr_images ) {

		if ( !isset( $search_for ) || empty( $search_for ) ) {
			return __( 'Nothing to search', 'instagram-slider-widget' );
		}

		$opt_name  = 'jr_facebook_' . md5( $search_for );
		$fbData = get_transient( $opt_name );
		$old_opts  = (array) get_option( $opt_name );
		$new_opts  = array(
			'search_string' => $search_for,
			'cache_hours'   => $cache_hours,
			'nr_images'     => $nr_images,
		);

		if ( true === $this->trigger_refresh_data( $fbData, $old_opts, $new_opts ) ) {

			$entry_data                    = array();
			$old_opts['search_string'] = $search_for;
			$old_opts['cache_hours']   = $cache_hours;
			$old_opts['nr_images']     = $nr_images;

			$nr_images = ! $this->WIS->is_premium() && $nr_images > 20 ? 20 : $nr_images;
			$account   = $this->getAccountByName( $search_for );

            $args = array(
                'access_token' => $account['token'],
                'fields'       => "id,created_time,message,full_picture,picture,attachments{media_type,media,title,type,url},comments{comment_count,id},likes{id}",
                'limit'        => $nr_images,
            );

            $url      = self::FACEBOOK_SELF_URL . $account['id'] . "/feed";
            $response = wp_remote_get( add_query_arg( $args, $url ) );
			if ( is_wp_error( $response ) ) {
				return [ 'error' => __( 'Something went wrong', 'instagram-slider-widget' ) ];
			}
            if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
                $media   = json_decode( wp_remote_retrieve_body( $response ), true );
                if($media) {
	                if ( isset($media['data']) && is_array( $media['data'] ) ){
		                $entry_data = $media['data'];

		                if ( ! count( $entry_data ) ) {
			                return [ 'error' => __( 'There are no publications in this account yet', 'instagram-slider-widget' ) ];
		                }

		                update_option( $opt_name, $old_opts );
		                set_transient( $opt_name, $entry_data, $cache_hours * 60 * 60 );
	                }
	                else {
		                return ['error' => __( 'No images found', 'instagram-slider-widget' )];
	                }
                }
                else {
	                return [ 'error' => __( 'Something went wrong (json)', 'instagram-slider-widget' ) ];
                }
            }
            else {
	            return [ 'error' => __( 'Something went wrong. API error', 'instagram-slider-widget' ) ];
            }

            //Обновляем данные профиля: подписчики, количество постов
            //$this->update_account_profiles( $account['token'], $account['username'] );
		}

		return $entry_data;
	}

	/**
	 * Get Account data by NAME from option in wp_options
	 *
	 * @param string $name
	 *
	 * @return array
	 */
	public function getAccountByName($name)
	{
		$token = WIS_Plugin::app()->getOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME );
		return $token[$name];
	}

	/**
	 * Trigger refresh for new data
	 * @param  array  $fbData
	 * @param  array  $old_args
	 * @param  array  $new_args
	 * @return bool
	 */
	private function trigger_refresh_data( $fbData, $old_args, $new_args ) {

		$trigger = 0;

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		if ( false === $fbData ) {
			$trigger = 1;
		}


		if ( isset( $old_args['saved_images'] ) ) {
			unset($old_args['saved_images']);
		}

		if ( isset( $old_args['deleted_images'] ) ) {
			unset($old_args['deleted_images']);
		}

		if ( is_array( $old_args ) && is_array( $new_args ) && array_diff( $old_args, $new_args ) !== array_diff( $new_args, $old_args ) ) {
			$trigger = 1;
		}

		if ( $trigger == 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $token
	 * @param $username
	 *
	 * @return bool|array
	 */
	public function update_account_profiles($token, $username = "") {
		//Получаем аккаунты привязанные к фейсбуку
		$args = array(
			'access_token' => $token,
			'fields'       => 'id,name,username,picture,photos',
			'limit'        => 200,
		);
		$url = self::FACEBOOK_SELF_URL."me/accounts";
		$response = wp_remote_get( add_query_arg( $args, $url ) );
		if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
			$pages = json_decode( wp_remote_retrieve_body( $response ), true );
			$html  = "";
			foreach($pages['data'] as $key => $result)
			{
				$result['token'] = $token;
				$users[] = $result;
				$picture = isset($result['picture']['data']['url']) ? $result['picture']['data']['url'] : "";
				$html .= "<div class='wis-row wis-row-style' id='wis-facebook-row' data-account='".json_encode($result)."'>";
				$html .= "<div class='wis-col-1 wis-col1-style'><img src='{$picture}' width='50' alt='{$result['name']}'></div>";
				$html .= "<div class='wis-col-2 wis-col2-style'><a href='https://www.facebook.com/{$result['username']}'>{$result['name']}</a></div>";
				$html .= "</div>";
			}
		}
		return array($html, $users);
	}

	/**
	 * Ajax Call to Add account
	 *
     * @return void
	 */
	public function add_account () {
		if( isset( $_POST['account'] ) && ! empty( $_POST['account'] ) && isset($_POST['_ajax_nonce']))
		{
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 2 );
			} else {
				wp_verify_nonce( $_POST['_ajax_nonce'], 'addAccountByToken' );

				$account      = json_decode( stripslashes( $_POST['account']), true );
				$user_profile = array();
				//$user_profile = apply_filters( 'wis/account/profiles', $user_profile );

				$user_profile[ $account['username'] ] = $account;
				WIS_Plugin::app()->updateOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME, $user_profile );
				wp_die( '' );
			}
		}
	}

	/**
	 * Ajax Call to delete account
	 * @return void
	 */
	public function delete_account() {
		if ( isset( $_POST['item_id'] ) && ! empty( $_POST['item_id'] ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( - 1 );
			} else {
				check_ajax_referer( 'wis_nonce' );

				$accounts = WIS_Plugin::app()->getPopulateOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME );
				$accounts_new = array();
				foreach($accounts as $name => $acc) { if($acc['id'] !== $_POST['item_id']) $accounts_new[$name] = $acc; }
				WIS_Plugin::app()->updatePopulateOption( WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME, $accounts_new);

				wp_send_json_success(__('Account deleted successfully', 'instagram-slider-widget'));
			}
		}
	}


}
