<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SearchWP\Dependencies\Monolog\Handler;

use SearchWP\Dependencies\Aws\Sdk;
use SearchWP\Dependencies\Aws\DynamoDb\DynamoDbClient;
use SearchWP\Dependencies\Monolog\Formatter\FormatterInterface;
use SearchWP\Dependencies\Aws\DynamoDb\Marshaler;
use SearchWP\Dependencies\Monolog\Formatter\ScalarFormatter;
use SearchWP\Dependencies\Monolog\Logger;
/**
 * Amazon DynamoDB handler (http://aws.amazon.com/dynamodb/)
 *
 * @link https://github.com/aws/aws-sdk-php/
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class DynamoDbHandler extends \SearchWP\Dependencies\Monolog\Handler\AbstractProcessingHandler
{
    public const DATE_FORMAT = 'Y-m-d\\TH:i:s.uO';
    /**
     * @var DynamoDbClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var int
     */
    protected $version;
    /**
     * @var Marshaler
     */
    protected $marshaler;
    /**
     * @param int|string $level
     */
    public function __construct(\SearchWP\Dependencies\Aws\DynamoDb\DynamoDbClient $client, string $table, $level = \SearchWP\Dependencies\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        if (\defined('Aws\\Sdk::VERSION') && \version_compare(\SearchWP\Dependencies\Aws\Sdk::VERSION, '3.0', '>=')) {
            $this->version = 3;
            $this->marshaler = new \SearchWP\Dependencies\Aws\DynamoDb\Marshaler();
        } else {
            $this->version = 2;
        }
        $this->client = $client;
        $this->table = $table;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritdoc}
     */
    protected function write(array $record) : void
    {
        $filtered = $this->filterEmptyFields($record['formatted']);
        if ($this->version === 3) {
            $formatted = $this->marshaler->marshalItem($filtered);
        } else {
            $formatted = $this->client->formatAttributes($filtered);
        }
        $this->client->putItem(['TableName' => $this->table, 'Item' => $formatted]);
    }
    protected function filterEmptyFields(array $record) : array
    {
        return \array_filter($record, function ($value) {
            return !empty($value) || \false === $value || 0 === $value;
        });
    }
    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter() : \SearchWP\Dependencies\Monolog\Formatter\FormatterInterface
    {
        return new \SearchWP\Dependencies\Monolog\Formatter\ScalarFormatter(self::DATE_FORMAT);
    }
}
