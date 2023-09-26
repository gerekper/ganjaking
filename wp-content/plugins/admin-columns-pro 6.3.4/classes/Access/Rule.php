<?php

namespace ACP\Access;

interface Rule
{

    public function get_permissions(): Permissions;

}