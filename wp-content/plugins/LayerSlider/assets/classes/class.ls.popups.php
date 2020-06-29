<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

class LS_Popups {

	public static $index;
	public static $popups;
	public static $postType;
	public static $frontPage;

	public static $optionKey = 'ls-popup-index';


	/**
	 * Private constructor to prevent instantiate static class
	 *
	 * @since 6.5.0
	 * @access private
	 * @return void
	 */
	private function __construct() {

	}



	public static function init() {

		// Popup is an exclusive feature, don't try to initialize it
		// in case of unactivated sites.
		if( ! LS_Config::isActivatedSite() ) {
			return false;
		}

		// Init Popups data
		self::$index = get_option( self::$optionKey, array());
		self::$popups = array();

		// Make sure that the Popup Index is an array
		if( ! is_array( self::$index ) ) {
			self::$index = array();
		}

		// Examine the Popup Index, see if there are popups
		// that needs to be automatically included on page
		// based on the user settings.
		if( ! is_admin() ) {
			add_action('wp', array(__CLASS__, 'setup'));
		}
	}


	public static function setup() {
		self::$postType = get_post_type();
		self::$frontPage = is_front_page();

		self::autoinclude();
		self::display();

		add_action('wp_footer', array(__CLASS__, 'render'), 1);
	}



	public static function addIndex( $data ) {

		if( empty( $data ) || empty( $data['id'] ) ) {
			return false;
		}

		if( ! is_array( self::$index ) ) {
			self::$index = array();
		}

		self::$index[ $data['id'] ] = $data;
		update_option(self::$optionKey, self::$index);
	}



	public static function removeIndex( $id ) {

		if( ! is_array( self::$index ) ) {
			self::$index = array();
		}

		if( empty( $id ) || empty( self::$index[ $id ] ) ) {
			return false;
		}

		unset( self::$index[ $id ] );
		update_option(self::$optionKey, self::$index);
	}



	protected static function autoinclude() {

		if( is_array(self::$index) && ! empty( self::$index ) ) {
			foreach( self::$index as $key => $popup ) {

				// First time visitor
				if( $popup['first_time_visitor'] && ! empty($_COOKIE['ls-popup-last-displayed'] ) ) {
					continue;
				}

				// Repeat control
				if( ! $popup['repeat'] && ! empty( $_COOKIE['ls-popup-'.$popup['id']] ) ) {
					continue;

				} elseif( $popup['repeat'] && $popup['repeat_days'] !== '' ) {
					if( 0 === (int)$popup['repeat_days'] ) {
						if( ! empty($_COOKIE['ls-popup-last-displayed'] ) ) {
							continue;
						}

					} elseif( ! empty($_COOKIE['ls-popup-'.$popup['id']]) && $_COOKIE['ls-popup-'.$popup['id']] > time() - 60 * 60 * 24 * (int)$popup['repeat_days'] ) {
						continue;
					}
				}


				// User roles
				$user = wp_get_current_user();
				if(
					( empty( $user->ID ) && empty( $popup['roles']['visitor'] ) ) ||
					( ! empty($user->roles[0]) && empty( $popup['roles'][ $user->roles[0] ] ) )
				) {
					continue;
				}

				// Include pages
				if( ! empty( $popup['pages'] ) ) {
					if( ! self::checkPages( $popup['pages'] ) ) {
						continue;
					}
				}

				// Exclude pages
				if( ! empty( $popup['pages']['exclude'] ) ) {
					if( self::checkPages( $popup['pages']['exclude'] ) ) {
						continue;
					}
				}


				// Passed every test, include the Popup
				self::$popups[] = $popup;
			}
		}
	}



	protected static function checkPages( $pages ) {
		if( ! empty( $pages ) && is_array( $pages ) ) {

			if(
				$pages['all'] ||
				( $pages['home'] && self::$frontPage ) ||
				( ! empty( $pages[ self::$postType ] ) && ! self::$frontPage )
			) {

				return true;
			}

			$pages = $pages['custom'];
		}

		if( ! empty( $pages ) ) {
			$pages = explode(',', $pages);
			foreach( $pages as $page ) {
				if( is_category( trim( $page ) ) || is_page( trim( $page ) ) || is_single( trim( $page ) ) ) {
					return true;
				}
			}
		}

		return false;
	}


	protected static function display( ) {

		if( ! empty(self::$popups) && is_array(self::$popups) ) {

			// Update the date of last displayed popup
			setcookie('ls-popup-last-displayed', time(), time()+60*60*24*30*24, '/');

			foreach( self::$popups as $popup ) {

				// Update the last opened date of this particular Popup
				// for the purpose of serving a repeat control.
				$expires = ( (int)$popup['repeat_days'] === 0 ) ? 0 : time() + 60*60*24*365;
				setcookie('ls-popup-'.$popup['id'], time(), $expires );
			}
		}
	}


	public static function render( $popup ) {

		if( ! empty(self::$popups) && is_array(self::$popups) ) {
			foreach( self::$popups as $popup ) {
				layerslider( $popup['id'], '', array( 'popup' => true ) );
			}

		}
	}

}