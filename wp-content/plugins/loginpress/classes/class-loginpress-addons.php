<?php
/**
 * This is an Add-ons page. Purpose of this page is to show a list of all the add-ons available to extend the functionality of LoginPress.
 *
 * @package LoginPress
 * @since 1.0.19
 * @since 1.1.24
 *
 */

if ( ! class_exists( 'LoginPress_Addons' ) ) :

	class LoginPress_Addons {

		/**
		* Get the plugins list.
		*
		* @since  1.0.19
		* @access protected
		* @var    array
		*/
		protected $plugins_list;

		/* * * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		function __construct() {

			$this->plugins_list = get_plugins();
		}

		function _get_addons() {

			// For Testing
			//delete_transient( 'loginpress_api_addons' );

		    // Get the transient where the addons are stored on-site.
		    $data = get_transient( 'loginpress_api_addons' );

		    // If we already have data, return it.
		    if ( ! empty( $data ) )
		        return $data;

		    // Make sure this matches the exact URL from your site.
		    //$url = 'http://localhost/wpbrigade.local/wp-json/wpbrigade/v1/plugins?addons=loginpress-pro-add-ons';
		    $url = 'https://wpbrigade.com/wp-json/wpbrigade/v1/plugins?addons=loginpress-pro-add-ons';

		    // Get data from the remote URL.
		    $response = wp_remote_get( $url, array( 'timeout' => 20 ) );

		    if ( ! is_wp_error( $response ) ) {

		        // Decode the data that we got.
		        $data = json_decode( wp_remote_retrieve_body( $response ) );

		        if ( ! empty( $data ) && is_array( $data ) ) {

		            // Store the data for a week.
		            set_transient( 'loginpress_api_addons', $data, 7 * DAY_IN_SECONDS );

		            return $data;
		        }
		    }

		    return false;
		}

		function _addon_card( $addon ) { ?>

			<div class="loginpress-extension<?php if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){ echo ' loginpress-free-add-ons'; } ?>">
				<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="logoinpress_addons_links">

				  <h3><img src=<?php if ( $addon->media->icon->url ) echo $addon->media->icon->url; else echo plugins_url( '../img/thumbnail/gray-loginpress.png', __FILE__ );?> class="logoinpress_addons_thumbnails"/><span><?php echo esc_html( $addon->title ); ?></span></h3>
				</a>

				<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) ); ?>
				<p>
					<?php
					// $this->check_plugin_status( $addon->id, $addon->slug, $this->convert_to_array( $addon->categories ) );
					$this->sa_check_plugin_status( $addon->id, $addon->slug, $this->convert_to_array( $addon->categories ) );
					?>
				</p>
				<?php echo $this->_ajax_responce( $addon->title, $addon->slug ); ?>
				<!-- <input id="<?php // echo $addon->slug ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios" value="<?php // echo $addon->slug ?>"> -->
				<!-- <label for="<?php // echo $addon->slug ?>" class="loginpress-radio-btn"></label> -->
			</div>

		<?php }

		function _ajax_responce( $text, $slug ){
			$html = '<div id="loginpressEnableAddon' . $slug . '" class="loginpress-addon-enable" style="display:none;">
	      <div class="loginpress-logo-container">
	        <img src="' .  plugins_url( "../../loginpress-pro/img/loginpress-logo2.png", __FILE__ ) . '" alt="loginpress">
	        <svg class="circular-loader" viewBox="25 25 50 50" >
	          <circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
	        </svg>
	      </div>
	      <p>' .  __( "Activating " . $text . "...", "loginpress" ) . '</p>
		</div>';
		$html .= '<div id="loginpressActivatedAddon' . $slug . '" class="loginpress-install activated" style="display:none">
					<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#00c853" stroke-width="1" />
					</svg>
					<div class="checkmark draw"></div>
					<p>' . __( $text . ' Activated.', 'loginpress' ) . '</p>
				</div>';
		$html .= '<div id="loginpressUnnstallingAddon' . $slug . '" class="loginpress-uninstalling activated" style="display:none">
					<div class="loginpress-logo-container">
						<img src="' .  plugins_url( "../../loginpress-pro/img/loginpress-logo2.png", __FILE__ ) . '" alt="loginpress">
						<svg class="circular-loader" viewBox="25 25 50 50" >
						<circle class="loader-path" cx="50" cy="50" r="18" fill="none" stroke="#d8d8d8" stroke-width="1" />
						</svg>
					</div>
					<p>' .  __( "Deactivating " . $text . "...", "loginpress" ) . '</p>
				</div>';
		$html .= '<div id="loginpressDeactivatedAddon' . $slug . '" class="loginpress-uninstall activated" style="display:none">
					<svg class="circular-loader2" viewBox="25 25 50 50" >
						<circle class="loader-path2" cx="50" cy="50" r="18" fill="none" stroke="#ff0000" stroke-width="1" />
					</svg>
					<div class="checkmark draw"></div>
					<p>' . __( $text . ' Deactivated.', 'loginpress' ) . '</p>
				</div>';
				$html .= '<div id="loginpressWrongAddon' . $slug . '" class="loginpress-wrong activated" style="display:none">
					<svg class="checkmark_login" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
						<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle>
						<path class="checkmark__check" stroke="#ff0000" fill="none" d="M16 16 36 36 M36 16 16 36"></path>
					</svg>
					<p>' . __( $text . ' Something Wrong.', 'loginpress' ) . '</p>
				</div>';
			return $html;
		}

		function _addon_card_free( $addon ) { ?>

			<div class="loginpress-extension<?php if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){ echo ' loginpress-free-add-ons'; } ?>">
				<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="logoinpress_addons_links">

				  <h3><img src=<?php if ( $addon->media->icon->url ) echo $addon->media->icon->url; else echo plugins_url( '../img/thumbnail/gray-loginpress.png', __FILE__ );?> class="logoinpress_addons_thumbnails"/><span><?php echo esc_html( $addon->title ); ?></span></h3>
				</a>

				<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) );
				$slugid = $addon->slug;
				if( in_array('loginpress-free-add-ons', $this->convert_to_array($addon->categories) ) ){
					$slug = $addon->slug.'/'.$addon->slug.'.php';

					if ( is_plugin_active( $slug ) ) { ?>

						<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'uninstall_' . $slug ); ?>">
						<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
						<!-- <a class="button-primary loginpress-uninstall-pro-addon" href="#">Uninstall</a> -->
						<input id="<?php echo $slugid ?>" type="checkbox" checked class="loginpress-radio loginpress-radio-ios loginpress-uninstall-pro-addon" value="<?php echo $slugid ?>">
						<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>

					<?php

						// echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'loginpress' ), '<button class="button-primary">', '</button>' );
					} else if ( array_key_exists( $slug , $this->plugins_list ) ) { ?>

            <input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
            <input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
            <!-- <a class="button-primary loginpress-active-pro-addon" href="#">Activate Plugin</a> -->
						<input id="<?php echo $slugid ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-active-pro-addon" value="<?php echo $slugid ?>">
						<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>

					<?php

						// $link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
						// echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'loginpress' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );
					}else{

						$action = 'install-plugin';
						$slug   = 'login-logout-menu';
						$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );
						?>
						<!-- <p> -->
						  <!-- <a target="_self" href="<?php // echo $link; ?>" class="button-primary">INSTALL</a> -->
						<!-- </p> -->
						<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
            <input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
						<input id="<?php echo $slugid ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-install-pro-addon" value="<?php echo $slugid ?>">
						<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>
					<?php
					}
				}else{

				?>
				<p>
				  <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary"><?php esc_html_e( 'UPGRADE NOW', 'loginpress' ); ?></a>
				</p>
				<?php
				}
				?>
				<?php echo $this->_ajax_responce( $addon->title, $addon->slug ); ?>
			</div>

		<?php }

		function convert_to_array( $categories ) {

			$arr = array();

			foreach ($categories as $category) {

				$arr[] = $category->slug;
			}

			return $arr;
		}


		function get_addons_link() {

			$addons = $this->get_addons_name();

			foreach ( $addons as $addon ) {

				$action = 'install-plugin';
				$slug   = $addon['key'];
				$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug, 'lgp' => 1 ), admin_url( 'update.php' ) ), $action . '_' . $slug );
			}
		}


		function is_addon_licensed( $categories ) {

			if( LoginPress_Pro::get_license_id() == 2 and in_array('loginpress-pro-small-business', $categories) ){
				return true;
			}
			else if( LoginPress_Pro::get_license_id() == 3 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 4 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 7 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 8 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 9 and in_array('loginpress-pro-agency', $categories) ){
				return true;
			}else if( LoginPress_Pro::get_license_id() == 1 and in_array('loginpress-free-add-ons', $categories) ){
				return true;
			}else{
				return false;
			}
		}

		/**
		* Check plugin status
		*
		* @return array
		* @since 1.0.19
		*/
		public function check_plugin_status( $id, $slug, $categories = array() ) {

			$slug = $slug.'/'.$slug.'.php';

			if ( $this->is_addon_licensed ( $categories ) ) {

			 	if ( is_plugin_active( $slug ) ) { ?>

				<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'uninstall_' . $slug ); ?>">
				<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
				<a class="button-primary loginpress-uninstall-pro-addon" href="#">Uninstall</a>

					<?php
					// echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'loginpress' ), '<button class="button-primary">', '</button>' );
				}
				else if ( array_key_exists( $slug , $this->plugins_list ) ) { ?>

					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<a class="button-primary loginpress-active-pro-addon" href="#">Activate Plugin</a>

					<?php
          // $link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
          // echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'loginpress' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );
				}
				else{
					// $link   = wp_nonce_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $slug, 'lgp' => 1, 'id' => $id), admin_url( 'update.php' ) ), 'install-plugin_' . $slug );
					// echo sprintf( esc_html__( '%1$s Install %2$s', 'loginpress' ), '<a  href="' . $link . '" class="button-primary">', '</a>' );

					?>
					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<input name="loginpress_pro_addon_id" type="hidden" value="<?php echo $id; ?>">
					<a class="button-primary loginpress-install-pro-addon" href="#">Install</a>
					<!-- <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary">INSTALL NOW</a> -->
					<?php
				}
			}
			else {

				?>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary"><?php esc_html_e( 'UPGRADE NOW', 'loginpress' ); ?></a>
					<?php
			}
		}

		/**
		* Check plugin status
		*
		* @return array
		* @since 1.0.19
		*/
		public function sa_check_plugin_status( $id, $slug, $categories = array() ) {
			$slugid = $slug;
			$slug = $slug.'/'.$slug.'.php';

			if ( $this->is_addon_licensed ( $categories ) ) {

			 	if ( is_plugin_active( $slug ) ) { ?>

				<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'uninstall_' . $slug ); ?>">
				<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
				<!-- <a class="button-primary loginpress-uninstall-pro-addon" href="#">Uninstall</a> -->

				<input id="<?php echo $slugid ?>" type="checkbox" checked class="loginpress-radio loginpress-radio-ios loginpress-uninstall-pro-addon" value="<?php echo $slugid ?>">
				<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>

					<?php
					// echo sprintf( esc_html__( '%1$s Already Installed %2$s', 'loginpress' ), '<button class="button-primary">', '</button>' );
				}
				else if ( array_key_exists( $slug , $this->plugins_list ) ) { ?>

					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<input id="<?php echo $slugid ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-active-pro-addon" value="<?php echo $slugid ?>">
				<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>
					<!-- <a class="button-primary loginpress-active-pro-addon" href="#">Activate Plugin</a> -->

					<?php
          // $link = wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $slug ), admin_url( 'plugins.php' ) ),  'activate-plugin_' . $slug ) ;
          // echo sprintf( esc_html__( '%1$s Activate Plugin %2$s', 'loginpress' ), '<a href="' .  $link . '" class="button-primary">', '</a>' );
				}
				else{
					// $link   = wp_nonce_url( add_query_arg( array( 'action' => 'install-plugin', 'plugin' => $slug, 'lgp' => 1, 'id' => $id), admin_url( 'update.php' ) ), 'install-plugin_' . $slug );
					// echo sprintf( esc_html__( '%1$s Install %2$s', 'loginpress' ), '<a  href="' . $link . '" class="button-primary">', '</a>' );

					?>
					<input name="loginpress_pro_addon_nonce" type="hidden" value="<?php echo wp_create_nonce( 'install-plugin_' . $slug ); ?>">
					<input name="loginpress_pro_addon_slug" type="hidden" value="<?php echo $slug; ?>">
					<input name="loginpress_pro_addon_id" type="hidden" value="<?php echo $id; ?>">
					<input id="<?php echo $slugid ?>" type="checkbox" class="loginpress-radio loginpress-radio-ios loginpress-install-pro-addon" value="<?php echo $slugid ?>">
					<label for="<?php echo $slugid ?>" class="loginpress-radio-btn"></label>
					<!-- <a class="button-primary loginpress-install-pro-addon" href="#">Install</a> -->
					<!-- <a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary">INSTALL NOW</a> -->
					<?php
				}
			}
			else {

				?>
					<a target="_blank" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=addons-coming-soon&utm_campaign=pro-upgrade" class="button-primary"><?php esc_html_e( 'UPGRADE NOW', 'loginpress' ); ?></a>
					<?php
			}
		}

		public function validate_addons() {

			$data = get_transient( 'loginpress_api_addons' );
		}

		public function show_addon_page() {

			if ( class_exists('LoginPress_Pro') ) {

				if ( LoginPress_Pro::is_activated() ) {

			            $expiration_date = LoginPress_Pro::get_expiration_date();

			            if ( 'lifetime' == $expiration_date ) {
			                echo esc_html__( 'You have a lifetime license, it will never expire.', 'loginpress-pro' );
			            }
			            else {
			                echo '<div class="main_notice_msg">' . sprintf(
			                  esc_html__( 'Your (%2$s) license key is valid until %s.', 'loginpress-pro' ),
			                  '<strong>' . date_i18n( get_option( 'date_format' ), strtotime( $expiration_date, current_time( 'timestamp' ) ) ) . '</strong>', LoginPress_Pro::get_license_type()
			                ) . '</div>';
			            }

			    	?> <div class="addon_cards_wraper"> <?php
					foreach( $this->_get_addons() as $addon ) {

						$this->_addon_card( $addon );
					}
					?> </div> <?php

				}else{

					echo '<div class="main_notice_msg">' . sprintf( esc_html__( 'You need to activate your license to download the following add-ons.', 'loginpress-pro' ) ) . '</div>';

					// Show full list of add-ons
					?> <div class="addon_cards_wraper"> <?php
					foreach( $this->_get_addons() as $addon ) {

						$this->_addon_card_free( $addon );
					}
					?> </div> <?php
				}

			}else{

				echo '<div class="main_notice_msg">' . sprintf( esc_html__( 'You need to upgrade to LoginPress Pro to access these add-ons.', 'loginpress-pro' ) ) . '</div>';

				// Show full list of add-ons
				?> <div class="addon_cards_wraper"> <?php
				foreach( $this->_get_addons() as $addon ) {

					$this->_addon_card_free( $addon );
				}
				?> </div> <?php
			}
		}

		function _addon_html() { ?>

			<!-- Style for Add-ons Page -->
			<style media="screen">
				.loginpress-extension h3 {
					box-sizing: border-box;
					height: 110px;
					margin: -2px -2px 0 0;
					padding: 20px 10px 0 135px;
					border-bottom: 1px solid #a5dff6;

					background-size: 115px 100px;
					position: relative;
					overflow: hidden;
				}
				.loginpress-free-add-ons h3:after{
				    content: "Free";
				    position: absolute;
				    top: 10px;
				    right: -30px;
				    width: 100px;
				    height: 30px;
				    background-color: #00a0d2;
				    color: #fff;
				    transform: rotate(45deg);
				    line-height: 30px;
				    text-align: center;
				    font-size: 13px;
				}
				.loginpress-extension {
					float: none;
					box-sizing: border-box;
					width: calc(33% - 20px);
					margin: 0px 0px 30px 30px;
					border: 1px solid #a5dff6;
					display: inline-block;
					height: auto;
					vertical-align: top;
					background: #fff;
					min-height: 300px;
					position: relative;
					padding-bottom: 50px;
					max-width: 465px;
			  	}
				.loginpress-extension:nth-child(3n+1){
					margin-left: 0;
				}
			  .loginpress-extension .button-primary{
					border:0;
					text-shadow:none;
					background:#1a61a7;
					padding:8px 18px;
					height:auto;
					font-size:15px;
					cursor: pointer;
				    position: absolute;
		    		bottom: 15px;
		    		left: 50%;
		    		transform: translateX(-50%);
					box-shadow:none;
					border-radius:0;
					transition: background-color .3s;
			  	}
			   .loginpress-extension .button-primary:active,.loginpress-extension .button-primary:hover,.loginpress-extension .button-primary:focus{
				background: #36bcf2;
				box-shadow: none;
				outline: none;
			  }
			  .notice_msg{
			  	box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 1px 0px;
			    background: rgb(255, 255, 255);
			    border-left: 4px solid #46b450;
			    margin: 5px 0 20px;
			    padding: 15px;
			  }
			  .main_notice_msg{
			    background: #1a61a7;
			    margin: 5px 0 20px;
			    padding: 15px;
				color: #fff;
				display: inline-block;
			  }
			  .loginpress-extension button.button-primary{
				background: #f9fafa;
				border-radius: 0;
				box-shadow: none;
				color: #444;
				    position: absolute;
		    bottom: 15px;
		    left: 50%;
		    transform: translateX(-50%);

		 border: 2px solid #a5dff6 !important;
		    background: #d3f3ff54 !important;
				cursor: default;
				transition: background-color .3s;
			  }
			  .loginpress-extension button.button-primary:visited,.loginpress-extension button.button-primary:active,.loginpress-extension button.button-primary:hover,.loginpress-extension button.button-primary:focus{
				background: #36bcf2;
				color: #444;
				border: 0;

				outline: none;
				box-shadow: none;
			  }
			  .logoinpress_addons_thumbnails{

				max-width: 100px;
				position: absolute;
				top: 5px;
				left: 10px;
				max-height: 95px;
				height: auto;
				width: auto;
			  }
			  .loginpress-extension .logoinpress_addons_links{
				position: relative;
				background-color: #d3f3ff;
			  }
			  .loginpress-extension p {
				margin: 0;
				padding: 10px 20px;
			  }
			  .loginpress-addons-loading-errors {
				padding-top: 15px;
			  }
			  .loginpress-addons-loading-errors img {
				float: left;
				padding-right: 10px;
			  }
				.loginpress-addons-wrap{

				}
				.loginpress-extension h3 {
			    box-sizing: border-box;
			    /* height: 110px; */

			    padding: 0 10px 0 20px;
			    border-bottom: 2px solid #a5dff6;
			    background-size: 115px 100px;
			    height: 100px;
			     color: #000000;
			}
				a.logoinpress_addons_links {
				    display: inline-block;
				    width: 100%;
				    line-height: 90px;
				    padding-bottom: 0px;
				    height: auto;
						text-decoration: none;
				}
				.logoinpress_addons_thumbnails {
				    max-width: 100px;
				    position: absolute;
				    top: 5px;
				    left: 10px;
				    max-height: 75px;
				    height: auto;
				    width: auto;
				    position: static;
				    vertical-align: middle;
				    margin-right: 20px;
				}
				.loginpress-extension{
					border-width: 2px;
				}
				@media only screen and (min-width: 1680px) {
					.loginpress-extension{
						min-height: 315px;
						width: calc(25% - 27px);
					}
					.loginpress-extension:nth-child(3n+1){
						margin-left: 30px;
					}
					.loginpress-extension:nth-child(4n+1){
						margin-left: 0;
					}
				}
				@media only screen and (max-width: 1500px) {
					.loginpress-extension{
						min-height: 330px
					}
				}
				@media only screen and (max-width: 1400px) {
					.loginpress-extension{
						min-height: 300px;
						width: calc(50% - 20px);
					}
					.loginpress-extension:nth-child(3n+1){
						margin-left: 30px;
					}
					.loginpress-extension:nth-child(2n+1){
						margin-left: 0;
					}
				}
				@media only screen and (max-width: 600px) {
					.loginpress-extension:nth-child(n){
						width:100%;
						margin-left: 0;
					}
				}
				.loginpress-addon-enable{
					position: absolute;
					top: -2px;;
					left: -2px;
					bottom: -2px;
					right: -2px;
					background: rgba(255,255,255, .9);
					z-index: 100;
				}
				.loginpress-logo-container{
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
					width: 250px;
					height: 250px;
					display: flex;
					flex-direction: column;
					align-items: center;
				}
				.loginpress-logo-container img{
					width: auto;
					height: auto;
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
				}
				.loginpress-addon-enable p{
					position: absolute;
					bottom: 0;
					left: 0;
					width: 100%;
					text-align: center;
					box-sizing: border-box;
				}
				.loader-path {
				stroke-dasharray: 150,200;
				stroke-dashoffset: -10;
				-webkit-animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
				animation: dash 1.5s ease-in-out infinite, color 6s ease-in-out infinite;
				stroke-linecap: round;
				}
				@-webkit-keyframes rotate {
				100% {
					-webkit-transform: rotate(360deg);
					transform: rotate(360deg);
				}
				}

				@keyframes rotate {
				100% {
					-webkit-transform: rotate(360deg);
					transform: rotate(360deg);
				}
				}
				.circular-loader{
					-webkit-animation: rotate 2s ease-in-out infinite, color 6s ease-in-out infinite;
				animation: rotate 2s ease-in-out infinite, color 6s ease-in-out infinite;
				stroke-linecap: round;
				}
				@keyframes loader-spin {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
				}
				@keyframes dash {
				0% {
					stroke-dasharray: 1,200;
					stroke-dashoffset: 0;
				}
				50% {
					stroke-dasharray: 89,200;
					stroke-dashoffset: -35;
				}
				100% {
					stroke-dasharray: 89,200;
					stroke-dashoffset: -124;
				}
				}
				.loginpress-install,.loginpress-uninstall,.loginpress-uninstalling, .loginpress-wrong{
					position: absolute;
					top: -2px;;
					left: -2px;
					bottom: -2px;
					right: -2px;
					background: rgba(255,255,255, .9);
					z-index: 100;
				}
				.loader-path2{
					stroke-dasharray: 150,200;
					stroke-dashoffset: 150px;
					-webkit-animation: dashtwo 1s ease-in-out 1 forwards;
					animation: dashtwo 1s ease-in-out 1 forwards;
				}
				.checkmark__circle {
				stroke-width: 2;
				stroke: #ff0000;
				}
				.checkmark_login {
				width: 150px;
				height: 150px;
				border-radius: 50%;
				display: block;
				stroke-width: 2;
				stroke: #fff;
				stroke-miterlimit: 10;
				margin: 10% auto;
				animation: scale .3s ease-in-out .2s both;
				position: absolute;
					top: 50%;
					left: 50%;
					margin: -75px 0 0 -75px;
				}
				.checkmark__check {
				transform-origin: 50% 50%;
				stroke-dasharray: 29;
				stroke-dashoffset: 29;
				animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
				}
				@keyframes stroke {
				100% {
					stroke-dashoffset: 0;
				}
				}
				@keyframes scale {
				0%, 100% {
					transform: none;
				}
				50% {
					transform: scale3d(1.1, 1.1, 1);
				}
				}
				@keyframes fill {
				100% {
					box-shadow: inset 0px 0px 0px 30px #7ac142;
				}
				}
				@keyframes dashtwo {
				0% {
					stroke-dashoffset: 150px;
				}
				100% {
					stroke-dashoffset: 20px;
				}
				}
				.circular-loader2, .circular-loader3{
					width: 200px;
					height: 200px;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%) rotate(-90deg);
					position: absolute;
				}
				.loginpress-install.activated p{
					position: absolute;
					bottom: 0;
					left: 0;
					text-align: center;
					width: 100%;
					box-sizing: border-box;
				}
				.loginpress-wrong.activated p{
					position: absolute;
					bottom: 0;
					left: 0;
					text-align: center;
					width: 100%;
					box-sizing: border-box;
					color: #ff0000;
					font-weight: 700;
				}
				.checkmark {
				/*   display: none; */
					top: 50%;
					position: absolute;
					left: 50%;
					transform: translate(-50%, -50%);
					width: 140px;
					height: 140px;
				}
				.checkmark.draw:after {
					animation-duration: 800ms;
					animation-delay: 1s;
					animation-timing-function: ease;
					animation-name: checkmark;
					transform: scaleX(-1) rotate(135deg);
					opacity: 0;
					animation-fill-mode: forwards;
				}
				.checkmark:after {
					height: 4em;
					width: 2em;
					transform-origin: left top;
					border-right: 2px solid #00c853;
					border-top: 2px solid #00c853;
					content: '';
					left: 42px;
					top: 70px;
					position: absolute;
				}
				.loginpress-uninstall .checkmark:after{
					border-right: 2px solid #ff0000;
					border-top: 2px solid #ff0000;
				}
				.loginpress-uninstall p, .loginpress-uninstalling p{
					position: absolute;
					bottom: 0;
					left: 0;
					text-align: center;
					width: 100%;
					box-sizing: border-box;
				}
				@keyframes checkmark {
					0% {
						height: 0;
						width: 0;
						opacity: 1;
					}
					20% {
						height: 0;
						width: 2em;
						opacity: 1;
					}
					40% {
						height: 4em;
						width: 2em;
						opacity: 1;
					}
					100% {
						height: 4em;
						width: 2em;
						opacity: 1;
					}
					}
					.loginpress-extension input[type="checkbox"]{
						display: none;
					}
					.loginpress-extension .loginpress-radio-btn{
							outline: 0;
						display: block;
						width: 36px;
						height: 18px;
						position: relative;
						cursor: pointer;
						-webkit-user-select: none;
						-moz-user-select: none;
						-ms-user-select: none;
						user-select: none;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio-ios + .loginpress-radio-btn {
						background: #fff;
						border-radius: 2em;
						padding: 2px;
						-webkit-transition: all .4s ease;
						transition: all .4s ease;
						border: 2px solid #555d66;
						position: absolute;
						bottom: 15px;
						left: 50%;
						transform: translateX(-50%);
					}
					.loginpress-extension input[type=checkbox].loginpress-radio + .loginpress-radio-btn:after{
						position: relative;
						display: block;
						content: "";
						width: 18px;
						height: 18px;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio-ios + .loginpress-radio-btn:after {
						border-radius: 2em;
						background: #fbfbfb;
						-webkit-transition: left 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), padding 0.3s ease, margin 0.3s ease;
						transition: left 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), padding 0.3s ease, margin 0.3s ease;
						border: 2px solid #555d66;
						box-sizing: border-box;
						left: 0;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio + .loginpress-radio-btn:hover {
						background-color: #e2e4e7;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio-ios + .loginpress-radio-btn:active:after {
						border-width: 9px;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio:checked + .loginpress-radio-btn:after {
							left: 18px;
						border-color: #fff;
						background: #33b3db;
						border-width: 9px;
					}
					.loginpress-extension input[type=checkbox].loginpress-radio:checked + .loginpress-radio-btn{
						background: #33b3db;
						border-color: #33b3db;
					}
		  	</style>

			<div class="wrap loginpress-addons-wrap">

				<h2 class='opt-title'>
				  <?php esc_html_e( 'Extend the functionality of LoginPress with these awesome Add-ons', 'loginpress' ); ?>
				</h2>

				<div class="tabwrapper">
					<?php
					$this->show_addon_page();
					?>
				</div>
			</div>
			<?php
		}

	} // Enf of Class.

endif;


	// $obj_loginpress_addons	= new LoginPress_Addons();
	// $loginpress_addons 		= $obj_loginpress_addons->_get_addons();
	//
	// $obj_loginpress_addons->_addon_html();
	?>
