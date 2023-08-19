<?php

namespace ACP\Service;

use AC\Registerable;
use ACP\Access\PermissionChecker;
use ACP\Access\PermissionsStorage;

class Permissions implements Registerable
{

    private $permission_storage;

    private $permission_checker;

    public function __construct(PermissionsStorage $permission_storage, PermissionChecker $permission_checker)
    {
        $this->permission_storage = $permission_storage;
        $this->permission_checker = $permission_checker;
    }

    public function register(): void
    {
        $this->set_permissions();
    }

    public function set_permissions(): void
    {
        if ($this->permission_storage->exists()) {
            return;
        }

        $this->permission_checker->apply();
    }

}