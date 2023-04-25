<?php

declare (strict_types=1);
namespace WPMailSMTP\Vendor\GuzzleHttp\Psr7;

use WPMailSMTP\Vendor\Psr\Http\Message\RequestFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ServerRequestFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\ServerRequestInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\UploadedFileFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\UploadedFileInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\UriFactoryInterface;
use WPMailSMTP\Vendor\Psr\Http\Message\UriInterface;
/**
 * Implements all of the PSR-17 interfaces.
 *
 * Note: in consuming code it is recommended to require the implemented interfaces
 * and inject the instance of this class multiple times.
 */
final class HttpFactory implements \WPMailSMTP\Vendor\Psr\Http\Message\RequestFactoryInterface, \WPMailSMTP\Vendor\Psr\Http\Message\ResponseFactoryInterface, \WPMailSMTP\Vendor\Psr\Http\Message\ServerRequestFactoryInterface, \WPMailSMTP\Vendor\Psr\Http\Message\StreamFactoryInterface, \WPMailSMTP\Vendor\Psr\Http\Message\UploadedFileFactoryInterface, \WPMailSMTP\Vendor\Psr\Http\Message\UriFactoryInterface
{
    public function createUploadedFile(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, int $size = null, int $error = \UPLOAD_ERR_OK, string $clientFilename = null, string $clientMediaType = null) : \WPMailSMTP\Vendor\Psr\Http\Message\UploadedFileInterface
    {
        if ($size === null) {
            $size = $stream->getSize();
        }
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\UploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }
    public function createStream(string $content = '') : \WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface
    {
        return \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::streamFor($content);
    }
    public function createStreamFromFile(string $file, string $mode = 'r') : \WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface
    {
        try {
            $resource = \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::tryFopen($file, $mode);
        } catch (\RuntimeException $e) {
            if ('' === $mode || \false === \in_array($mode[0], ['r', 'w', 'a', 'x', 'c'], \true)) {
                throw new \InvalidArgumentException(\sprintf('Invalid file opening mode "%s"', $mode), 0, $e);
            }
            throw $e;
        }
        return \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createStreamFromResource($resource) : \WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface
    {
        return \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::streamFor($resource);
    }
    public function createServerRequest(string $method, $uri, array $serverParams = []) : \WPMailSMTP\Vendor\Psr\Http\Message\ServerRequestInterface
    {
        if (empty($method)) {
            if (!empty($serverParams['REQUEST_METHOD'])) {
                $method = $serverParams['REQUEST_METHOD'];
            } else {
                throw new \InvalidArgumentException('Cannot determine HTTP method');
            }
        }
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\ServerRequest($method, $uri, [], null, '1.1', $serverParams);
    }
    public function createResponse(int $code = 200, string $reasonPhrase = '') : \WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface
    {
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Response($code, [], null, '1.1', $reasonPhrase);
    }
    public function createRequest(string $method, $uri) : \WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface
    {
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request($method, $uri);
    }
    public function createUri(string $uri = '') : \WPMailSMTP\Vendor\Psr\Http\Message\UriInterface
    {
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Uri($uri);
    }
}
