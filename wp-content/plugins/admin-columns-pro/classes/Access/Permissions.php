<?php

namespace ACP\Access;

final class Permissions
{

    public const UPDATE = 'update';
    public const USAGE = 'usage';

    /**
     * @var array
     */
    private $permissions;

    public function __construct(array $permissions = [])
    {
        $this->permissions = $permissions;
    }

    public function with_permission(string $permission): self
    {
        $permissions = $this->to_array();
        $permissions[] = $permission;

        return new self($permissions);
    }

    public function to_array(): array
    {
        $permissions = array_unique($this->permissions);

        return array_filter($permissions, static function ($permission): bool {
            return in_array($permission, [self::USAGE, self::UPDATE], true);
        });
    }

    public function has_permission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }

    public function has_usage_permission(): bool
    {
        return true;

        return $this->has_permission(self::USAGE);
    }

    public function has_updates_permission(): bool
    {
        return $this->has_permission(self::UPDATE);
    }

}