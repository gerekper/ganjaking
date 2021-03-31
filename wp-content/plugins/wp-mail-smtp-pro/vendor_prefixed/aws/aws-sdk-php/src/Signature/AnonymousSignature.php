<?php

namespace WPMailSMTP\Vendor\Aws\Signature;

use WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * Provides anonymous client access (does not sign requests).
 */
class AnonymousSignature implements \WPMailSMTP\Vendor\Aws\Signature\SignatureInterface
{
    /**
     * /** {@inheritdoc}
     */
    public function signRequest(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, \WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials)
    {
        return $request;
    }
    /**
     * /** {@inheritdoc}
     */
    public function presign(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, \WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = [])
    {
        return $request;
    }
}
