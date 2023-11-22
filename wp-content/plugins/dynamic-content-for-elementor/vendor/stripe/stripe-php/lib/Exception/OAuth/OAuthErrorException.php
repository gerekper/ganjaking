<?php

namespace DynamicOOOS\Stripe\Exception\OAuth;

/**
 * Implements properties and methods common to all (non-SPL) Stripe OAuth
 * exceptions.
 */
abstract class OAuthErrorException extends \DynamicOOOS\Stripe\Exception\ApiErrorException
{
    protected function constructErrorObject()
    {
        if (null === $this->jsonBody) {
            return null;
        }
        return \DynamicOOOS\Stripe\OAuthErrorObject::constructFrom($this->jsonBody);
    }
}
