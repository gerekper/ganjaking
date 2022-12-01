<?php
/**
 * WooCommerce Memberships.
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\API\v4;

use SkyVerge\WooCommerce\Memberships\API\Controller\User_Memberships as User_Memberships_Controller;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined('ABSPATH') or exit;

/**
 * User Memberships REST API V3 handler.
 *
 * @since 1.23.0
 */
class User_Memberships extends User_Memberships_Controller
{
    /**
     * User Memberships REST API V4 constructor.
     *
     * @since 1.23.0
     */
    public function __construct()
    {
        parent::__construct();

        $this->version = 'v4';
        $this->namespace = 'wc/v4/memberships';
    }

    /**
     * Gets the available query parameters for collections.
     *
     * @internal
     *
     * @since 1.23.0
     *
     * @return array associative array
     */
    public function get_collection_params()
    {
        $params = parent::get_collection_params();

        $params['plan'] = [
            'description'       => __('Limit results to user memberships for a specific plan (matched by ID or slug).', 'woocommerce-memberships'),
            'type'              => 'array',
            'items'             => [
                'type'          => 'integer',
            ],
            'sanitize_callback' => 'wp_parse_id_list',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        $params['status'] = [
            'default'           => 'any',
            'description'       => __('Limit results to user memberships of a specific status.', 'woocommerce-memberships'),
            'type'              => 'array',
            'items'             => [
                'type'          => 'string',
            ],
            'sanitize_callback' => 'wp_parse_list',
            'validate_callback' => 'rest_validate_request_arg',
        ];

        /*
         * Filters the user membership collection params for REST API queries.
         *
         * @since 1.11.0
         *
         * @param array $params associative array
         */
        return (array) apply_filters('wc_memberships_rest_api_user_memberships_collection_params', $params);
    }

    /**
     * Prepares query args for items collection query.
     *
     * @since 1.23.0
     *
     * @param array|\WP_REST_Request $request request object (with array access)
     * @return array
     */
    protected function prepare_items_query_args($request)
    {
        $query_args = parent::prepare_items_query_args($request);

        if (isset($request['status']) && 'any' !== $request['status']) {
            $statuses = $request['status'];
            $statuses = array_map(function ($status) {
                return Framework\SV_WC_Helper::str_starts_with($status, 'wcm-') ? $status : 'wcm-'.$status;
            }, $statuses);

            $query_args['post_status'] = ! empty($statuses) ? $statuses : 'any';
        }

        if (isset($request['search']) && ! empty($request['search'])) {
            $user_ids = $this->get_users_for_search($request['search']);
            $query_args['author__in'] = ! empty($user_ids) ? $user_ids : [0];
        }

        /*
         * Filters the WP API query arguments for user memberships.
         *
         * This filter's name follows the WooCommerce core pattern.
         * @see \WC_REST_Posts_Controller::get_items()
         *
         * @since 1.11.0
         *
         * @param array $args associative array of query args
         * @param \WP_REST_Request $request request object
         */
        return (array) apply_filters("woocommerce_rest_{$this->post_type}s_query_args", $query_args, $request);
    }
}
