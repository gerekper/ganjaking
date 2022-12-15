<?php

namespace WPMailSMTP\Vendor\Aws\Api\Serializer;

use WPMailSMTP\Vendor\Aws\Api\MapShape;
use WPMailSMTP\Vendor\Aws\Api\Service;
use WPMailSMTP\Vendor\Aws\Api\Operation;
use WPMailSMTP\Vendor\Aws\Api\Shape;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Aws\Api\TimestampShape;
use WPMailSMTP\Vendor\Aws\CommandInterface;
use WPMailSMTP\Vendor\Aws\EndpointV2\EndpointProviderV2;
use WPMailSMTP\Vendor\Aws\EndpointV2\EndpointV2SerializerTrait;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7\Uri;
use WPMailSMTP\Vendor\GuzzleHttp\Psr7\UriResolver;
use WPMailSMTP\Vendor\Psr\Http\Message\RequestInterface;
/**
 * Serializes HTTP locations like header, uri, payload, etc...
 * @internal
 */
abstract class RestSerializer
{
    use EndpointV2SerializerTrait;
    /** @var Service */
    private $api;
    /** @var Uri */
    private $endpoint;
    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     */
    public function __construct(\WPMailSMTP\Vendor\Aws\Api\Service $api, $endpoint)
    {
        $this->api = $api;
        $this->endpoint = \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::uriFor($endpoint);
    }
    /**
     * @param CommandInterface $command Command to serialize into a request.
     * @param $endpointProvider Provider used for dynamic endpoint resolution.
     * @param $clientArgs Client arguments used for dynamic endpoint resolution.
     *
     * @return RequestInterface
     */
    public function __invoke(\WPMailSMTP\Vendor\Aws\CommandInterface $command, $endpointProvider = null, $clientArgs = null)
    {
        $operation = $this->api->getOperation($command->getName());
        $commandArgs = $command->toArray();
        $opts = $this->serialize($operation, $commandArgs);
        $headers = isset($opts['headers']) ? $opts['headers'] : [];
        if ($endpointProvider instanceof \WPMailSMTP\Vendor\Aws\EndpointV2\EndpointProviderV2) {
            $this->setRequestOptions($endpointProvider, $command, $operation, $commandArgs, $clientArgs, $headers);
            $this->endpoint = new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Uri($this->endpoint);
        }
        $uri = $this->buildEndpoint($operation, $commandArgs, $opts);
        return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Request($operation['http']['method'], $uri, $headers, isset($opts['body']) ? $opts['body'] : null);
    }
    /**
     * Modifies a hash of request options for a payload body.
     *
     * @param StructureShape   $member  Member to serialize
     * @param array            $value   Value to serialize
     * @param array            $opts    Request options to modify.
     */
    protected abstract function payload(\WPMailSMTP\Vendor\Aws\Api\StructureShape $member, array $value, array &$opts);
    private function serialize(\WPMailSMTP\Vendor\Aws\Api\Operation $operation, array $args)
    {
        $opts = [];
        $input = $operation->getInput();
        // Apply the payload trait if present
        if ($payload = $input['payload']) {
            $this->applyPayload($input, $payload, $args, $opts);
        }
        foreach ($args as $name => $value) {
            if ($input->hasMember($name)) {
                $member = $input->getMember($name);
                $location = $member['location'];
                if (!$payload && !$location) {
                    $bodyMembers[$name] = $value;
                } elseif ($location == 'header') {
                    $this->applyHeader($name, $member, $value, $opts);
                } elseif ($location == 'querystring') {
                    $this->applyQuery($name, $member, $value, $opts);
                } elseif ($location == 'headers') {
                    $this->applyHeaderMap($name, $member, $value, $opts);
                }
            }
        }
        if (isset($bodyMembers)) {
            $this->payload($operation->getInput(), $bodyMembers, $opts);
        } else {
            if (!isset($opts['body']) && $this->hasPayloadParam($input, $payload)) {
                $this->payload($operation->getInput(), [], $opts);
            }
        }
        return $opts;
    }
    private function applyPayload(\WPMailSMTP\Vendor\Aws\Api\StructureShape $input, $name, array $args, array &$opts)
    {
        if (!isset($args[$name])) {
            return;
        }
        $m = $input->getMember($name);
        if ($m['streaming'] || ($m['type'] == 'string' || $m['type'] == 'blob')) {
            // Streaming bodies or payloads that are strings are
            // always just a stream of data.
            $opts['body'] = \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Utils::streamFor($args[$name]);
            return;
        }
        $this->payload($m, $args[$name], $opts);
    }
    private function applyHeader($name, \WPMailSMTP\Vendor\Aws\Api\Shape $member, $value, array &$opts)
    {
        if ($member->getType() === 'timestamp') {
            $timestampFormat = !empty($member['timestampFormat']) ? $member['timestampFormat'] : 'rfc822';
            $value = \WPMailSMTP\Vendor\Aws\Api\TimestampShape::format($value, $timestampFormat);
        } elseif ($member->getType() === 'boolean') {
            $value = $value ? 'true' : 'false';
        }
        if ($member['jsonvalue']) {
            $value = \json_encode($value);
            if (empty($value) && \JSON_ERROR_NONE !== \json_last_error()) {
                throw new \InvalidArgumentException('Unable to encode the provided value' . ' with \'json_encode\'. ' . \json_last_error_msg());
            }
            $value = \base64_encode($value);
        }
        $opts['headers'][$member['locationName'] ?: $name] = $value;
    }
    /**
     * Note: This is currently only present in the Amazon S3 model.
     */
    private function applyHeaderMap($name, \WPMailSMTP\Vendor\Aws\Api\Shape $member, array $value, array &$opts)
    {
        $prefix = $member['locationName'];
        foreach ($value as $k => $v) {
            $opts['headers'][$prefix . $k] = $v;
        }
    }
    private function applyQuery($name, \WPMailSMTP\Vendor\Aws\Api\Shape $member, $value, array &$opts)
    {
        if ($member instanceof \WPMailSMTP\Vendor\Aws\Api\MapShape) {
            $opts['query'] = isset($opts['query']) && \is_array($opts['query']) ? $opts['query'] + $value : $value;
        } elseif ($value !== null) {
            $type = $member->getType();
            if ($type === 'boolean') {
                $value = $value ? 'true' : 'false';
            } elseif ($type === 'timestamp') {
                $timestampFormat = !empty($member['timestampFormat']) ? $member['timestampFormat'] : 'iso8601';
                $value = \WPMailSMTP\Vendor\Aws\Api\TimestampShape::format($value, $timestampFormat);
            }
            $opts['query'][$member['locationName'] ?: $name] = $value;
        }
    }
    private function buildEndpoint(\WPMailSMTP\Vendor\Aws\Api\Operation $operation, array $args, array $opts)
    {
        // Create an associative array of variable definitions used in expansions
        $varDefinitions = $this->getVarDefinitions($operation, $args);
        $relative = \preg_replace_callback('/\\{([^\\}]+)\\}/', function (array $matches) use($varDefinitions) {
            $isGreedy = \substr($matches[1], -1, 1) == '+';
            $k = $isGreedy ? \substr($matches[1], 0, -1) : $matches[1];
            if (!isset($varDefinitions[$k])) {
                return '';
            }
            if ($isGreedy) {
                return \str_replace('%2F', '/', \rawurlencode($varDefinitions[$k]));
            }
            return \rawurlencode($varDefinitions[$k]);
        }, $operation['http']['requestUri']);
        // Add the query string variables or appending to one if needed.
        if (!empty($opts['query'])) {
            $relative = $this->appendQuery($opts['query'], $relative);
        }
        $path = $this->endpoint->getPath();
        //Accounts for trailing '/' in path when custom endpoint
        //is provided to endpointProviderV2
        if ($this->api->isModifiedModel() && $this->api->getServiceName() === 's3') {
            if (\substr($path, -1) === '/' && $relative[0] === '/') {
                $path = \rtrim($path, '/');
            }
            $relative = $path . $relative;
        }
        // If endpoint has path, remove leading '/' to preserve URI resolution.
        if ($path && $relative[0] === '/') {
            $relative = \substr($relative, 1);
        }
        //Append path to endpoint when leading '//...' present
        // as uri cannot be properly resolved
        if ($this->api->isModifiedModel() && \strpos($relative, '//') === 0) {
            return new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Uri($this->endpoint . $relative);
        }
        // Expand path place holders using Amazon's slightly different URI
        // template syntax.
        return \WPMailSMTP\Vendor\GuzzleHttp\Psr7\UriResolver::resolve($this->endpoint, new \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Uri($relative));
    }
    /**
     * @param StructureShape $input
     */
    private function hasPayloadParam(\WPMailSMTP\Vendor\Aws\Api\StructureShape $input, $payload)
    {
        if ($payload) {
            $potentiallyEmptyTypes = ['blob', 'string'];
            if ($this->api->getMetadata('protocol') == 'rest-xml') {
                $potentiallyEmptyTypes[] = 'structure';
            }
            $payloadMember = $input->getMember($payload);
            if (\in_array($payloadMember['type'], $potentiallyEmptyTypes)) {
                return \false;
            }
        }
        foreach ($input->getMembers() as $member) {
            if (!isset($member['location'])) {
                return \true;
            }
        }
        return \false;
    }
    private function appendQuery($query, $endpoint)
    {
        $append = \WPMailSMTP\Vendor\GuzzleHttp\Psr7\Query::build($query);
        return $endpoint .= \strpos($endpoint, '?') !== \false ? "&{$append}" : "?{$append}";
    }
    private function getVarDefinitions($command, $args)
    {
        $varDefinitions = [];
        foreach ($command->getInput()->getMembers() as $name => $member) {
            if ($member['location'] == 'uri') {
                $varDefinitions[$member['locationName'] ?: $name] = isset($args[$name]) ? $args[$name] : null;
            }
        }
        return $varDefinitions;
    }
}
