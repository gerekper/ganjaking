<?php

namespace WPDeveloper\BetterDocsPro\Admin;

use WPDeveloper\BetterDocs\Utils\Database;
use WPDeveloper\BetterDocs\Admin\Analytics as FreeAnalytics;

class Analytics extends FreeAnalytics {
    public function __construct( Database $database ) {
        parent::__construct( $database );

        add_action( 'template_redirect', [$this, 'set_cookies'] );
        add_action( 'wp_head', [$this, 'update_analytics'] );
    }

    public function set_cookies() {
        if ( is_singular( 'docs' ) && $this->is_eligible_visits() == true ) {
            $post_id = get_the_ID();
            if ( ! isset( $_COOKIE["docs_visited_{$post_id}"] ) ) {
                setcookie( 'docs_visited_' . $post_id, true, time() + ( 86400 * 180 ), "/" );
            }
        }
    }

    public function update_analytics() {
        global $post_type, $post, $user_ID, $wpdb;

        if ( $post_type !== 'docs' || ! is_singular( 'docs' ) ) {
            return;
        }

        if ( wp_is_post_revision( $post ) || is_preview() ) {
            return;
        }

        $post_id            = isset( $post->ID ) ? (int) $post->ID : null;
        $is_eligible_visits = $this->is_eligible_visits();

        if ( ! $is_eligible_visits || $post_id === null ) {
            return;
        }

        // find if this date data available on betterdocs_analytics
        $result = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * from {$wpdb->prefix}betterdocs_analytics where post_id = %d and created_at = %s",
                [
                    $post_id,
                    date( "Y-m-d" )
                ]
            )
        );

        if ( ! empty( $result ) ) {
            $impressions_increment = $result[0]->impressions + 1;
            if ( ! isset( $_COOKIE['docs_visited_' . $post->ID] ) ) {
                $unique_visit = $result[0]->unique_visit + 1;
            } else {
                $unique_visit = $result[0]->unique_visit;
            }
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}betterdocs_analytics
                            SET impressions = " . $impressions_increment . ", unique_visit = " . $unique_visit . "
                            WHERE created_at = %s AND post_id = %d",
                    [date( 'Y-m-d' ), $post_id]
                )
            );
        } else {
            $unique_visit = ( ! isset( $_COOKIE['docs_visited_' . $post->ID] ) ) ? 1 : 0;
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO {$wpdb->prefix}betterdocs_analytics
                            ( post_id, impressions, unique_visit, created_at )
                            VALUES ( %d, %d, %d, %s )",
                    [$post_id, 1, $unique_visit, date( 'Y-m-d' )]
                )
            );
        }

        $views = get_post_meta( $post_id, '_betterdocs_meta_views', true );

        if ( $views === null ) {
            add_post_meta( $post_id, '_betterdocs_meta_views', 1 );
        } else {
            update_post_meta( $post_id, '_betterdocs_meta_views', ++$views );
        }
    }

    protected function is_eligible_visits() {
        $should_count   = false;
        $analytics_from = betterdocs()->settings->get( 'analytics_from', 'everyone' );
        /**
         * Inspired from WP-Postviews for
         * this pece of code.
         */
        switch ( $analytics_from ) {
            case 'everyone':
                $should_count = true;
                break;
            case 'guests':
                if ( empty( $_COOKIE[USER_COOKIE] ) && (int) $user_ID === 0 ) {
                    $should_count = true;
                }
                break;
            case 'registered_users':
                if ( (int) $user_ID > 0 ) {
                    $should_count = true;
                }
                break;
        }

        $exclude_bot_analytics = betterdocs()->settings->get( 'exclude_bot_analytics', true );
        if ( $exclude_bot_analytics == 1 ) {
            /**
             * Inspired from WP-Postviews for
             * this piece of code.
             */
            $bots = [
                'Google Bot'    => 'google',
                'MSN'           => 'msnbot',
                'Alex'          => 'ia_archiver',
                'Lycos'         => 'lycos',
                'Ask Jeeves'    => 'jeeves',
                'Altavista'     => 'scooter',
                'AllTheWeb'     => 'fast-webcrawler',
                'Inktomi'       => 'slurp@inktomi',
                'Turnitin.com'  => 'turnitinbot',
                'Technorati'    => 'technorati',
                'Yahoo'         => 'yahoo',
                'Findexa'       => 'findexa',
                'NextLinks'     => 'findlinks',
                'Gais'          => 'gaisbo',
                'WiseNut'       => 'zyborg',
                'WhoisSource'   => 'surveybot',
                'Bloglines'     => 'bloglines',
                'BlogSearch'    => 'blogsearch',
                'PubSub'        => 'pubsub',
                'Syndic8'       => 'syndic8',
                'RadioUserland' => 'userland',
                'Gigabot'       => 'gigabot',
                'Become.com'    => 'become.com',
                'Baidu'         => 'baiduspider',
                'so.com'        => '360spider',
                'Sogou'         => 'spider',
                'soso.com'      => 'sosospider',
                'Yandex'        => 'yandex'
            ];
            $useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
            foreach ( $bots as $name => $lookfor ) {
                if ( ! empty( $useragent ) && ( false !== stripos( $useragent, $lookfor ) ) ) {
                    $should_count = false;
                    break;
                }
            }
        }

        return $should_count;
    }

    public function get_views( $post_id ) {
        global $wpdb;
        $reactions = $wpdb->get_results(
            $wpdb->prepare( "
                SELECT sum(impressions) as totalViews
                FROM {$wpdb->prefix}betterdocs_analytics
                WHERE post_id = %d",
                $post_id
            )
        );

        return $reactions[0]->totalViews;
    }

    public function enqueue( $hook ) {
        if ( $hook !== 'betterdocs_page_betterdocs-analytics' ) {
            return;
        }

        betterdocs_pro()->assets->enqueue( 'betterdocs-analytics', 'admin/css/analytics.css' );
        betterdocs_pro()->assets->enqueue( 'betterdocs-analytics', 'admin/js/analytics.js' );

        betterdocs_pro()->assets->localize(
            'betterdocs-analytics',
            'betterdocs',
            [
                'dir_url'      => BETTERDOCS_PRO_ABSURL,
                'rest_url'     => get_rest_url(),
                'free_version' => betterdocs()->version,
                'pro_version'  => betterdocs_pro()->version,
                'nonce'        => wp_create_nonce( 'wp_rest' )
            ]
        );
    }

    public function views() {
        betterdocs()->views->get( 'admin/analytics-pro' );
    }
}
