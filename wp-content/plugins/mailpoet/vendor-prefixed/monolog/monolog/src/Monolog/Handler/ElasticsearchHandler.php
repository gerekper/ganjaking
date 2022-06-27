<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use Throwable;
use RuntimeException;
use MailPoetVendor\Monolog\Logger;
use MailPoetVendor\Monolog\Formatter\FormatterInterface;
use MailPoetVendor\Monolog\Formatter\ElasticsearchFormatter;
use InvalidArgumentException;
use MailPoetVendor\Elasticsearch\Common\Exceptions\RuntimeException as ElasticsearchRuntimeException;
use MailPoetVendor\Elasticsearch\Client;
class ElasticsearchHandler extends AbstractProcessingHandler
{
 protected $client;
 protected $options = [];
 public function __construct(Client $client, array $options = [], $level = Logger::DEBUG, bool $bubble = \true)
 {
 parent::__construct($level, $bubble);
 $this->client = $client;
 $this->options = \array_merge([
 'index' => 'monolog',
 // Elastic index name
 'type' => '_doc',
 // Elastic document type
 'ignore_error' => \false,
 ], $options);
 }
 protected function write(array $record) : void
 {
 $this->bulkSend([$record['formatted']]);
 }
 public function setFormatter(FormatterInterface $formatter) : HandlerInterface
 {
 if ($formatter instanceof ElasticsearchFormatter) {
 return parent::setFormatter($formatter);
 }
 throw new InvalidArgumentException('ElasticsearchHandler is only compatible with ElasticsearchFormatter');
 }
 public function getOptions() : array
 {
 return $this->options;
 }
 protected function getDefaultFormatter() : FormatterInterface
 {
 return new ElasticsearchFormatter($this->options['index'], $this->options['type']);
 }
 public function handleBatch(array $records) : void
 {
 $documents = $this->getFormatter()->formatBatch($records);
 $this->bulkSend($documents);
 }
 protected function bulkSend(array $records) : void
 {
 try {
 $params = ['body' => []];
 foreach ($records as $record) {
 $params['body'][] = ['index' => ['_index' => $record['_index'], '_type' => $record['_type']]];
 unset($record['_index'], $record['_type']);
 $params['body'][] = $record;
 }
 $responses = $this->client->bulk($params);
 if ($responses['errors'] === \true) {
 throw $this->createExceptionFromResponses($responses);
 }
 } catch (Throwable $e) {
 if (!$this->options['ignore_error']) {
 throw new RuntimeException('Error sending messages to Elasticsearch', 0, $e);
 }
 }
 }
 protected function createExceptionFromResponses(array $responses) : ElasticsearchRuntimeException
 {
 foreach ($responses['items'] ?? [] as $item) {
 if (isset($item['index']['error'])) {
 return $this->createExceptionFromError($item['index']['error']);
 }
 }
 return new ElasticsearchRuntimeException('Elasticsearch failed to index one or more records.');
 }
 protected function createExceptionFromError(array $error) : ElasticsearchRuntimeException
 {
 $previous = isset($error['caused_by']) ? $this->createExceptionFromError($error['caused_by']) : null;
 return new ElasticsearchRuntimeException($error['type'] . ': ' . $error['reason'], 0, $previous);
 }
}
