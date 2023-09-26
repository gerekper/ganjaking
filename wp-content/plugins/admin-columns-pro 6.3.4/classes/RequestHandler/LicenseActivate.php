<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use ACP\Access\ActivationKeyStorage;
use ACP\Access\ActivationUpdater;
use ACP\Access\PermissionChecker;
use ACP\Access\PermissionsStorage;
use ACP\Access\Rule\ApiActivateResponse;
use ACP\API;
use ACP\ApiFactory;
use ACP\Nonce;
use ACP\RequestHandler;
use ACP\Type\Activation\Key;
use ACP\Type\LicenseKey;
use ACP\Type\SiteUrl;
use ACP\Updates\PluginDataUpdater;
use InvalidArgumentException;

class LicenseActivate implements RequestHandler
{

    /**
     * @var ActivationKeyStorage
     */
    private $activation_key_storage;

    /**
     * @var ApiFactory
     */
    private $api_factory;

    /**
     * @var SiteUrl
     */
    private $site_url;

    /**
     * @var PluginDataUpdater
     */
    private $products_updater;

    /**
     * @var ActivationUpdater
     */
    private $activation_updater;

    /**
     * @var PermissionChecker
     */
    private $permission_checker;

    /**
     * @var PermissionsStorage
     */
    private $permission_storage;

    public function __construct(
        ActivationKeyStorage $activation_key_storage,
        ApiFactory $api_factory,
        SiteUrl $site_url,
        PluginDataUpdater $products_updater,
        ActivationUpdater $activation_updater,
        PermissionChecker $permission_checker,
        PermissionsStorage $permission_storage
    ) {
        $this->activation_key_storage = $activation_key_storage;
        $this->api_factory = $api_factory;
        $this->site_url = $site_url;
        $this->products_updater = $products_updater;
        $this->activation_updater = $activation_updater;
        $this->permission_checker = $permission_checker;
        $this->permission_storage = $permission_storage;
    }

    public function handle(Request $request): void
    {
        if ( ! current_user_can(Capabilities::MANAGE)) {
            return;
        }

        if ( ! (new Nonce\LicenseNonce())->verify($request)) {
            return;
        }

        $key = sanitize_text_field($request->get('license'));

        if ( ! $key) {
            $this->error_notice(__('Empty license key.', 'codepress-admin-columns'));

            return;
        }

        if ( ! LicenseKey::is_valid($key)) {
            $this->error_notice(__('Invalid license key.', 'codepress-admin-columns'));

            return;
        }

        $license_key = new LicenseKey($key);

        $response = $this->api_factory->create()->dispatch(
            new API\Request\Activate($license_key, $this->site_url)
        );

        $this->permission_checker
            ->add_rule(new ApiActivateResponse($response))
            ->apply();

        if ($response->has_error()) {
            $this->error_notice($response->get_error()->get_error_message());

            if ($this->permission_storage->retrieve()->has_usage_permission()) {
                $this->succes_notice(
                    __('Product is activated, but automatic updates are disabled.', 'codepress-admin-columns')
                );
            }

            return;
        }

        try {
            $activation_key = new Key($response->get('activation_key'));
        } catch (InvalidArgumentException $e) {
            $this->error_notice($e->getMessage());

            return;
        }

        $this->activation_key_storage->save($activation_key);
        $this->activation_updater->update($activation_key);
        $this->products_updater->update($activation_key);

        wp_clean_plugins_cache();
        wp_update_plugins();

        $this->succes_notice($response->get('message'));
    }

    private function succes_notice(string $message): void
    {
        (new Notice($message))->register();
    }

    private function error_notice($message): void
    {
        (new Notice($message))->set_type(Message::ERROR)->register();
    }

}