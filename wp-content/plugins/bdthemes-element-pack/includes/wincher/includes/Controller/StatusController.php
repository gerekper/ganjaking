<?php

namespace ElementPack\Wincher\Controller;

use WP_REST_Request;
use WP_REST_Response;

/**
 * The Status Controller.
 */
class StatusController extends RestController
{
    /**
     * Gets the account status.
     *
     * @param WP_REST_Request $request the request
     *
     * @return WP_REST_Response the response
     */
    public function get(WP_REST_Request $request)
    {
        // If tokens were previously set but have expired, automatically refresh them.
        if ($this->client->hasTokens() && $this->client->hasExpiredTokens()) {
            $this->client->refreshTokens($this->client->getTokens());
        }

        if ($this->client->hasValidTokens()) {
            $account = $this->client->getAccount();
            $domains = $this->client->getWebsites()['data'];

            return new WP_REST_Response([
                'account' => $account,
                'domains' => $domains,
            ], 200);
        }

        return new WP_REST_Response([
            'error' => 'Unauthorized',
        ], 401);
    }
}
