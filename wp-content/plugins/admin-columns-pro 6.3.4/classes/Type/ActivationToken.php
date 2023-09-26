<?php

namespace ACP\Type;

interface ActivationToken
{

    public function get_token(): string;

    public function get_type(): string;

}