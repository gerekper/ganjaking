<?php

namespace WPMailSMTP\Vendor\Aws\Signature;

use WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface;
use WPMailSMTP\Vendor\AWS\CRT\Auth\SignatureType;
use WPMailSMTP\Vendor\AWS\CRT\Auth\Signing;
use WPMailSMTP\Vendor\AWS\CRT\Auth\SigningAlgorithm;
use WPMailSMTP\Vendor\AWS\CRT\Auth\SigningConfigAWS;
use WPMailSMTP\Vendor\AWS\CRT\Auth\StaticCredentialsProvider;
use WPMailSMTP\Vendor\AWS\CRT\HTTP\Request;
use WPMailSMTP\Vendor\AWS\CRT\IO\InputStream;
use WPMailSMTP\Vendor\AWS\CRT\Auth\Signable;
use WPMailSMTP\Vendor\Aws\Exception\AwsException;
use WPMailSMTP\Vendor\Aws\Exception\CommonRuntimeException;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * Amazon S3 signature version 4 support.
 */
class S3SignatureV4 extends \WPMailSMTP\Vendor\Aws\Signature\SignatureV4
{
    /**
     * S3-specific signing logic
     *
     * {@inheritdoc}
     */
    public function signRequest(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, \WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials, $signingService = null)
    {
        // Always add a x-amz-content-sha-256 for data integrity
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader('x-amz-content-sha256', $this->getPayload($request));
        }
        $useCrt = \strpos($request->getUri()->getHost(), "accesspoint.s3-global") !== \false;
        if (!$useCrt) {
            if (\strpos($request->getUri()->getHost(), "s3-object-lambda")) {
                return parent::signRequest($request, $credentials, "s3-object-lambda");
            }
            return parent::signRequest($request, $credentials);
        }
        return $this->signWithV4a($credentials, $request, $signingService);
    }
    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     *
     * {@inheritdoc}
     */
    public function presign(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, \WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials, $expires, array $options = [])
    {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader('X-Amz-Content-Sha256', $this->getPresignedPayload($request));
        }
        if (\strpos($request->getUri()->getHost(), "accesspoint.s3-global")) {
            $request = $request->withHeader("x-amz-region-set", "*");
        }
        return parent::presign($request, $credentials, $expires, $options);
    }
    /**
     * Override used to allow pre-signed URLs to be created for an
     * in-determinate request payload.
     */
    protected function getPresignedPayload(\WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request)
    {
        return \WPMailSMTP\Vendor\Aws\Signature\SignatureV4::UNSIGNED_PAYLOAD;
    }
    /**
     * Amazon S3 does not double-encode the path component in the canonical request
     */
    protected function createCanonicalizedPath($path)
    {
        // Only remove one slash in case of keys that have a preceding slash
        if (\substr($path, 0, 1) === '/') {
            $path = \substr($path, 1);
        }
        return '/' . $path;
    }
    /**
     * @param CredentialsInterface $credentials
     * @param RequestInterface $request
     * @param $signingService
     * @return RequestInterface
     */
    private function signWithV4a(\WPMailSMTP\Vendor\Aws\Credentials\CredentialsInterface $credentials, \WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface $request, $signingService)
    {
        if (!\extension_loaded('awscrt')) {
            throw new \WPMailSMTP\Vendor\Aws\Exception\CommonRuntimeException("AWS Common Runtime for PHP is required to use Signature V4A and multi-region" . " access points.  Please install it using the instructions found at" . " https://github.com/aws/aws-sdk-php/blob/master/CRT_INSTRUCTIONS.md");
        }
        $credentials_provider = new \WPMailSMTP\Vendor\AWS\CRT\Auth\StaticCredentialsProvider(['access_key_id' => $credentials->getAccessKeyId(), 'secret_access_key' => $credentials->getSecretKey(), 'session_token' => $credentials->getSecurityToken()]);
        $signingService = $signingService ?: 's3';
        $sha = $this->getPayload($request);
        $signingConfig = new \WPMailSMTP\Vendor\AWS\CRT\Auth\SigningConfigAWS(['algorithm' => \WPMailSMTP\Vendor\AWS\CRT\Auth\SigningAlgorithm::SIGv4_ASYMMETRIC, 'signature_type' => \WPMailSMTP\Vendor\AWS\CRT\Auth\SignatureType::HTTP_REQUEST_HEADERS, 'credentials_provider' => $credentials_provider, 'signed_body_value' => $sha, 'region' => "*", 'service' => $signingService, 'date' => \time()]);
        $sha = $request->getHeader("x-amz-content-sha256");
        $request = $request->withoutHeader("x-amz-content-sha256");
        $invocationId = $request->getHeader("aws-sdk-invocation-id");
        $retry = $request->getHeader("aws-sdk-retry");
        $request = $request->withoutHeader("aws-sdk-invocation-id");
        $request = $request->withoutHeader("aws-sdk-retry");
        $http_request = new \WPMailSMTP\Vendor\AWS\CRT\HTTP\Request($request->getMethod(), (string) $request->getUri(), [], \array_map(function ($header) {
            return $header[0];
        }, $request->getHeaders()));
        \WPMailSMTP\Vendor\AWS\CRT\Auth\Signing::signRequestAws(\WPMailSMTP\Vendor\AWS\CRT\Auth\Signable::fromHttpRequest($http_request), $signingConfig, function ($signing_result, $error_code) use(&$http_request) {
            $signing_result->applyToHttpRequest($http_request);
        });
        $sigV4AHeaders = $http_request->headers();
        foreach ($sigV4AHeaders->toArray() as $h => $v) {
            $request = $request->withHeader($h, $v);
        }
        $request = $request->withHeader("aws-sdk-invocation-id", $invocationId);
        $request = $request->withHeader("x-amz-content-sha256", $sha);
        $request = $request->withHeader("aws-sdk-retry", $retry);
        $request = $request->withHeader("x-amz-region-set", "*");
        return $request;
    }
}
