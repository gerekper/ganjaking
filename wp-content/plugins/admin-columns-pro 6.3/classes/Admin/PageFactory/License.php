<?php

declare(strict_types=1);

namespace ACP\Admin\PageFactory;

use AC;
use AC\Admin\MenuFactoryInterface;
use AC\Admin\PageFactoryInterface;
use AC\Asset\Location;
use ACP\Access\ActivationStorage;
use ACP\Access\PermissionsStorage;
use ACP\ActivationTokenFactory;
use ACP\Admin\MenuFactory;
use ACP\Admin\Page;
use ACP\LicenseKeyRepository;
use ACP\Type\SiteUrl;

class License implements PageFactoryInterface
{

    /**
     * @var Location\Absolute
     */
    private $location;

    /**
     * @var MenuFactoryInterface
     */
    private $menu_factory;

    /**
     * @var SiteUrl
     */
    private $site_url;

    /**
     * @var ActivationTokenFactory
     */
    private $activation_token_factory;

    /**
     * @var ActivationStorage
     */
    private $activation_storage;

    /**
     * @var PermissionsStorage
     */
    private $permission_storage;

    /**
     * @var LicenseKeyRepository
     */
    private $license_key_repository;

    private $plugin;

    public function __construct(
        Location\Absolute $location,
        MenuFactory $menu_factory,
        SiteUrl $site_url,
        ActivationTokenFactory $activation_token_factory,
        ActivationStorage $activation_storage,
        PermissionsStorage $permission_storage,
        LicenseKeyRepository $license_key_repository,
        AC\Entity\Plugin $plugin
    ) {
        $this->location = $location;
        $this->menu_factory = $menu_factory;
        $this->site_url = $site_url;
        $this->activation_token_factory = $activation_token_factory;
        $this->activation_storage = $activation_storage;
        $this->permission_storage = $permission_storage;
        $this->license_key_repository = $license_key_repository;
        $this->plugin = $plugin;
    }

    public function create()
    {
        return new Page\License(
            $this->location,
            new AC\Admin\View\Menu($this->menu_factory->create('license')),
            $this->site_url,
            $this->activation_token_factory,
            $this->activation_storage,
            $this->permission_storage,
            $this->license_key_repository,
            $this->plugin
        );
    }

}