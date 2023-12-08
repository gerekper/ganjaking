<?php

declare(strict_types=1);

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\Request;
use AC\Type\ListScreenId;
use ACP\Migrate;
use ACP\Nonce\PreviewNonce;
use ACP\RequestHandler;

class PreviewMode implements RequestHandler
{

    private $storage;

    public function __construct()
    {
        $this->storage = new Migrate\Preference\PreviewMode();
    }

    public function handle(Request $request): void
    {
        if ( ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        $nonce = new PreviewNonce();

        if ( ! $nonce->verify($request)) {
            return;
        }

        $mode = $request->get('preview_method');
        $list_id = $request->get('layout');

        if ( ! $mode) {
            return;
        }

        if ('activate' === $mode && ListScreenId::is_valid_id($list_id)) {
            $this->storage->set_active(new ListScreenId($list_id));
        }
        if ('deactivate' === $mode) {
            $this->storage->set_inactive();
        }

        wp_safe_redirect(
            remove_query_arg(['ac_action', 'preview_method', $nonce->get_name()])
        );
        exit;
    }

}