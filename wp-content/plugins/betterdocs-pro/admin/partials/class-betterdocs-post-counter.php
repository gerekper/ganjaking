<?php
///**
// * This class is responsible for counting unique user's post view count based on Post ID
// *
// * @since 1.6.4
// */
//
//class BetterDocsPro_Post_Counter{
//
//	private $cookie = array(
//		'exists'        => false,
//		'visited_posts' => array(),
//		'expiration'    => 0
//	);
//
//	private $time_format = array(
//		'number' => 24,
//		'type' => 'hours'
//	);
//
//	public function __construct() {
//		add_action( 'plugins_loaded', array( $this, 'initialize_cookie' ), 1 );
//		add_action( 'wp_head', array( $this, 'unique_views_counter' ) );
//	}
//
//	public function unique_views_counter() {
//		global $user_ID, $post, $post_type;
//
//		$get_docs = get_posts( array( 'post_type' => 'docs', 'post_status' => 'publish') );
//
//		if( $post_type != 'docs' || count( $get_docs ) == 0 ) {
//			return;
//		}
//
//		$this->checkpost( get_the_ID() );
//	}
//
//	public function checkpost( $id = 0 ) {
//		if ( defined( 'SHORTINIT' ) && SHORTINIT ) {
//			$this->initialize_cookie();
//		}
//
//		//get the post id
//		$id = intval( empty( $id ) ? get_the_ID() : $id );
//
//		//get current user id
//		$user_id = get_current_user_id();
//
//		//get current user ip
//		$user_ip = strval( $this->get_current_user_ip() );
//
//		//if user id empty then exit
//		if( empty( $id ) ) {
//			return;
//		}
//
//		$current_time = current_time( 'timestamp', true );
//
//		if( $this->cookie['exists'] ) {
//			//post previously viewed, but validity still remains
//			if( in_array( $id, array_keys( $this->cookie['visited_posts'] ), true )  && $current_time < $this->cookie['visited_posts'][$id] ) {
//				//update the cookie but not the count visit
//				if ( in_array( $id, array_keys( $this->cookie['visited_posts'] ), true ) && $current_time < $this->cookie['visited_posts'][$id] ) {
//					// update cookie but do not count visit
//					$this->save_cookie( $id, $this->cookie, false );
//					return;
//					// update cookie
//				} else
//					$this->save_cookie( $id, $this->cookie );
//			}
//		}else {
//			//save new cookie if it does not exist
//			$this->save_cookie( $id );
//		}
//	}
//
//	public function save_cookie( $id, $cookie = array(), $expired = true ) {
//		$expiration = $this->get_timestamp($this->time_format['type'], $this->time_format['number']);
//		$cookie_label = 'bdocs_counter';
//
//		//if it is a new cookie, then set a cookie
//		if( empty( $cookie ) ) {
//			setcookie( $cookie_label, $expiration.'-'.$id, COOKIEPATH, COOKIE_DOMAIN, (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? true : false ), true );
//		} else {
//
//		}
//
//	}
//
//	public function get_timestamp( $type, $number, $timestamp = true ) {
//		$converter = array(
//			'minutes'	 => 60,
//			'hours'		 => 3600,
//			'days'		 => 86400,
//			'weeks'		 =>  s,
//			'months'	 => 2592000,
//			'years'		 => 946080000
//		);
//		return (int) ( ( $timestamp ? current_time( 'timestamp', true ) : 0 ) + $number * $converter[$type] );
//	}
//
//	public function get_current_user_ip() {
//		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
//
//		foreach ( array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ) as $key ) {
//			if ( array_key_exists( $key, $_SERVER ) === true ) {
//				foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
//					// trim for safety purpose
//					$ip = trim( $ip );
//
//					// attempt to validate IP
//					if ( $this->validate_user_ip( $ip ) ) {
//						continue;
//					}
//				}
//			}
//		}
//
//		return $ip;
//	}
//
//	public function validate_user_ip( $ip ) {
//		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
//			return false;
//		}
//
//		return true;
//	}
//
//	public function initialize_cookie() {
//		//if accessed by admin, then exit the cookie initialization process
//		if( is_admin() ) {
//			return;
//		}
//
//		//cookie name
//		$cookie_label = 'bdocs_counter';
//
//		if( isset( $_COOKIE[$cookie_label] ) && !empty( $_COOKIE[$cookie_label] ) ) {
//			$visited_posts = $expirations = array();
//
//			foreach ($_COOKIE[$cookie_label] as $content) {
//				//check if cookie is valid
//				if( preg_match( '/^(([0-9]+b[0-9]+a?)+)$/', $content ) === 1 ) {
//					//store single id with expiration
//					$expire_ids = explode( 'a', $content );
//
//					foreach ($expire_ids as $pair) {
//						$pair = explode( 'b', $pair);
//						$expirations[] = intval( $pair[0] );
//						$visited_posts[intval($pair[1])] = intval($pair[0]);
//					}
//
//				}
//			}
//			$this->cookie['exists']        = true ;
//			$this->cookie['visited_posts'] = $visited_posts;
//			$this->cookie['expiration']    =  max($expirations);
//		}
//
//	}
//}
//new BetterDocsPro_Post_Counter();