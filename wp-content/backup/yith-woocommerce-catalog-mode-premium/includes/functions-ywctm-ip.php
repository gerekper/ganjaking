<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !function_exists( 'ywctm_get_ip_address' ) ) {

    /**
     * Get user IP address
     *
     * @since   1.3.4
     * @return  string
     * @author  Alberto Ruggiero
     */
    function ywctm_get_ip_address() {

        if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip_addr = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip_addr = $_SERVER['REMOTE_ADDR'];
        }

        if ( $ip_addr === false ) {
            $ip_addr = '0.0.0.0';

            return $ip_addr;
        }

        if ( strpos( $ip_addr, ',' ) !== false ) {
            $x       = explode( ',', $ip_addr );
            $ip_addr = trim( end( $x ) );
        }

        if ( !ywctm_validate_ip( $ip_addr ) ) {
            $ip_addr = '0.0.0.0';
        }

        return $ip_addr;

    }

}

if ( !function_exists( 'ywctm_validate_ip' ) ) {

    /**
     * Validate IP Address
     *
     * @since   1.3.4
     *
     * @param   $ip
     * @param   $which (ipv4 or ipv6)
     *
     * @return  bool
     * @author  Alberto Ruggiero
     */
    function ywctm_validate_ip( $ip, $which = '' ) {

        $which = strtolower( $which );

        // First check if filter_var is available
        if ( is_callable( 'filter_var' ) ) {
            switch ( $which ) {
                case 'ipv4':
                    $flag = FILTER_FLAG_IPV4;
                    break;

                case 'ipv6':
                    $flag = FILTER_FLAG_IPV6;
                    break;

                default:
                    $flag = '';
                    break;
            }
            return ( bool ) filter_var( $ip, FILTER_VALIDATE_IP, $flag );
        }

        if ( $which !== 'ipv6' && $which !== 'ipv4' ) {
            if ( strpos( $ip, ':' ) !== false ) {
                $which = 'ipv6';
            }
            elseif ( strpos( $ip, '.' ) !== false ) {
                $which = 'ipv4';
            }
            else {
                return false;
            }
        }
        return call_user_func( 'validate_' . $which, $ip );
    }

}

if ( !function_exists( 'ywctm_validate_ipv4' ) ) {

    /**
     * Validate IPv4 Address
     *
     * @since   1.3.4
     *
     * @param   $ip
     *
     * @return  bool
     * @author  Alberto Ruggiero
     */
    function ywctm_validate_ipv4( $ip ) {

        $ip_segments = explode( '.', $ip );

        // Always 4 segments needed
        if ( count( $ip_segments ) !== 4 ) {
            return false;
        }
        // IP can not start with 0
        if ( $ip_segments[0][0] == '0' ) {
            return false;
        }

        // Check each segment
        foreach ( $ip_segments as $segment ) {
            // IP segments must be digits and can not be longer than 3 digits or greater then 255
            if ( $segment == '' || preg_match( "/[^0-9]/", $segment ) || $segment > 255 || strlen( $segment ) > 3 ) {
                return false;
            }
        }
        return true;
    }

}

if ( !function_exists( 'ywctm_validate_ipv6' ) ) {

    /**
     * Validate IPv6 Address
     *
     * @since   1.3.4
     *
     * @param   $str
     *
     * @return  bool
     * @author  Alberto Ruggiero
     */
    function ywctm_validate_ipv6( $str ) {

        // 8 groups, separated by : 0-ffff per group one set of consecutive 0 groups can be collapsed to ::
        $groups    = 8;
        $collapsed = false;
        $chunks    = array_filter( preg_split( '/(:{1,2})/', $str, NULL, PREG_SPLIT_DELIM_CAPTURE ) );

        // Rule out easy nonsense
        if ( current( $chunks ) == ':' || end( $chunks ) == ':' ) {
            return false;
        }

        // PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well
        if ( strpos( end( $chunks ), '.' ) !== false ) {
            $ipv4 = array_pop( $chunks );
            if ( !ywctm_validate_ipv4( $ipv4 ) ) {
                return false;
            }
            $groups --;
        }

        while ( $seg = array_pop( $chunks ) ) {
            if ( $seg[0] == ':' ) {
                if ( -- $groups == 0 ) {
                    return false; // too many groups
                }
                if ( strlen( $seg ) > 2 ) {
                    return false; // long separator
                }
                if ( $seg == '::' ) {
                    if ( $collapsed ) {
                        return false; // multiple collapsed
                    }
                    $collapsed = true;
                }
            }
            elseif ( preg_match( "/[^0-9a-f]/i", $seg ) || strlen( $seg ) > 4 ) {
                return false; // invalid segment
            }
        }

        return $collapsed || $groups == 1;
    }

}
