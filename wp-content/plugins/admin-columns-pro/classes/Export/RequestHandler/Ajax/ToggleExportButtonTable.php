<?php

declare(strict_types=1);

namespace ACP\Export\RequestHandler\Ajax;

use AC\Nonce;
use AC\Request;
use AC\RequestAjaxHandler;
use ACP\Export\UserPreference;

class ToggleExportButtonTable implements RequestAjaxHandler
{

    private $preference;

    public function __construct()
    {
        $this->preference = new UserPreference\ShowExportButton();
    }

    public function handle(): void
    {
        $request = new Request();

        if ( ! (new Nonce\Ajax())->verify($request)) {
            wp_send_json_error('invalid nonce');
        }

        $list_key = (string)$request->filter('list_screen');

        if ( ! $list_key) {
            wp_send_json_error('invalid list screen');
        }

        $is_active = 'true' === $request->filter('value');

        $this->preference->set(
            $list_key,
            (int)$is_active
        );

        wp_send_json_success();
    }

}