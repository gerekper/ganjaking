<?php
/*
	Copyright (C) 2015-20 CERBER TECH INC., https://cerber.tech
	Copyright (C) 2015-20 CERBER TECH INC., https://wpcerber.com

    Licenced under the GNU GPL.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'CERBER_PIN_LENGTH', 4 );
define( 'CERBER_PIN_EXPIRES', 15 );

final class CRB_2FA {
	private static $user_id = null;

	/**
	 * Enforce 2FA for a user if needed
	 *
	 * @param $login string
	 * @param $user WP_User
	 *
	 * @return bool|WP_Error
	 */
	static function enforce( $login, $user ) {
		static $done = false;

		if ( $done ) {
			return false;
		}

		$done = true;

		if ( crb_acl_is_white() ) {
			return false;
		}

		if ( ( ! $user instanceof WP_User ) || empty( $user->ID ) ) {
			return new WP_Error( 'no-user', 'Invalid user data' );
		}

		$cus = cerber_get_set( 'cerber_user', $user->ID );
		$tfm = crb_array_get( $cus, 'tfm' );
		if ( $tfm === 2 ) {
			return false;
		}

		$login = (string) $login;

		$go = false;

		if ( $tfm == 1 ) {
			$go = true;
		}
		else {

			$u_roles = null;

			if ( ! empty( $user->roles ) ) {
				$u_roles = $user->roles;
			}
			else { // a backup way
				$data = get_userdata( $user->ID );
				if ( ! empty( $data->roles ) ) {
					$u_roles = $data->roles;
				}
			}

			if ( empty( $u_roles ) ) {
				return new WP_Error( 'no-roles', 'No roles found for the user #' . $user->ID );
			}

			$go = self::check_role_policies( $cus, $u_roles );

		}

		if ( ! $go ) {
			return false;
		}

		// This user must complete 2FA

		$ret = self::enforce2fa( $user, $login );

		if ( is_wp_error( $ret ) ) {
			return $ret;
		}

		cerber_log( 400, $login, $user->ID );

		wp_safe_redirect( get_home_url() );
		exit;

	}

	/**
     * @param array $cus
	 * @param array $roles
	 *
	 * @return bool
	 */
	private static function check_role_policies( $cus, $roles ) {

		foreach ( $roles as $role ) {
			$policies = cerber_get_role_policies( $role );

			if ( empty( $policies['2famode'] ) ) {
				continue;
			}
            elseif ( $policies['2famode'] == 1 ) {
				return true;
			}

			if ( $history = crb_array_get( $cus, '2fa_history' ) ) {
				if ( ( $logins = crb_array_get( $policies, '2falogins' ) )
				     && ( $history[0] >= $logins ) ) {
					return true;
				}
				if ( ( $days = crb_array_get( $policies, '2fadays' ) )
				     && ( ( time() - $history[1] ) > $days * 24 * 3600 ) ) {
					return true;
				}
			}

			if ( $last_login = crb_array_get( $cus, 'last_login' ) ) {
				if ( crb_array_get( $policies, '2fanewip' ) ) {
					if ( $last_login['ip'] != cerber_get_remote_ip() ) {
						return true;
					}
				}
				if ( crb_array_get( $policies, '2fanewnet4' ) ) {
					if ( cerber_get_subnet_ipv4( $last_login['ip'] ) != cerber_get_subnet_ipv4( cerber_get_remote_ip() ) ) {
						return true;
					}
				}
				if ( crb_array_get( $policies, '2fanewua' ) ) {
					if ( $last_login['ua'] != sha1( crb_array_get( $_SERVER, 'HTTP_USER_AGENT', '' ) ) ) {
						return true;
					}
				}
				if ( crb_array_get( $policies, '2fanewcountry' ) ) {
					if ( lab_get_country( $last_login['ip'], false ) != lab_get_country( cerber_get_remote_ip(), false ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Initiate 2FA process
	 *
	 * @param $user
	 * @param string $login
	 *
	 * @return bool|string|WP_Error
	 */
	private static function enforce2fa( $user, $login = '' ) {

		if ( ! $pin = self::generate_pin( $user->ID ) ) {
			return new WP_Error( '2fa-error', 'Unable to create PIN for the user #' . $user->ID );
		}

		$cus = cerber_get_set( 'cerber_user', $user->ID );

		$cus['2fa']['login']   = $login;
		$cus['2fa']['to']      = cerber_2fa_get_redirect_to( $user );
		$cus['2fa']['ajax']    = cerber_is_wp_ajax();
		$cus['2fa']['interim'] = isset( $_REQUEST['interim-login'] ) ? 1 : 0;

		cerber_update_set( 'cerber_user', $cus, $user->ID );

		return $pin;

	}

	/**
	 * Generates PIN and its expiration
	 *
	 * @param $user_id
	 *
	 * @return bool|string
	 */
	private static function generate_pin( $user_id ) {

		$cus = cerber_get_set( 'cerber_user', $user_id );

		if ( ! $cus || ! is_array( $cus ) ) {
			$cus = array();
		}

		$pin = substr( str_shuffle( '1234567890' ), 0, CERBER_PIN_LENGTH );

		$cus['2fa'] = array(
			'pin'      => $pin,
			'expires'  => time() + CERBER_PIN_EXPIRES * 60,
			'attempts' => 0,
			'ip'       => cerber_get_remote_ip(),
			'ua'       => sha1( crb_array_get( $_SERVER, 'HTTP_USER_AGENT', '' ) )
		);

		if ( $ret = cerber_update_set( 'cerber_user', $cus, $user_id ) ) {
			self::send_user_pin( $user_id, $pin );
			return $pin;
		}

		return false;

	}

	static function restrict_and_verify( $user_id = null ) {
	    global $cerber_status;
		static $done = false;

		if ( $done ) {
			return;
		}

    	$done = true;

		if ( ! $user_id && ! $user_id = get_current_user_id() ) {
			return;
		}

		self::$user_id = $user_id;

		$cus = cerber_get_set( 'cerber_user', $user_id );

		if ( ! $cus
		     || empty( $cus['2fa']['pin'] ) ) {
			return;
		}

		if ( crb_acl_is_white() ) {
			self::delete_2fa( $user_id );

			return;
		}

		// Check user settings again
		$tfm = crb_array_get( $cus, 'tfm' );
		if ( $tfm === 2 ) {
			self::delete_2fa( $user_id );

			return;
		}
        elseif ( ! $tfm ) {
			$user = wp_get_current_user();
	        if ( ! self::check_role_policies( $cus, $user->roles ) ) {
				self::delete_2fa( $user->ID );

				return;
			}
		}

		$twofactor = $cus['2fa'];

		// Check: must be the same browser
		if ( $twofactor['ip'] != cerber_get_remote_ip()
		     || $twofactor['ua'] != sha1( crb_array_get( $_SERVER, 'HTTP_USER_AGENT', '' ) )
		     || ! cerber_is_ip_allowed() ) {
			self::delete_2fa( $user_id );
			cerber_user_logout();
			wp_redirect( get_home_url() );
			exit;
		}

		// User wants to abort 2FA?
		if ( $now = cerber_get_get( 'cerber_2fa_now' ) ) {
			$go = null;
			if ( $now == 'different' ) {
				$go = wp_login_url( ( ! empty( $twofactor['to'] ) ) ? urldecode( $twofactor['to'] ) : '' );
			}
			if ( $now == 'cancel' ) {
				$go = get_home_url();
			}
			if ( $go ) {
				cerber_user_logout( 28 );
				wp_redirect( $go );
				exit;
			}
		}

		if ( $twofactor['attempts'] > 10 ) {
			cerber_block_add( cerber_get_remote_ip(), 721 );
			cerber_user_logout();
			wp_redirect( get_home_url() );
			exit;
		}

		$new_pin = '';
		if ( $twofactor['expires'] < time() ) {
			$new_pin = self::generate_pin( $user_id );
		}

		// The first step of verification, ajax
		if ( cerber_is_http_post() ) {
			self::process_ajax( $new_pin );
		}

		// The second, final step of verification
		if ( cerber_is_http_post()
		     && ! empty( $twofactor['nonce'] )
		     && $_POST['cerber_tag'] === $twofactor['nonce']
		     && ( $pin = cerber_get_post( 'cerber_pin' ) )
		     && self::verify_pin( trim( $pin ) ) ) {

			unset( $cus['2fa'] );
			$cus['2fa_history'] = array( 0, time() );
			cerber_update_set( 'cerber_user', $cus, $user_id );

			$cerber_status = 27;
			cerber_log( 5, $twofactor['login'], $user_id );
			cerber_login_history( $user_id );

			cerber_2fa_checker( true );

			$url = ( ! empty( $twofactor['to'] ) ) ? $twofactor['to'] : get_home_url();
			wp_safe_redirect( $url );
			exit;
		}

		self::show_2fa_page();
		exit;
	}

	static function process_ajax( $new_pin ) {
		if ( ( ! $nonce = cerber_get_post( 'the_2fa_nonce', '\w+' ) )
		     || ( ! $pin = cerber_get_post( 'cerber_verify_pin' ) ) ) {
			return;
		}

		$err = '';
		if ( ! wp_verify_nonce( $nonce, 'crb-ajax-2fa' ) ) {
			$err = 'Nonce error.';
		}
        elseif ( $new_pin) {
	        $err = __('This verification PIN code is expired. We have just sent a new one to your email.','wp-cerber');
        }
        elseif ( ! self::verify_pin( trim( $pin ), $nonce ) ) {
			$err = __('You have entered an incorrect verification PIN code','wp-cerber');
		}

		echo json_encode( array( 'error' => $err ) );
		exit;
	}

	private static function verify_pin( $pin, $nonce = null ) {
		$cus = cerber_get_set( 'cerber_user', self::$user_id );

		if ( ! $cus
		     || empty( $cus['2fa']['pin'] )
		     || $cus['2fa']['expires'] < time() ) {
			return false;
		}

		if ( (string) $pin === (string) $cus['2fa']['pin'] ) {
			$ret = true;
			if ( ! $nonce ) {
				return $ret;
			}
			$cus['2fa']['nonce'] = $nonce;
		}
		else {
			$cus['2fa']['attempts'] ++;
			$ret = false;
		}

		cerber_update_set( 'cerber_user', $cus, self::$user_id );

		return $ret;
	}

	static function show_2fa_page( $echo = true ) {
		@ini_set( 'display_errors', 0 );
		$ajax_vars = 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";';
		$ajax_vars .= 'var nonce2fa = "'. wp_create_nonce( 'crb-ajax-2fa' ).'";';
		if ( ! defined( 'CONCATENATE_SCRIPTS' ) ) {
			define( 'CONCATENATE_SCRIPTS', false );
		}
		wp_enqueue_script( 'jquery' );
		ob_start();
		?>
        <!DOCTYPE html>
        <html style="height: 100%;">
        <head>
            <meta charset="UTF-8">
            <title><?php _e( 'Please verify that it’s you', 'wp-cerber' ); ?></title>
            <style>
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    color: #555;
                }
                #cerber_2fa_inner  {
                    width: 350px;
                }
                @media (-webkit-min-device-pixel-ratio: 2) and (max-width: 1000px),
                (min-resolution: 192dpi) and (max-width: 1000px), {
                    #cerber_2fa_inner {
                        width: 100%;
                    }
                }
                @media screen and (max-width: 900px) {
                    #cerber_2fa_inner {
                        /*width: 100%;*/
                    }
                }
                #cerber_2fa_msg {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    /*height: 80px;*/
                    padding: 40px 0 40px 0;
                    background-color: #FF5733;
                    color: #fff;
                    opacity: 0.9;
                }

                #cerber_2fa_form input[type="text"] {
                    color: #000;
                    text-align: center;
                    font-size: 1.5em;
                    letter-spacing: 0.1em;
                    padding: 5px;
                    min-width: 140px;
                    border-radius: 4px;
                }
                #cerber_2fa_form input[type="submit"] {
                    color: white;
                    background: #0085ba;
                    /*background: #0073aa;*/
                    border: 0;
                    font-size: 1em;
                    font-weight: 600;
                    letter-spacing: 0.1em;
                    text-align: center;
                    cursor: pointer;
                    padding: 1em;
                    min-width: 150px;
                    border-radius: 4px;
                }
            </style>
			<?php print_head_scripts(); ?>
            <script>
				<?php echo $ajax_vars; ?>
            </script>
        </head>

        <body style="height: 90%; text-align: center;">
        <div style="display: flex; align-items: center; justify-content: center; text-align: center; height: 90%;">
			<?php
			self::cerber_2fa_form();
			?>
        </div>
        </body>
        </html>

		<?php

		$html = ob_get_clean();
		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	static function send_user_pin( $user_id, $pin, $details = true ) {
		$to     = self::get_user_email( $user_id );
		$subj   = __( 'Please verify that it’s you', 'wp-cerber' );
		$body   = array();
		$body[] = 'We need to verify that it’s you because you are trying to sign-in from a different device or a different location or you have not signed in for a long time. If this wasn’t you, please reset your password immediately.';
		$body[] = __( 'Please use the following verification PIN code to confirm your identity', 'wp-cerber' ) . '. ' . sprintf( __( 'The code is valid for %s minutes.', 'wp-cerber' ), CERBER_PIN_EXPIRES );

		if ( ! $pin && ( $p = self::get_user_pin( $user_id ) ) ) {
			$pin = $p['pin'];
		}

		if ( ! $pin ) {
			return false;
		}

		$body[] = $pin;

		$data   = get_userdata( $user_id );

		if ( $details ) {
			$ds   = array();
			$ds[] = 'Login: ' . $data->user_login;
			$ds[] = 'IP: ' . cerber_get_remote_ip();
			if ( $c = lab_get_country( cerber_get_remote_ip(), false ) ) {
				$ds[] = 'Location: ' . cerber_country_name( $c ) . ' (' . $c . ')';
			}
			$ds[] = 'Browser: ' . substr( strip_tags( crb_array_get( $_SERVER, 'HTTP_USER_AGENT', 'Not set' ) ), 0, 1000 );
			$ds[] = 'Date: ' . cerber_date( time(), false );

			$body[] = '';
			$body[] = __( 'Here are the details of the sign-in attempt', 'wp-cerber' );
			$body[] = implode( "\n", $ds );
		}

		$body = implode( "\n\n", $body );

		$result = wp_mail( $to, $subj, $body );

		if ( $result && ( $data->user_email != $to ) ) {
		    // TODO Add a notification to the main email
			//wp_mail( $data->user_email, $subj, $body );
		}
	}

	static function get_user_email( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$cus = cerber_get_set( 'cerber_user', $user_id );
		if ( $cus && ( $email = crb_array_get( $cus, 'tfemail' ) ) ) {
			return $email;
		}

		$data = get_userdata( $user_id );

		return $data->user_email;
	}

	static function get_user_pin( $user_id ) {

		$cus = cerber_get_set( 'cerber_user', $user_id );

		if ( ! $cus
		     || empty( $cus['2fa']['pin'] )
		     || $cus['2fa']['expires'] < time() ) {
			return false;
		}

		return $cus['2fa'];

	}

	static function get_user_pin_info( $user_id ) {

		if ( ! $pin = self::get_user_pin( $user_id ) ) {
			return '';
		}

		return '<code style="font-size: 120%;">' . $pin['pin'] . '</code> ' . __( 'expires', 'wp-cerber' ) . ' ' . cerber_ago_time( $pin['expires'] );

	}

	static function delete_2fa( $uid ) {
		if ( ! $uid = absint( $uid ) ) {
			return;
		}
		$cus = cerber_get_set( 'cerber_user', $uid );
		if ( $cus && isset( $cus['2fa'] ) ) {
			unset( $cus['2fa'] );
			cerber_update_set( 'cerber_user', $cus, $uid );
		}
	}

	static function cerber_2fa_form() {
		$max    = CERBER_PIN_LENGTH;
		$atts   = 'pattern="\d{' . $max . '}" maxlength="' . $max . '" size="' . $max . '" title="' . __( 'only digits are allowed', 'wp-cerber' ) . '"';
		// Please enter your PIN code to continue
		$email = self::get_user_email();
		$text = __( "We've sent a verification PIN code to your email", 'wp-cerber' ) . ' ' . cerber_mask_email( $email ) .
		        '<p>'. __( 'Enter the code from the email in the field below.', 'wp-cerber' ).'</p>';
		//$change = '<a href="' . cerber_get_home_url() . '/?cerber_2fa_now=different">' . __( 'Sign in with a different account', 'wp-cerber' ) . '</a>';
		$change = '<a href="' . cerber_get_home_url() . '/?cerber_2fa_now=different">' . __( 'Try again', 'wp-cerber' ) . '</a>';
		$cancel = '<a href="' . cerber_get_home_url() . '/?cerber_2fa_now=cancel">' . __( 'Cancel', 'wp-cerber' ) . '</a>';
		$links = '<p>'.__( 'Did not receive an email?', 'wp-cerber' ) .'</p>'. $change . ' ' . __( 'or', 'wp-cerber' ) . ' ' . $cancel;
		?>
        <div id="cerber_2fa_msg"></div>
        <div>
            <div id="cerber_2fa_wrap" style="text-align: center; background-color: #eee; border-top: solid 4px #ddd; padding: 1.5em 3em 1.5em 3em;">
                <div id="cerber_2fa_inner" style="text-align: center;">
                    <h1 style="color:#000;"><?php _e( "Verify it's you", 'wp-cerber' ); ?></h1>
                    <div id="cerber_2fa_info" style="color: #333;"><?php echo $text; ?></div>
                    <form id="cerber_2fa_form" method="post" data-verified="no" style="margin-bottom: 3em;">
                        <p><input required type="text" name="cerber_pin" <?php echo $atts; ?> ></p>
                        <p><input type="hidden" name="cerber_tag" value="2FA"></p>
                        <p><input type="submit" value="<?php _e( 'Verify', 'wp-cerber' ); ?>"></p>
                    </form>
                </div>
            </div>
			<?php echo $links; ?>
        </div>
        <script>
            jQuery(document).ready(function ($) {
                var cform = $('#cerber_2fa_form');
                var umsg = 'cerber_2fa_msg';
                cform.submit(function (event) {
                    crb_hide_user_msg();
                    if (cform.data('verified') === 'yes') {
                        return;
                    }
                    event.preventDefault();
                    $.post(ajaxurl, {
                            the_2fa_nonce: nonce2fa,
                            cerber_verify_pin: $(this).find('input[type="text"]').val()
                        },
                        function (server_response, textStatus, jqXHR) {
                            var server_data = $.parseJSON(server_response);
                            if (server_data.error.length === 0) {
                                cform.find('[name="cerber_tag"]').val(nonce2fa);
                                cform.data('verified', 'yes');
                                cform.submit();
                            }
                            else {
                                crb_display_user_msg(server_data['error']);
                            }
                        }
                    ).fail(function (jqXHR, textStatus, errorThrown) {
                        var err = errorThrown + ' ' + jqXHR.status;
                        alert(err);
                        console.error('Server Error: ' + err);
                    });
                });

                function crb_display_user_msg(msg) {
                    $('#' + umsg).fadeIn(500).html(msg);
                    setTimeout(function (args) {
                        crb_hide_user_msg();
                    }, 5000);
                }

                function crb_hide_user_msg() {
                    document.getElementById(umsg).style.display = "none";
                }
            });
        </script>
		<?php
	}
}

/**
 * @param $user WP_User
 *
 * @return string
 */
function cerber_2fa_get_redirect_to( $user ) {
	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		$requested_redirect_to = $redirect_to;
	}
	else {
		$redirect_to = admin_url();
		$requested_redirect_to = '';
	}

	$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

	return $redirect_to;
}

/**
 * Verify that 2FA on the website works
 * If it works, 2FA can be enabled for admins
 *
 */
function cerber_2fa_checker( $save = false ) {
	if ( $save ) {
		cerber_update_set( 'cerber_2fa_is_ok', 1, null, false );
	}
	else {
		if ( cerber_get_set( 'cerber_2fa_is_ok', null, false ) ) {
			return true;
		}

		return false;
	}
}