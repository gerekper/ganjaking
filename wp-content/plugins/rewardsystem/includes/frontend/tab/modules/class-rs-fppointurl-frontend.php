<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForPointURL' ) ) {

	class RSFunctionForPointURL {

		public static function init() {
			add_action( 'wp_head', array( __CLASS__, 'award_points_for_url_click' ) );
		}

		public static function award_points_for_url_click() {

			if ( ! is_user_logged_in() || ! isset( $_GET['rsid'] ) ) {
				return;
			}

			$uniqueid = sanitize_text_field( ( $_GET['rsid'] ) );
			$UserId   = get_current_user_id();
			$BanType  = check_banning_type( $UserId );
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return;
			}

			$PointUrlData = get_option( 'points_for_url_click' );

			if ( ! srp_check_is_array( $PointUrlData ) ) {
				return;
			}

			$OfferName = $PointUrlData[ $uniqueid ]['name'];
			$URLUsedBy = $PointUrlData[ $uniqueid ]['used_by'];
			$TimeLimit = $PointUrlData[ $uniqueid ]['time_limit'];
			$Date      = strtotime( gmdate( 'y-m-d' ) );
			$ExpDate   = strtotime( $PointUrlData[ $uniqueid ]['expiry_time'] );

			if ( ! in_array( $UserId, (array) $URLUsedBy ) ) {
				if ( '2' == $TimeLimit ) {
					if ( $Date <= $ExpDate ) {
						self::check_if_count_exceed( $PointUrlData, $uniqueid, $UserId, $OfferName );
					} else {
						$MsgToDisplay = str_replace( '[offer_name]', $OfferName, get_option( 'failure_msg_for_expired_url', '[offer_name] has been Expired' ) )
						?>                            
						<div class="sk_failure_msg_for_pointsurl"><?php echo esc_html( $MsgToDisplay ); ?></div>
						<?php
					}
				} else {
					self::check_if_count_exceed( $PointUrlData, $uniqueid, $UserId, $OfferName );
				}
			} else {
				?>
				<div class="sk_failure_msg_for_pointsurl"><?php echo esc_html( get_option( 'failure_msg_for_accessed_url', 'You cannot get coupon for this link because you have already claimed' ) ); ?></div>
				<?php
			}
		}

		public static function check_if_count_exceed( $PointUrlData, $uniqueid, $UserId, $OfferName ) {
			$UsageCount     = $PointUrlData[ $uniqueid ]['current_usage_count'];
			$CountLimit     = $PointUrlData[ $uniqueid ]['count'];
			$CountLimitType = $PointUrlData[ $uniqueid ]['count_limit'];
			$BoolValue      = ( '1' == $CountLimitType ) ? true : ( $UsageCount < $CountLimit );
			$PointsForUrl   = $PointUrlData[ $uniqueid ]['points'];

			if ( $BoolValue ) {
				$MsgToDisplay = str_replace( '[points]', $PointsForUrl, get_option( 'rs_success_message_for_pointurl', '[points] Points added for [offer_name]' ) );
				$MsgToDisplay = str_replace( '[offer_name]', $OfferName, $MsgToDisplay );
				?>
				<div class="rs_success_msg_for_pointurl"><?php echo esc_html( $MsgToDisplay ); ?></div>
				<?php
				$PointUrlData[ $uniqueid ]['current_usage_count'] = $UsageCount + 1;
				if ( ! is_array( $PointUrlData[ $uniqueid ]['used_by'] ) ) {
					$PointUrlData[ $uniqueid ]['used_by'] = array();
				}

				$PointUrlData[ $uniqueid ]['used_by'][] = $UserId;

				update_option( 'points_for_url_click', $PointUrlData );
				$table_args = array(
					'user_id'           => $UserId,
					'pointstoinsert'    => $PointsForUrl,
					'checkpoints'       => 'RPFURL',
					'totalearnedpoints' => $PointsForUrl,
				);
				RSPointExpiry::insert_earning_points( $table_args );
				RSPointExpiry::record_the_points( $table_args );
			} else {
				$MsgToDisplay = str_replace( '[points]', $PointsForUrl, get_option( 'failure_msg_for_count_exceed', 'Usage of Link Limitation reached' ) );
				$MsgToDisplay = str_replace( '[offer_name]', $OfferName, $MsgToDisplay );
				?>
				<div class="sk_failure_msg_for_pointsurl"><?php echo esc_html( $MsgToDisplay ); ?></div>
				<?php
			}
		}
	}

	RSFunctionForPointURL::init();
}
